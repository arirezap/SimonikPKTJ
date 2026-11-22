<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class BeasiswaPemeringkatanModel extends Model
{
    protected $table            = 'taruna_beasiswa_pemeringkatan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'program_beasiswa_id', 'tahun_akademik_id', 'program_studi_id', 'kelas_id',
        'kode_versi', 'versi', 'populasi_kelas', 'jumlah_kandidat', 'sumber_nilai',
        'parameter_aturan_json', 'ada_seri_kelima', 'aturan_seri', 'status_finalisasi',
        'alasan_ditahan', 'diproses_oleh', 'tanggal_hitung', 'catatan_validasi',
        'divalidasi_oleh', 'tanggal_validasi', 'created_at', 'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
