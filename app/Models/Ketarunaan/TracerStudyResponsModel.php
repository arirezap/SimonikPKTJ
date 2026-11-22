<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TracerStudyResponsModel extends Model
{
    protected $table            = 'tracer_study_respons';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'periode_id',
        'nomor_taruna',
        'is_alumni_terdaftar',
        'nama_responden',
        'prodi_id_responden',
        'tahun_lulus_responden',
        'tanggal_lahir_responden',
        'no_hp_responden',
        'email_responden',
        'status_transisi',
        'masa_tunggu_bulan',
        'tanggal_mulai_kerja',
        'nama_instansi',
        'sektor_industri',
        'jabatan_posisi',
        'kota_kerja',
        'kesesuaian_bidang',
        'kesesuaian_jenjang',
        'sumber_pengisian',
        'status_validasi',
        'catatan_validasi',
        'dicatat_oleh',
        'divalidasi_oleh',
        'tanggal_pengisian',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
