<?php

namespace App\Models;

use CodeIgniter\Model;

class SkpModel extends Model
{
    protected $table            = 'skp_targets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields = [
        'user_id',
        'skp_header_id',
        'tahun',
        'jenis',
        'rhk_pimpinan',
        'rencana_kinerja', // <-- Tambahkan rhk_pimpinan & skp_header_id
        'aspek',
        'indikator',
        'target',
        'satuan'
    ];

    protected $useTimestamps = true;
}
