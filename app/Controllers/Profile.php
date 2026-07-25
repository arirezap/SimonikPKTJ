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

        $data = [
            'title' => 'Profil Saya',
            'user'  => $user,
            'validation' => \Config\Services::validation()
        ];

        return view('profile', $data);
    }

    public function update()
    {
        $userId = session()->get('id');
        
        // 1. Validasi Input Dasar
        $rules = [
            'nama_lengkap' => 'required',
            'email'        => 'required|valid_email',
        ];

        // Validasi foto hanya jika pengguna memilih file (tidak kosong)
        $fileFoto = $this->request->getFile('foto');
        if ($fileFoto && $fileFoto->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['foto'] = 'is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Siapkan Data Update
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip'          => $this->request->getPost('nip'),
            'jabatan'      => $this->request->getPost('jabatan'),
            'pangkat'      => $this->request->getPost('pangkat'),
            'unit'         => $this->request->getPost('unit'),
            'email'        => $this->request->getPost('email'),
            'username'     => $this->request->getPost('username'),
        ];

        // 3. Cek apakah Password diubah
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // 4. Handle Upload Foto
        $user = $this->userModel->find($userId); // Ambil data user saat ini untuk cek foto lama dan role
        $hapusFoto = $this->request->getPost('hapus_foto');

        if ($hapusFoto === '1') {
            // Hapus foto lama jika ada dan bukan file default
            if (!empty($user['foto']) && $user['foto'] !== 'default.png' && file_exists('assets/uploads/profile/' . $user['foto'])) {
                unlink('assets/uploads/profile/' . $user['foto']);
            }
            $data['foto'] = null; // Set ke null di DB, akan otomatis pakai default.png
        } elseif ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            // Jika ada file baru diupload, proses upload
            $namaFoto = $fileFoto->getRandomName();
            
            // Hapus foto lama jika ada dan bukan file default
            if (!empty($user['foto']) && $user['foto'] !== 'default.png' && file_exists('assets/uploads/profile/' . $user['foto'])) {
                unlink('assets/uploads/profile/' . $user['foto']);
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