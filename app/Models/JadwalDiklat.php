<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalDiklat extends Model
{
    protected $table            = 'jadwal_diklat';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'nama_diklat', 'periode', 'jumlah_peserta', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
