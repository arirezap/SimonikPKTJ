<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TarunaNilaiIpModel extends Model
{
    protected $table            = 'taruna_nilai_ip';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_taruna', 'tahun_akademik_id', 'program_studi_id', 'kelas_id',
        'tingkat', 'semester', 'ip_semester', 'sks_lulus', 'sumber_data',
        'versi', 'status_validasi', 'diimpor_oleh', 'created_at', 'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
