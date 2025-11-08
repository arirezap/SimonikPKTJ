<?php

namespace App\Models;

use CodeIgniter\Model;

class LedSubmission extends Model
{
    protected $table            = 'led_submissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    // PERUBAHAN DI SINI: Tambahkan 'catatan_kabag' dan 'catatan_wadir'
    protected $allowedFields    = [
        'user_id', 
        'prodi', 
        'tahun', 
        'led_criteria_id', 
        'status', 
        'catatan', 
        'file_bukti', 
        'kabag_approved',
        'catatan_kabag', // BARU
        'catatan_wadir'  // BARU
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}