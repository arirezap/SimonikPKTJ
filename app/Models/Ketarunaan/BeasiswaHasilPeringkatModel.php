<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class BeasiswaHasilPeringkatModel extends Model
{
    protected $table            = 'taruna_beasiswa_hasil_peringkat';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pemeringkatan_id', 'nomor_taruna', 'nama_snapshot', 'kelas_snapshot',
        'ip_semester', 'peringkat', 'status_kandidat', 'is_seri', 'catatan', 'created_at',
    ];

    protected $useTimestamps = false;
}
