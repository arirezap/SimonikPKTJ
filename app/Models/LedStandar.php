<?php

namespace App\Models;

use CodeIgniter\Model;

// PASTIKAN NAMA CLASS SUDAH BENAR
class LedStandar extends Model
{
    // PASTIKAN NAMA TABEL SUDAH BENAR
    protected $table            = 'led_standar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    // PASTIKAN NAMA KOLOM SUDAH BENAR
    protected $allowedFields    = ['nama_standar'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        // PASTIKAN ATURAN VALIDASI SUDAH BENAR
        'nama_standar' => 'required|string|max_length[255]|is_unique[led_standar.nama_standar,id,{id}]',
    ];
    protected $validationMessages   = [
        'nama_standar' => [
            'required' => 'Nama standar wajib diisi.',
            'is_unique' => 'Nama standar ini sudah ada.'
        ],
    ];
}