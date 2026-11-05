<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class ProgramBeasiswaModel extends Model
{
    protected $table            = 'taruna_program_beasiswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_program', 'jenis_beasiswa', 'pemberi', 'tahun',
        'periode_mulai', 'periode_selesai', 'kuota', 'status_program',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

