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

    protected $allowedFields    = [
        'prodi', 
        'nama_kriteria', 
        'id_standar', // <--- PERBAIKAN DI SINI
        'role_assignment'
    ];

    // Dates
    protected $useTimestamps = false; 

    // Validation
    protected $validationRules      = [
        'prodi'           => 'required|string|max_length[50]',
        'nama_kriteria'   => 'required|string',
        'id_standar'     => 'permit_empty|integer', // <--- PERBAIKAN DI SINI
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