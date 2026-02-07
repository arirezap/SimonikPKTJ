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
        
        // 1. Validasi Input
        if (!$this->validate([
            'nama_lengkap' => 'required',
            'email'        => 'required|valid_email',
            'foto'         => 'is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]',
        ])) {
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
        $fileFoto = $this->request->getFile('foto');
        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            // Generate nama random
            $namaFoto = $fileFoto->getRandomName();
            // Pindahkan file ke folder public/assets/uploads/profile
            $fileFoto->move('assets/uploads/profile', $namaFoto);
            
            // Hapus foto lama jika bukan default (Opsional)
            // ...

            $data['foto'] = $namaFoto;
        }

        // 5. Eksekusi Update ke Database
        $this->userModel->update($userId, $data);

        // 6. Update Session Data (Agar nama di sidebar langsung berubah)
        session()->set([
            'nama' => $data['nama_lengkap'],
            'unit' => $data['unit']
        ]);

        return redirect()->to('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}