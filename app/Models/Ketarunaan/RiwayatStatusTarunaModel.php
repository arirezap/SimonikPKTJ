<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class RiwayatStatusTarunaModel extends Model
{
    protected $table            = 'riwayat_status_taruna';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_taruna', 'kode_status', 'mulai_berlaku', 'akhir_berlaku',
        'rencana_kembali', 'alasan_perubahan', 'nomor_keputusan',
        'tanggal_keputusan', 'bukti_dokumen', 'status_validasi',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

