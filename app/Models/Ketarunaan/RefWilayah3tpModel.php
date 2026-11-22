<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class RefWilayah3tpModel extends Model
{
    protected $table            = 'ref_wilayah_3tp';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_wilayah', 'provinsi', 'kabupaten_kota', 'kategori_3tp',
        'dasar_acuan', 'versi_acuan', 'tahun_berlaku', 'is_aktif', 'created_at',
    ];

    protected $useTimestamps = false;
}
