<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class MasterTarunaModel extends Model
{
    protected $table            = 'master_taruna';
    protected $primaryKey       = 'nomor_taruna';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_taruna', 'nama_lengkap', 'nik', 'nama_panggilan',
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama',
        'foto', 'nomor_hp', 'email', 'alamat_domisili', 'alamat_asal',
        'nama_wali', 'hubungan_wali', 'nomor_hp_wali', 'status_terkini',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

