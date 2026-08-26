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

        $userId = session()->get('id');
        $me = $this->userModel->find($userId);
        $myUnit = $me['unit'] ?? null;
        
        // Ambil staf: yang secara eksplisit atasan_id-nya = $userId ATAU unit-nya sama dengan unit manajer
        $stafQuery = $this->userModel->where('id !=', $userId);
        
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

        $userModel2 = new \App\Models\User();
        
        // Ambil semua pegawai kecuali admin dan direktur
        $semuaPegawaiDb = $userModel2
            ->whereNotIn('role', ['admin', 'direktur'])
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
            
        $semua_pegawai = [];
        foreach ($semuaPegawaiDb as $p) {
            if (!in_array($p['id'], $stafIds)) {
                $semua_pegawai[] = $p;
            }
        }

        $data = [
            'page_title' => 'Kelola Tim Saya',
            'staf' => $staf,
            'semua_pegawai' => $semua_pegawai,
            'unit_kerja_list' => $this->unitKerjaModel->orderBy('nama_unit', 'ASC')->findAll(),
            'my_unit' => $myUnit // Untuk indikasi di view
        ];

        return view('user/tim_saya', $data);
    }

    public function addStaf()
    {
        $stafId = $this->request->getPost('staf_id');
        $myId = session()->get('id');
        $me = $this->userModel->find($myId);
        $myUnit = $me['unit'] ?? '';

        if (empty($stafId)) {
            return redirect()->back()->with('error', 'Silakan pilih pegawai.');
        }

        $this->userModel->update($stafId, [
            'atasan_id' => $myId,
            'unit' => $myUnit
        ]);
        log_audit('UPDATE', 'users', $stafId, null, ['action' => 'ADD_TO_TEAM', 'atasan_id' => $myId, 'unit' => $myUnit]);
        return redirect()->back()->with('success', 'Pegawai berhasil ditambahkan ke tim Anda.');
    }

    public function removeStaf()
    {
        $stafId = $this->request->getPost('staf_id');
        $myId = session()->get('id');
        $me = $this->userModel->find($myId);
        $myUnit = $me['unit'] ?? null;

        // Pastikan yang dihapus benar-benar stafnya
        $staf = $this->userModel->find($stafId);
        $isStaf = ($staf['atasan_id'] == $myId) || (!empty($myUnit) && $staf['unit'] === $myUnit);
        
        if ($staf && $isStaf) {
            // Kita kosongkan atasan_id (set ke 0) dan unitnya (string kosong) jika dia dihapus dari tim
            $this->userModel->update($stafId, [
                'atasan_id' => 0, 
                'unit' => ''
            ]);
            log_audit('UPDATE', 'users', $stafId, null, ['action' => 'REMOVE_FROM_TEAM', 'old_atasan_id' => $myId]);
            return redirect()->back()->with('success', 'Pegawai berhasil dihapus dari tim Anda.');
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
        $myId = session()->get('id');
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
            $unitKerjaModel = new \App\Models\UnitKerja();
            if (is_numeric($unitInput)) {
                $unitDb = $unitKerjaModel->find($unitInput);
                if ($unitDb) {
                    $unitId = $unitDb['id'];
                    $unitNama = $unitDb['nama_unit'];
                }
            } else {
                $unitNama = trim((string)$unitInput);
                $unitDb = $unitKerjaModel->where('nama_unit', $unitNama)->first();
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
                                        ->whereIn('role', ['manajemen', 'kabag_aak', 'kabag_kuk', 'kabag'])
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
}
