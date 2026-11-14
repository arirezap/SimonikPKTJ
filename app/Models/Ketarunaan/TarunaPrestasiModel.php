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
        'nama_prestasi',
        'jenis_prestasi',
        'bidang_prestasi',
        'tingkat_prestasi',
        'peringkat',
        'penyelenggara',
        'lokasi_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis_keikutsertaan',
        'nama_tim',
        'pembimbing',
        'unit_pemilik',
        'sumber_data',
        'nomor_sertifikat',
        'bukti_berkas',
        'ukuran_berkas_kb',
        'dokumentasi_json',
        'status_validasi',
        'catatan_validasi',
        'diajukan_oleh',
        'divalidasi_oleh',
        'tanggal_validasi',
        'riwayat_validasi_json',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
