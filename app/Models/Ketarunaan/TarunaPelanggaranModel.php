<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TarunaPelanggaranModel extends Model
{
    protected $table            = 'taruna_pelanggaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_perkara', 'nomor_taruna', 'tanggal_kejadian', 'lokasi',
        'uraian', 'jenis_pelanggaran', 'tingkat_pelanggaran', 'pelapor',
        'status_penetapan', 'status_penyelesaian', 'sanksi', 'bukti_berkas',
        'status_validasi', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

