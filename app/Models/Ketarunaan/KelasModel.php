<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table            = 'ref_kelas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'program_studi_id', 'angkatan_id', 'tingkat', 'nama_kelas', 'is_aktif',
        'created_at', 'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
