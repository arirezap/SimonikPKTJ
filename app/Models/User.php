<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    // UPDATE: Menambahkan 'atasan_id' ke allowedFields
    protected $allowedFields    = [
        'username', 
        'password', 
        'nama_lengkap', 
        'email', 
        'role', 
        'atasan_id', // Field Baru
        'foto',
        'nip',
        'jabatan',
        'unit',
        'pangkat'
    ];

    // Dates
    protected $useTimestamps = false; // Ubah ke true jika ingin mencatat created_at/updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Helper untuk mengambil data atasan
    public function getAtasan($userId)
    {
        $user = $this->find($userId);
        if ($user && !empty($user['atasan_id'])) {
            return $this->find($user['atasan_id']);
        }
        return null;
    }

    // Helper untuk mengambil daftar bawahan langsung
    public function getBawahan($atasanId)
    {
        return $this->where('atasan_id', $atasanId)->orderBy('nama_lengkap', 'ASC')->findAll();
    }

    // Helper untuk mengambil semua bawahan secara rekursif (hierarki)
    public function getAllBawahan($userId, $role = null)
    {
        // Admin, direktur, dan wadir melihat semua orang
        if (in_array($role, ['admin', 'direktur', 'wadir'])) {
            return $this->where('id !=', $userId)
                        ->whereNotIn('role', ['admin', 'direktur'])
                        ->orderBy('nama_lengkap', 'ASC')
                        ->findAll();
        }

        $allBawahan = [];
        $collectedIds = [];

        // Fungsi rekursif untuk mencari bawahan dari bawahan
        $fetchBawahan = function($id) use (&$fetchBawahan, &$allBawahan, &$collectedIds) {
            $bawahanList = $this->where('atasan_id', $id)->orderBy('nama_lengkap', 'ASC')->findAll();
            foreach ($bawahanList as $b) {
                if (!in_array($b['id'], $collectedIds)) {
                    $collectedIds[] = $b['id'];
                    $allBawahan[] = $b;
                    // Lanjut cari bawahan dari bawahan ini (rekursif)
                    $fetchBawahan($b['id']);
                }
            }
        };

        $fetchBawahan($userId);
        
        return $allBawahan;
    }
}