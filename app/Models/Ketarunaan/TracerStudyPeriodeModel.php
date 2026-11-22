<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TracerStudyPeriodeModel extends Model
{
    protected $table            = 'tracer_study_periode';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_periode',
        'public_token',
        'allow_public_link',
        'allow_unregistered_alumni',
        'nama_periode',
        'sumber_bundle_id',
        'sumber_periode_id',
        'target_responden',
        'tahun_lulus_sasaran_json',
        'prodi_sasaran_json',
        'tanggal_mulai',
        'tanggal_selesai',
        'target_partisipasi_persen',
        'status_periode',
        'dibuka_oleh',
        'dibuka_pada',
        'ditutup_oleh',
        'ditutup_pada',
        'deskripsi',
        'dibuat_oleh',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
