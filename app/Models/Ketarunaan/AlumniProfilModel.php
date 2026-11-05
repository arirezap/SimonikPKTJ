<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class AlumniProfilModel extends Model
{
    protected $table            = 'alumni_profil';
    protected $primaryKey       = 'nomor_taruna';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_taruna', 'tanggal_lulus', 'nomor_keputusan_lulus',
        'tahun_lulus', 'pekerjaan_saat_ini', 'instansi_saat_ini',
        'kontak_terakhir', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

