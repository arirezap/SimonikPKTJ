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
        'approved_by',
        'approved_at',
        'nilai_capaian',
        'status_penilaian',
        'penilai_id',
        'penilaian_at',
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
        $builder = $this->select('target_kinerja_bulanan.*, IFNULL(SUM(log_kegiatan_harian.jumlah_capaian), 0) as total_realisasi, penilai.nama_lengkap as nama_penilai')
                    ->join('users as penilai', 'penilai.id = target_kinerja_bulanan.penilai_id', 'left')
                    ->join('log_kegiatan_harian', "log_kegiatan_harian.target_id = target_kinerja_bulanan.id AND log_kegiatan_harian.status = 'terkirim'", 'left')
                    ->where('target_kinerja_bulanan.user_id', $userId)
                    ->where('target_kinerja_bulanan.tahun', $tahun);

        if ($onlySubmitted) {
            $builder->where('target_kinerja_bulanan.status', 'terkirim');
        }

        if ($bulan !== 'all' && $bulan !== '') {
            $builder->where('target_kinerja_bulanan.bulan', $bulan);
        }

        return $builder->groupBy('target_kinerja_bulanan.id')
                       ->orderBy('target_kinerja_bulanan.id', 'ASC')
                       ->findAll();
    }

    /**
     * Mengambil target kinerja bulanan berdasarkan user_id, bulan, dan tahun.
     */
    public function getTargetsByUserAndPeriod(int $userId, int $bulan, int $tahun): array
    {
        return $this->where('user_id', $userId)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    /**
     * Memeriksa apakah suatu baris data target kinerja telah terisi lengkap.
     */
    public function isComplete(array $target): bool
    {
        $sasaran   = trim((string)($target['sasaran_program'] ?? ''));
        $indikator = trim((string)($target['indikator_kinerja'] ?? ''));
        $satuan    = trim((string)($target['satuan'] ?? ''));
        $targetVal = $target['target_bulanan'] ?? null;

        if ($sasaran === '' || $indikator === '' || $satuan === '') {
            return false;
        }

        if ($targetVal === null || !is_numeric($targetVal) || (float)$targetVal <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Mengambil daftar target kinerja yang siap disetujui (berstatus terkirim dan belum disetujui).
     */
    public function getPendingApprovalTargets(int $userId, int $bulan, int $tahun): array
    {
        return $this->where('user_id', $userId)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->where('status', 'terkirim')
                    ->where('status_approval !=', 'disetujui')
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }
}
