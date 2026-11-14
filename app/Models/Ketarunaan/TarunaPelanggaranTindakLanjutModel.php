<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TarunaPelanggaranTindakLanjutModel extends Model
{
    protected $table            = 'taruna_pelanggaran_tindak_lanjut';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pelanggaran_id',
        'tanggal_tindakan',
        'jenis_tindakan',
        'status_penetapan_saat_itu',
        'status_penyelesaian_saat_itu',
        'nomor_dasar',
        'sanksi_tindakan',
        'pic',
        'target_selesai',
        'berkas_pendukung',
        'dicatat_oleh',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
