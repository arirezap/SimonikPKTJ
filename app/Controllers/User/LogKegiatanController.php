<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\LogKegiatanHarian;
use App\Models\LaporanHarian;

class LogKegiatanController extends BaseController
{
    public function index()
    {
        $logModel = new LogKegiatanHarian();
        $targetModel = new LaporanHarian();

        $userId = session()->get('id') ?? session()->get('user_id');
        
        // Gunakan session agar URL tetap bersih
        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            if ($this->request->getPost('tanggal')) session()->set('log_kegiatan_tanggal', $this->request->getPost('tanggal'));
        }

        $tanggalTerpilih = session()->get('log_kegiatan_tanggal') ?? date('Y-m-d');
        
        $bulanTerpilih = date('n', strtotime($tanggalTerpilih));
        $tahunTerpilih = date('Y', strtotime($tanggalTerpilih));

        // Ambil daftar target yang sudah dibuat user pada bulan tersebut
        $allTargets = $targetModel->where('user_id', $userId)
                                  ->where('bulan', $bulanTerpilih)
                                  ->where('tahun', $tahunTerpilih)
                                  ->findAll();

        $userModel = new \App\Models\User();
        $currentUser = $userModel->find($userId);
        $hasAtasan = $currentUser && !empty($currentUser['atasan_id']) && $currentUser['role'] !== 'direktur';

        $targetStatus = 'disetujui';
        if (empty($allTargets)) {
            $targetStatus = 'belum_ada';
        } else if ($hasAtasan) {
            foreach ($allTargets as $t) {
                if ($t['status_approval'] !== 'disetujui') {
                    $targetStatus = 'belum_disetujui';
                    break;
                }
            }
        }

        $daftarTarget = ($targetStatus === 'disetujui') ? $allTargets : [];

        // Ambil log kegiatan harian yang sudah diinput pada tanggal tersebut
        $rekapData = $logModel->getLogWithTarget($userId, $tanggalTerpilih);

        $isLocked = false;
        $isDirektur = ($currentUser && $currentUser['role'] === 'direktur');
        if ($targetStatus !== 'disetujui' && !$isDirektur) {
            $isLocked = true;
        } elseif (!empty($rekapData) && !$isDirektur) {
            foreach ($rekapData as $row) {
                if (isset($row['status']) && $row['status'] === 'terkirim') {
                    $isLocked = true;
                    break;
                }
            }
        }

        $logTambahanModel = new \App\Models\LogTugasTambahan();
        $rekapDataTambahan = $logTambahanModel->getLogByDate($userId, $tanggalTerpilih);

        $data = [
            'title' => 'Lapor Kegiatan Harian',
            'tanggal_terpilih' => $tanggalTerpilih,
            'daftar_target' => $daftarTarget,
            'rekap_data' => $rekapData,
            'rekap_data_tambahan' => $rekapDataTambahan,
            'is_locked' => $isLocked,
            'target_status' => $targetStatus
        ];

