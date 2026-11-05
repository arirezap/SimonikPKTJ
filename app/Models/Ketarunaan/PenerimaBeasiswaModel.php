<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class PenerimaBeasiswaModel extends Model
{
    protected $table            = 'taruna_penerima_beasiswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'program_beasiswa_id', 'nomor_taruna', 'status_pengajuan',
        'status_penetapan', 'tanggal_penetapan', 'nomor_keputusan',
        'status_validasi', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

