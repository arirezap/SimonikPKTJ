<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\UnitKerja;

class DaftarPegawaiController extends BaseController
{
    public function index()
    {
        helper(['avatar']);
        
        $userModel = new User();
        $unitKerjaModel = new UnitKerja();
        $db = \Config\Database::connect();

        $search    = $this->request->getGet('search');
        $role      = $this->request->getGet('role');
        $unit      = $this->request->getGet('unit');
        $sortBy    = $this->request->getGet('sort_by') ?? 'nama_lengkap';
        $sortOrder = strtolower($this->request->getGet('sort_order') ?? 'asc') === 'desc' ? 'desc' : 'asc';

        // Mapping sort columns
        $validSortColumns = [
            'nama_lengkap' => 'users.nama_lengkap',
            'jabatan'      => 'users.jabatan',
            'unit'         => 'users.unit',
            'atasan'       => 'atasan.nama_lengkap'
        ];

        $dbSortColumn = $validSortColumns[$sortBy] ?? 'users.nama_lengkap';

        // Dapatkan ID pengguna yang memiliki role 'admin' di pivot table user_roles
        $adminIds = array_column(
            $db->table('user_roles')->select('user_id')->where('role_name', 'admin')->get()->getResultArray(),
            'user_id'
        );

        // Query dasar: Sembunyikan akun Superadmin (role = 'admin' atau username = 'admin')
        $query = $userModel->select('users.*, atasan.nama_lengkap as nama_atasan')
                           ->join('users as atasan', 'atasan.id = users.atasan_id', 'left')
                           ->where('users.role !=', 'admin')
                           ->where('users.username !=', 'admin');

        if (!empty($adminIds)) {
            $query->whereNotIn('users.id', $adminIds);
        }

        if (!empty($search)) {
            $query->groupStart()
                  ->like('users.nama_lengkap', $search)
                  ->orLike('users.nip', $search)
                  ->orLike('users.username', $search)
                  ->groupEnd();
        }

        if (!empty($role)) {
            $query->where('users.role', $role);
        }

        if (!empty($unit)) {
            $query->where('users.unit', $unit);
        }

        $users = $query->orderBy($dbSortColumn, $sortOrder)->findAll();

        $data = [
            'title'           => 'Daftar Pegawai',
            'users'           => $users,
            'unit_kerja_list' => $unitKerjaModel->orderBy('nama_unit', 'ASC')->findAll(),
            'search'          => $search,
            'filter_role'     => $role,
            'filter_unit'     => $unit,
            'sort_by'         => $sortBy,
            'sort_order'      => $sortOrder
        ];

        return view('user/daftar_pegawai', $data);
    }
}
