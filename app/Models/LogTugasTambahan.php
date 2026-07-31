<?php

namespace App\Models;

use CodeIgniter\Model;

class LogTugasTambahan extends Model
{
    protected $table            = 'log_tugas_tambahan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'user_id',
        'tanggal_kegiatan',
        'deskripsi_kegiatan',
        'jumlah_capaian',
        'satuan',
        'link_bukti',
        'status',
        'status_approval',
        'nilai_capaian',
        'status_penilaian'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getLogByDate($userId, $tanggal)
    {
        return $this->where('user_id', $userId)
                    ->where('tanggal_kegiatan', $tanggal)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    public function getLogByMonth($userId, $bulan, $tahun)
    {
        return $this->where('user_id', $userId)
                    ->where('MONTH(tanggal_kegiatan)', $bulan)
                    ->where('YEAR(tanggal_kegiatan)', $tahun)
                    ->orderBy('tanggal_kegiatan', 'ASC')
                    ->findAll();
    }
}
