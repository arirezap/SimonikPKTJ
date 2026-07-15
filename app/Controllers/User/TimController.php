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
        $userId = session()->get('id');
        $me = $this->userModel->find($userId);
        $myUnit = $me['unit'] ?? null;
        
        // Ambil bawahan: yang secara eksplisit atasan_id-nya = $userId ATAU unit-nya sama dengan unit manajer
        $bawahanQuery = $this->userModel->where('id !=', $userId);
        
        if (!empty($myUnit)) {
            $bawahanQuery->groupStart()
                         ->where('atasan_id', $userId)
                         ->orWhere('unit', $myUnit)
                         ->groupEnd();
        } else {
            $bawahanQuery->where('atasan_id', $userId);
        }
        $bawahan = $bawahanQuery->orderBy('nama_lengkap', 'ASC')->findAll();

        $bawahanIds = array_column($bawahan, 'id');
        $bawahanIds[] = $userId; // Tambahkan diri sendiri agar tidak muncul di dropdown

        $userModel2 = new \App\Models\User();
        
        // Ambil semua pegawai kecuali admin dan direktur
        $semuaPegawaiDb = $userModel2
            ->whereNotIn('role', ['admin', 'direktur'])
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
            
        $semua_pegawai = [];
        foreach ($semuaPegawaiDb as $p) {
            if (!in_array($p['id'], $bawahanIds)) {
                $semua_pegawai[] = $p;
            }
        }

        $data = [
            'page_title' => 'Kelola Tim Saya',
            'bawahan' => $bawahan,
            'semua_pegawai' => $semua_pegawai,
            'unit_kerja_list' => $this->unitKerjaModel->orderBy('nama_unit', 'ASC')->findAll(),
            'my_unit' => $myUnit // Untuk indikasi di view
        ];

        return view('user/tim_saya', $data);
    }

    public function addBawahan()
    {
        $bawahanId = $this->request->getPost('bawahan_id');
        $myId = session()->get('id');
        $me = $this->userModel->find($myId);
        $myUnit = $me['unit'] ?? '';

        if (empty($bawahanId)) {
            return redirect()->back()->with('error', 'Silakan pilih pegawai.');
        }

        $this->userModel->update($bawahanId, [
            'atasan_id' => $myId,
            'unit' => $myUnit
        ]);
        return redirect()->back()->with('success', 'Pegawai berhasil ditambahkan ke tim Anda.');
    }

    public function removeBawahan()
    {
        $bawahanId = $this->request->getPost('bawahan_id');
        $myId = session()->get('id');
        $me = $this->userModel->find($myId);
        $myUnit = $me['unit'] ?? null;

        // Pastikan yang dihapus benar-benar bawahannya
        $bawahan = $this->userModel->find($bawahanId);
        $isBawahan = ($bawahan['atasan_id'] == $myId) || (!empty($myUnit) && $bawahan['unit'] === $myUnit);
        
        if ($bawahan && $isBawahan) {
            // Kita kosongkan atasan_id (set ke 0) dan unitnya (string kosong) jika dia dihapus dari tim
            $this->userModel->update($bawahanId, [
                'atasan_id' => 0, 
                'unit' => ''
            ]);
            return redirect()->back()->with('success', 'Pegawai berhasil dihapus dari tim Anda.');
        }
        
        return redirect()->back()->with('error', 'Pegawai tidak ditemukan atau bukan bawahan Anda.');
    }

    public function updateUnit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $userId = $this->request->getPost('user_id');
        $unit = $this->request->getPost('unit');
        $myId = session()->get('id');
        $me = $this->userModel->find($myId);
        $myUnit = $me['unit'] ?? null;

        // Validasi bawahan
        $bawahan = $this->userModel->find($userId);
        if (!$bawahan || ($bawahan['atasan_id'] != $myId && $bawahan['unit'] !== $myUnit)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Bukan bawahan Anda.'])->setStatusCode(403);
        }

        $updateData = ['unit' => $unit];
        if (strtolower(trim($unit ?? '')) === 'satuan penjaminan mutu') {
            $updateData['role'] = 'spm';
        }

        // Sinkronisasi otomatis atasan berdasarkan unit yang baru
        if (!empty($unit)) {
            $pimpinan = $this->userModel->where('unit', $unit)
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
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Unit kerja berhasil diperbarui.',
                csrf_token() => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui unit kerja.', csrf_token() => csrf_hash()])->setStatusCode(500);
    }
}
