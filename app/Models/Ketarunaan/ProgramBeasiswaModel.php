<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class ProgramBeasiswaModel extends Model
{
    protected $table            = 'taruna_program_beasiswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_program', 'jenis_beasiswa', 'pemberi', 'tahun', 'tahun_akademik_id',
        'periode_mulai', 'periode_selesai', 'kuota', 'nominal', 'persyaratan',
        'penanggung_jawab', 'dasar_penilaian', 'periode_nilai_id', 'basis_kelas',
        'jumlah_peringkat', 'aturan_seri', 'status_program',
        'created_at', 'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

