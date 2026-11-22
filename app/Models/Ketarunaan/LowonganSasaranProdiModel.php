<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

/**
 * Model pemetaan program studi sasaran untuk setiap lowongan pekerjaan.
 */
class LowonganSasaranProdiModel extends Model
{
    protected $table            = 'lowongan_sasaran_prodi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'lowongan_id', 'program_studi_id', 'created_at',
    ];

    protected $useTimestamps = false;
}
