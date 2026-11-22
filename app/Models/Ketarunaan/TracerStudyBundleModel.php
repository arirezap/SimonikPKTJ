<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TracerStudyBundleModel extends Model
{
    protected $table            = 'tracer_study_bundle';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_bundle',
        'nama_bundle',
        'target_responden',
        'deskripsi',
        'is_aktif',
        'dibuat_oleh',
        'diperbarui_oleh',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
