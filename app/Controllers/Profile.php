<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;

class Profile extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        $userId = session()->get('id');
        
        // AMBIL DATA FRESH DARI DB (Solusi Error Undefined Index)
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('logout');
        }

        $unitKerjaModel = new \App\Models\UnitKerja();
        $unit_kerja_list = $unitKerjaModel->orderBy('nama_unit', 'ASC')->findAll();
        
        $bossRoles = ['direktur', 'wadir', 'manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kanit', 'katim', 'kapokja'];
        $potential_bosses = $this->userModel->where('id !=', $userId)
                                            ->whereIn('role', $bossRoles)
                                            ->orderBy('nama_lengkap', 'ASC')
                                            ->findAll();

        $data = [
            'title' => 'Profil Saya',
            'user'  => $user,
            'unit_kerja_list'  => $unit_kerja_list,
            'potential_bosses' => $potential_bosses,
            'validation' => \Config\Services::validation()
        ];

        return view('profile', $data);
    }

    public function update()
    {
        $userId = session()->get('id');
        
        // 1. Validasi Input Dasar
        $rules = [
            'nama_lengkap' => [
                'rules'  => 'required',
                'errors' => ['required' => 'Nama Lengkap wajib diisi.']
            ],
            'email' => [
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.'
                ]
            ],
            'nip' => [
                'rules'  => 'required',
                'errors' => ['required' => 'NIP / NIK wajib diisi.']
            ],
            'jabatan' => [
                'rules'  => 'required',
                'errors' => ['required' => 'Jabatan wajib diisi.']
            ],
            'unit' => [
                'rules'  => 'required',
                'errors' => ['required' => 'Unit Kerja wajib dipilih.']
            ]
        ];

        // Validasi foto hanya jika pengguna memilih file (tidak kosong)
        $fileFoto = $this->request->getFile('foto');
        if ($fileFoto && $fileFoto->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['foto'] = [
                'rules'  => 'is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]',
                'errors' => [
                    'is_image'  => 'Berkas yang diunggah harus berupa gambar.',
                    'mime_in'   => 'Format foto harus JPG, JPEG, atau PNG.',
                    'max_size'  => 'Ukuran foto maksimal adalah 2MB.'
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

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip'          => $this->request->getPost('nip'),
            'jabatan'      => $this->request->getPost('jabatan'),
            'pangkat'      => $this->request->getPost('pangkat'),
            'unit'         => $this->request->getPost('unit'),
            'atasan_id'    => $this->request->getPost('atasan_id') ?: null,
            'no_hp'        => $this->request->getPost('no_hp'),
            'email'        => $this->request->getPost('email'),
            'username'     => $user['username'], // Proteksi: Username tidak dapat diubah via POST payload
        ];

        // 3. Cek apakah Password diubah
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            if (strlen($password) < 6) {
                return redirect()->back()->withInput()->with('errors', ['password' => 'Password minimal harus 6 karakter.']);
            }
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // 4. Handle Upload Foto
        $hapusFoto = $this->request->getPost('hapus_foto');

        if ($hapusFoto === '1') {
            // Hapus foto lama jika ada dan bukan file default
            if (!empty($user['foto']) && $user['foto'] !== 'default.png') {
                $oldFile = 'assets/uploads/profile/' . basename($user['foto']);
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }
            $data['foto'] = null; // Set ke null di DB
        } elseif ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            
            // Hapus foto lama jika ada
            if (!empty($user['foto']) && $user['foto'] !== 'default.png') {
                $oldFile = 'assets/uploads/profile/' . basename($user['foto']);
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            // Pindahkan file baru ke folder
            $fileFoto->move('assets/uploads/profile', $namaFoto);
            $data['foto'] = $namaFoto;
        }

        // 5. Eksekusi Update ke Database
        $this->userModel->update($userId, $data);
        log_audit('UPDATE', 'users', $userId, null, $data);

        // Otomatis jadikan role 'spm' jika unit kerjanya diubah ke Satuan Penjaminan Mutu
        $role_aplikasi = $user['role'];
        if (strtolower(trim($data['unit'] ?? '')) === 'satuan penjaminan mutu') {
            $role_aplikasi = 'spm';
        }

        // 6. Update Session Data (Agar nama, unit, role & FOTO di header langsung berubah)
        $sessionData = [
            'nama' => $data['nama_lengkap'],
            'unit' => $data['unit'],
            'role' => $role_aplikasi
        ];
        if (isset($data['foto'])) {
            $sessionData['foto'] = $data['foto'];
        }
        session()->set($sessionData);

        return redirect()->to('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}