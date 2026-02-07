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

    // Helper untuk mengambil daftar bawahan (jika diperlukan nanti)
    public function getBawahan($atasanId)
    {
        return $this->where('atasan_id', $atasanId)->findAll();
    }
}