<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\UnitKerja; // Tambahkan model UnitKerja
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    private function checkAdminOrKepegawaian(): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (!hasAnyRole(['admin', 'kepegawaian'])) {
            return redirect()->to(site_url('daftar-pegawai'))->with('error', 'Anda tidak memiliki hak akses ke modul Kelola Pengguna.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAdminOrKepegawaian()) {
            return $redirect;
        }

        $data = [
            'title' => 'Kelola Pengguna',
        ];
        
        $search = $this->request->getGet('search');
        $role = $this->request->getGet('role');
        $unit = $this->request->getGet('unit');
        $statusPegawai = $this->request->getGet('status_pegawai');
        $perPage = $this->request->getGet('per_page') ?? 10;
        
        $sortBy = $this->request->getGet('sort_by') ?? 'users.nama_lengkap';
        $sortOrder = $this->request->getGet('sort_order') ?? 'asc';

        // Join dengan tabel users (self join) untuk mengambil nama atasan (kueri selektif tanpa hash password)
        $query = $this->userModel->select('users.id, users.nama_lengkap, users.nip, users.username, users.email, users.jabatan, users.pangkat, users.unit, users.unit_id, users.role, users.status_pegawai, users.tmt_selesai, users.dispensasi_lampau_sampai, users.foto, users.atasan_id, atasan.nama_lengkap as nama_atasan')
                                 ->join('users as atasan', 'atasan.id = users.atasan_id', 'left');

        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('users.nama_lengkap', $search)
                           ->orLike('users.nip', $search)
                           ->orLike('users.username', $search)
                           ->groupEnd();
        }
        
        if (!empty($role)) {
            $targetRoles = ($role === 'kabag') ? ['kabag', 'kabag_aak', 'kabag_kuk'] : [$role];
            $db = \Config\Database::connect();
            $matchedPivotIds = array_column(
                $db->table('user_roles')->select('user_id')->whereIn('role_name', $targetRoles)->get()->getResultArray(),
                'user_id'
            );

            $query = $query->groupStart()
                           ->whereIn('users.role', $targetRoles);
            if (!empty($matchedPivotIds)) {
                $query = $query->orWhereIn('users.id', $matchedPivotIds);
            }
            $query = $query->groupEnd();
        }
        
        if (!empty($unit)) {
            if ($unit === 'kosong') {
                $query = $query->groupStart()
                               ->where('users.unit', '')
                               ->orWhere('users.unit IS NULL')
                               ->groupEnd();
            } else {
                $query = $query->where('users.unit', $unit);
            }
        }

        if (!empty($statusPegawai)) {
            $query = $query->where('users.status_pegawai', $statusPegawai);
        }

        $allowedSorts = [
            'users.id'            => 'users.id',
            'id'                  => 'users.id',
            'users.nama_lengkap'  => 'users.nama_lengkap',
            'nama_lengkap'        => 'users.nama_lengkap',
            'users.jabatan'       => 'users.jabatan',
            'jabatan'             => 'users.jabatan',
            'users.unit'          => 'users.unit',
            'unit'                => 'users.unit',
            'users.role'          => 'users.role',
            'role'                => 'users.role',
            'users.status_pegawai'=> 'users.status_pegawai',
            'status_pegawai'      => 'users.status_pegawai',
            'atasan.nama_lengkap' => 'atasan.nama_lengkap',
            'nama_atasan'         => 'atasan.nama_lengkap',
        ];
        $sortColumn = $allowedSorts[$sortBy] ?? 'users.nama_lengkap';
        $sortOrder = (strtolower($sortOrder) === 'desc') ? 'desc' : 'asc';
        $query = $query->orderBy($sortColumn, $sortOrder);
        
        // Load all data for client-side infinite scroll
        $data['users'] = $query->findAll();
        $data['pager'] = null;

        // Ambil pemetaan pimpinan per unit untuk referensi sinkronisasi visual di View
        $pimpinanUnits = $this->userModel->select('unit, nama_lengkap')
                                         ->whereIn('role', ['manajemen', 'kabag_aak', 'kabag_kuk', 'kabag'])
                                         ->where('unit !=', '')
                                         ->where('unit IS NOT NULL')
                                         ->findAll();
        $unitManagers = [];
        foreach ($pimpinanUnits as $p) {
            $unitManagers[$p['unit']] = $p['nama_lengkap'];
        }
        $data['unitManagers'] = $unitManagers;

        $data['search'] = $search;
        $data['filter_role'] = $role;
        $data['filter_unit'] = $unit;
        $data['filter_status_pegawai'] = $statusPegawai;
        $data['per_page'] = $perPage;
        $data['sortBy'] = $sortBy;
        $data['sortOrder'] = $sortOrder;

        // Ambil data pimpinan untuk dropdown atasan di modal batch edit (hanya role pimpinan & kolom selektif)
        $bossRoles = ['direktur', 'wadir', 'manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kanit', 'katim', 'kapokja', 'kepegawaian'];
        $data['potential_bosses'] = $this->userModel->select('id, nama_lengkap, jabatan, role')
                                                    ->whereIn('role', $bossRoles)
                                                    ->orderBy('nama_lengkap', 'ASC')
                                                    ->findAll();

        // Ambil master unit kerja (kueri tunggal)
        $unitKerjaModel = new UnitKerja();
        $data['unit_kerja_list'] = $unitKerjaModel->orderBy('nama_unit', 'ASC')->findAll();
        $unitKerjaMap = array_column($data['unit_kerja_list'], 'parent_unit', 'nama_unit');
        
        foreach($data['users'] as &$u) {
            // Jika nama_atasan null, ubah jadi strip
            $u['nama_atasan'] = $u['nama_atasan'] ?? '-';
            
            // Tentukan Unit Kabag (AAK/KUK) dari master data unit kerja
            $u['unit_kabag'] = null; // Default
            if (!empty($u['unit']) && isset($unitKerjaMap[$u['unit']])) {
                $u['unit_kabag'] = $unitKerjaMap[$u['unit']];
            }
        }

        return view('admin/users', $data);
    }

    // --- FITUR BARU: CREATE ---
    public function create()
    {
        if ($redirect = $this->checkAdminOrKepegawaian()) {
            return $redirect;
        }

        // Ambil list semua user untuk dropdown "Pilih Atasan" (Termasuk Katim & Staf Kepegawaian)
        $bossRoles = ['direktur', 'wadir', 'manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kanit', 'katim', 'kapokja', 'kepegawaian'];
        $potentialBosses = $this->userModel->whereIn('role', $bossRoles)->orderBy('nama_lengkap', 'ASC')->findAll();
        $unitKerjaModel = new UnitKerja();

        $data = [
            'title' => 'Tambah Pengguna Baru',
            'potential_bosses' => $potentialBosses,
            'unit_kerja_list' => $unitKerjaModel->orderBy('nama_unit', 'ASC')->findAll()
        ];

        return view('admin/user_create', $data);
    }

    public function store()
    {
        if ($redirect = $this->checkAdminOrKepegawaian()) {
            return $redirect;
        }

        // Validasi input dasar
        // PERBAIKAN: min_length(4) diubah menjadi min_length[4]
        if (!$this->validate([
            'username' => [
                'rules'  => 'required|is_unique[users.username]',
                'errors' => [
                    'required'  => 'Username (atau NIP) wajib diisi.',
                    'is_unique' => 'Gagal: Username / NIP tersebut sudah terdaftar di sistem. Silakan gunakan yang lain.'
                ]
            ],
            'email' => [
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.'
                ]
            ],
            'password' => [
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'Password wajib diisi.',
                    'min_length' => 'Password minimal harus terdiri dari 6 karakter.'
                ]
            ],
            'nama_lengkap' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi.'
                ]
            ],
            'status_pegawai' => [
                'rules'  => 'permit_empty|in_list[aktif,mutasi_keluar,pensiun,nonaktif]',
                'errors' => [
                    'in_list' => 'Status pegawai tidak valid.'
                ]
            ],
            'tmt_selesai' => [
                'rules'  => 'permit_empty|valid_date[Y-m-d]',
                'errors' => [
                    'valid_date' => 'Format tanggal TMT selesai tidak valid.'
                ]
            ],
            'dispensasi_lampau_sampai' => [
                'rules'  => 'permit_empty|valid_date[Y-m-d]',
                'errors' => [
                    'valid_date' => 'Format tanggal dispensasi tidak valid.'
                ]
            ]
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $unitInput = $this->request->getPost('unit_id') ?? $this->request->getPost('unit');
        $unitId = null;
        $unitNama = '';
        if (!empty($unitInput)) {
            $unitKerjaModel = new UnitKerja();
            if (is_numeric($unitInput)) {
                $unitDb = $unitKerjaModel->find($unitInput);
                if ($unitDb) {
                    $unitId = $unitDb['id'];
                    $unitNama = $unitDb['nama_unit'];
                }
            } else {
                $unitNama = trim((string)$unitInput);
                $unitDb = $unitKerjaModel->where('nama_unit', $unitNama)->first();
                if ($unitDb) {
                    $unitId = $unitDb['id'];
                }
            }
        }

        $role = $this->request->getPost('role');
        $statusPegawai = $this->request->getPost('status_pegawai') ?: 'aktif';
        if (!in_array($statusPegawai, ['aktif', 'mutasi_keluar', 'pensiun', 'nonaktif'])) {
            $statusPegawai = 'aktif';
        }
        $tmtSelesai = ($statusPegawai !== 'aktif') ? ($this->request->getPost('tmt_selesai') ?: null) : null;
        $dispensasiSampai = $this->request->getPost('dispensasi_lampau_sampai') ?: null;

        $data = [
            'nama_lengkap'             => $this->request->getPost('nama_lengkap'),
            'nip'                      => $this->request->getPost('nip'),
            'jabatan'                  => $this->request->getPost('jabatan'),
            'pangkat'                  => $this->request->getPost('pangkat'),
            'unit'                     => $unitNama,
            'unit_id'                  => $unitId,
            'role'                     => $role,
            'status_pegawai'           => $statusPegawai,
            'tmt_selesai'              => !empty($tmtSelesai) ? $tmtSelesai : null,
            'dispensasi_lampau_sampai' => !empty($dispensasiSampai) ? $dispensasiSampai : null,
            'email'                    => $this->request->getPost('email'),
            'username'                 => $this->request->getPost('username'),
            'password'                 => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        // Logic Atasan
        $atasan_id = $this->request->getPost('atasan_id');
        if (!empty($atasan_id)) {
            $data['atasan_id'] = $atasan_id;
        }

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $insertID = $this->userModel->insert($data);

            // 1. Insert role primer (wajib ada)
            $db->table('user_roles')->insert([
                'user_id' => $insertID,
                'role_name' => strtolower($role),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // 2. Insert role sekunder yang dicentang
            $secondaryRoles = $this->request->getPost('secondary_roles');
            if (!empty($secondaryRoles) && is_array($secondaryRoles)) {
                foreach ($secondaryRoles as $secRole) {
                    // Jangan masukkan lagi jika sama dengan role primer
                    if (strtolower($secRole) !== strtolower($role)) {
                        $db->table('user_roles')->insert([
                            'user_id' => $insertID,
                            'role_name' => strtolower($secRole),
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }

            log_audit('CREATE', 'users', $insertID, null, $data);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data pengguna.');
            }

            return redirect()->to('users')->with('success', 'Pengguna baru berhasil ditambahkan.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data pengguna: ' . $e->getMessage());
        }
    }
    // --- END FITUR BARU ---

    public function edit($id)
    {
        if ($redirect = $this->checkAdminOrKepegawaian()) {
            return $redirect;
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('users')->with('error', 'User tidak ditemukan');
        }

        // Ambil data atasan jika ada (menggunakan atasan_id yang tersimpan di DB)
        $user['auto_synced_atasan'] = false;

        // Kecualikan diri sendiri dari list atasan (termasuk Katim/Staf Kepegawaian)
        $bossRoles = ['direktur', 'wadir', 'manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kanit', 'katim', 'kapokja', 'kepegawaian'];
        $potentialBosses = $this->userModel->where('id !=', $id)->whereIn('role', $bossRoles)->orderBy('nama_lengkap', 'ASC')->findAll();
        $unitKerjaModel = new UnitKerja();

        // Fetch secondary roles from pivot table
        $db = \Config\Database::connect();
        $userRoles = $db->table('user_roles')->where('user_id', $id)->get()->getResultArray();
        $secondaryRoles = array_column($userRoles, 'role_name');

        $data = [
            'title' => 'Edit Pengguna & Atasan',
            'user'  => $user,
            'secondary_roles' => $secondaryRoles,
            'potential_bosses' => $potentialBosses,
            'unit_kerja_list' => $unitKerjaModel->orderBy('nama_unit', 'ASC')->findAll(),
            'query_string' => $this->request->getServer('QUERY_STRING')
        ];

        return view('admin/user_edit', $data);
    }

    public function update()
    {
        if ($redirect = $this->checkAdminOrKepegawaian()) {
            return $redirect;
        }

        $id = $this->request->getPost('id');

        if (!$this->validate([
            'username' => [
                'rules'  => "required|is_unique[users.username,id,{$id}]",
                'errors' => [
                    'required'  => 'Username (atau NIP) wajib diisi.',
                    'is_unique' => 'Gagal: Username / NIP tersebut sudah terdaftar di sistem. Silakan gunakan yang lain.'
                ]
            ],
            'email' => [
                'rules'  => "permit_empty|valid_email|is_unique[users.email,id,{$id}]",
                'errors' => [
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique'   => 'Email tersebut sudah digunakan oleh akun lain.'
                ]
            ],
            'password' => [
                'rules'  => 'permit_empty|min_length[6]',
                'errors' => [
                    'min_length' => 'Password baru minimal harus terdiri dari 6 karakter.'
                ]
            ],
            'nama_lengkap' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi.'
                ]
            ],
            'status_pegawai' => [
                'rules'  => 'permit_empty|in_list[aktif,mutasi_keluar,pensiun,nonaktif]',
                'errors' => [
                    'in_list' => 'Status pegawai tidak valid.'
                ]
            ],
            'tmt_selesai' => [
                'rules'  => 'permit_empty|valid_date[Y-m-d]',
                'errors' => [
                    'valid_date' => 'Format tanggal TMT selesai tidak valid.'
                ]
            ],
            'dispensasi_lampau_sampai' => [
                'rules'  => 'permit_empty|valid_date[Y-m-d]',
                'errors' => [
                    'valid_date' => 'Format tanggal dispensasi tidak valid.'
                ]
            ]
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $existingUser = $this->userModel->find($id);

        $unitInput = $this->request->getPost('unit_id') ?? $this->request->getPost('unit');
        $unitId = null;
        $unitNama = '';
        if (!empty($unitInput)) {
            $unitKerjaModel = new UnitKerja();
            if (is_numeric($unitInput)) {
                $unitDb = $unitKerjaModel->find($unitInput);
                if ($unitDb) {
                    $unitId = $unitDb['id'];
                    $unitNama = $unitDb['nama_unit'];
                }
            } else {
                $unitNama = trim((string)$unitInput);
                $unitDb = $unitKerjaModel->where('nama_unit', $unitNama)->first();
                if ($unitDb) {
                    $unitId = $unitDb['id'];
                }
            }
        }

        $role = $this->request->getPost('role');
        $statusPegawai = $this->request->getPost('status_pegawai') ?: 'aktif';

        // Kunci status akun Superadmin utama agar selalu 'aktif'
        if ($existingUser && ($existingUser['username'] === 'admin' || $existingUser['role'] === 'admin')) {
            $statusPegawai = 'aktif';
        } elseif (!in_array($statusPegawai, ['aktif', 'mutasi_keluar', 'pensiun', 'nonaktif'])) {
            $statusPegawai = 'aktif';
        }

        $tmtSelesai = ($statusPegawai !== 'aktif') ? ($this->request->getPost('tmt_selesai') ?: null) : null;
        $dispensasiSampai = $this->request->getPost('dispensasi_lampau_sampai') ?: null;

        $data = [
            'nama_lengkap'             => $this->request->getPost('nama_lengkap'),
            'nip'                      => $this->request->getPost('nip'),
            'jabatan'                  => $this->request->getPost('jabatan'),
            'pangkat'                  => $this->request->getPost('pangkat'),
            'unit'                     => $unitNama,
            'unit_id'                  => $unitId,
            'role'                     => $role,
            'status_pegawai'           => $statusPegawai,
            'tmt_selesai'              => !empty($tmtSelesai) ? $tmtSelesai : null,
            'dispensasi_lampau_sampai' => !empty($dispensasiSampai) ? $dispensasiSampai : null,
            'email'                    => $this->request->getPost('email'),
            'username'                 => $this->request->getPost('username'),
        ];

        $atasan_id = $this->request->getPost('atasan_id');
        // Proteksi Self-Atasan Loop: Pengguna tidak boleh menjadi atasan bagi dirinya sendiri
        if (!empty($atasan_id) && (int)$atasan_id === (int)$id) {
            $atasan_id = 0;
        }
        $data['atasan_id'] = !empty($atasan_id) ? (int)$atasan_id : 0;

        // Jika atasan tidak dipilih tapi unit ada, cari pimpinannya secara otomatis
        if (empty($data['atasan_id']) && !empty($data['unit'])) {
            $pimpinan = $this->userModel->where('unit', $data['unit'])
                                        ->whereIn('role', ['manajemen', 'kabag_aak', 'kabag_kuk', 'kabag'])
                                        ->first();
            if ($pimpinan && (int)$pimpinan['id'] !== (int)$id) {
                $data['atasan_id'] = $pimpinan['id'];
            }
        }

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $this->userModel->update($id, $data);

            // Update Multi-Roles (Pivot Table)
            // 1. Hapus role sekunder yang lama
            $db->table('user_roles')->where('user_id', $id)->delete();
            
            // 2. Insert role primer (wajib ada)
            $db->table('user_roles')->insert([
                'user_id' => $id,
                'role_name' => strtolower($role),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // 3. Insert role sekunder yang dicentang
            $secondaryRoles = $this->request->getPost('secondary_roles');
            if (!empty($secondaryRoles) && is_array($secondaryRoles)) {
                foreach ($secondaryRoles as $secRole) {
                    // Jangan masukkan lagi jika sama dengan role primer
                    if (strtolower($secRole) !== strtolower($role)) {
                        $db->table('user_roles')->insert([
                            'user_id' => $id,
                            'role_name' => strtolower($secRole),
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }

            log_audit('UPDATE', 'users', $id, null, $data);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data pengguna.');
            }

            $returnQs = $this->request->getPost('return_qs');
            $redirectUrl = 'users';
            if (!empty($returnQs)) {
                $redirectUrl .= '?' . $returnQs;
            }

            return redirect()->to($redirectUrl)->with('success', 'Data pengguna berhasil diperbarui.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data pengguna: ' . $e->getMessage());
        }
    }

    public function ajaxUpdateUnit()
    {
        // Check if it's an AJAX request for security
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        if (!hasAnyRole(['admin', 'kepegawaian'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki hak akses.'])->setStatusCode(403);
        }

        $userId = $this->request->getPost('user_id');
        $unitInput = $this->request->getPost('unit_id') ?? $this->request->getPost('unit');

        // Basic validation
        if (empty($userId) || ! is_numeric($userId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID tidak valid.'])->setStatusCode(400);
        }

        $unitId = null;
        $unitNama = '';
        if (!empty($unitInput)) {
            $unitKerjaModel = new UnitKerja();
            if (is_numeric($unitInput)) {
                $unitDb = $unitKerjaModel->find($unitInput);
                if ($unitDb) {
                    $unitId = $unitDb['id'];
                    $unitNama = $unitDb['nama_unit'];
                }
            } else {
                $unitNama = trim((string)$unitInput);
                $unitDb = $unitKerjaModel->where('nama_unit', $unitNama)->first();
                if ($unitDb) {
                    $unitId = $unitDb['id'];
                }
            }
        }

        $updateData = [
            'unit'    => $unitNama,
            'unit_id' => $unitId
        ];

        // Jika unit diubah menjadi Satuan Penjaminan Mutu, otomatis ubah role
        if (strtolower(trim($unitNama ?? '')) === 'satuan penjaminan mutu') {
            $updateData['role'] = 'spm';
        }

        // Sinkronisasi Atasan otomatis
        if (!empty($unitNama)) {
            $pimpinan = $this->userModel->where('unit', $unitNama)
                                        ->whereIn('role', ['manajemen', 'kabag_aak', 'kabag_kuk', 'kabag'])
                                        ->first();
            $updateData['atasan_id'] = $pimpinan ? $pimpinan['id'] : 0;
        } else {
            $updateData['atasan_id'] = 0;
        }

        if ($this->userModel->update($userId, $updateData)) {
            log_audit('UPDATE', 'users', $userId, null, $updateData);
            // SOLUSI: Sertakan token CSRF yang baru di dalam response
            $response_data = [
                'success' => true, 
                'message' => 'Unit kerja berhasil diperbarui.',
                csrf_token() => csrf_hash() // Menambahkan token baru ke response
            ];
            return $this->response->setJSON($response_data);
        } else {
            // Juga sertakan di response gagal untuk konsistensi
            $response_data = ['success' => false, 'message' => 'Gagal memperbarui unit kerja.', csrf_token() => csrf_hash()];
            return $this->response->setJSON($response_data)->setStatusCode(500);
        }
    }

    public function batch_update()
    {
        if ($redirect = $this->checkAdminOrKepegawaian()) {
            return $redirect;
        }

        $userIds = $this->request->getPost('user_ids');
        $atasanId = $this->request->getPost('atasan_id');

        if (empty($userIds)) {
            return redirect()->to('users')->with('error', 'Tidak ada pengguna yang dipilih untuk diupdate.');
        }

        $atasan = null;
        if (!empty($atasanId)) {
            $atasan = $this->userModel->find($atasanId);
        }

        // Konversi string "1,2,3" menjadi array [1, 2, 3]
        $idArray = explode(',', $userIds);

        // Siapkan data untuk batch update
        $dataToUpdate = [];
        foreach ($idArray as $id) {
            $parsedId = (int)trim($id);
            if (is_numeric(trim($id)) && $parsedId > 0) {
                // Cegah penugasan atasan ke diri sendiri
                if (!empty($atasanId) && $parsedId === (int)$atasanId) {
                    continue;
                }
                $updateItem = [
                    'id' => $parsedId,
                    'atasan_id' => !empty($atasanId) ? (int)$atasanId : 0
                ];
                $dataToUpdate[] = $updateItem;
            }
        }

        if (empty($dataToUpdate)) {
            return redirect()->to('users')->with('error', 'Tidak ada data valid untuk diupdate.');
        }

        $this->userModel->updateBatch($dataToUpdate, 'id');
        log_audit('UPDATE', 'users', 'batch', null, $dataToUpdate);

        $returnQs = $this->request->getPost('return_qs');
        $redirectUrl = 'users';
        if (!empty($returnQs)) {
            $redirectUrl .= $returnQs;
        }

        return redirect()->to($redirectUrl)->with('success', count($dataToUpdate) . ' data pengguna berhasil diperbarui.');
    }
    
    public function delete($id)
    {
        if ($redirect = $this->checkAdminOrKepegawaian()) {
            return $redirect;
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('users')->with('error', 'Metode permintaan tidak diizinkan.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $currentUserId = session()->get('id') ?? session()->get('user_id');
        if ($user['username'] === 'admin' || $user['role'] === 'admin' || (int)$id === (int)$currentUserId) {
            return redirect()->to('users')->with('error', 'Akun Administrator utama atau akun Anda sendiri tidak dapat dihapus demi keamanan sistem.');
        }

        // Hapus foto jika ada dan bukan file default
        if (!empty($user['foto']) && $user['foto'] !== 'default.png') {
            $fotoBasename = basename($user['foto']);
            $fotoPath = FCPATH . 'assets/uploads/profile/' . $fotoBasename;
            if (file_exists($fotoPath)) {
                @unlink($fotoPath);
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            // Hapus entri multi-roles di user_roles
            $db->table('user_roles')->where('user_id', $id)->delete();

            // Reset atasan_id pada staf yang dipimpin oleh user ini agar tidak terjadi orphan link
            $this->userModel->where('atasan_id', $id)->set(['atasan_id' => 0])->update();

            $this->userModel->delete($id);
            log_audit('DELETE', 'users', $id, $user, null);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('users')->with('error', 'Gagal menghapus pengguna.');
            }

            return redirect()->to('users')->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to('users')->with('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }
    }

    /**
     * Menghasilkan file Excel (.xlsx) sebagai template impor data pengguna.
     */
    public function exportExcel()
    {
        if ($redirect = $this->checkAdminOrKepegawaian()) {
            return $redirect;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        // Column Headers
        $headers = [
            'A1' => 'username (wajib, unik)',
            'B1' => 'email (wajib, unik)',
            'C1' => 'password (wajib, min: 6 karakter)',
            'D1' => 'nama_lengkap (wajib)',
            'E1' => 'role (wajib: admin, direktur, wadir, kabag_aak, kabag_kuk, manajemen, user, dll.)',
            'F1' => 'nip',
            'G1' => 'jabatan',
            'H1' => 'pangkat',
            'I1' => 'unit',
            'J1' => 'atasan_id (opsional)'
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Styling Header (Navy Blue Background, White Bold Text)
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Baris Contoh (Sample Row)
        $sampleRow = [
            'A2' => '199806112023211004',
            'B2' => 'pegawai@pktj.ac.id',
            'C2' => '123456',
            'D2' => 'Ahmad Pegawai, S.T.',
            'E2' => 'user',
            'F2' => '199806112023211004',
            'G2' => 'Staf Administrasi',
            'H2' => 'Penata Muda / III/a',
            'I2' => 'Unit Bahasa',
            'J2' => ''
        ];
        foreach ($sampleRow as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Auto-fit Column Widths
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Template_Import_Pengguna_ECC.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    /**
     * Mengimpor data pengguna dari file Excel (.xlsx, .xls, .csv).
     */
    public function importExcel()
    {
        if ($redirect = $this->checkAdminOrKepegawaian()) {
            return $redirect;
        }

        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            return redirect()->to('users')->with('error', 'File tidak valid. Harap unggah file Excel (.xlsx / .xls) atau CSV.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            return redirect()->to('users')->with('error', 'Format file tidak didukung. Harap unggah file .xlsx, .xls, atau .csv');
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        } catch (\Exception $e) {
            return redirect()->to('users')->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        $dataToInsert = [];
        $insertedCount = 0;
        $skippedCount = 0;
        $errors = [];

        // Ambil semua username dan email yang ada untuk validasi duplikat
        $existingUsers = $this->userModel->select('username, email')->findAll();
        $existingUsernames = array_column($existingUsers, 'username');
        $existingEmails = array_column($existingUsers, 'email');

        // Peta unit kerja untuk dual-sync unit_id
        $unitKerjaModel = new UnitKerja();
        $allUnits = $unitKerjaModel->findAll();
        $unitMapByName = [];
        foreach ($allUnits as $uk) {
            $unitMapByName[strtolower(trim($uk['nama_unit']))] = (int)$uk['id'];
        }

        $rowIndex = 0;
        foreach ($sheetData as $row) {
            $rowIndex++;
            if ($rowIndex === 1) {
                continue; // Skip Header Row
            }

            $username     = trim($row['A'] ?? '');
            $email        = trim($row['B'] ?? '');
            $password     = trim($row['C'] ?? '');
            $nama_lengkap = trim($row['D'] ?? '');
            $role         = trim($row['E'] ?? '');

            // Validasi dasar: Lewati baris jika data wajib kosong
            if (empty($username) || empty($email) || empty($password) || empty($nama_lengkap) || empty($role)) {
                $skippedCount++;
                continue;
            }

            // Validasi duplikasi
            if (in_array($username, $existingUsernames)) {
                $errors[] = "Baris {$rowIndex}: Username '{$username}' sudah ada di database.";
                $skippedCount++;
                continue;
            }
            if (in_array($email, $existingEmails)) {
                $errors[] = "Baris {$rowIndex}: Email '{$email}' sudah ada di database.";
                $skippedCount++;
                continue;
            }

            $unit = trim($row['I'] ?? '');
            $unitId = null;
            if (!empty($unit)) {
                $unitKey = strtolower($unit);
                if (isset($unitMapByName[$unitKey])) {
                    $unitId = $unitMapByName[$unitKey];
                }
            }

            if (strtolower($unit) === 'satuan penjaminan mutu') {
                $role = 'spm';
            }

            $dataToInsert[] = [
                'username'     => $username,
                'email'        => $email,
                'password'     => password_hash($password, PASSWORD_DEFAULT),
                'nama_lengkap' => $nama_lengkap,
                'role'         => strtolower($role),
                'nip'          => trim($row['F'] ?? ''),
                'jabatan'      => trim($row['G'] ?? ''),
                'pangkat'      => trim($row['H'] ?? ''),
                'unit'         => $unit,
                'unit_id'      => $unitId,
                'atasan_id'    => !empty(trim($row['J'] ?? '')) ? (int)trim($row['J']) : null,
            ];

            // Tambahkan username & email baru ke daftar pengecekan
            $existingUsernames[] = $username;
            $existingEmails[] = $email;
        }

        if (!empty($dataToInsert)) {
            $db = \Config\Database::connect();
            $db->transStart();
            try {
                foreach ($dataToInsert as $newUserData) {
                    $newId = $this->userModel->insert($newUserData);
                    if ($newId) {
                        $db->table('user_roles')->insert([
                            'user_id'    => $newId,
                            'role_name'  => strtolower($newUserData['role']),
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                        $insertedCount++;
                    }
                }
                $db->transComplete();

                if ($db->transStatus() === false) {
                    return redirect()->to('users')->with('error', 'Gagal mengimpor data pengguna karena kendala transaksi basis data.');
                }
            } catch (\Throwable $e) {
                $db->transRollback();
                return redirect()->to('users')->with('error', 'Gagal mengimpor data pengguna: ' . $e->getMessage());
            }
        }

        if ($insertedCount > 0) {
            $message = "Import berhasil! {$insertedCount} pengguna baru berhasil ditambahkan.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} baris dilewati karena data tidak lengkap atau duplikat.";
            }
            if (!empty($errors)) {
                session()->setFlashdata('import_errors', $errors);
            }
            return redirect()->to('users')->with('success', $message);
        }

        return redirect()->to('users')->with('error', 'Tidak ada data pengguna baru yang dapat diimpor dari file yang diunggah.');
    }

    /**
     * Reset Kinerja Bulanan (Target, Log Harian, dan Nilai Rekap)
     */
    public function resetKinerja()
    {
        // Hanya admin/superadmin
        if (!hasAnyRole(['admin']) && session()->get('username') !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized akses.');
        }

        $userId = $this->request->getPost('user_id');
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');

        if (!$userId || !$bulan || !$tahun) {
            return redirect()->back()->with('error', 'Data tidak lengkap untuk melakukan reset.');
        }

        // Ambil ID target dari target_kinerja_bulanan (sebagai referensi jika diperlukan)
        $targetKinerjaModel = new \App\Models\TargetKinerja();
        $targetData = $targetKinerjaModel->where('user_id', $userId)
                                         ->where('bulan', $bulan)
                                         ->where('tahun', $tahun)
                                         ->findAll();
                                         
        $targetIds = array_column($targetData, 'id');

        $logKegiatanModel = new \App\Models\LogKegiatanHarian();
        $logTugasTambahanModel = new \App\Models\LogTugasTambahan();
        $remunerasiModel = new \App\Models\Remunerasi();

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            // 1. Hapus Log Kegiatan Harian berdasarkan user_id, bulan, dan tahun
            $logKegiatanModel->where('user_id', $userId)
                             ->where('MONTH(tanggal_kegiatan)', $bulan)
                             ->where('YEAR(tanggal_kegiatan)', $tahun)
                             ->delete();

            // 2. Hapus Log Tugas Tambahan berdasarkan user_id, bulan, dan tahun
            $logTugasTambahanModel->where('user_id', $userId)
                                  ->where('MONTH(tanggal_kegiatan)', $bulan)
                                  ->where('YEAR(tanggal_kegiatan)', $tahun)
                                  ->delete();

            // 3. Hapus Target Kinerja Bulanan (target_kinerja_bulanan)
            if (!empty($targetIds)) {
                $targetKinerjaModel->whereIn('id', $targetIds)->delete();
            }

            // 4. Hapus Nilai Rekap Remunerasi
            $remunerasiModel->where('user_id', $userId)
                            ->where('bulan', $bulan)
                            ->where('tahun', $tahun)
                            ->delete();

            // Audit Log
            if (function_exists('log_audit')) {
                log_audit('DELETE', 'reset_kinerja', $userId, null, ['bulan' => $bulan, 'tahun' => $tahun]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal mereset data kinerja.');
            }

            return redirect()->back()->with('success', "Seluruh data kinerja bulan $bulan tahun $tahun berhasil direset.");
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal mereset data kinerja: ' . $e->getMessage());
        }
    }
}