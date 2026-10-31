<?php

namespace App\Models;

use CodeIgniter\Model;

class SurveiDelegasiModel extends Model
{
    protected $table            = 'survei_pemetaan_delegasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'user_id',
        'delegated_by',
        'can_export',
        'catatan',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil daftar seluruh staf delegasi yang aktif dengan kolom selektif (tanpa password).
     */
    public function getActiveDelegatesList(): array
    {
        return $this->select('survei_pemetaan_delegasi.id, survei_pemetaan_delegasi.user_id, survei_pemetaan_delegasi.delegated_by, survei_pemetaan_delegasi.can_export, survei_pemetaan_delegasi.catatan, survei_pemetaan_delegasi.is_active, survei_pemetaan_delegasi.updated_at, u.nama_lengkap, u.nip, u.unit, u.jabatan, u.foto, pemberi.nama_lengkap as nama_pemberi')
                    ->join('users as u', 'u.id = survei_pemetaan_delegasi.user_id', 'inner')
                    ->join('users as pemberi', 'pemberi.id = survei_pemetaan_delegasi.delegated_by', 'left')
                    ->where('survei_pemetaan_delegasi.is_active', 1)
                    ->orderBy('u.nama_lengkap', 'ASC')
                    ->findAll();
    }
}

