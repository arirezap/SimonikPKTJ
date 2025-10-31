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

    // PERBARUI: Tambahkan 'kategori' dan 'role_assignment'
    protected $allowedFields    = ['nomor_kriteria', 'nama_kriteria', 'kategori', 'role_assignment'];

    // Dates
    protected $useTimestamps = false; // Di file migrasi kita tidak menambahkannya, jadi ini false

    // Validation
    protected $validationRules      = [
        'nomor_kriteria' => 'required|string|max_length[50]',
        'nama_kriteria'  => 'required|string',
        'kategori'       => 'permit_empty|string|max_length[255]', // Kategori boleh kosong
        'role_assignment'=> 'permit_empty|string|max_length[50]', // Penugasan peran boleh kosong
    ];
    protected $validationMessages   = [
        'nomor_kriteria' => [
            'required' => 'Nomor kriteria wajib diisi.',
        ],
        'nama_kriteria' => [
            'required' => 'Nama kriteria wajib diisi.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
