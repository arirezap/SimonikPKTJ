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

    // Helper untuk mengambil data atasan
    public function getAtasan($userId)
    {
        $user = $this->find($userId);
        if ($user && !empty($user['atasan_id'])) {
            return $this->find($user['atasan_id']);
        }
        return null;
    }

    // Helper untuk mengambil daftar staf langsung (dengan penyaringan periode aktif)
    public function getStaf($atasanId, ?string $periodDate = null)
    {
        $builder = $this->select('id, nama_lengkap, nip, username, jabatan, pangkat, unit, role, status_pegawai, tmt_selesai, foto, atasan_id')
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