<?php

namespace App\Models;

use CodeIgniter\Model;

class LedCriteria extends Model
{
    protected $table            = 'led_criteria';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    /**
     * INI ADALAH PERBAIKANNYA:
     * Kita ganti 'kategori' dan 'nomor_kriteria' lama dengan
     * 'prodi', 'id_kategori', dan 'role_assignment' yang baru.
     */
    protected $allowedFields    = [
        'prodi', 
        'nama_kriteria', 
        'id_kategori', // Ini yang hilang
        'role_assignment'
        // 'sort_order' sudah kita hapus
    ];

    // Dates
    protected $useTimestamps = false; 

    // Validation
    protected $validationRules      = [
        'prodi'           => 'required|string|max_length[50]',
        'nama_kriteria'   => 'required|string',
        'id_kategori'     => 'permit_empty|integer',
        'role_assignment' => 'permit_empty|string|max_length[50]',
    ];
    protected $validationMessages   = [
        'prodi' => [
            'required' => 'Program Studi wajib diisi.',
        ],
        'nama_kriteria' => [
            'required' => 'Nama kriteria wajib diisi.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}