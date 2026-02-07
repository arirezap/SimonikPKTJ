<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Pengguna',
            'users' => $this->userModel->findAll()
        ];
        
        // Mapping nama atasan
        $userMap = [];
        foreach($data['users'] as $u) {
            $userMap[$u['id']] = $u['nama_lengkap'];
        }
        
        foreach($data['users'] as &$u) {
            $u['nama_atasan'] = ($u['atasan_id'] && isset($userMap[$u['atasan_id']])) 
                                ? $userMap[$u['atasan_id']] 
                                : '-';
        }

        return view('Admin/users', $data);
    }

    // --- FITUR BARU: CREATE ---
    public function create()
    {
        // Ambil list semua user untuk dropdown "Pilih Atasan"
        $potentialBosses = $this->userModel->orderBy('nama_lengkap', 'ASC')->findAll();

        $data = [
            'title' => 'Tambah Pengguna Baru',
            'potential_bosses' => $potentialBosses
        ];

        return view('Admin/user_create', $data);
    }

    public function store()
    {
        // Validasi input dasar
        // PERBAIKAN: min_length(4) diubah menjadi min_length[4]
        if (!$this->validate([
            'username' => 'required|is_unique[users.username]',
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[4]', 
            'nama_lengkap' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip'          => $this->request->getPost('nip'),
            'jabatan'      => $this->request->getPost('jabatan'),
            'pangkat'      => $this->request->getPost('pangkat'),
            'unit'         => $this->request->getPost('unit'),
            'role'         => $this->request->getPost('role'),
            'email'        => $this->request->getPost('email'),
            'username'     => $this->request->getPost('username'),
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        // Logic Atasan
        $atasan_id = $this->request->getPost('atasan_id');
        if (!empty($atasan_id)) {
            $data['atasan_id'] = $atasan_id;
        }

        $this->userModel->insert($data);

        return redirect()->to('admin/users')->with('success', 'User baru berhasil ditambahkan.');
    }
    // --- END FITUR BARU ---

    public function edit($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('admin/users')->with('error', 'User tidak ditemukan');
        }

        // Kecualikan diri sendiri dari list atasan
        $potentialBosses = $this->userModel->where('id !=', $id)->orderBy('nama_lengkap', 'ASC')->findAll();

        $data = [
            'title' => 'Edit Pengguna & Atasan',
            'user'  => $user,
            'potential_bosses' => $potentialBosses
        ];

        return view('Admin/user_edit', $data);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip'          => $this->request->getPost('nip'),
            'jabatan'      => $this->request->getPost('jabatan'),
            'pangkat'      => $this->request->getPost('pangkat'),
            'unit'         => $this->request->getPost('unit'),
            'role'         => $this->request->getPost('role'),
            'email'        => $this->request->getPost('email'),
            'username'     => $this->request->getPost('username'),
        ];

        $atasan_id = $this->request->getPost('atasan_id');
        $data['atasan_id'] = !empty($atasan_id) ? $atasan_id : null;

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        return redirect()->to('admin/users')->with('success', 'Data pengguna berhasil diperbarui.');
    }
    
    public function delete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('admin/users')->with('success', 'User berhasil dihapus');
    }
}