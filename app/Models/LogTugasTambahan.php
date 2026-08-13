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

    public function getLogByMonth($userId, $bulan, $tahun, $onlySubmitted = false)
    {
        $startDate = sprintf('%04d-%02d-01', (int)$tahun, (int)$bulan);
        $endDate   = date('Y-m-t', strtotime($startDate));

        $builder = $this->where('user_id', $userId)
                        ->where('tanggal_kegiatan >=', $startDate)
                        ->where('tanggal_kegiatan <=', $endDate);

        if ($onlySubmitted) {
            $builder->where('status', 'terkirim');
        }

        return $builder->orderBy('tanggal_kegiatan', 'ASC')->findAll();
    }
}
