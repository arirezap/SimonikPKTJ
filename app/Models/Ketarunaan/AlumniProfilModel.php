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
        'kontak_terakhir', 'email_alumni', 'telepon_alumni',
        'kota_domisili', 'alamat_domisili', 'status_profil',
        'sumber_konfirmasi', 'tanggal_pembaruan_kontak', 'dicatat_oleh',
        'divalidasi_oleh', 'tanggal_validasi', 'catatan_profil',
        'created_at', 'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

