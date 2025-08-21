<?php
// File: app/Models/Indikator.php
namespace App\Models;
use CodeIgniter\Model;

class Indikator extends Model
{
    protected $table = 'indikator';
    protected $allowedFields = ['nama_indikator'];
}