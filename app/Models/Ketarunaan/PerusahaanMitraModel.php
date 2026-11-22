<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class PerusahaanMitraModel extends Model
{
    protected $table            = 'perusahaan_mitra';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_perusahaan', 'sektor', 'kota', 'alamat', 'website',
        'kontak_person', 'pic_jabatan', 'email_rekrutmen', 'telepon_rekrutmen',
        'is_verified', 'sumber_verifikasi', 'tanggal_pemeriksaan_kontak',
        'catatan_verifikasi', 'diperiksa_oleh', 'created_at', 'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

