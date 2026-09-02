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
        'unit_id',
        'pangkat',
        'no_hp'
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

    // Helper untuk mengambil daftar staf langsung
    public function getStaf($atasanId)
    {
        return $this->where('atasan_id', $atasanId)->orderBy('nama_lengkap', 'ASC')->findAll();
    }

    // Helper untuk mengambil semua staf secara rekursif (hierarki)
    public function getAllStaf($userId, $role = null)
    {
        // Admin dan direktur melihat semua staf
        if (in_array($role, ['admin', 'direktur'])) {
            return $this->where('id !=', $userId)
                        ->whereNotIn('role', ['admin', 'direktur'])
                        ->orderBy('nama_lengkap', 'ASC')
                        ->findAll();
        }

        // Wakil direktur adalah peran eksekutif pengamat/monitoring, tidak membawahi staf secara operasional
        if ($role === 'wadir') {
            return [];
        }

        $allStaf = [];
        $collectedIds = [];

        // Fungsi rekursif untuk mencari staf dari staf
        $fetchStaf = function($id) use (&$fetchStaf, &$allStaf, &$collectedIds) {
            $stafList = $this->where('atasan_id', $id)->orderBy('nama_lengkap', 'ASC')->findAll();
            foreach ($stafList as $b) {
                if (!in_array($b['id'], $collectedIds)) {
                    $collectedIds[] = $b['id'];
                    $allStaf[] = $b;
                    // Lanjut cari staf dari staf ini (rekursif)
                    $fetchStaf($b['id']);
                }
            }
        };

        $fetchStaf($userId);
        
        return $allStaf;
    }
}