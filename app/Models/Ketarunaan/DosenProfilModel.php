<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class DosenProfilModel extends Model
{
    protected $table            = 'dosen_profil';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'nidn',
        'nuptk',
        'homebase_prodi_id',
        'jabatan_fungsional',
        'tmt_jabatan_fungsional',
        'pendidikan_terakhir',
        'bidang_keilmuan',
        'kepakaran_tags',
        'status_serdos',
        'tahun_serdos',
        'nomor_sertifikat_serdos',
        'status_ikatan',
        'is_siap_ditugaskan',
        'sinta_id',
        'google_scholar_url',
        'scopus_id',
        'catatan_pimpinan',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
