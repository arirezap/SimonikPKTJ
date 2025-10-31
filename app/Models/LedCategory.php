<?php

namespace App\Models;

use CodeIgniter\Model;

class LedCategory extends Model
{
    protected $table            = 'led_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_kategori'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'nama_kategori' => 'required|string|max_length[255]|is_unique[led_categories.nama_kategori,id,{id}]',
    ];
    protected $validationMessages   = [
        'nama_kategori' => [
            'required' => 'Nama kategori wajib diisi.',
            'is_unique' => 'Nama kategori ini sudah ada.'
        ],
    ];
}
