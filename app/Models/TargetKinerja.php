<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class TargetKinerja
 * 
 * Model resmi untuk mengelola data Target Kinerja Bulanan (RHK)
 * yang dipetakan ke tabel `target_kinerja_bulanan`.
 */
class TargetKinerja extends Model
{
    protected $table            = 'target_kinerja_bulanan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'user_id',
        'tanggal',
        'bulan',
        'tahun',
        'sasaran_program',
        'indikator_kinerja',
        'target_bulanan',
        'satuan',
        'status_approval',
        'nilai_capaian',
        'status_penilaian',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil daftar target kinerja bulanan beserta total realisasi log harian.
     */
    public function getTargetWithRealization($userId, $bulan, $tahun, $onlySubmitted = false)
    {
        $builder = $this->select('target_kinerja_bulanan.*, IFNULL(SUM(log_kegiatan_harian.jumlah_capaian), 0) as total_realisasi')
                    ->join('log_kegiatan_harian', "log_kegiatan_harian.target_id = target_kinerja_bulanan.id AND log_kegiatan_harian.status = 'terkirim'", 'left')
                    ->where('target_kinerja_bulanan.user_id', $userId)
                    ->where('target_kinerja_bulanan.tahun', $tahun);

        if ($onlySubmitted) {
            $builder->where('target_kinerja_bulanan.status', 'terkirim');
        }

        if ($bulan !== 'all' && $bulan !== '') {
            $builder->where('target_kinerja_bulanan.bulan', $bulan);
        }

        return $builder->groupBy('target_kinerja_bulanan.id')->findAll();
    }
}
