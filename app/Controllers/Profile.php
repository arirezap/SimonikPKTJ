<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;

class Profile extends BaseController
{
    protected $userModel;
    protected $unitKerjaModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->unitKerjaModel = new \App\Models\UnitKerja();
    }

    public function index()
    {
        helper(['avatar', 'audit']);

        $userId = session()->get('id') ?? session()->get('user_id');
        
        // Ambil data terbaru pengguna dari basis data
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('logout');
        }

        $unit_kerja_list = $this->unitKerjaModel->orderBy('nama_unit', 'ASC')->findAll();
        
        // PRF-05: Deteksi role Direktur (Pimpinan Tertinggi tanpa atasan langsung)
        $isDirektur = (strtolower(trim($user['role'] ?? '')) === 'direktur');

        // PRF-04: Filter kandidat atasan hanya pegawai aktif (status_pegawai = 'aktif')
        $potential_bosses = [];
        if (!$isDirektur) {
            $bossRoles = ['direktur', 'wadir', 'manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kanit', 'katim', 'kapokja'];
            $potential_bosses = $this->userModel->select('id, nama_lengkap, nip, unit, jabatan, role')
                                                ->where('id !=', $userId)
                                                ->where('status_pegawai', 'aktif')
                                                ->whereIn('role', $bossRoles)
                                                ->orderBy('nama_lengkap', 'ASC')
                                                ->findAll();

            // Failsafe: Jika pengguna sudah memiliki atasan_id, pastikan record atasan tetap termuat agar tidak terputus
            if (!empty($user['atasan_id'])) {
                $existingBossFound = false;
                foreach ($potential_bosses as $pb) {
                    if ((int)$pb['id'] === (int)$user['atasan_id']) {
                        $existingBossFound = true;
                        break;
                    }
                }
                if (!$existingBossFound) {
                    $curBoss = $this->userModel->select('id, nama_lengkap, nip, unit, jabatan, role')
                                               ->find($user['atasan_id']);
                    if ($curBoss) {
                        array_unshift($potential_bosses, $curBoss);
                    }
                }
            }
        }

        $data = [
            'title'            => 'Profil Saya',
            'user'             => $user,
            'is_direktur'      => $isDirektur,
            'unit_kerja_list'  => $unit_kerja_list,
            'potential_bosses' => $potential_bosses,
            'validation'       => \Config\Services::validation()
        ];

        return view('profile', $data);
    }

    public function update()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        
        // 1. Validasi Input Dasar
        $rules = [
            'nama_lengkap' => [
                'rules'  => 'required|max_length[100]',
                'errors' => [
                    'required'   => 'Nama lengkap wajib diisi.',
                    'max_length' => 'Nama lengkap maksimal 100 karakter.'
                ]
            ],
            'email' => [
                'rules'  => 'required|valid_email|max_length[100]',
                'errors' => [
                    'required'    => 'Alamat email wajib diisi.',
                    'valid_email' => 'Format email tidak sesuai.',
                    'max_length'  => 'Alamat email maksimal 100 karakter.'
                ]
            ],
            'nip' => [
                'rules'  => 'required|max_length[50]',
                'errors' => [
                    'required'   => 'NIP atau NIK wajib diisi.',
                    'max_length' => 'NIP atau NIK maksimal 50 karakter.'
                ]
            ],
            'jabatan' => [
                'rules'  => 'required|max_length[100]',
                'errors' => [
                    'required'   => 'Jabatan wajib diisi.',
                    'max_length' => 'Jabatan maksimal 100 karakter.'
                ]
            ],
            'unit' => [
                'rules'  => 'required|max_length[100]',
                'errors' => [
                    'required'   => 'Pilih unit kerja.',
                    'max_length' => 'Unit kerja maksimal 100 karakter.'
                ]
            ]
        ];

        // Validasi foto hanya jika pengguna memilih berkas (tidak kosong)
        $fileFoto = $this->request->getFile('foto');
        if ($fileFoto && $fileFoto->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['foto'] = [
                'rules'  => 'is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]',
                'errors' => [
                    'is_image'  => 'Pilih berkas foto.',
                    'mime_in'   => 'Format foto harus JPG atau PNG.',
                    'max_size'  => 'Ukuran foto maksimal 2MB.'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Ambil data user saat ini untuk verifikasi & proteksi
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('logout');
        }

        $emailPost   = trim((string)$this->request->getPost('email'));
        $nipPost     = trim((string)$this->request->getPost('nip'));
        $namaPost    = trim((string)$this->request->getPost('nama_lengkap'));
        $jabatanPost = trim((string)$this->request->getPost('jabatan'));
        $pangkatPost = trim((string)$this->request->getPost('pangkat'));
        $unitPost    = trim((string)$this->request->getPost('unit'));
        $noHpPost    = preg_replace('/[^0-9+]/', '', trim((string)$this->request->getPost('no_hp')));

        // Validasi Keunikan Email (Mencegah tabrakan dengan akun lain)
        $existingEmail = $this->userModel->where('email', $emailPost)->where('id !=', $userId)->first();
        if ($existingEmail) {
            return redirect()->back()->withInput()->with('errors', ['email' => 'Alamat email sudah digunakan.']);
        }

        // Validasi Keunikan NIP / NIK (Mencegah tabrakan dengan akun lain)
        $existingNip = $this->userModel->where('nip', $nipPost)->where('id !=', $userId)->first();
        if ($existingNip) {
            return redirect()->back()->withInput()->with('errors', ['nip' => 'NIP atau NIK sudah terdaftar.']);
        }

        // Dual-Sync unit_id berdasarkan kecocokan nama_unit
        $unitId = null;
        if (!empty($unitPost)) {
            $unitDb = $this->unitKerjaModel->where('nama_unit', $unitPost)->first();
            if ($unitDb) {
                $unitId = (int)$unitDb['id'];
            }
        }

        // PRF-04 & PRF-05: Penanganan Khusus Direktur & Validasi Atasan Langsung
        $isDirektur = (strtolower(trim($user['role'] ?? '')) === 'direktur');
        $atasanId = null;

        if (!$isDirektur) {
            $atasanIdPost = $this->request->getPost('atasan_id');
            if (!empty($atasanIdPost) && (int)$atasanIdPost !== (int)$userId) {
                // Pastikan target atasan benar-benar ada di database dan berstatus aktif
                $bossCandidate = $this->userModel->select('id, status_pegawai, role')->find((int)$atasanIdPost);
                if ($bossCandidate && ($bossCandidate['status_pegawai'] ?? 'aktif') === 'aktif') {
                    $atasanId = (int)$atasanIdPost;
                }
            }
        }

        $data = [
            'nama_lengkap' => $namaPost,
            'nip'          => $nipPost,
            'jabatan'      => $jabatanPost,
            'pangkat'      => $pangkatPost,
            'unit'         => $unitPost,
            'unit_id'      => $unitId,
            'atasan_id'    => $atasanId,
            'no_hp'        => $noHpPost,
            'email'        => $emailPost,
            'username'     => $user['username'], // Proteksi: Username tidak dapat diubah via POST payload
        ];

        // 3. Cek apakah Kata Sandi diubah
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');
        if (!empty($password)) {
            if (strlen($password) < 6) {
                return redirect()->back()->withInput()->with('errors', ['password' => 'Kata sandi minimal 6 karakter.']);
            }
            if ($password !== $passwordConfirm) {
                return redirect()->back()->withInput()->with('errors', ['password_confirm' => 'Konfirmasi kata sandi tidak cocok.']);
            }
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // 4. Handle Upload Foto (PRF-06: Menggunakan direktori absolut eksplisit FCPATH)
        $hapusFoto = $this->request->getPost('hapus_foto');
        $uploadDir = FCPATH . 'assets/uploads/profile';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        if ($hapusFoto === '1') {
            // Hapus foto lama jika ada dan bukan berkas bawaan
            if (!empty($user['foto']) && !in_array($user['foto'], ['default.png', 'undraw_profile.svg'])) {
                $oldFile = $uploadDir . '/' . basename($user['foto']);
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }
            $data['foto'] = null; // Set ke null di basis data
        } elseif ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            
            // Hapus foto lama jika ada dan bukan berkas bawaan
            if (!empty($user['foto']) && !in_array($user['foto'], ['default.png', 'undraw_profile.svg'])) {
                $oldFile = $uploadDir . '/' . basename($user['foto']);
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            // Pindahkan berkas baru ke direktori absolut
            $fileFoto->move($uploadDir, $namaFoto);
            $data['foto'] = $namaFoto;
        }

        // PRF-01: Peran pengguna tetap konsisten (penghapusan eskalasi peran SPM mandiri)

        // 5. Eksekusi Update ke Database dengan Transaksi
        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $this->userModel->update($userId, $data);

            // PRF-02: Sanitasi Hash Kata Sandi agar Tidak Bocor ke Audit Log
            $auditOld = $user;
            unset($auditOld['password']);
            $auditNew = $data;
            if (isset($auditNew['password'])) {
                $auditNew['password'] = '[DIUBAH]';
            }

            log_audit('UPDATE', 'users', $userId, $auditOld, $auditNew);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('errors', ['db' => 'Gagal memperbarui profil. Silakan coba lagi.']);
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Gagal update profil pengguna ID ' . $userId . ': ' . $e->getMessage());
            return redirect()->back()->withInput()->with('errors', ['db' => 'Gagal menyimpan profil. Silakan coba lagi.']);
        }

        // 6. Update Session Data Lengkap (PRF-03: Menyelaraskan nama, NIP, jabatan, pangkat, unit, & foto ke sesi)
        $sessionData = [
            'nama'         => $data['nama_lengkap'],
            'nama_lengkap' => $data['nama_lengkap'],
            'nip'          => $data['nip'],
            'jabatan'      => $data['jabatan'],
            'pangkat'      => $data['pangkat'],
            'unit'         => $data['unit'],
            'unit_id'      => $unitId,
            'no_hp'        => $data['no_hp'],
        ];
        if (array_key_exists('foto', $data)) {
            $sessionData['foto'] = $data['foto'];
        }
        session()->set($sessionData);

        return redirect()->to('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}