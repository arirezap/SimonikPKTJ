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
        'waktu_penyelesaian',
        'kualitas_hasil',
        'disiplin',
        'kerjasama',
        'nilai_harian',
        'link_bukti'
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
                    ->join('laporan_harian', 'laporan_harian.id = log_kegiatan_harian.target_id')
                    ->where('log_kegiatan_harian.user_id', $userId)
                    ->where('log_kegiatan_harian.tanggal_kegiatan', $tanggal)
                    ->orderBy('log_kegiatan_harian.created_at', 'ASC')
                    ->findAll();
    }

    public function getLogByMonth($userId, $bulan, $tahun)
    {
        return $this->select('log_kegiatan_harian.*, laporan_harian.sasaran_program, laporan_harian.indikator_kinerja, laporan_harian.target_bulanan, laporan_harian.satuan')
                    ->join('laporan_harian', 'laporan_harian.id = log_kegiatan_harian.target_id')
                    ->where('log_kegiatan_harian.user_id', $userId)
                    ->where('MONTH(log_kegiatan_harian.tanggal_kegiatan)', $bulan)
                    ->where('YEAR(log_kegiatan_harian.tanggal_kegiatan)', $tahun)
                    ->orderBy('log_kegiatan_harian.tanggal_kegiatan', 'ASC')
                    ->findAll();
    }
}
