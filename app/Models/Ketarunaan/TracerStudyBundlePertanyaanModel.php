<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TracerStudyBundlePertanyaanModel extends Model
{
    protected $table            = 'tracer_study_bundle_pertanyaan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'bundle_id',
        'kode_pertanyaan',
        'bagian',
        'urutan',
        'teks_pertanyaan',
        'keterangan_bantuan',
        'tipe_jawaban',
        'opsi_jawaban_json',
        'is_wajib',
        'is_aktif',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
