<?php

namespace App\Models;

use CodeIgniter\Model;

class SurveiResponModel extends Model
{
    protected $table            = 'survei_pemetaan_respon';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'user_id',
        'nomor_identitas',
        'tipe_responden',
        'tahun_survei',
        'status',
        'last_step',
        'total_answered',
        'total_applicable',
        'progress_percent',
        'resume_token_hash',
        'filled_via_auth',
        'pending_profile_sync',
        'is_valid',
        'snapshot_nama',
        'snapshot_nip',
        'snapshot_status_kepegawaian',
        'snapshot_pangkat',
        'snapshot_jabatan',
        'snapshot_unit',
        'snapshot_unit_id',
        'submitted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Cari sesi survei berdasarkan nomor identitas (NIP/NIK) dan tahun survei.
     */
    public function findByIdentitasTahun(string $nomorIdentitas, int $tahun): ?array
    {
        $row = $this->where('nomor_identitas', $nomorIdentitas)
                    ->where('tahun_survei', $tahun)
                    ->first();
        return $row ?: null;
    }

    /**
     * Cari sesi survei berdasarkan user_id akun ECC dan tahun survei.
     */
    public function findByUserTahun(int $userId, int $tahun): ?array
    {
        $row = $this->where('user_id', $userId)
                    ->where('tahun_survei', $tahun)
                    ->first();
        return $row ?: null;
    }
}

