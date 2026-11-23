<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class DosenRiwayatKualifikasiModel extends Model
{
    protected $table            = 'dosen_riwayat_kualifikasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'dosen_profil_id',
        'kategori',
        'judul',
        'institusi_penyelenggara',
        'tahun_mulai',
        'tahun_selesai',
        'nomor_dokumen',
        'tautan_bukti',
        'keterangan',
        'status_verifikasi',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
