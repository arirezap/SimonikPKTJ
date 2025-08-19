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
            'user' => $userModel->find($user_id)
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
            'password'     => 'if_exist|min_length[6]',
            'konfirmasi_password' => 'matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $userModel = new UserModel();
        $user_id = session()->get('user_id');

        $dataToUpdate = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email'        => $this->request->getPost('email'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $dataToUpdate['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if ($userModel->update($user_id, $dataToUpdate)) {
            // PERBAIKAN: Bangun ulang seluruh sesi setelah update berhasil
            $updatedUser = $userModel->find($user_id);
            $this->regenerateSession($updatedUser);
            
            return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui profil.');
    }

    /**
     * Fungsi bantuan untuk membangun ulang sesi pengguna.
     */
    private function regenerateSession(array $user)
    {
        $sessionData = [
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role'       => $user['role'],
            'isLoggedIn' => true
        ];
        session()->set($sessionData);
    }
}
