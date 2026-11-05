<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class PerusahaanMitraModel extends Model
{
    protected $table            = 'perusahaan_mitra';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_perusahaan', 'sektor', 'alamat', 'website',
        'kontak_person', 'is_verified', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

