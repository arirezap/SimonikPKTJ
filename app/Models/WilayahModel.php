<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model data referensi wilayah administrasi Indonesia (tabel ref_wilayah).
 * Mengacu pada struktur resmi https://github.com/cahyadsn/wilayah (Kepmendagri 2025).
 */
class WilayahModel extends Model
{
    protected $table            = 'ref_wilayah';
    protected $primaryKey       = 'kode';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['kode', 'nama'];
    protected $useTimestamps    = false;
}
