<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterIndikatorModel extends Model
{
    // PERBAIKAN: Arahkan langsung ke tabel yang sudah ada
    protected $table            = 'indikator'; 
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    
    // Pastikan kolom-kolom ini sesuai dengan struktur tabel 'indikator' Anda
    protected $allowedFields    = [
        'sasaran_id', 
        'nama_indikator', 
        'satuan' // <--- Pastikan kolom 'satuan' ini ada di tabel indikator Anda
    ];
}