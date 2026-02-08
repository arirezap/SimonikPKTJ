<?php

namespace App\Models;

use CodeIgniter\Model;

class SkpHeaderModel extends Model
{
    protected $table            = 'skp_headers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'user_id',
        'tahun',
        'model_skp', // <--- TAMBAHKAN INI
        'periode_awal',
        'periode_akhir',
        'status'
    ];
    protected $useTimestamps    = true;

    // Fungsi Cek Apakah Direktur Sudah Buat SKP di Tahun Tersebut
    public function isDirekturSkpExists($tahun)
    {
        return $this->select('skp_headers.id')
            ->join('users', 'users.id = skp_headers.user_id')
            // UPDATE DISINI: Pastikan mencari role 'direktur'
            ->where('users.role', 'direktur')
            ->where('skp_headers.tahun', $tahun)
            ->countAllResults() > 0;
    }
}
