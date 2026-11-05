<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class AngkatanModel extends Model
{
    protected $table            = 'ref_angkatan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tahun_masuk', 'perkiraan_lulus', 'is_aktif',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

