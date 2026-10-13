<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    // UPDATE: Menambahkan 'atasan_id' ke allowedFields
    protected $allowedFields    = [
        'username', 
        'password', 
        'nama_lengkap', 
        'email', 
        'role', 
        'status_pegawai',
        'tmt_selesai',
        'dispensasi_lampau_sampai',
        'atasan_id', // Field Baru
        'foto',
        'nip',
        'jabatan',
        'unit',
        'unit_id',
        'pangkat',
        'no_hp'
    ];

    // Dates
    protected $useTimestamps = false; // Ubah ke true jika ingin mencatat created_at/updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Helper untuk mengambil data atasan (mendukung resolusi periode)
    public function getAtasan($userId, ?int $bulan = null, ?int $tahun = null)
    {
        $penugasan = $this->getPenugasanPeriode((int)$userId, $bulan, $tahun);
        if ($penugasan && !empty($penugasan['atasan_id'])) {
            return $this->find($penugasan['atasan_id']);
        }
        return null;
    }

    /**
     * Mengambil data penugasan resmi pegawai pada periode bulan & tahun tertentu.
     * Mengutamakan data dari tabel riwayat_atasan. Jika belum ada (fallback),
     * menggunakan profil default dari tabel users.
     */
    public function getPenugasanPeriode(int $userId, ?int $bulan = null, ?int $tahun = null): ?array
    {
        $user = $this->select('id, nama_lengkap, nip, username, jabatan, pangkat, unit, unit_id, role, status_pegawai, tmt_selesai, foto, atasan_id')
                     ->find($userId);
        if (!$user) {
            return null;
        }

        $bulan = $bulan ? (int)$bulan : (int)date('n');
        $tahun = $tahun ? (int)$tahun : (int)date('Y');

        $db = \Config\Database::connect();
        $riwayat = null;
        if ($db->tableExists('riwayat_atasan')) {
            $riwayat = $db->table('riwayat_atasan')
                          ->select('riwayat_atasan.*, atasan.nama_lengkap as nama_atasan, atasan.nip as nip_atasan, atasan.jabatan as jabatan_atasan')
                          ->join('users as atasan', 'atasan.id = riwayat_atasan.atasan_id', 'left')
                          ->where('riwayat_atasan.user_id', $userId)
                          ->where('riwayat_atasan.tahun', $tahun)
                          ->where('riwayat_atasan.bulan_mulai <=', $bulan)
                          ->where('riwayat_atasan.bulan_selesai >=', $bulan)
                          ->where('riwayat_atasan.is_aktif', 1)
                          ->orderBy('riwayat_atasan.id', 'DESC')
                          ->get()
                          ->getRowArray();
        }

        if ($riwayat) {
            return [
                'user_id'       => (int)$user['id'],
                'nama_lengkap'  => $user['nama_lengkap'],
                'nip'           => $user['nip'],
                'atasan_id'     => (int)$riwayat['atasan_id'],
                'nama_atasan'   => $riwayat['nama_atasan'] ?? '',
                'nip_atasan'    => $riwayat['nip_atasan'] ?? '',
                'jabatan'       => !empty($riwayat['jabatan']) ? $riwayat['jabatan'] : $user['jabatan'],
                'unit'          => !empty($riwayat['unit']) ? $riwayat['unit'] : $user['unit'],
                'unit_id'       => !empty($riwayat['unit_id']) ? (int)$riwayat['unit_id'] : (int)$user['unit_id'],
                'role'          => !empty($riwayat['role']) ? $riwayat['role'] : $user['role'],
                'is_riwayat'    => true,
                'riwayat_id'    => (int)$riwayat['id'],
                'bulan_mulai'   => (int)$riwayat['bulan_mulai'],
                'bulan_selesai' => (int)$riwayat['bulan_selesai'],
                'tahun'         => (int)$riwayat['tahun'],
            ];
        }

        // Fallback ke profil aktif pengguna di tabel users
        $namaAtasan = '';
        $nipAtasan = '';
        if (!empty($user['atasan_id'])) {
            $atasanUser = $this->select('nama_lengkap, nip')->find($user['atasan_id']);
            if ($atasanUser) {
                $namaAtasan = $atasanUser['nama_lengkap'];
                $nipAtasan  = $atasanUser['nip'];
            }
        }

        return [
            'user_id'       => (int)$user['id'],
            'nama_lengkap'  => $user['nama_lengkap'],
            'nip'           => $user['nip'],
            'atasan_id'     => !empty($user['atasan_id']) ? (int)$user['atasan_id'] : null,
            'nama_atasan'   => $namaAtasan,
            'nip_atasan'    => $nipAtasan,
            'jabatan'       => $user['jabatan'],
            'unit'          => $user['unit'],
            'unit_id'       => $user['unit_id'] ? (int)$user['unit_id'] : null,
            'role'          => $user['role'],
            'is_riwayat'    => false,
            'riwayat_id'    => null,
            'bulan_mulai'   => 1,
            'bulan_selesai' => 12,
            'tahun'         => $tahun,
        ];
    }

    // Helper untuk mengambil daftar staf langsung (dengan resolusi riwayat penugasan dan status aktif)
    public function getStaf($atasanId, ?string $periodDate = null)
    {
        $atasanId = (int)$atasanId;
        $db = \Config\Database::connect();

        if ($periodDate !== null && $db->tableExists('riwayat_atasan')) {
            $periodMonth = date('Y-m', strtotime($periodDate));
            $targetBulan = (int)date('n', strtotime($periodDate));
            $targetTahun = (int)date('Y', strtotime($periodDate));

            // 1. Ambil staf yang tercatat resmi di riwayat_atasan pada periode bulan & tahun ini
            $riwayatRows = $db->table('riwayat_atasan')
                              ->select('user_id, jabatan, unit, unit_id, role')
                              ->where('atasan_id', $atasanId)
                              ->where('tahun', $targetTahun)
                              ->where('bulan_mulai <=', $targetBulan)
                              ->where('bulan_selesai >=', $targetBulan)
                              ->where('is_aktif', 1)
                              ->get()
                              ->getResultArray();

            $riwayatUserMap = [];
            foreach ($riwayatRows as $rRow) {
                $riwayatUserMap[(int)$rRow['user_id']] = $rRow;
            }
            $riwayatUserIds = array_keys($riwayatUserMap);

            // 2. Cegah staf yang pada periode ini sudah resmi dipetakan ke ATASAN LAIN agar tidak ikut fallback
            $otherAssignedIds = array_column(
                $db->table('riwayat_atasan')
                   ->select('user_id')
                   ->where('atasan_id !=', $atasanId)
                   ->where('tahun', $targetTahun)
                   ->where('bulan_mulai <=', $targetBulan)
                   ->where('bulan_selesai >=', $targetBulan)
                   ->where('is_aktif', 1)
                   ->get()
                   ->getResultArray(),
                'user_id'
            );
            $otherAssignedIds = array_map('intval', $otherAssignedIds);

            // 3. Ambil staf fallback (yang memiliki users.atasan_id = $atasanId dan belum dipetakan ke atasan lain)
            $fallbackQuery = $this->select('id')->where('atasan_id', $atasanId);
            if (!empty($otherAssignedIds)) {
                $fallbackQuery->whereNotIn('id', $otherAssignedIds);
            }
            $fallbackUserIds = array_column($fallbackQuery->findAll(), 'id');
            $fallbackUserIds = array_map('intval', $fallbackUserIds);

            $allTargetUserIds = array_unique(array_merge($riwayatUserIds, $fallbackUserIds));
            if (empty($allTargetUserIds)) {
                return [];
            }

            $builder = $this->select('id, nama_lengkap, nip, username, jabatan, pangkat, unit, unit_id, role, status_pegawai, tmt_selesai, foto, atasan_id')
                            ->whereIn('id', $allTargetUserIds);

            $builder->groupStart()
                    ->where('status_pegawai', 'aktif')
                    ->orGroupStart()
                        ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun'])
                        ->where('tmt_selesai IS NOT NULL')
                        ->where('tmt_selesai >=', $periodMonth . '-01')
                    ->groupEnd()
                    ->groupEnd();

            $results = $builder->orderBy('nama_lengkap', 'ASC')->findAll();

            // Override jabatan, unit, role sesuai penugasan riwayat jika ada
            foreach ($results as &$staf) {
                $sId = (int)$staf['id'];
                if (isset($riwayatUserMap[$sId])) {
                    $rw = $riwayatUserMap[$sId];
                    if (!empty($rw['jabatan'])) $staf['jabatan'] = $rw['jabatan'];
                    if (!empty($rw['unit']))    $staf['unit']    = $rw['unit'];
                    if (!empty($rw['unit_id'])) $staf['unit_id'] = $rw['unit_id'];
                    if (!empty($rw['role']))    $staf['role']    = $rw['role'];
                }
            }
            unset($staf);

            return $results;
        }

        // Jalur standar jika periodDate kosong atau tabel riwayat belum ada
        $builder = $this->select('id, nama_lengkap, nip, username, jabatan, pangkat, unit, unit_id, role, status_pegawai, tmt_selesai, foto, atasan_id')
                        ->where('atasan_id', $atasanId);

        if ($periodDate !== null) {
            $periodMonth = date('Y-m', strtotime($periodDate));
            $builder->groupStart()
                    ->where('status_pegawai', 'aktif')
                    ->orGroupStart()
                        ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun'])
                        ->where('tmt_selesai IS NOT NULL')
                        ->where('tmt_selesai >=', $periodMonth . '-01')
                    ->groupEnd()
                    ->groupEnd();
        }

        return $builder->orderBy('nama_lengkap', 'ASC')->findAll();
    }

    // Helper untuk mengambil semua staf secara rekursif (hierarki)
    public function getAllStaf($userId, $role = null, ?string $periodDate = null)
    {
        // Admin dan direktur melihat semua staf
        if (in_array($role, ['admin', 'direktur'])) {
            $builder = $this->select('id, nama_lengkap, nip, username, jabatan, pangkat, unit, role, status_pegawai, tmt_selesai, foto, atasan_id')
                            ->where('id !=', $userId)
                            ->whereNotIn('role', ['admin', 'direktur']);

            if ($periodDate !== null) {
                $periodMonth = date('Y-m', strtotime($periodDate));
                $builder->groupStart()
                        ->where('status_pegawai', 'aktif')
                        ->orGroupStart()
                            ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun'])
                            ->where('tmt_selesai IS NOT NULL')
                            ->where('tmt_selesai >=', $periodMonth . '-01')
                        ->groupEnd()
                        ->groupEnd();
            }

            return $builder->orderBy('nama_lengkap', 'ASC')->findAll();
        }

        // Wakil direktur adalah peran eksekutif pengamat/monitoring, tidak membawahi staf secara operasional
        if ($role === 'wadir') {
            return [];
        }

        $allStaf = [];
        $collectedIds = [];

        // Fungsi rekursif untuk mencari staf dari staf
        $fetchStaf = function($id) use (&$fetchStaf, &$allStaf, &$collectedIds, $periodDate) {
            $stafList = $this->getStaf($id, $periodDate);
            foreach ($stafList as $b) {
                if (!in_array($b['id'], $collectedIds)) {
                    $collectedIds[] = $b['id'];
                    $allStaf[] = $b;
                    // Lanjut cari staf dari staf ini (rekursif)
                    $fetchStaf($b['id']);
                }
            }
        };

        $fetchStaf($userId);
        
        return $allStaf;
    }
}