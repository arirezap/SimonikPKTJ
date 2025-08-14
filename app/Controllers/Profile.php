<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User as UserModel;

class Profile extends BaseController
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    public function index()
    {
        $userModel = new UserModel();
        $user_id = session()->get('user_id');

        $data = [
            'page_title' => 'Profil Saya',
            'user' => $userModel->find($user_id) // Mengambil data user yang sedang login
        ];

        return view('profile', $data);
    }

    /**
     * Memproses pembaruan data profil.
     */
    public function update()
    {
        // Aturan validasi
        $rules = [
            'nama_lengkap' => 'required',
            'email'        => 'required|valid_email',
            // Validasi password hanya jika diisi
            'password'     => 'if_exist|min_length[6]',
            'konfirmasi_password' => 'matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $userModel = new UserModel();
        $user_id = session()->get('user_id');

        // Siapkan data untuk diupdate
        $dataToUpdate = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email'        => $this->request->getPost('email'),
        ];

        // Jika password diisi, hash dan tambahkan ke data update
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $dataToUpdate['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        // Lakukan update
        if ($userModel->update($user_id, $dataToUpdate)) {
            // Perbarui juga nama di session jika berubah
            session()->set('nama_lengkap', $dataToUpdate['nama_lengkap']);
            return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui profil.');
    }
}
