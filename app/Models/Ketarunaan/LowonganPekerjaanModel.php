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
        'perusahaan_id', 'judul_lowongan', 'jenis_pekerjaan', 'jenjang_pendidikan',
        'tahun_lulus_sasaran', 'jumlah_kebutuhan', 'lokasi', 'deskripsi',
        'persyaratan', 'pengalaman_minimum', 'sertifikasi_opsional',
        'batas_pendaftaran', 'metode_lamaran', 'tautan_pendaftaran', 'email_lamaran',
        'sumber_informasi', 'tanggal_diterima', 'pic_pengelola', 'catatan_sumber',
        'status_publikasi', 'status_validasi', 'checklist_verifikasi_json',
        'catatan_pemeriksa', 'diajukan_oleh', 'diperiksa_oleh', 'tanggal_pemeriksaan',
        'dipublikasi_oleh', 'tanggal_publikasi', 'alasan_penutupan', 'ditutup_oleh',
        'tanggal_ditutup', 'riwayat_proses_json', 'created_at', 'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

