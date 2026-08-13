<?php

namespace App\Models;

use CodeIgniter\Model;

class LogKegiatanHarian extends Model
{
    protected $table            = 'log_kegiatan_harian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'user_id',
        'target_id',
        'tanggal_kegiatan',
        'deskripsi_kegiatan',
        'jumlah_capaian',
        'link_bukti',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Join dengan laporan_harian untuk mengambil RHK
    public function getLogWithTarget($userId, $tanggal)
    {
        return $this->select('log_kegiatan_harian.*, laporan_harian.sasaran_program, laporan_harian.indikator_kinerja, laporan_harian.satuan')
                    ->join('laporan_harian', 'laporan_harian.id = log_kegiatan_harian.target_id', 'left')
                    ->where('log_kegiatan_harian.user_id', $userId)
                    ->where('log_kegiatan_harian.tanggal_kegiatan', $tanggal)
                    ->orderBy('log_kegiatan_harian.created_at', 'ASC')
                    ->findAll();
    }

    public function getLogByMonth($userId, $bulan, $tahun, $onlySubmitted = false)
    {
        $startDate = sprintf('%04d-%02d-01', (int)$tahun, (int)$bulan);
        $endDate   = date('Y-m-t', strtotime($startDate));

        $builder = $this->select('log_kegiatan_harian.*, laporan_harian.sasaran_program, laporan_harian.indikator_kinerja, laporan_harian.target_bulanan, laporan_harian.satuan')
                    ->join('laporan_harian', 'laporan_harian.id = log_kegiatan_harian.target_id', 'left')
                    ->where('log_kegiatan_harian.user_id', $userId)
                    ->where('log_kegiatan_harian.tanggal_kegiatan >=', $startDate)
                    ->where('log_kegiatan_harian.tanggal_kegiatan <=', $endDate);

        if ($onlySubmitted) {
            $builder->where('log_kegiatan_harian.status', 'terkirim');
        }

        return $builder->orderBy('log_kegiatan_harian.tanggal_kegiatan', 'ASC')->findAll();
    }
}
