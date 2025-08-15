<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User as UserModel;

class UserController extends BaseController
{
    /**
     * Menampilkan halaman daftar pengguna.
     */
    public function index()
    {
        $userModel = new UserModel();

        $data = [
            'page_title' => 'Kelola Pengguna',
            'users'      => $userModel->findAll(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/users', $data);
    }

    /**
     * Memproses pembuatan pengguna baru.
     */
    public function store()
    {
        // Aturan validasi (tidak berubah)
        $rules = [
            'nama_lengkap' => 'required',
            'username'     => 'required|is_unique[users.username]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'role'         => 'required|in_list[admin,user,manajemen]',
            'password'     => 'required|min_length[6]',
            'konfirmasi_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/users')->withInput()->with('error', 'Terdapat kesalahan input. Silakan periksa kembali.')->with('show_modal', 'addUserModal');
        }

        $userModel = new UserModel();

        // Siapkan data untuk disimpan
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'role'         => $this->request->getPost('role'),
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'foto'         => 'default.png', // <-- TAMBAHKAN BARIS INI
        ];

        if ($userModel->insert($data)) {
            return redirect()->to('/admin/users')->with('success', 'Pengguna baru berhasil ditambahkan.');
        }

        return redirect()->back()->with('error', 'Gagal menambahkan pengguna baru.');
    }

    /**
     * FUNGSI BARU: Memproses pembaruan data pengguna.
     */
    public function update($id)
    {
        // Aturan validasi untuk update
        $rules = [
            'nama_lengkap' => 'required',
            // Username dan email harus unik, kecuali untuk user yang sedang diedit
            'username'     => "required|is_unique[users.username,id,{$id}]",
            'email'        => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role' => 'required|in_list[admin,user,manajemen]',
        ];

        // Validasi password hanya jika diisi
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
            $rules['konfirmasi_password'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/users')->withInput()->with('error', 'Terdapat kesalahan input. Silakan periksa kembali.')->with('show_modal', 'editUserModal' . $id);
        }

        $userModel = new UserModel();
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'role'         => $this->request->getPost('role'),
        ];

        // Jika password diisi, hash dan tambahkan ke data update
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        }

        if ($userModel->update($id, $data)) {
            return redirect()->to('/admin/users')->with('success', 'Data pengguna berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui data pengguna.');
    }

    /**
     * FUNGSI BARU: Menghapus data pengguna.
     */
    public function delete($id)
    {
        $userModel = new UserModel();

        // Jangan biarkan user menghapus dirinya sendiri
        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($userModel->delete($id)) {
            return redirect()->to('/admin/users')->with('success', 'Pengguna berhasil dihapus.');
        }

        return redirect()->to('/admin/users')->with('error', 'Gagal menghapus pengguna.');
    }
}
