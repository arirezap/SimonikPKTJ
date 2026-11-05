<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class ProgramStudiModel extends Model
{
    protected $table            = 'ref_program_studi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_prodi', 'nama_prodi', 'jenjang', 'is_aktif',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

