<?php
// File: app/Models/Sasaran.php
namespace App\Models;
use CodeIgniter\Model;

class Sasaran extends Model
{
    protected $table = 'sasaran';
    protected $allowedFields = ['nama_sasaran'];
}
