<?php

namespace App\Models;

use CodeIgniter\Model;

class LedScore extends Model
{
    protected $table            = 'led_scores';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'prodi', 'tahun', 'led_criteria_id', 'skor'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
