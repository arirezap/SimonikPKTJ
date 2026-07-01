<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanHarian extends Model
{
    protected $table            = 'laporan_harian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'user_id',
        'tanggal',
        'bulan',
        'tahun',
        'sasaran_program',
        'indikator_kinerja',
        'target_bulanan',
        'satuan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
