<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TarunaPelanggaranPihakModel extends Model
{
    protected $table            = 'taruna_pelanggaran_pihak';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pelanggaran_id',
        'nomor_taruna',
        'nama_pihak',
        'jenis_pihak',
        'peran',
        'keterangan',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
