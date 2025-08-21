<?php
// File: app/Models/Satuan.php
namespace App\Models;
use CodeIgniter\Model;

class Satuan extends Model
{
    protected $table = 'satuan';
    protected $allowedFields = ['nama_satuan'];
}