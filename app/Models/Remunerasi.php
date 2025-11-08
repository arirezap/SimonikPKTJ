<?php

namespace App\Models;

use CodeIgniter\Model;

class Remunerasi extends Model
{
    protected $table            = 'remunerasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'tahun', 'bulan', 'jumlah', 'created_by_user_id'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}