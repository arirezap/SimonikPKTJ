<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatAtasan extends Model
{
    protected $table            = 'riwayat_atasan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'atasan_id',
        'jabatan',
        'unit_id',
        'unit',
        'role',
        'tahun',
        'bulan_mulai',
        'bulan_selesai',
        'is_aktif',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil daftar penugasan seorang pegawai untuk tahun tertentu,
     * diurutkan berdasarkan bulan_mulai ASC.
     */
    public function getRiwayatUser(int $userId, ?int $tahun = null): array
    {
        $builder = $this->select('riwayat_atasan.*, atasan.nama_lengkap as nama_atasan, atasan.nip as nip_atasan, atasan.jabatan as jabatan_atasan')
                        ->join('users as atasan', 'atasan.id = riwayat_atasan.atasan_id', 'left')
                        ->where('riwayat_atasan.user_id', $userId);

        if ($tahun !== null) {
            $builder->where('riwayat_atasan.tahun', $tahun);
        }

        return $builder->orderBy('riwayat_atasan.tahun', 'DESC')
                       ->orderBy('riwayat_atasan.bulan_mulai', 'ASC')
                       ->findAll();
    }

    /**
     * Mengambil penugasan aktif seorang pegawai untuk bulan dan tahun tertentu.
     */
    public function getPenugasanAktif(int $userId, int $bulan, int $tahun): ?array
    {
        return $this->select('riwayat_atasan.*, atasan.nama_lengkap as nama_atasan, atasan.nip as nip_atasan, atasan.jabatan as jabatan_atasan')
                    ->join('users as atasan', 'atasan.id = riwayat_atasan.atasan_id', 'left')
                    ->where('riwayat_atasan.user_id', $userId)
                    ->where('riwayat_atasan.tahun', $tahun)
                    ->where('riwayat_atasan.bulan_mulai <=', $bulan)
                    ->where('riwayat_atasan.bulan_selesai >=', $bulan)
                    ->where('riwayat_atasan.is_aktif', 1)
                    ->orderBy('riwayat_atasan.id', 'DESC')
                    ->first();
    }

    /**
     * Memeriksa apakah terdapat tumpang tindih rentang bulan penugasan
     * untuk seorang pegawai pada tahun yang sama.
     */
    public function checkOverlapping(int $userId, int $tahun, int $bulanMulai, int $bulanSelesai, ?int $excludeId = null): bool
    {
        $builder = $this->where('user_id', $userId)
                        ->where('tahun', $tahun)
                        ->where('bulan_mulai <=', $bulanSelesai)
                        ->where('bulan_selesai >=', $bulanMulai);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}
