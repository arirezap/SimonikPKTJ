<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\UnitKerja;

class TimController extends BaseController
{
    protected $userModel;
    protected $unitKerjaModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->unitKerjaModel = new UnitKerja();
    }

    public function index()
    {
        helper(['avatar', 'role', 'audit']);
        
        if (!hasAnyRole(['manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kanit', 'katim', 'kapokja', 'admin'])) {
            return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki hak akses ke modul Kelola Tim.');
        }

        $userId = $this->getNormalizedCurrentUserId();
        $me = $this->userModel->find($userId);
        $myUnit = $me['unit'] ?? null;
        
        // Ambil staf dengan selective columns: yang atasan_id-nya = $userId ATAU unit-nya sama dengan unit manajer
        $stafQuery = $this->userModel->select('id, nama_lengkap, nip, unit, unit_id, jabatan, role, atasan_id, foto')
                                     ->where('id !=', $userId);
        
        if (!empty($myUnit)) {
            $stafQuery->groupStart()
                         ->where('atasan_id', $userId)
                         ->orWhere('unit', $myUnit)
                         ->groupEnd();
        } else {
            $stafQuery->where('atasan_id', $userId);
        }
        $staf = $stafQuery->orderBy('nama_lengkap', 'ASC')->findAll();

        $stafIds = array_column($staf, 'id');
        $stafIds[] = $userId; // Tambahkan diri sendiri agar tidak muncul di dropdown
        
        // Ambil semua pegawai kecuali admin dan direktur dengan selective columns untuk efisiensi RAM
        $semuaPegawaiDb = $this->userModel
            ->select('id, nama_lengkap, nip, unit, unit_id, jabatan, role, atasan_id, foto')
            ->whereNotIn('role', ['admin', 'direktur'])
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
            
        $semua_pegawai = [];
        foreach ($semuaPegawaiDb as $p) {
            if (!in_array($p['id'], $stafIds)) {
                $semua_pegawai[] = $p;
            }
        }

        $unitKerjaList = $this->unitKerjaModel->orderBy('nama_unit', 'ASC')->findAll();

        $data = [
            'page_title'      => 'Kelola Tim Saya',
            'staf'            => $staf,
            'semua_pegawai'   => $semua_pegawai,
            'unit_kerja_list' => $unitKerjaList,
            'my_unit'         => $myUnit,
            'me'              => $me,
            'total_staf'      => count($staf)
        ];

        return view('user/tim_saya', $data);
    }

    public function addStaf()
    {
        $stafId = $this->request->getPost('staf_id');
        $myId = $this->getNormalizedCurrentUserId();
        $me = $this->userModel->find($myId);
        $myUnit = $me['unit'] ?? '';
        $myUnitId = $me['unit_id'] ?? null;

        // Lookup unit_id jika belum terisi di profil pimpinan
        if (empty($myUnitId) && !empty($myUnit)) {
            $uDb = $this->unitKerjaModel->where('nama_unit', $myUnit)->first();
            if ($uDb) {
                $myUnitId = $uDb['id'];
            }
        }

        if (empty($stafId) || (int)$stafId === (int)$myId) {
            return redirect()->back()->with('error', 'Silakan pilih pegawai yang valid.');
        }

        $targetUser = $this->userModel->find($stafId);
        if (!$targetUser || in_array($targetUser['role'], ['admin', 'direktur'])) {
            return redirect()->back()->with('error', 'Pengguna tersebut tidak dapat ditambahkan sebagai anggota tim.');
        }

        $this->userModel->update($stafId, [
            'atasan_id' => $myId,
            'unit'      => $myUnit,
            'unit_id'   => $myUnitId
        ]);
        log_audit('UPDATE', 'users', $stafId, null, ['action' => 'ADD_TO_TEAM', 'atasan_id' => $myId, 'unit' => $myUnit, 'unit_id' => $myUnitId]);
        return redirect()->back()->with('success', 'Pegawai berhasil ditambahkan ke tim Anda.');
    }

    public function removeStaf()
    {
        $stafId = $this->request->getPost('staf_id');
        $myId = $this->getNormalizedCurrentUserId();
        $me = $this->userModel->find($myId);
        $myUnit = $me['unit'] ?? null;

        // Pastikan yang dihapus benar-benar stafnya
        $staf = $this->userModel->find($stafId);
        $isStaf = ($staf && (($staf['atasan_id'] == $myId) || (!empty($myUnit) && $staf['unit'] === $myUnit)));
        
        if ($staf && $isStaf) {
            // Kosongkan atasan_id (set ke 0), unit (''), dan unit_id (null)
            $this->userModel->update($stafId, [
                'atasan_id' => 0, 
                'unit'      => '',
                'unit_id'   => null
            ]);
            log_audit('UPDATE', 'users', $stafId, null, ['action' => 'REMOVE_FROM_TEAM', 'old_atasan_id' => $myId]);
            return redirect()->back()->with('success', 'Pegawai berhasil dikeluarkan dari tim Anda.');
        }
        
        return redirect()->back()->with('error', 'Pegawai tidak ditemukan atau bukan staf Anda.');
    }

    public function updateUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $userId = $this->request->getPost('user_id');
        $unitInput = $this->request->getPost('unit_id') ?? $this->request->getPost('unit');
        $myId = $this->getNormalizedCurrentUserId();
        $me = $this->userModel->find($myId);
        $myUnit = $me['unit'] ?? null;

        // Validasi staf
        $staf = $this->userModel->find($userId);
        if (!$staf || ($staf['atasan_id'] != $myId && $staf['unit'] !== $myUnit)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bukan staf Anda.'])->setStatusCode(403);
        }

        $unitId = null;
        $unitNama = '';
        if (!empty($unitInput)) {
            if (is_numeric($unitInput)) {
                $unitDb = $this->unitKerjaModel->find($unitInput);
                if ($unitDb) {
                    $unitId = $unitDb['id'];
                    $unitNama = $unitDb['nama_unit'];
                }
            } else {
                $unitNama = trim((string)$unitInput);
                $unitDb = $this->unitKerjaModel->where('nama_unit', $unitNama)->first();
                if ($unitDb) {
                    $unitId = $unitDb['id'];
                }
            }
        }

        $updateData = [
            'unit'    => $unitNama,
            'unit_id' => $unitId
        ];

        if (strtolower(trim($unitNama ?? '')) === 'satuan penjaminan mutu') {
            $updateData['role'] = 'spm';
        }

        // Sinkronisasi otomatis atasan berdasarkan unit yang baru
        if (!empty($unitNama)) {
            $pimpinan = $this->userModel->where('unit', $unitNama)
                                        ->whereIn('role', ['manajemen', 'kabag_aak', 'kabag_kuk', 'kabag', 'kanit', 'katim', 'kapokja'])
                                        ->first();
            if ($pimpinan) {
                $updateData['atasan_id'] = $pimpinan['id'];
            } else {
                $updateData['atasan_id'] = 0;
            }
        } else {
            $updateData['atasan_id'] = 0;
        }

        if ($this->userModel->update($userId, $updateData)) {
            log_audit('UPDATE', 'users', $userId, null, ['action' => 'UPDATE_UNIT_TIM', 'unit' => $unitNama, 'unit_id' => $unitId]);
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Unit kerja berhasil diperbarui.',
                csrf_token() => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui unit kerja.', csrf_token() => csrf_hash()])->setStatusCode(500);
    }

    /**
     * Helper privat: Normalisasi ID Sesi Pengguna
     */
    private function getNormalizedCurrentUserId(): int
    {
        $currentUserId = session()->get('id') ?? session()->get('user_id');
        if (!is_numeric($currentUserId) || strlen((string)$currentUserId) > 10) {
            $userDb = $this->userModel->where('username', $currentUserId)
                                      ->orWhere('nip', $currentUserId)
                                      ->orWhere('id', $currentUserId)
                                      ->first();
            if ($userDb) {
                return (int)$userDb['id'];
            }
        }
        return (int)$currentUserId;
    }
}
