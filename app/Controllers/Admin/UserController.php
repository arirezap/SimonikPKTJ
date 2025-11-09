<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User as UserModel;

class UserController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();

        // PERBARUI: Tambahkan 'spm' ke dalam urutan
        $data = [
            'page_title' => 'Kelola Pengguna',
            'users'      => $userModel
                            ->orderBy("FIELD(role, 'admin', 'manajemen', 'spm', 'kabag_aak', 'kabag_kuk', 'aak', 'kuk')")
                            ->orderBy('nama_lengkap', 'ASC')
                            ->findAll(),
            'validation' => \Config\Services::validation()
        ];

        return view('Admin/users', $data);
    }

    public function store()
    {
        // PERBARUI: Tambahkan 'spm' ke aturan validasi
        $rules = [
            'nama_lengkap' => 'required',
            'username'     => 'required|is_unique[users.username]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'role'         => 'required|in_list[admin,manajemen,kabag_aak,kabag_kuk,aak,kuk,spm]',
            'password'     => 'required|min_length[6]',
            'konfirmasi_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Terdapat kesalahan input. Silakan periksa kembali.');
            return redirect()->to('/admin/users')->withInput()->with('show_modal', 'addUserModal');
        }

        $userModel = new UserModel();

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'role'         => $this->request->getPost('role'),
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'foto'         => 'default.png',
        ];

        if ($userModel->insert($data)) {
            return redirect()->to('/admin/users')->with('success', 'Pengguna baru berhasil ditambahkan.');
        }

        return redirect()->back()->with('error', 'Gagal menambahkan pengguna baru.');
    }

    public function update($id)
    {
        // PERBARUI: Tambahkan 'spm' ke aturan validasi
        $rules = [
            'nama_lengkap' => 'required',
            'username'     => "required|is_unique[users.username,id,{$id}]",
            'email'        => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role'         => 'required|in_list[admin,manajemen,kabag_aak,kabag_kuk,aak,kuk,spm]',
        ];

        // Validasi password hanya jika diisi
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
            $rules['konfirmasi_password'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Terdapat kesalahan input. Silakan periksa kembali.');
            return redirect()->to('/admin/users')->withInput()->with('show_modal', 'editUserModal-' . $id);
        }

        $userModel = new UserModel();
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'role'         => $this->request->getPost('role'),
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        }

        if ($userModel->update($id, $data)) {
            return redirect()->to('/admin/users')->with('success', 'Data pengguna berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui data pengguna.');
    }

    public function delete($id)
    {
        $userModel = new UserModel();

        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($userModel->delete($id)) {
            return redirect()->to('/admin/users')->with('success', 'Pengguna berhasil dihapus.');
        }

        return redirect()->to('/admin/users')->with('error', 'Gagal menghapus pengguna.');
    }
}
