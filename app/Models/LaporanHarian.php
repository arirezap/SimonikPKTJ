<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanHarian extends Model
{
    protected $table            = 'laporan_harian';
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
        'nilai_capaian'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getTargetWithRealization($userId, $bulan, $tahun)
    {
        $builder = $this->select('laporan_harian.*, IFNULL(SUM(log_kegiatan_harian.jumlah_capaian), 0) as total_realisasi')
                    ->join('log_kegiatan_harian', "log_kegiatan_harian.target_id = laporan_harian.id AND log_kegiatan_harian.status = 'terkirim'", 'left')
                    ->where('laporan_harian.user_id', $userId)
                    ->where('laporan_harian.tahun', $tahun);

        if ($bulan !== 'all' && $bulan !== '') {
            $builder->where('laporan_harian.bulan', $bulan);
        }

        return $builder->groupBy('laporan_harian.id')->findAll();
    }
}