        return view('user/log_kegiatan/index', $data);
    }

    public function store()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        $tanggal = $this->request->getPost('tanggal');
        $bulanTerpilih = date('n', strtotime($tanggal));
        $tahunTerpilih = date('Y', strtotime($tanggal));

        // Cek persetujuan target bulanan
        $targetModel = new LaporanHarian();
        $allTargets = $targetModel->where('user_id', $userId)
                                  ->where('bulan', $bulanTerpilih)
                                  ->where('tahun', $tahunTerpilih)
                                  ->findAll();

        $userModel = new \App\Models\User();
        $currentUser = $userModel->find($userId);
        $hasAtasan = $currentUser && !empty($currentUser['atasan_id']) && $currentUser['role'] !== 'direktur';

        if (empty($allTargets)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan. Anda belum membuat Target Kinerja Bulanan untuk bulan ini.', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan. Anda belum membuat Target Kinerja Bulanan untuk bulan ini.');
        }

        if ($hasAtasan) {
            foreach ($allTargets as $t) {
                if ($t['status_approval'] !== 'disetujui') {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan. Target Kinerja Bulanan Anda untuk bulan ini belum disetujui oleh atasan langsung.', 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', 'Gagal menyimpan. Target Kinerja Bulanan Anda untuk bulan ini belum disetujui oleh atasan langsung.');
                }
            }
        }

        $isDraft = $this->request->isAJAX() || $this->request->getPost('action') === 'draft';
        $status = $isDraft ? 'draft' : 'terkirim';

        // Validation: Pastikan minimal ada 1 Tugas Pokok ATAU 1 Tugas Tambahan yang terisi
        $target_id_check = $this->request->getPost('target_id');
        $deskripsi_pokok_check = $this->request->getPost('deskripsi_kegiatan');
        $deskripsi_tambahan_check = $this->request->getPost('deskripsi_kegiatan_tambahan');

        $hasPokokInput = false;
        if (is_array($target_id_check)) {
            foreach ($target_id_check as $idx => $tId) {
                if (!empty($tId) && !empty($deskripsi_pokok_check[$idx])) {
                    $hasPokokInput = true;
                    break;
                }
            }
        }

        $hasTambahanInput = false;
        if (is_array($deskripsi_tambahan_check)) {
            foreach ($deskripsi_tambahan_check as $tmbDesc) {
                if (!empty(trim($tmbDesc))) {
                    $hasTambahanInput = true;
                    break;
                }
            }
        }

        $logModel = new LogKegiatanHarian();
        $tanggal = $this->request->getPost('tanggal');
        $existingData = $logModel->getLogWithTarget($userId, $tanggal);
        $existingTambahanData = (new \App\Models\LogTugasTambahan())->getLogByDate($userId, $tanggal);

        // Jika tidak ada input baru dan tidak ada data draf eksisting, tolak
        if (!$hasPokokInput && !$hasTambahanInput && empty($existingData) && empty($existingTambahanData)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan. Silakan isi minimal 1 Kegiatan Utama (Tugas Pokok) ATAU 1 Tugas Tambahan hari ini.',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan. Silakan isi minimal 1 Kegiatan Utama (Tugas Pokok) ATAU 1 Tugas Tambahan hari ini.');
        }

        // Pengecekan keamanan: Apakah TUGAS POKOK sudah dikunci?
        // Jika Tugas Pokok sudah terkirim → blokir seluruh form.
        // Jika hanya Tugas Tambahan yang sudah terkirim → izinkan penambahan Tugas Pokok, Tambahan yang sudah terkirim akan di-skip saat proses.
        $isDirektur = ($currentUser && $currentUser['role'] === 'direktur');
        if (!$isDirektur) {
            foreach ($existingData as $row) {
                if (isset($row['status']) && $row['status'] === 'terkirim') {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Laporan hari ini telah dikunci.', 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', 'Laporan hari ini telah dikunci dan tidak dapat diedit.');
                }
            }
        }

        // Buat lookup status Tambahan yang sudah terkirim (agar tidak di-overwrite)
        $lockedTambahanIds = [];
        foreach ($existingTambahanData as $row) {
            if (isset($row['status']) && $row['status'] === 'terkirim') {
                $lockedTambahanIds[] = $row['id'];
            }
        }

        // Untuk submit "Simpan & Kirim" jika tidak ada data baru (hanya mengupdate draf -> terkirim)
        if (!$isDraft && !$hasPokokInput && !$hasTambahanInput && (!empty($existingData) || !empty($existingTambahanData))) {
            $logModel->where('user_id', $userId)
                     ->where('tanggal_kegiatan', $tanggal)
                     ->set(['status' => 'terkirim'])
                     ->update();

            $logTambahanModel = new \App\Models\LogTugasTambahan();
            $logTambahanModel->where('user_id', $userId)
                             ->where('tanggal_kegiatan', $tanggal)
                             ->set(['status' => 'terkirim'])
                             ->update();
                     
            $user = (new \App\Models\User())->find($userId);
            if ($user && !empty($user['atasan_id'])) {
                helper('notification');
                send_notification(
                    $user['atasan_id'], 
                    'Laporan Harian Baru', 
                    $user['nama_lengkap'] . " mengirimkan Laporan Harian untuk tanggal $tanggal.",
                    site_url('penilaian-staf')
                );
            }
            
            return redirect()->to('/log-kegiatan')->with('success', 'Kegiatan harian berhasil dikirim.');
        }

        $tanggalTerpilihObj = new \DateTime($tanggal);
        $todayObj = new \DateTime(date('Y-m-d'));
        
        if ($tanggalTerpilihObj > $todayObj) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan. Tidak dapat melaporkan kegiatan untuk tanggal di masa depan.', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan. Anda tidak dapat melaporkan kegiatan untuk tanggal di masa depan.');
        }

        // Fitur batas pelaporan masa lalu dihapus atas permintaan user


        $log_ids = $this->request->getPost('log_id');
        $target_id_arr = $this->request->getPost('target_id');
        $deskripsi_kegiatan_arr = $this->request->getPost('deskripsi_kegiatan');
        $jumlah_capaian_arr = $this->request->getPost('jumlah_capaian');
        $link_bukti_arr = $this->request->getPost('link_bukti');

        $dataToUpdate = [];
        $dataToInsert = [];

        if ($target_id_arr) {
            foreach ($target_id_arr as $index => $targetId) {
                if (empty($targetId)) continue;

                $rowData = [
                    'user_id'            => $userId,
                    'target_id'          => $targetId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $deskripsi_kegiatan_arr[$index] ?? '',
                    'jumlah_capaian'     => $jumlah_capaian_arr[$index] ?? 0,
                    'link_bukti'         => !empty($link_bukti_arr[$index]) ? $link_bukti_arr[$index] : null,
                    'status'             => $status
                ];

                if (!empty($log_ids[$index])) {
                    $rowData['id'] = $log_ids[$index];
                    $dataToUpdate[] = $rowData;
                } else {
                    $dataToInsert[$index] = $rowData;
                }
            }
        }

        $insertedIds = [];
        if (!empty($dataToUpdate)) {
            $logModel->updateBatch($dataToUpdate, 'id');
        }
        if (!empty($dataToInsert)) {
            foreach ($dataToInsert as $origIndex => $insertRow) {
                $logModel->insert($insertRow);
                $insertedIds[$origIndex] = $logModel->getInsertID();
            }
        }

        // --- OLAP SIMULTAN UNTUK TUGAS TAMBAHAN ---
        $logTambahanModel = new \App\Models\LogTugasTambahan();
        $log_tambahan_ids = $this->request->getPost('log_tambahan_id');
        $deskripsi_kegiatan_tambahan_arr = $this->request->getPost('deskripsi_kegiatan_tambahan');
        $jumlah_capaian_tambahan_arr = $this->request->getPost('jumlah_capaian_tambahan');
        $satuan_tambahan_arr = $this->request->getPost('satuan_tambahan');
        $link_bukti_tambahan_arr = $this->request->getPost('link_bukti_tambahan');

        $dataTambahanToUpdate = [];
        $dataTambahanToInsert = [];

        if ($deskripsi_kegiatan_tambahan_arr) {
            foreach ($deskripsi_kegiatan_tambahan_arr as $index => $deskripsiTmb) {
                if (empty($deskripsiTmb)) continue;

                $existingTambahanId = !empty($log_tambahan_ids[$index]) ? $log_tambahan_ids[$index] : null;

                // Skip Tambahan yang sudah terkirim — tidak boleh diubah
                if ($existingTambahanId && in_array($existingTambahanId, $lockedTambahanIds)) {
                    continue;
                }

                $rowTmbData = [
                    'user_id'            => $userId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $deskripsiTmb,
                    'jumlah_capaian'     => !empty($jumlah_capaian_tambahan_arr[$index]) ? (float)$jumlah_capaian_tambahan_arr[$index] : 1.00,
                    'satuan'             => !empty($satuan_tambahan_arr[$index]) ? $satuan_tambahan_arr[$index] : 'Kegiatan',
                    'link_bukti'         => !empty($link_bukti_tambahan_arr[$index]) ? $link_bukti_tambahan_arr[$index] : null,
                    'status'             => $status,
                    'status_approval'    => 'menunggu_persetujuan'
                ];

                if ($existingTambahanId) {
                    $rowTmbData['id'] = $existingTambahanId;
                    $dataTambahanToUpdate[] = $rowTmbData;
                } else {
                    $dataTambahanToInsert[$index] = $rowTmbData;
                }
            }
        }

        $insertedTambahanIds = [];
        if (!empty($dataTambahanToUpdate)) {
            $logTambahanModel->updateBatch($dataTambahanToUpdate, 'id');
        }
        if (!empty($dataTambahanToInsert)) {
            foreach ($dataTambahanToInsert as $origIndex => $insertRow) {
                $logTambahanModel->insert($insertRow);
                $insertedTambahanIds[$origIndex] = $logTambahanModel->getInsertID();
            }
        }

        // Jika submit normal (Simpan & Kirim), ubah status semua log tanggal ini menjadi terkirim
        if (!$this->request->isAJAX()) {
            $logModel->where('user_id', $userId)
                     ->where('tanggal_kegiatan', $tanggal)
                     ->set(['status' => 'terkirim'])
                     ->update();

            $logTambahanModel->where('user_id', $userId)
                             ->where('tanggal_kegiatan', $tanggal)
                             ->set(['status' => 'terkirim'])
                             ->update();
                     
            $user = (new \App\Models\User())->find($userId);
            if ($user && !empty($user['atasan_id'])) {
                helper('notification');
                send_notification(
                    $user['atasan_id'], 
                    'Laporan Harian Baru', 
                    $user['nama_lengkap'] . " mengirimkan Laporan Harian untuk tanggal $tanggal.",
                    site_url('penilaian-staf')
                );
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Laporan harian & tugas tambahan berhasil disimpan sementara.',
                'new_ids' => $insertedIds ?? [],
                'new_tambahan_ids' => $insertedTambahanIds ?? [],
                'csrf_hash' => csrf_hash()
            ]);
        }

        return redirect()->to('/log-kegiatan')
                         ->with('success', 'Laporan harian dan tugas tambahan berhasil dikirim.');
    }
    
    public function hapus()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $logModel = new LogKegiatanHarian();
            $userId = session()->get('id') ?? session()->get('user_id');
            $row = $logModel->find($id);
            $currentUser = (new \App\Models\User())->find($userId);
            $isDirektur = ($currentUser && $currentUser['role'] === 'direktur');
            if ($row && $row['user_id'] == $userId) {
                if (!$isDirektur && isset($row['status']) && $row['status'] === 'terkirim') {
                    return $this->response->setJSON(['success' => false, 'message' => 'Laporan yang telah terkirim/dikunci tidak dapat dihapus.', 'csrf_hash' => csrf_hash()]);
                }
                $logModel->delete($id);
                return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
            }
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus data.', 'csrf_hash' => csrf_hash()]);
    }

    public function storeTugasTambahan()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        
        $rules = [
            'tanggal'              => 'required',
            'deskripsi_kegiatan_tambahan.*' => 'required',
            'link_bukti_tambahan.*'         => 'required|valid_url',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan. Pastikan semua form tugas tambahan terisi dengan benar (URL Bukti Pekerjaan harus valid).',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan. Pastikan semua form tugas tambahan terisi dengan benar (URL Bukti Pekerjaan harus valid).');
        }

        $logTambahanModel = new \App\Models\LogTugasTambahan();
        $tanggal = $this->request->getPost('tanggal');
        $status = $this->request->isAJAX() ? 'draft' : 'terkirim';

        $log_ids = $this->request->getPost('log_tambahan_id');
        $deskripsi_kegiatan_arr = $this->request->getPost('deskripsi_kegiatan_tambahan');
        $link_bukti_arr = $this->request->getPost('link_bukti_tambahan');

        $dataToUpdate = [];
        $dataToInsert = [];

        if ($deskripsi_kegiatan_arr) {
            foreach ($deskripsi_kegiatan_arr as $index => $deskripsi) {
                if (empty($deskripsi)) continue;

                $rowData = [
                    'user_id'            => $userId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $deskripsi,
                    'link_bukti'         => $link_bukti_arr[$index] ?? '',
                    'status'             => $status,
                    'status_approval'    => 'menunggu_persetujuan'
                ];

                if (!empty($log_ids[$index])) {
                    $rowData['id'] = $log_ids[$index];
                    $dataToUpdate[] = $rowData;
                } else {
                    $dataToInsert[$index] = $rowData;
                }
            }
        }

        $insertedIds = [];
        if (!empty($dataToUpdate)) {
            $logTambahanModel->updateBatch($dataToUpdate, 'id');
        }
        if (!empty($dataToInsert)) {
            foreach ($dataToInsert as $origIndex => $insertRow) {
                $logTambahanModel->insert($insertRow);
                $insertedIds[$origIndex] = $logTambahanModel->getInsertID();
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data tugas tambahan berhasil disimpan sementara.',
                'new_ids' => $insertedIds,
                'csrf_hash' => csrf_hash()
            ]);
        }

        return redirect()->to('/log-kegiatan')->with('success', 'Tugas Tambahan berhasil disimpan dan dikirim.');
    }

    public function hapusTugasTambahan()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $logTambahanModel = new \App\Models\LogTugasTambahan();
            $userId = session()->get('id') ?? session()->get('user_id');
            $row = $logTambahanModel->find($id);
            // Boleh hapus asalkan belum disetujui atasan (jika mau) atau statusnya draft
            if ($row && $row['user_id'] == $userId) {
                $logTambahanModel->delete($id);
                return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
            }
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus.', 'csrf_hash' => csrf_hash()]);
    }
}
