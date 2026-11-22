<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TahunAkademikModel extends Model
{
    protected $table            = 'ref_tahun_akademik';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_tahun', 'tahun', 'semester', 'tanggal_mulai', 'tanggal_selesai', 'is_aktif',
        'created_at', 'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
