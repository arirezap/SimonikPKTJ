<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class PendidikanTarunaModel extends Model
{
    protected $table            = 'pendidikan_taruna';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_taruna', 'program_studi_id', 'angkatan_id', 'kelas_id',
        'tingkat', 'semester_aktif', 'jalur_penerimaan', 'asal_formasi',
        'tahun_akademik_id', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

