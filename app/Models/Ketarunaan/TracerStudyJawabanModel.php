<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TracerStudyJawabanModel extends Model
{
    protected $table            = 'tracer_study_jawaban';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'respons_id',
        'periode_pertanyaan_id',
        'kode_pertanyaan',
        'nilai_teks',
        'nilai_angka',
        'nilai_opsi_json',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
