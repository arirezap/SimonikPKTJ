<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TarunaPelanggaranModel extends Model
{
    protected $table            = 'taruna_pelanggaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_perkara', 'nomor_taruna', 'tanggal_kejadian', 'lokasi',
        'uraian', 'jenis_pelanggaran', 'tingkat_pelanggaran', 'pelapor',
        'unit_pemilik', 'sumber_data', 'tingkat_kerahasiaan',
        'tanggal_pemeriksaan', 'pemeriksa', 'dasar_peraturan',
        'ringkasan_klarifikasi', 'hasil_pemeriksaan', 'berkas_bap',
        'status_penetapan', 'status_penyelesaian', 'sanksi',
        'jenis_sanksi', 'nomor_keputusan', 'tanggal_keputusan',
        'tindak_lanjut', 'tanggal_selesai', 'pic_tindak_lanjut',
        'status_pemantauan', 'catatan_pemantauan',
        'bukti_berkas', 'dokumen_bukti_json',
        'status_validasi', 'diajukan_oleh', 'divalidasi_oleh',
        'tanggal_validasi', 'catatan_validasi', 'riwayat_validasi_json',
        'created_at', 'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
