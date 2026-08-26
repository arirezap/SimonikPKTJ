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
        
        // Ambil parameter tanggal dari GET atau POST, sinkronkan ke session
        $reqTanggal = $this->request->getVar('tanggal');
        if (!empty($reqTanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $reqTanggal)) {
            session()->set('log_kegiatan_tanggal', $reqTanggal);
            $tanggalTerpilih = $reqTanggal;
        } else {
            $tanggalTerpilih = session()->get('log_kegiatan_tanggal') ?? date('Y-m-d');
        }
        
        $bulanTerpilih = (int) date('n', strtotime($tanggalTerpilih));
        $tahunTerpilih = (int) date('Y', strtotime($tanggalTerpilih));

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

        // Pastikan dropdown target selalu memiliki daftar target valid
        $daftarTarget = [];
        foreach ($allTargets as $t) {
            if (!$hasAtasan || $t['status_approval'] === 'disetujui' || $targetStatus === 'disetujui') {
                $daftarTarget[] = $t;
            }
        }
        if (empty($daftarTarget) && !empty($allTargets)) {
            $daftarTarget = $allTargets;
        }

        // Ambil log kegiatan harian & tugas tambahan yang sudah diinput pada tanggal tersebut
        $rekapData = $logModel->getLogWithTarget($userId, $tanggalTerpilih);
        $logTambahanModel = new \App\Models\LogTugasTambahan();
        $rekapDataTambahan = $logTambahanModel->getLogByDate($userId, $tanggalTerpilih);

        $isLocked = false;
        $isDirektur = ($currentUser && $currentUser['role'] === 'direktur');
        $today = date('Y-m-d');

        // Aturan Khusus: Tanggal di masa depan DILARANG KERAS
        if ($tanggalTerpilih > $today) {
            $isLocked = true;
        } elseif ($targetStatus !== 'disetujui' && !$isDirektur) {
            $isLocked = true;
        } elseif (!$isDirektur) {
            // Cek pembatasan deadline masa lalu jika saklar log aktif
            $settingModel = new \App\Models\SettingModel();
            $isDeadlineActive = $settingModel->getValue('enable_log_deadline', '0') === '1';
            if ($isDeadlineActive) {
                $batasLogDays = (int) $settingModel->getValue('batas_input_log', 3);
                $diffDays = (int) floor((strtotime($today) - strtotime($tanggalTerpilih)) / 86400);
                if ($diffDays > $batasLogDays) {
                    $isLocked = true;
                }
            }

            if (!$isLocked && !empty($rekapData)) {
                foreach ($rekapData as $row) {
                    if (isset($row['status']) && $row['status'] === 'terkirim') {
                        $isLocked = true;
                        break;
                    }
                }
            }
            if (!$isLocked && !empty($rekapDataTambahan)) {
                foreach ($rekapDataTambahan as $rowTmb) {
                    if (isset($rowTmb['status']) && $rowTmb['status'] === 'terkirim') {
                        $isLocked = true;
                        break;
                    }
                }
            }
        }

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
        $today = date('Y-m-d');

        // 1. Aturan Mutlak: Tanggal kegiatan tidak boleh melebihi hari ini (masa depan)
        if ($tanggal > $today) {
            $msg = 'Gagal menyimpan: Tanggal kegiatan tidak boleh melebihi hari ini (tidak dapat mengisi laporan kegiatan di masa depan).';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', $msg);
        }

        // 2. Cek Batas Toleransi Hari jika Saklar Batas Waktu Log Aktif
        $settingModel = new \App\Models\SettingModel();
        $isDeadlineActive = $settingModel->getValue('enable_log_deadline', '0') === '1';
        if ($isDeadlineActive && !hasAnyRole(['admin', 'direktur'])) {
            $batasLogDays = (int) $settingModel->getValue('batas_input_log', 3);
            $diffDays = (int) floor((strtotime($today) - strtotime($tanggal)) / 86400);
            if ($diffDays > $batasLogDays) {
                $msg = "Gagal menyimpan: Batas waktu pengisian laporan kegiatan harian adalah maksimal {$batasLogDays} hari setelah tanggal kegiatan.";
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->with('error', $msg);
            }
        }

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
                $capaianCheck = trim((string)(($this->request->getPost('jumlah_capaian') ?? [])[$idx] ?? ''));
                if (!empty($tId) && (!empty($deskripsi_pokok_check[$idx]) || $capaianCheck !== '')) {
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

        // Pengecekan keamanan: Apakah laporan hari ini telah dikunci?
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
            foreach ($existingTambahanData as $rowTmb) {
                if (isset($rowTmb['status']) && $rowTmb['status'] === 'terkirim') {
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
        $allPokokIds = []; // Semua ID tugas pokok (existing + baru) untuk sinkronisasi UI

        if ($target_id_arr) {
            foreach ($target_id_arr as $index => $targetId) {
                if (empty($targetId)) continue;

                $capaianStr = trim((string)($jumlah_capaian_arr[$index] ?? ''));
                $capaianValNum = str_replace(',', '.', $capaianStr);
                if ($capaianStr === '' || !is_numeric($capaianValNum)) {
                    if (!$isDraft) {
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan. Kolom Jumlah Capaian pada Tugas Pokok harus diisi angka yang valid.', 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', 'Gagal menyimpan. Kolom Jumlah Capaian pada Tugas Pokok harus diisi angka yang valid.');
                    }
                    $capaianValNum = 0.00;
                }

                $linkBukti = !empty($link_bukti_arr[$index]) ? trim((string)$link_bukti_arr[$index]) : null;
                if ($linkBukti === 'https://...' || $linkBukti === '') {
                    $linkBukti = null;
                }

                $rowData = [
                    'user_id'            => $userId,
                    'target_id'          => $targetId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $deskripsi_kegiatan_arr[$index] ?? '',
                    'jumlah_capaian'     => (float)$capaianValNum,
                    'link_bukti'         => $linkBukti,
                    'status'             => $status
                ];

                if (!empty($log_ids[$index])) {
                    $rowData['id'] = $log_ids[$index];
                    $dataToUpdate[] = $rowData;
                    $allPokokIds[$index] = $log_ids[$index];
                } else {
                    $dataToInsert[$index] = $rowData;
                }
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
        $allTambahanIds = []; // Semua ID tugas tambahan (existing + baru) untuk sinkronisasi UI

        if ($deskripsi_kegiatan_tambahan_arr) {
            foreach ($deskripsi_kegiatan_tambahan_arr as $index => $deskripsiTmb) {
                if (empty($deskripsiTmb)) continue;

                $existingTambahanId = !empty($log_tambahan_ids[$index]) ? $log_tambahan_ids[$index] : null;

                // Skip Tambahan yang sudah terkirim — tidak boleh diubah
                if ($existingTambahanId && in_array($existingTambahanId, $lockedTambahanIds)) {
                    continue;
                }
                
                $capaianStrTmb = trim((string)($jumlah_capaian_tambahan_arr[$index] ?? ''));
                $capaianValNumTmb = str_replace(',', '.', $capaianStrTmb);
                if ($capaianStrTmb === '' || !is_numeric($capaianValNumTmb)) {
                    if (!$isDraft) {
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan. Kolom Jumlah Capaian pada Tugas Tambahan harus diisi angka yang valid.', 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', 'Gagal menyimpan. Kolom Jumlah Capaian pada Tugas Tambahan harus diisi angka yang valid.');
                    }
                    $capaianValNumTmb = 0.00;
                }

                $satuanTmb = !empty($satuan_tambahan_arr[$index]) ? trim((string)$satuan_tambahan_arr[$index]) : 'Kegiatan';
                if ($satuanTmb === 'Satuan' || $satuanTmb === '') {
                    $satuanTmb = 'Kegiatan';
                }

                $linkBuktiTmb = !empty($link_bukti_tambahan_arr[$index]) ? trim((string)$link_bukti_tambahan_arr[$index]) : null;
                if ($linkBuktiTmb === 'https://...' || $linkBuktiTmb === '') {
                    $linkBuktiTmb = null;
                }

                $rowTmbData = [
                    'user_id'            => $userId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $deskripsiTmb,
                    'jumlah_capaian'     => (float)$capaianValNumTmb,
                    'satuan'             => $satuanTmb,
                    'link_bukti'         => $linkBuktiTmb,
                    'status'             => $status,
                    'status_approval'    => 'menunggu_persetujuan'
                ];

                if ($existingTambahanId) {
                    $rowTmbData['id'] = $existingTambahanId;
                    $dataTambahanToUpdate[] = $rowTmbData;
                    $allTambahanIds[$index] = $existingTambahanId; // Catat ID existing
                } else {
                    $dataTambahanToInsert[$index] = $rowTmbData;
                }
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if (!empty($dataToUpdate)) {
                $logModel->updateBatch($dataToUpdate, 'id');
            }
            if (!empty($dataToInsert)) {
                foreach ($dataToInsert as $origIndex => $insertRow) {
                    $logModel->insert($insertRow);
                    $newId = $logModel->getInsertID();
                    $allPokokIds[$origIndex] = $newId;
                }
            }

            if (!empty($dataTambahanToUpdate)) {
                $logTambahanModel->updateBatch($dataTambahanToUpdate, 'id');
            }
            if (!empty($dataTambahanToInsert)) {
                foreach ($dataTambahanToInsert as $origIndex => $insertRow) {
                    $logTambahanModel->insert($insertRow);
                    $newId = $logTambahanModel->getInsertID();
                    $allTambahanIds[$origIndex] = $newId; // Catat ID baru
                }
            }

            $db->transComplete();
        } catch (\Throwable $e) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan ke database. Coba lagi atau hubungi admin.', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan data ke database.');
        }

        if ($db->transStatus() === false) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal terhubung ke database. Coba lagi.', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan data ke database.');
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
                'new_ids' => $allPokokIds ?? [],
                'new_tambahan_ids' => $allTambahanIds ?? [],
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

        $jumlah_capaian_tambahan_arr = $this->request->getPost('jumlah_capaian_tambahan');

        $dataToUpdate = [];
        $dataToInsert = [];

        if ($deskripsi_kegiatan_arr) {
            foreach ($deskripsi_kegiatan_arr as $index => $deskripsi) {
                if (empty($deskripsi)) continue;

                $capaianStrTmb = trim((string)($jumlah_capaian_tambahan_arr[$index] ?? ''));
                if ($capaianStrTmb === '') {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan. Kolom Jumlah Capaian pada Tugas Tambahan tidak boleh kosong. Harap isi angka (minimal 0).', 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', 'Gagal menyimpan. Kolom Jumlah Capaian tidak boleh kosong. Harap isi angka (minimal 0).');
                }

                $capaianValNumTmb = str_replace(',', '.', $capaianStrTmb);
                if (!is_numeric($capaianValNumTmb)) {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan. Format Jumlah Capaian pada Tugas Tambahan tidak valid.', 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', 'Gagal menyimpan. Format Jumlah Capaian tidak valid.');
                }

                $rowData = [
                    'user_id'            => $userId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $deskripsi,
                    'jumlah_capaian'     => (float)$capaianValNumTmb,
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
        try {
            if (!empty($dataToUpdate)) {
                $logTambahanModel->updateBatch($dataToUpdate, 'id');
            }
            if (!empty($dataToInsert)) {
                foreach ($dataToInsert as $origIndex => $insertRow) {
                    $logTambahanModel->insert($insertRow);
                    $insertedIds[$origIndex] = $logTambahanModel->getInsertID();
                }
            }
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal terhubung ke database. Coba lagi atau hubungi admin.', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan data ke database.');
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

    /**
     * [SUPERADMIN ONLY] Membuka kunci laporan harian staf yang sudah berstatus 'terkirim'
     * agar staf dapat merevisi laporannya. Setelah staf menyimpan ulang dengan
     * "Simpan & Kirim", laporan akan terkunci kembali otomatis ke status 'terkirim'.
     */
    public function bukaKunci()
    {
        $targetUserId = $this->request->getPost('target_user_id');
        $tanggal      = $this->request->getPost('tanggal');

        if (!$targetUserId || !$tanggal) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $currentUserId = session()->get('id') ?? session()->get('user_id');
        $logModel        = new LogKegiatanHarian();
        $logTambahanModel = new \App\Models\LogTugasTambahan();
        $userModel       = new \App\Models\User();
        // Verifikasi staf yang akan dibuka kuncinya
        $targetUser = $userModel->find($targetUserId);
        if (!$targetUser) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Staf tidak ditemukan.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        // Otorisasi: Superadmin BISA untuk semua pengguna. Atasan Langsung BISA untuk staf di bawahnya.
        $isAtasanLangsung = !empty($targetUser['atasan_id']) && ($targetUser['atasan_id'] == $currentUserId);
        if (!hasRole('admin') && !$isAtasanLangsung) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Fitur izin revisi laporan ini hanya dapat dilakukan oleh Superadmin atau Atasan Langsung dari staf bersangkutan.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        // Ambil data lama untuk pengecekan & audit trail (baik tugas pokok maupun tugas tambahan)
        $existingData     = $logModel->where('user_id', $targetUserId)
                                     ->where('tanggal_kegiatan', $tanggal)
                                     ->findAll();
        $existingTambahan = $logTambahanModel->where('user_id', $targetUserId)
                                             ->where('tanggal_kegiatan', $tanggal)
                                             ->findAll();

        if (empty($existingData) && empty($existingTambahan)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada laporan (tugas pokok maupun tugas tambahan) untuk tanggal dan staf tersebut.',
                'csrf_hash' => csrf_hash()
            ]);
        }


        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Ubah status log_kegiatan_harian kembali menjadi 'draft'
            $logModel->where('user_id', $targetUserId)
                     ->where('tanggal_kegiatan', $tanggal)
                     ->set(['status' => 'draft', 'status_approval' => 'menunggu_persetujuan'])
                     ->update();

            // Ubah status log_tugas_tambahan kembali menjadi 'draft'
            $logTambahanModel->where('user_id', $targetUserId)
                             ->where('tanggal_kegiatan', $tanggal)
                             ->set(['status' => 'draft', 'status_approval' => 'menunggu_persetujuan'])
                             ->update();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memberikan izin revisi. Terjadi kesalahan saat memperbarui database.',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            $stafNama = $targetUser['nama'] ?? $targetUser['nama_lengkap'] ?? 'Staf';

            // Catat ke Audit Log
            if (function_exists('log_audit')) {
                log_audit(
                    'REVISI_LAPORAN',
                    'log_kegiatan_harian',
                    $targetUserId,
                    null,
                    ['tanggal' => $tanggal, 'staf' => $stafNama, 'dibuka_oleh' => session()->get('nama') ?? session()->get('nama_lengkap')]
                );
            }

            // Kirim notifikasi ke staf bersangkutan
            if (function_exists('send_notification')) {
                $tanggalFormatted = date('d M Y', strtotime($tanggal));
                send_notification(
                    $targetUserId,
                    'Laporan Dibuka untuk Revisi',
                    "Laporan harian Anda untuk tanggal {$tanggalFormatted} telah dibuka kembali untuk direvisi. Silakan perbarui dan kirim ulang laporan Anda.",
                    site_url('log-kegiatan')
                );
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Izin revisi laporan {$stafNama} tanggal " . date('d M Y', strtotime($tanggal)) . " berhasil diberikan.",
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memberikan izin revisi: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }
}
