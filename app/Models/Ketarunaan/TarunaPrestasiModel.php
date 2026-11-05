<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TarunaPrestasiModel extends Model
{
    protected $table            = 'taruna_prestasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_prestasi', 'jenis_prestasi', 'bidang_prestasi', 'tingkat_prestasi',
        'peringkat', 'penyelenggara', 'tanggal_mulai', 'tanggal_selesai',
        'jenis_keikutsertaan', 'nama_tim', 'nomor_sertifikat', 'bukti_berkas',
        'status_validasi', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

