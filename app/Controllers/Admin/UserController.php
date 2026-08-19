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

    public function index()
    {
        if (!hasAnyRole(['admin', 'kepegawaian'])) {
            return redirect()->to(site_url('daftar-pegawai'));
        }

        $data = [
            'title' => 'Kelola Pengguna',
        ];
        
        $search = $this->request->getGet('search');
        $role = $this->request->getGet('role');
        $unit = $this->request->getGet('unit');
        $perPage = $this->request->getGet('per_page') ?? 10;
        
        $sortBy = $this->request->getGet('sort_by') ?? 'users.nama_lengkap';
        $sortOrder = $this->request->getGet('sort_order') ?? 'asc';

        // Join dengan tabel users (self join) untuk mengambil nama atasan
        $query = $this->userModel->select('users.*, atasan.nama_lengkap as nama_atasan')
                                 ->join('users as atasan', 'atasan.id = users.atasan_id', 'left');

        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('users.nama_lengkap', $search)
                           ->orLike('users.username', $search)
                           ->groupEnd();
        }
        
        if (!empty($role)) {
            if ($role === 'kabag') {
                $query = $query->like('users.role', 'kabag', 'after');
            } else {
                $query = $query->where('users.role', $role);
            }
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

        $query = $query->orderBy($sortBy, $sortOrder);
        
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
        $data['per_page'] = $perPage;
        $data['sortBy'] = $sortBy;
        $data['sortOrder'] = $sortOrder;

        // Ambil data untuk dropdown atasan di modal batch edit
        $data['potential_bosses'] = $this->userModel->orderBy('nama_lengkap', 'ASC')->findAll();

        // Ambil master unit kerja
        $unitKerjaModel = new UnitKerja();
        $unitKerjaMap = array_column($unitKerjaModel->findAll(), 'parent_unit', 'nama_unit');
        $data['unit_kerja_list'] = $unitKerjaModel->orderBy('nama_unit', 'ASC')->findAll();
        
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
            ]
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $unit = $this->request->getPost('unit');
        $role = $this->request->getPost('role');

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip'          => $this->request->getPost('nip'),
            'jabatan'      => $this->request->getPost('jabatan'),
            'pangkat'      => $this->request->getPost('pangkat'),
            'unit'         => $unit,
            'role'         => $role,
            'email'        => $this->request->getPost('email'),
            'username'     => $this->request->getPost('username'),
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        // Logic Atasan
        $atasan_id = $this->request->getPost('atasan_id');
        if (!empty($atasan_id)) {
            $data['atasan_id'] = $atasan_id;
        }

        $insertID = $this->userModel->insert($data);

        // Insert Multi-Roles (Pivot Table)
        $db = \Config\Database::connect();
        
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

        return redirect()->to('users')->with('success', 'User baru berhasil ditambahkan.');
    }
    // --- END FITUR BARU ---

    public function edit($id)
    {
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
        $id = $this->request->getPost('id');

        if (!$this->validate([
            'username' => [
                'rules'  => "required|is_unique[users.username,id,{$id}]",
                'errors' => [
                    'required'  => 'Username (atau NIP) wajib diisi.',
                    'is_unique' => 'Gagal: Username / NIP tersebut sudah dipakai oleh pengguna lain.'
                ]
            ],
            'email' => [
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.'
                ]
            ],
            'nama_lengkap' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi.'
                ]
            ]
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $unit = $this->request->getPost('unit');
        $role = $this->request->getPost('role');
        
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip'          => $this->request->getPost('nip'),
            'jabatan'      => $this->request->getPost('jabatan'),
            'pangkat'      => $this->request->getPost('pangkat'),
            'unit'         => $unit,
            'role'         => $role,
            'email'        => $this->request->getPost('email'),
            'username'     => $this->request->getPost('username'),
        ];

        $atasan_id = $this->request->getPost('atasan_id');
        $data['atasan_id'] = !empty($atasan_id) ? $atasan_id : 0;

        // Jika atasan tidak dipilih tapi unit ada, cari pimpinannya secara otomatis
        if (empty($atasan_id) && !empty($data['unit'])) {
            $pimpinan = $this->userModel->where('unit', $data['unit'])
                                        ->whereIn('role', ['manajemen', 'kabag_aak', 'kabag_kuk', 'kabag'])
                                        ->first();
            if ($pimpinan) {
                $data['atasan_id'] = $pimpinan['id'];
            }
        }

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        // Update Multi-Roles (Pivot Table)
        $db = \Config\Database::connect();
        
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

        $returnQs = $this->request->getPost('return_qs');
        $redirectUrl = 'users';
        if (!empty($returnQs)) {
            $redirectUrl .= '?' . $returnQs;
        }

        return redirect()->to($redirectUrl)->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function ajaxUpdateUnit()
    {
        // Check if it's an AJAX request for security
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $userId = $this->request->getPost('user_id');
        $unit = $this->request->getPost('unit');

        // Basic validation
        if (empty($userId) || ! is_numeric($userId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID tidak valid.'])->setStatusCode(400);
        }

        $updateData = ['unit' => $unit];

        // Jika unit diubah menjadi Satuan Penjaminan Mutu, otomatis ubah role
        if (strtolower(trim($unit ?? '')) === 'satuan penjaminan mutu') {
            $updateData['role'] = 'spm';
        }

        // Sinkronisasi Atasan otomatis
        if (!empty($unit)) {
            $pimpinan = $this->userModel->where('unit', $unit)
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
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('users')->with('error', 'Pengguna tidak ditemukan.');
        }

        // Hapus foto jika ada dan bukan file default
        if (!empty($user['foto']) && $user['foto'] !== 'default.png') {
            $fotoPath = 'assets/uploads/profile/' . basename($user['foto']);
            if (file_exists($fotoPath)) {
                @unlink($fotoPath);
            }
        }

        // Hapus entri multi-roles di user_roles
        $db = \Config\Database::connect();
        $db->table('user_roles')->where('user_id', $id)->delete();

        $this->userModel->delete($id);
        log_audit('DELETE', 'users', $id, $user, null);
        return redirect()->to('users')->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Menghasilkan file Excel (.xlsx) sebagai template impor data pengguna.
     */
    public function exportExcel()
    {
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
                'atasan_id'    => !empty(trim($row['J'] ?? '')) ? (int)trim($row['J']) : null,
            ];

            // Tambahkan username & email baru ke daftar pengecekan
            $existingUsernames[] = $username;
            $existingEmails[] = $email;
        }

        if (!empty($dataToInsert)) {
            $this->userModel->insertBatch($dataToInsert);
            $insertedCount = count($dataToInsert);
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

        // Ambil ID target dari laporan_harian (sebagai referensi jika diperlukan)
        $laporanHarianModel = new \App\Models\LaporanHarian();
        $targetData = $laporanHarianModel->where('user_id', $userId)
                                         ->where('bulan', $bulan)
                                         ->where('tahun', $tahun)
                                         ->findAll();
                                         
        $targetIds = array_column($targetData, 'id');

        $logKegiatanModel = new \App\Models\LogKegiatanHarian();
        $remunerasiModel = new \App\Models\Remunerasi();

        // 1. Hapus Log Kegiatan Harian berdasarkan user_id, bulan, dan tahun
        $logKegiatanModel->where('user_id', $userId)
                         ->where('MONTH(tanggal_kegiatan)', $bulan)
                         ->where('YEAR(tanggal_kegiatan)', $tahun)
                         ->delete();

        // 2. Hapus Target Kinerja Bulanan (laporan_harian)
        if (!empty($targetIds)) {
            $laporanHarianModel->whereIn('id', $targetIds)->delete();
        }

        // 3. Hapus Nilai Rekap Remunerasi
        $remunerasiModel->where('user_id', $userId)
                        ->where('bulan', $bulan)
                        ->where('tahun', $tahun)
                        ->delete();

        // Audit Log
        if (function_exists('log_audit')) {
            log_audit('DELETE', 'reset_kinerja', $userId, null, ['bulan' => $bulan, 'tahun' => $tahun]);
        }

        return redirect()->back()->with('success', "Seluruh Data Kinerja (Target, Log Harian, dan Rekap Nilai) untuk bulan $bulan tahun $tahun berhasil dihapus permanen.");
    }
}