<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

/**
 * Model riwayat pembaruan kontak dan koreksi kelulusan alumni terkendali (ALM-02, ALM-03).
 */
class AlumniRiwayatPerubahanModel extends Model
{
    protected $table            = 'alumni_riwayat_perubahan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_taruna',
        'jenis_kejadian',
        'judul_ringkas',
        'data_sebelum_json',
        'data_sesudah_json',
        'tanggal_lulus_baru',
        'nomor_keputusan_baru',
        'status_taruna_pengganti',
        'sumber_konfirmasi',
        'tanggal_kejadian',
        'alasan_perubahan',
        'dampak_ringkasan',
        'status_validasi',
        'catatan_validator',
        'diajukan_oleh',
        'peran_pencatat',
        'divalidasi_oleh',
        'tanggal_validasi',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
