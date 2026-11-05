<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class LowonganPekerjaanModel extends Model
{
    protected $table            = 'lowongan_pekerjaan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'perusahaan_id', 'judul_lowongan', 'jenis_pekerjaan', 'lokasi',
        'deskripsi', 'persyaratan', 'batas_pendaftaran', 'tautan_pendaftaran',
        'status_publikasi', 'status_validasi', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

