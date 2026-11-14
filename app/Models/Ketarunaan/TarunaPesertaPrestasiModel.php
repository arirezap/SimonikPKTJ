<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class TarunaPesertaPrestasiModel extends Model
{
    protected $table            = 'taruna_peserta_prestasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'prestasi_id',
        'nomor_taruna',
        'peran',
        'created_at',
    ];

    protected $useTimestamps = false;
}
