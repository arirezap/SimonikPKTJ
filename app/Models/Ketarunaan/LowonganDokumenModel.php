<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

/**
 * Model dokumen pendukung & tautan sumber lowongan kerja (LOW-03).
 */
class LowonganDokumenModel extends Model
{
    protected $table            = 'lowongan_dokumen';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'lowongan_id', 'kategori_dokumen', 'judul_dokumen', 'sumber_dokumen',
        'nama_berkas', 'url_sumber', 'tanggal_diterima', 'status_pemeriksaan',
        'is_internal', 'catatan', 'diunggah_oleh', 'created_at', 'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
