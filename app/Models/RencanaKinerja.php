<?php

namespace App\Models;

use CodeIgniter\Model;

class RencanaKinerja extends Model
{
    protected $table            = 'rencana_kinerja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    // Sesuaikan dengan semua kolom di tabel Anda yang boleh diisi
    protected $allowedFields    = [
        'user_id',
        'sasaran_program',
        'indikator_kinerja',
        'satuan',
        'target_utama',
        'kegiatan',
        'target_bulanan',
        'realisasi_bulanan',
        'tahun_anggaran'
    ];
}