<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TarunaCapaianModel extends Model
{
    protected $table            = 'taruna_capaian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'nomor_taruna',
        'kategori',
        'nama_capaian',
        'tanggal_kegiatan',
        'tahun_akademik_id',
        'semester',
        'penyelenggara',
        'tingkat_kegiatan',
        'uraian_capaian',
        'hasil_capaian',
        'nilai_angka',
        'satuan_nilai',
        'nomor_dokumen',
        'unit_pemilik',
        'sumber_dokumen',
        'bukti_berkas',
        'ukuran_berkas_kb',
        'jumlah_berkas',
        'status_validasi',
        'catatan_validasi',
        'diajukan_oleh',
        'divalidasi_oleh',
    ];
}

