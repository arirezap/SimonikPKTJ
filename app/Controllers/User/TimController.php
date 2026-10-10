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

    /**
     * Otorisasi Hak Akses Pimpinan Tim (Katim, Kabag, Kanit, Kapokja, Manajemen, Admin)
     */
    private function isAuthorizedTimLeader(): bool
    {
        helper('role');
        return hasAnyRole(['manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kanit', 'katim', 'kapokja', 'admin']);
    }

    public function index()
    {
        helper(['avatar', 'role', 'audit']);
        
        if (!$this->isAuthorizedTimLeader()) {
            return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki hak akses ke modul Kelola Tim.');
        }

        $userId = $this->getNormalizedCurrentUserId();
        $me = $this->userModel->find($userId);
        $myUnit = $me['unit'] ?? null;
        
        // Ambil staf aktif: yang atasan_id = $userId ATAU (unit sama dan belum memiliki atasan lain)
        $stafQuery = $this->userModel->select('id, nama_lengkap, nip, unit, unit_id, jabatan, role, atasan_id, foto')
                                     ->where('id !=', $userId)
                                     ->where('status_pegawai', 'aktif');
        
        if (!empty($myUnit)) {
            $stafQuery->groupStart()
                         ->where('atasan_id', $userId)
                         ->orGroupStart()
                             ->where('unit', $myUnit)
                             ->groupStart()
                                 ->where('atasan_id', null)
                                 ->orWhere('atasan_id', 0)
                             ->groupEnd()
                         ->groupEnd()
                      ->groupEnd();
        } else {
            $stafQuery->where('atasan_id', $userId);
        }
        $staf = $stafQuery->orderBy('nama_lengkap', 'ASC')->findAll();

        $stafIds = array_column($staf, 'id');
        $stafIds[] = $userId; // Tambahkan diri sendiri agar tidak muncul di dropdown
        
        // Ambil pegawai aktif kecuali admin dan direktur dengan kueri selektif
        $semuaPegawaiQuery = $this->userModel
            ->select('id, nama_lengkap, nip, unit, unit_id, jabatan, role, atasan_id, foto')
            ->whereNotIn('role', ['admin', 'direktur'])
            ->where('status_pegawai', 'aktif');

        if (!empty($stafIds)) {
            $semuaPegawaiQuery->whereNotIn('id', $stafIds);
        }

        $semua_pegawai = $semuaPegawaiQuery->orderBy('nama_lengkap', 'ASC')->findAll();

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
        helper(['role', 'audit']);
        if (!$this->isAuthorizedTimLeader()) {
            return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki hak akses untuk menambah anggota tim.');
        }

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
        if (!$targetUser || in_array($targetUser['role'], ['admin', 'direktur']) || ($targetUser['status_pegawai'] ?? 'aktif') !== 'aktif') {
            return redirect()->back()->with('error', 'Pengguna tersebut tidak dapat ditambahkan sebagai anggota tim.');
        }

        // Proteksi Siklus Hierarki (Cycle Prevention): Calon staf tidak boleh merupakan atasan pimpinan ini
        $currentCheckId = $me['atasan_id'] ?? null;
        $visited = [(int)$myId];
        while (!empty($currentCheckId) && !in_array((int)$currentCheckId, $visited, true)) {
            if ((int)$currentCheckId === (int)$stafId) {
                return redirect()->back()->with('error', 'Tidak dapat menambahkan atasan Anda sebagai staf tim.');
            }
            $visited[] = (int)$currentCheckId;
            $parent = $this->userModel->select('id, atasan_id')->find($currentCheckId);
            $currentCheckId = $parent['atasan_id'] ?? null;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $oldData = [
            'atasan_id' => $targetUser['atasan_id'],
            'unit'      => $targetUser['unit'],
            'unit_id'   => $targetUser['unit_id']
        ];
        $newData = [
            'atasan_id' => $myId,
            'unit'      => $myUnit,
            'unit_id'   => $myUnitId
        ];

        $this->userModel->update($stafId, $newData);
        log_audit('UPDATE', 'users', $stafId, $oldData, ['action' => 'ADD_TO_TEAM', 'old' => $oldData, 'new' => $newData]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menambahkan anggota tim. Silakan coba lagi.');
        }

        return redirect()->back()->with('success', 'Pegawai berhasil ditambahkan ke tim Anda.');
    }

    public function removeStaf()
    {
        helper(['role', 'audit']);
        if (!$this->isAuthorizedTimLeader()) {
            return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki hak akses untuk mengelola anggota tim.');
        }

        $stafId = $this->request->getPost('staf_id');
        $myId = $this->getNormalizedCurrentUserId();
        $me = $this->userModel->find($myId);
        $myUnit = $me['unit'] ?? null;

        $staf = $this->userModel->find($stafId);
        $isStaf = ($staf && (((int)$staf['atasan_id'] === (int)$myId) || (!empty($myUnit) && $staf['unit'] === $myUnit && (empty($staf['atasan_id']) || (int)$staf['atasan_id'] === 0))));
        
        if (!$staf || !$isStaf) {
            return redirect()->back()->with('error', 'Pegawai tidak ditemukan atau bukan staf Anda.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $oldData = [
            'atasan_id' => $staf['atasan_id'],
            'unit'      => $staf['unit'],
            'unit_id'   => $staf['unit_id']
        ];
        $newData = [
            'atasan_id' => null, 
            'unit'      => '',
            'unit_id'   => null
        ];

        $this->userModel->update($stafId, $newData);
        log_audit('UPDATE', 'users', $stafId, $oldData, ['action' => 'REMOVE_FROM_TEAM', 'old' => $oldData, 'new' => $newData]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal mengeluarkan staf dari tim. Silakan coba lagi.');
        }

        return redirect()->back()->with('success', 'Pegawai berhasil dikeluarkan dari tim Anda.');
    }

    public function updateUnit()
    {
        helper(['role', 'audit']);
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        if (!$this->isAuthorizedTimLeader()) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Akses ditolak.',
                csrf_token() => csrf_hash()
            ])->setStatusCode(403);
        }

        $userId = $this->request->getPost('user_id');
        $unitInput = $this->request->getPost('unit_id') ?? $this->request->getPost('unit');
        $myId = $this->getNormalizedCurrentUserId();
        $me = $this->userModel->find($myId);
        $myUnit = $me['unit'] ?? null;

        // Validasi staf
        $staf = $this->userModel->find($userId);
        $isStaf = ($staf && (((int)$staf['atasan_id'] === (int)$myId) || (!empty($myUnit) && $staf['unit'] === $myUnit)));
        if (!$staf || !$isStaf) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Bukan staf Anda.',
                csrf_token() => csrf_hash()
            ])->setStatusCode(403);
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

        // Sinkronisasi atasan berdasarkan unit yang baru
        if (!empty($unitNama)) {
            if ($unitNama === $myUnit) {
                // Tetap berada dalam unit pimpinan saat ini
                $updateData['atasan_id'] = $myId;
            } else {
                // Dipindahkan ke unit lain: cari pimpinan aktif di unit tersebut (bukan staf itu sendiri)
                $pimpinan = $this->userModel->where('unit', $unitNama)
                                            ->where('id !=', $userId)
                                            ->where('status_pegawai', 'aktif')
                                            ->whereIn('role', ['manajemen', 'kabag_aak', 'kabag_kuk', 'kabag', 'kanit', 'katim', 'kapokja'])
                                            ->first();
                $updateData['atasan_id'] = $pimpinan ? (int)$pimpinan['id'] : null;
            }
        } else {
            $updateData['atasan_id'] = null;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $oldData = [
            'atasan_id' => $staf['atasan_id'],
            'unit'      => $staf['unit'],
            'unit_id'   => $staf['unit_id']
        ];

        $this->userModel->update($userId, $updateData);
        log_audit('UPDATE', 'users', $userId, $oldData, ['action' => 'UPDATE_UNIT_TIM', 'old' => $oldData, 'new' => $updateData]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Gagal memperbarui unit kerja.',
                csrf_token() => csrf_hash()
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success'    => true, 
            'message'    => 'Unit kerja berhasil diperbarui.',
            csrf_token() => csrf_hash()
        ]);
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

