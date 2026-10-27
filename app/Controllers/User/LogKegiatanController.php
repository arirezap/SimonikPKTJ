<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\HolidayModel;
use App\Models\LogKegiatanHarian;
use App\Models\LogTugasTambahan;
use App\Models\SettingModel;
use App\Models\TargetKinerja;
use App\Models\User;
use App\Libraries\PenilaianKinerjaPolicy;

class LogKegiatanController extends BaseController
{
    public function index()
    {
        $logModel = new LogKegiatanHarian();
        $targetModel = new TargetKinerja();

        $userId = session()->get('id') ?? session()->get('user_id');
        $userModel = new User();
        $currentUser = $userModel->find($userId);
        
        // Ambil parameter tanggal dari GET atau POST, sinkronkan ke session
        $reqTanggal = $this->request->getVar('tanggal');
        if (!empty($reqTanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $reqTanggal)) {
            session()->set('log_kegiatan_tanggal', $reqTanggal);
            $tanggalTerpilih = $reqTanggal;
        } else {
            $sessTanggal = session()->get('log_kegiatan_tanggal');
            if (!empty($sessTanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $sessTanggal)) {
                $tanggalTerpilih = $sessTanggal;
            } else {
                $tanggalTerpilih = date('Y-m-d');
            }

            // Jika pegawai berstatus mutasi/pensiun/nonaktif dan tanggal default melampaui tmt_selesai, arahkan ke tmt_selesai
            if (($currentUser['status_pegawai'] ?? 'aktif') !== 'aktif' && !empty($currentUser['tmt_selesai'])) {
                if ($tanggalTerpilih > $currentUser['tmt_selesai']) {
                    $tanggalTerpilih = $currentUser['tmt_selesai'];
                    session()->set('log_kegiatan_tanggal', $tanggalTerpilih);
                }
            }
        }
        
        $bulanTerpilih = (int) date('n', strtotime($tanggalTerpilih));
        $tahunTerpilih = (int) date('Y', strtotime($tanggalTerpilih));

        // Ambil daftar target yang sudah dibuat user pada bulan tersebut
        $allTargets = $targetModel->where('user_id', $userId)
                                  ->where('bulan', $bulanTerpilih)
                                  ->where('tahun', $tahunTerpilih)
                                  ->findAll();

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
        $logTambahanModel = new LogTugasTambahan();
        $rekapDataTambahan = $logTambahanModel->getLogByDate($userId, $tanggalTerpilih);

        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $endOfMonth = date('Y-m-t', strtotime($today));
        $maxAllowedDate = ($endOfMonth > $tomorrow) ? $endOfMonth : $tomorrow;

        // Batasi tanggal maksimal jika pegawai berstatus mutasi/pensiun/nonaktif dengan tmt_selesai
        if (($currentUser['status_pegawai'] ?? 'aktif') !== 'aktif' && !empty($currentUser['tmt_selesai'])) {
            if ($maxAllowedDate > $currentUser['tmt_selesai']) {
                $maxAllowedDate = $currentUser['tmt_selesai'];
            }
        }

        $isFutureDate = ($tanggalTerpilih > $today);
        $isTomorrow = ($tanggalTerpilih === $tomorrow);

        $isLocked = false;
        $lockReason = '';

        // Deteksi apakah ada data eksisting berstatus draf revisi (izin buka kunci atasan) pada tanggal lampau
        $isRevision = false;
        if (!empty($rekapData) || !empty($rekapDataTambahan)) {
            $hasSent = false;
            foreach ($rekapData as $r) {
                if (isset($r['status']) && $r['status'] === 'terkirim') {
                    $hasSent = true;
                    break;
                }
            }
            if (!$hasSent) {
                foreach ($rekapDataTambahan as $rT) {
                    if (isset($rT['status']) && $rT['status'] === 'terkirim') {
                        $hasSent = true;
                        break;
                    }
                }
            }
            if (!$hasSent && $tanggalTerpilih <= $today) {
                $isRevision = true;
            }
        }

        // 1. Aturan Khusus: Tanggal di luar batas masa depan / melewati TMT DILARANG KERAS
        if ($tanggalTerpilih > $maxAllowedDate) {
            $isLocked = true;
            if (($currentUser['status_pegawai'] ?? 'aktif') !== 'aktif' && !empty($currentUser['tmt_selesai']) && $tanggalTerpilih > $currentUser['tmt_selesai']) {
                $lockReason = 'Batas pengisian laporan adalah sampai akhir masa penugasan Anda (' . date('d/m/Y', strtotime($currentUser['tmt_selesai'])) . ').';
            } else {
                $lockReason = 'Laporan kegiatan hanya dapat diisi untuk tanggal di bulan ini.';
            }
        } else {
            // 2. Cek pembatasan deadline masa lalu / bulan sebelumnya dari Pengaturan Sistem Admin
            $lockCheck = $this->checkDateLockStatus($tanggalTerpilih, $currentUser, $isFutureDate, $isRevision);
            if ($lockCheck['is_locked']) {
                $isLocked = true;
                $lockReason = $lockCheck['reason'];
            } elseif ($targetStatus !== 'disetujui') {
                // 3. Cek persetujuan target bulanan (pengguna harus sudah memiliki target yang disetujui)
                $isLocked = true;
                $lockReason = ($targetStatus === 'belum_ada') 
                    ? 'Target Kinerja Bulanan untuk bulan ini belum dibuat.' 
                    : 'Target Kinerja Bulanan untuk bulan ini belum disetujui atasan.';
            } else {
                // 4. Cek apakah laporan pada tanggal terpilih sudah pernah dikirim ke atasan
                if (!empty($rekapData)) {
                    foreach ($rekapData as $row) {
                        if (isset($row['status']) && $row['status'] === 'terkirim') {
                            $isLocked = true;
                            $lockReason = 'Laporan tanggal ini sudah dikirim dan terkunci.';
                            break;
                        }
                    }
                }
                if (!$isLocked && !empty($rekapDataTambahan)) {
                    foreach ($rekapDataTambahan as $rowTmb) {
                        if (isset($rowTmb['status']) && $rowTmb['status'] === 'terkirim') {
                            $isLocked = true;
                            $lockReason = 'Laporan tugas tambahan tanggal ini sudah dikirim dan terkunci.';
                            break;
                        }
                    }
                }
            }
        }

        // DATA STATUS HARIAN UNTUK DOT DATEPICKER (Tahun Terpilih - SARGable Date Range Query)
        $holidayModel = new HolidayModel();
        $startDateYear = sprintf('%04d-01-01', $tahunTerpilih);
        $endDateYear   = sprintf('%04d-12-31', $tahunTerpilih);

        $holidays = $holidayModel->where('holiday_date >=', $startDateYear)
                                 ->where('holiday_date <=', $endDateYear)
                                 ->findAll();
        $holidayMap = [];
        foreach ($holidays as $h) {
            $holidayMap[$h['holiday_date']] = $h['holiday_name'];
        }

        // Ambil semua log kegiatan harian & tugas tambahan user pada tahun ini (memanfaatkan index idx_lkh_user_tgl & idx_ltt_user_tgl)
        $yearlyLogs = $logModel->select('tanggal_kegiatan, status')
                               ->where('user_id', $userId)
                               ->where('tanggal_kegiatan >=', $startDateYear)
                               ->where('tanggal_kegiatan <=', $endDateYear)
                               ->findAll();
        $yearlyLogsTambahan = $logTambahanModel->select('tanggal_kegiatan, status')
                                              ->where('user_id', $userId)
                                              ->where('tanggal_kegiatan >=', $startDateYear)
                                              ->where('tanggal_kegiatan <=', $endDateYear)
                                              ->findAll();

        $dateStatusMap = [];
        foreach (array_merge($yearlyLogs, $yearlyLogsTambahan) as $rowLog) {
            $d = $rowLog['tanggal_kegiatan'];
            if (!isset($dateStatusMap[$d])) {
                $dateStatusMap[$d] = ['has_draft' => false, 'has_sent' => false, 'count' => 0];
            }
            $dateStatusMap[$d]['count']++;
            if ($rowLog['status'] === 'draft') {
                $dateStatusMap[$d]['has_draft'] = true;
            } elseif ($rowLog['status'] === 'terkirim') {
                $dateStatusMap[$d]['has_sent'] = true;
            }
        }

        // Format map sederhana untuk Flatpickr JS: [ 'YYYY-MM-DD' => 'terkirim' | 'draft' ]
        $flatpickrDateStatus = [];
        foreach ($dateStatusMap as $dateStr => $info) {
            if ($info['has_draft']) {
                $flatpickrDateStatus[$dateStr] = 'draft';
            } elseif ($info['has_sent']) {
                $flatpickrDateStatus[$dateStr] = 'terkirim';
            }
        }

        $data = [
            'title'               => 'Lapor Kegiatan Harian',
            'tanggal_terpilih'    => $tanggalTerpilih,
            'today'               => $today,
            'tomorrow'            => $tomorrow,
            'max_future_date'     => $maxAllowedDate,
            'is_future_date'      => $isFutureDate,
            'is_tomorrow'         => $isTomorrow,
            'daftar_target'       => $daftarTarget,
            'rekap_data'          => $rekapData,
            'rekap_data_tambahan' => $rekapDataTambahan,
            'is_locked'           => $isLocked,
            'lock_reason'         => $lockReason,
            'target_status'       => $targetStatus,
            'date_status_map'     => $flatpickrDateStatus,
            'holiday_map'         => $holidayMap
        ];

        return view('user/log_kegiatan/index', $data);
    }

    public function store()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        $tanggal = $this->request->getPost('tanggal');
        $userModel = new User();
        $currentUser = $userModel->find($userId);
        $isDirektur = ($currentUser && $currentUser['role'] === 'direktur');

        $isDraft = $this->request->isAJAX() || $this->request->getPost('action') === 'draft';
        $status = $isDraft ? 'draft' : 'terkirim';

        $logModel = new LogKegiatanHarian();
        $logTambahanModel = new LogTugasTambahan();
        $existingData = $logModel->getLogWithTarget($userId, $tanggal);
        $existingTambahanData = $logTambahanModel->getLogByDate($userId, $tanggal);

        // Deteksi apakah ada data eksisting berstatus draf revisi (izin buka kunci atasan) pada tanggal lampau
        $isRevision = false;
        if (!empty($existingData) || !empty($existingTambahanData)) {
            $hasSent = false;
            foreach ($existingData as $r) {
                if (isset($r['status']) && $r['status'] === 'terkirim') {
                    $hasSent = true;
                    break;
                }
            }
            if (!$hasSent) {
                foreach ($existingTambahanData as $rT) {
                    if (isset($rT['status']) && $rT['status'] === 'terkirim') {
                        $hasSent = true;
                        break;
                    }
                }
            }
            if (!$hasSent && $tanggal <= date('Y-m-d')) {
                $isRevision = true;
            }
        }

        // 1. Cek apakah tanggal kegiatan terkunci oleh kebijakan batas waktu sistem (Pengaturan Admin & Masa Depan)
        $lockCheck = $this->checkDateLockStatus($tanggal, $currentUser, $isDraft, $isRevision);
        if ($lockCheck['is_locked']) {
            $msg = 'Gagal menyimpan: ' . $lockCheck['reason'];
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', $msg);
        }

        $bulanTerpilih = date('n', strtotime($tanggal));
        $tahunTerpilih = date('Y', strtotime($tanggal));

        // Cek persetujuan target bulanan
        $targetModel = new TargetKinerja();
        $allTargets = $targetModel->where('user_id', $userId)
                                  ->where('bulan', $bulanTerpilih)
                                  ->where('tahun', $tahunTerpilih)
                                  ->findAll();

        $hasAtasan = $currentUser && !empty($currentUser['atasan_id']) && $currentUser['role'] !== 'direktur';

        if (empty($allTargets)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Target Kinerja Bulanan bulan ini belum dibuat.', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'Target Kinerja Bulanan bulan ini belum dibuat.');
        }

        if ($hasAtasan) {
            foreach ($allTargets as $t) {
                if ($t['status_approval'] !== 'disetujui') {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Target Kinerja Bulanan bulan ini belum disetujui atasan.', 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', 'Target Kinerja Bulanan bulan ini belum disetujui atasan.');
                }
            }
        }

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

        // Jika tidak ada input baru dan tidak ada data draf eksisting, tolak
        if (!$hasPokokInput && !$hasTambahanInput && empty($existingData) && empty($existingTambahanData)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Isi minimal 1 kegiatan pokok atau tugas tambahan.',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Isi minimal 1 kegiatan pokok atau tugas tambahan.');
        }

        // Pengecekan keamanan: Apakah laporan hari ini telah dikunci?
        if (!hasRole('admin')) {
            foreach ($existingData as $row) {
                if (isset($row['status']) && $row['status'] === 'terkirim') {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Laporan tanggal ini sudah dikunci.', 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', 'Laporan tanggal ini sudah dikunci.');
                }
            }
            foreach ($existingTambahanData as $rowTmb) {
                if (isset($rowTmb['status']) && $rowTmb['status'] === 'terkirim') {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Laporan tanggal ini sudah dikunci.', 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', 'Laporan tanggal ini sudah dikunci.');
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
            // LKH-03: Validasi kelengkapan semua draf eksisting sebelum diubah statusnya menjadi terkirim
            foreach ($existingData as $rPokok) {
                $desc = trim((string)($rPokok['deskripsi_kegiatan'] ?? ''));
                $cVal = (float)($rPokok['jumlah_capaian'] ?? 0);
                $bukti = trim((string)($rPokok['link_bukti'] ?? ''));
                if ($desc === '' || $cVal <= 0 || $bukti === '' || !preg_match('#^https?://#i', $bukti)) {
                    $msg = 'Lengkapi deskripsi, capaian (> 0), dan tautan bukti pada draf sebelum mengirim.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
            }

            foreach ($existingTambahanData as $rTmb) {
                $desc = trim((string)($rTmb['deskripsi_kegiatan'] ?? ''));
                $cVal = (float)($rTmb['jumlah_capaian'] ?? 0);
                $bukti = trim((string)($rTmb['link_bukti'] ?? ''));
                if ($desc === '' || $cVal <= 0 || $bukti === '' || !preg_match('#^https?://#i', $bukti)) {
                    $msg = 'Lengkapi deskripsi, capaian (> 0), dan tautan bukti pada draf sebelum mengirim.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
            }

            $db = \Config\Database::connect();
            $db->transStart();
            try {
                $logModel->where('user_id', $userId)
                         ->where('tanggal_kegiatan', $tanggal)
                         ->set(['status' => 'terkirim'])
                         ->update();

                $logTambahanModel = new LogTugasTambahan();
                $logTambahanModel->where('user_id', $userId)
                                 ->where('tanggal_kegiatan', $tanggal)
                                 ->set(['status' => 'terkirim'])
                                 ->update();

                $db->transComplete();
            } catch (\Throwable $e) {
                try { @$db->transRollback(); } catch (\Throwable $t) {}
                log_message('error', '[LogKegiatanController::store:draft_to_sent] ' . $e->getMessage() . ' | User: ' . $userId);
                return redirect()->back()->with('error', 'Gagal mengirim laporan harian.');
            }

            if ($db->transStatus() === false) {
                try { @$db->transRollback(); } catch (\Throwable $t) {}
                return redirect()->back()->with('error', 'Gagal mengirim laporan. Silakan coba lagi.');
            }
                     
            if ($currentUser && !empty($currentUser['atasan_id']) && $currentUser['role'] !== 'direktur') {
                try {
                    helper('notification');
                    send_notification(
                        $currentUser['atasan_id'], 
                        'Laporan Harian Baru', 
                        $currentUser['nama_lengkap'] . " mengirimkan Laporan Harian untuk tanggal $tanggal.",
                        site_url('penilaian-kinerja')
                    );
                } catch (\Throwable $e) {
                    log_message('error', '[LogKegiatanController::store:draft_to_sent:notification] ' . $e->getMessage() . ' | User: ' . $userId);
                }
            }
            
            return redirect()->to('/log-kegiatan')->with('success', 'Laporan kegiatan berhasil dikirim.');
        }

        $today = date('Y-m-d');
        $endOfMonth = date('Y-m-t', strtotime($today));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $maxAllowedDate = ($endOfMonth > $tomorrow) ? $endOfMonth : $tomorrow;

        if ($tanggal > $maxAllowedDate) {
            $msg = 'Laporan kegiatan hanya dapat diisi untuk tanggal di bulan ini.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', $msg);
        } elseif ($tanggal > $today && !$isDraft) {
            $msg = 'Kegiatan tanggal mendatang hanya dapat disimpan sebagai draf.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Fitur batas pelaporan masa lalu dihapus atas permintaan user


        $rawLogIds = (array) ($this->request->getPost('log_id') ?? []);
        $target_id_arr = $this->request->getPost('target_id');
        $deskripsi_kegiatan_arr = $this->request->getPost('deskripsi_kegiatan');
        $jumlah_capaian_arr = $this->request->getPost('jumlah_capaian');
        $link_bukti_arr = $this->request->getPost('link_bukti');

        // LKH-01: Validasi IDOR Kepemilikan Record Tugas Pokok
        $validTargetIds = array_map('intval', array_column($allTargets, 'id'));
        $cleanLogIds = array_values(array_unique(array_filter(array_map('intval', $rawLogIds), fn($v) => $v > 0)));
        $validExistingLogIds = [];
        if (!empty($cleanLogIds)) {
            $checkPokokRows = $logModel->whereIn('id', $cleanLogIds)->findAll();
            foreach ($checkPokokRows as $cRow) {
                if ((int)$cRow['user_id'] !== (int)$userId || $cRow['tanggal_kegiatan'] !== $tanggal) {
                    $msg = 'Akses ditolak. Data kegiatan tidak sesuai akun Anda.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
                $validExistingLogIds[(int)$cRow['id']] = true;
            }
            foreach ($cleanLogIds as $cId) {
                if (!isset($validExistingLogIds[$cId])) {
                    $msg = 'Data kegiatan tidak ditemukan.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
            }
        }

        $dataToUpdate = [];
        $dataToInsert = [];
        $allPokokIds = []; // Semua ID tugas pokok (existing + baru) untuk sinkronisasi UI

        if ($target_id_arr) {
            foreach ($target_id_arr as $index => $targetId) {
                if (empty($targetId)) continue;

                // Validasi IDOR Target RHK: pastikan target_id milik user
                if (!in_array((int)$targetId, $validTargetIds, true)) {
                    $msg = 'Akses ditolak. Target tidak sesuai akun Anda.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }

                $deskripsiPokok = trim((string)($deskripsi_kegiatan_arr[$index] ?? ''));
                $capaianStr = trim((string)($jumlah_capaian_arr[$index] ?? ''));
                $capaianValNum = str_replace(',', '.', $capaianStr);

                $linkBukti = !empty($link_bukti_arr[$index]) ? trim((string)$link_bukti_arr[$index]) : null;
                if ($linkBukti === 'https://...' || $linkBukti === 'http://...' || $linkBukti === '') {
                    $linkBukti = null;
                } elseif ($linkBukti !== null && !preg_match('/^https?:\/\//i', $linkBukti)) {
                    $linkBukti = 'https://' . $linkBukti;
                }

                // LKH-03 & LKH-04: Validasi deskripsi, capaian, dan link bukti
                if (!$isDraft) {
                    if ($deskripsiPokok === '') {
                        $msg = 'Deskripsi kegiatan baris ' . ($index + 1) . ' wajib diisi.';
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', $msg);
                    }
                    if ($capaianStr === '' || !is_numeric($capaianValNum) || (float)$capaianValNum <= 0) {
                        $msg = 'Jumlah capaian kegiatan baris ' . ($index + 1) . ' harus lebih dari 0.';
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', $msg);
                    }
                    if ($linkBukti === null || !preg_match('/^https?:\/\/[a-z0-9]/i', $linkBukti)) {
                        $msg = 'Tautan bukti kegiatan baris ' . ($index + 1) . ' wajib diisi dengan benar.';
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', $msg);
                    }
                } else {
                    // Mode Draf: simpan draf dengan capaian 0 jika belum diisi, jangan dilewati agar tidak hilang
                    if ($capaianStr === '' || !is_numeric($capaianValNum) || (float)$capaianValNum < 0) {
                        $capaianValNum = 0.0;
                    }
                }

                $rowData = [
                    'user_id'            => $userId,
                    'target_id'          => (int)$targetId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $deskripsiPokok,
                    'jumlah_capaian'     => (float)$capaianValNum,
                    'link_bukti'         => $linkBukti,
                    'status'             => $status
                ];

                $candidateId = !empty($rawLogIds[$index]) ? (int)$rawLogIds[$index] : 0;
                if ($candidateId > 0 && isset($validExistingLogIds[$candidateId])) {
                    $rowData['id'] = $candidateId;
                    $dataToUpdate[] = $rowData;
                    $allPokokIds[$index] = $candidateId;
                } else {
                    $dataToInsert[$index] = $rowData;
                }
            }
        }

        // --- OLAP SIMULTAN UNTUK TUGAS TAMBAHAN ---
        $logTambahanModel = new LogTugasTambahan();
        $rawTambahanIds = (array) ($this->request->getPost('log_tambahan_id') ?? []);
        $deskripsi_kegiatan_tambahan_arr = $this->request->getPost('deskripsi_kegiatan_tambahan');
        $jumlah_capaian_tambahan_arr = $this->request->getPost('jumlah_capaian_tambahan');
        $satuan_tambahan_arr = $this->request->getPost('satuan_tambahan');
        $link_bukti_tambahan_arr = $this->request->getPost('link_bukti_tambahan');

        // LKH-01: Validasi IDOR Kepemilikan Record Tugas Tambahan
        $cleanTambahanIds = array_values(array_unique(array_filter(array_map('intval', $rawTambahanIds), fn($v) => $v > 0)));
        $validExistingTambahanIds = [];
        if (!empty($cleanTambahanIds)) {
            $checkTmbRows = $logTambahanModel->whereIn('id', $cleanTambahanIds)->findAll();
            foreach ($checkTmbRows as $cTmb) {
                if ((int)$cTmb['user_id'] !== (int)$userId || $cTmb['tanggal_kegiatan'] !== $tanggal) {
                    $msg = 'Akses ditolak. Data tugas tambahan tidak sesuai akun Anda.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
                $validExistingTambahanIds[(int)$cTmb['id']] = true;
            }
            foreach ($cleanTambahanIds as $cTmbId) {
                if (!isset($validExistingTambahanIds[$cTmbId])) {
                    $msg = 'Data tugas tambahan tidak ditemukan.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
            }
        }

        $dataTambahanToUpdate = [];
        $dataTambahanToInsert = [];
        $allTambahanIds = []; // Semua ID tugas tambahan (existing + baru) untuk sinkronisasi UI

        if ($deskripsi_kegiatan_tambahan_arr) {
            foreach ($deskripsi_kegiatan_tambahan_arr as $index => $deskripsiTmb) {
                $cleanDescTmb = trim((string)$deskripsiTmb);
                if (empty($cleanDescTmb)) continue;

                $candidateTmbId = !empty($rawTambahanIds[$index]) ? (int)$rawTambahanIds[$index] : 0;

                // Skip Tambahan yang sudah terkirim — tidak boleh diubah
                if ($candidateTmbId > 0 && in_array($candidateTmbId, $lockedTambahanIds, true)) {
                    continue;
                }
                
                $capaianStrTmb = trim((string)($jumlah_capaian_tambahan_arr[$index] ?? ''));
                $capaianValNumTmb = str_replace(',', '.', $capaianStrTmb);

                $satuanTmb = !empty($satuan_tambahan_arr[$index]) ? trim((string)$satuan_tambahan_arr[$index]) : 'Kegiatan';
                if ($satuanTmb === 'Satuan' || $satuanTmb === '') {
                    $satuanTmb = 'Kegiatan';
                }

                $linkBuktiTmb = !empty($link_bukti_tambahan_arr[$index]) ? trim((string)$link_bukti_tambahan_arr[$index]) : null;
                if ($linkBuktiTmb === 'https://...' || $linkBuktiTmb === 'http://...' || $linkBuktiTmb === '') {
                    $linkBuktiTmb = null;
                } elseif ($linkBuktiTmb !== null && !preg_match('/^https?:\/\//i', $linkBuktiTmb)) {
                    $linkBuktiTmb = 'https://' . $linkBuktiTmb;
                }

                if (!$isDraft) {
                    if ($capaianStrTmb === '' || !is_numeric($capaianValNumTmb) || (float)$capaianValNumTmb <= 0) {
                        $msg = 'Jumlah capaian tugas tambahan baris ' . ($index + 1) . ' harus lebih dari 0.';
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', $msg);
                    }
                    if ($linkBuktiTmb === null || !preg_match('/^https?:\/\/[a-z0-9]/i', $linkBuktiTmb)) {
                        $msg = 'Tautan bukti tugas tambahan baris ' . ($index + 1) . ' wajib diisi dengan benar.';
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', $msg);
                    }
                } else {
                    if ($capaianStrTmb === '' || !is_numeric($capaianValNumTmb) || (float)$capaianValNumTmb < 0) {
                        $capaianValNumTmb = 0.0;
                    }
                }

                $rowTmbData = [
                    'user_id'            => $userId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $cleanDescTmb,
                    'jumlah_capaian'     => (float)$capaianValNumTmb,
                    'satuan'             => $satuanTmb,
                    'link_bukti'         => $linkBuktiTmb,
                    'status'             => $status,
                    'status_approval'    => ($isDirektur ? 'disetujui' : 'menunggu_persetujuan')
                ];

                if ($candidateTmbId > 0 && isset($validExistingTambahanIds[$candidateTmbId])) {
                    $rowTmbData['id'] = $candidateTmbId;
                    $dataTambahanToUpdate[] = $rowTmbData;
                    $allTambahanIds[$index] = $candidateTmbId; // Catat ID existing
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

            // Jika submit normal (Simpan & Kirim), ubah status semua log tanggal ini menjadi terkirim di dalam transaksi atomik
            if (!$isDraft) {
                $logModel->where('user_id', $userId)
                         ->where('tanggal_kegiatan', $tanggal)
                         ->set(['status' => 'terkirim'])
                         ->update();

                $logTambahanModel->where('user_id', $userId)
                                 ->where('tanggal_kegiatan', $tanggal)
                                 ->set(['status' => 'terkirim'])
                                 ->update();
            }

            $db->transComplete();
        } catch (\Throwable $e) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', '[LogKegiatanController::store] ' . $e->getMessage() . ' | User: ' . $userId . ' | Tanggal: ' . $tanggal);
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data. Silakan coba lagi.', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
        }

        if ($db->transStatus() === false) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', '[LogKegiatanController::store] transStatus false | User: ' . $userId . ' | Tanggal: ' . $tanggal);
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data. Silakan coba lagi.', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
        }

        // Kirim notifikasi ke atasan jika laporan resmi dikirim (bukan draf)
        if (!$isDraft && $currentUser && !empty($currentUser['atasan_id']) && $currentUser['role'] !== 'direktur') {
            try {
                helper('notification');
                send_notification(
                    $currentUser['atasan_id'], 
                    'Laporan Harian Baru', 
                    $currentUser['nama_lengkap'] . " mengirimkan Laporan Harian untuk tanggal $tanggal.",
                    site_url('penilaian-kinerja')
                );
            } catch (\Throwable $e) {
                log_message('error', '[LogKegiatanController::store:notification] ' . $e->getMessage() . ' | User: ' . $userId);
            }
        }

        if ($this->request->isAJAX()) {
            if (function_exists('log_audit')) {
                log_audit(
                    'DRAFT_LOG_HARIAN',
                    'log_kegiatan_harian',
                    $userId,
                    null,
                    [
                        'tanggal'         => $tanggal,
                        'jumlah_pokok'    => count($dataToUpdate) + count($dataToInsert),
                        'jumlah_tambahan' => count($dataTambahanToUpdate) + count($dataTambahanToInsert),
                        'mode'            => 'draft'
                    ]
                );
            }
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Draf kegiatan berhasil disimpan.',
                'new_ids' => $allPokokIds ?? [],
                'new_tambahan_ids' => $allTambahanIds ?? [],
                'csrf_hash' => csrf_hash()
            ]);
        }

        if (function_exists('log_audit')) {
            log_audit(
                'SUBMIT_LOG_HARIAN',
                'log_kegiatan_harian',
                $userId,
                null,
                [
                    'tanggal'         => $tanggal,
                    'jumlah_pokok'    => count($dataToUpdate) + count($dataToInsert),
                    'jumlah_tambahan' => count($dataTambahanToUpdate) + count($dataTambahanToInsert),
                    'mode'            => 'terkirim'
                ]
            );
        }

        return redirect()->to('/log-kegiatan')
                         ->with('success', 'Laporan kegiatan berhasil dikirim.');
    }
    
    public function hapus()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $logModel = new LogKegiatanHarian();
            $userId = session()->get('id') ?? session()->get('user_id');
            $row = $logModel->find($id);
            if ($row && ($row['user_id'] == $userId || hasRole('admin'))) {
                $userModel = new User();
                $currentUser = $userModel->find($userId);
                $isRowDraft = isset($row['status']) && $row['status'] === 'draft';
                $isRevision = $isRowDraft && ($row['tanggal_kegiatan'] <= date('Y-m-d'));
                $lockCheck = $this->checkDateLockStatus($row['tanggal_kegiatan'], $currentUser, $isRowDraft, $isRevision);
                if ($lockCheck['is_locked']) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Laporan terkunci: ' . $lockCheck['reason'], 'csrf_hash' => csrf_hash()]);
                }
                if (!hasRole('admin') && isset($row['status']) && $row['status'] === 'terkirim') {
                    return $this->response->setJSON(['success' => false, 'message' => 'Laporan yang sudah terkirim tidak dapat dihapus.', 'csrf_hash' => csrf_hash()]);
                }
                $logModel->delete($id);
                if (function_exists('log_audit')) {
                    log_audit('DELETE_LOG_HARIAN', 'log_kegiatan_harian', $id, $row, null);
                }
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
                    'message' => 'Form tugas tambahan belum lengkap atau tautan bukti belum benar.',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Form tugas tambahan belum lengkap atau tautan bukti belum benar.');
        }

        $logTambahanModel = new LogTugasTambahan();
        $logModel = new LogKegiatanHarian();
        $tanggal = $this->request->getPost('tanggal');
        $userModel = new User();
        $currentUser = $userModel->find($userId);
        $isDirektur = ($currentUser && $currentUser['role'] === 'direktur');

        $existingPokok = $logModel->where('user_id', $userId)->where('tanggal_kegiatan', $tanggal)->findAll();
        $existingTambahan = $logTambahanModel->where('user_id', $userId)->where('tanggal_kegiatan', $tanggal)->findAll();

        // LKH-02: Pengecekan apakah laporan pada tanggal ini sudah dikirim/terkunci
        if (!hasRole('admin') && !$isDirektur) {
            $isAnySent = false;
            foreach ($existingPokok as $r) {
                if (isset($r['status']) && $r['status'] === 'terkirim') {
                    $isAnySent = true;
                    break;
                }
            }
            if (!$isAnySent) {
                foreach ($existingTambahan as $rTmb) {
                    if (isset($rTmb['status']) && $rTmb['status'] === 'terkirim') {
                        $isAnySent = true;
                        break;
                    }
                }
            }

            if ($isAnySent) {
                $msg = 'Laporan tanggal ini sudah dikirim dan terkunci.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->with('error', $msg);
            }
        }

        $isDraft = $this->request->isAJAX() || $this->request->getPost('action') === 'draft';
        $status = $isDraft ? 'draft' : 'terkirim';

        // Deteksi apakah ada data eksisting berstatus draf revisi (izin buka kunci atasan) pada tanggal lampau
        $isRevision = false;
        if (!empty($existingPokok) || !empty($existingTambahan)) {
            $hasSent = false;
            foreach ($existingPokok as $r) {
                if (isset($r['status']) && $r['status'] === 'terkirim') {
                    $hasSent = true;
                    break;
                }
            }
            if (!$hasSent) {
                foreach ($existingTambahan as $rT) {
                    if (isset($rT['status']) && $rT['status'] === 'terkirim') {
                        $hasSent = true;
                        break;
                    }
                }
            }
            if (!$hasSent && $tanggal <= date('Y-m-d')) {
                $isRevision = true;
            }
        }

        // Cek apakah tanggal kegiatan terkunci oleh kebijakan batas waktu sistem (Pengaturan Admin & Masa Depan)
        $lockCheck = $this->checkDateLockStatus($tanggal, $currentUser, $isDraft, $isRevision);
        if ($lockCheck['is_locked']) {
            $msg = 'Gagal menyimpan: ' . $lockCheck['reason'];
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', $msg);
        }

        $bulanTerpilih = date('n', strtotime($tanggal));
        $tahunTerpilih = date('Y', strtotime($tanggal));

        // Cek persetujuan target bulanan
        $targetModel = new TargetKinerja();
        $allTargets = $targetModel->where('user_id', $userId)
                                  ->where('bulan', $bulanTerpilih)
                                  ->where('tahun', $tahunTerpilih)
                                  ->findAll();

        $hasAtasan = $currentUser && !empty($currentUser['atasan_id']) && $currentUser['role'] !== 'direktur';

        if (empty($allTargets)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Target Kinerja Bulanan bulan ini belum dibuat.', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'Target Kinerja Bulanan bulan ini belum dibuat.');
        }

        if ($hasAtasan) {
            foreach ($allTargets as $t) {
                if ($t['status_approval'] !== 'disetujui') {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Target Kinerja Bulanan bulan ini belum disetujui atasan.', 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', 'Target Kinerja Bulanan bulan ini belum disetujui atasan.');
                }
            }
        }

        $rawLogIds = (array) ($this->request->getPost('log_tambahan_id') ?? []);
        $deskripsi_kegiatan_arr = $this->request->getPost('deskripsi_kegiatan_tambahan');
        $link_bukti_arr = $this->request->getPost('link_bukti_tambahan');
        $jumlah_capaian_tambahan_arr = $this->request->getPost('jumlah_capaian_tambahan');
        $satuan_tambahan_arr = $this->request->getPost('satuan_tambahan');

        // LKH-01: Validasi IDOR Kepemilikan Record Tugas Tambahan
        $cleanTambahanIds = array_values(array_unique(array_filter(array_map('intval', $rawLogIds), fn($v) => $v > 0)));
        $validExistingTambahanIds = [];
        if (!empty($cleanTambahanIds)) {
            $checkTmbRows = $logTambahanModel->whereIn('id', $cleanTambahanIds)->findAll();
            foreach ($checkTmbRows as $cTmb) {
                if ((int)$cTmb['user_id'] !== (int)$userId || $cTmb['tanggal_kegiatan'] !== $tanggal) {
                    $msg = 'Akses ditolak. Data tugas tambahan tidak sesuai akun Anda.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
                $validExistingTambahanIds[(int)$cTmb['id']] = true;
            }
            foreach ($cleanTambahanIds as $cTmbId) {
                if (!isset($validExistingTambahanIds[$cTmbId])) {
                    $msg = 'Data tugas tambahan tidak ditemukan.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
            }
        }

        $dataToUpdate = [];
        $dataToInsert = [];

        if ($deskripsi_kegiatan_arr) {
            foreach ($deskripsi_kegiatan_arr as $index => $deskripsi) {
                $cleanDesc = trim((string)$deskripsi);
                if (empty($cleanDesc)) continue;

                $capaianStrTmb = trim((string)($jumlah_capaian_tambahan_arr[$index] ?? ''));
                $capaianValNumTmb = str_replace(',', '.', $capaianStrTmb);

                $satuanTmb = !empty($satuan_tambahan_arr[$index]) ? trim((string)$satuan_tambahan_arr[$index]) : 'Kegiatan';
                if ($satuanTmb === 'Satuan' || $satuanTmb === '') {
                    $satuanTmb = 'Kegiatan';
                }

                $linkBuktiTmb = !empty($link_bukti_arr[$index]) ? trim((string)$link_bukti_arr[$index]) : null;
                if ($linkBuktiTmb === 'https://...' || $linkBuktiTmb === 'http://...' || $linkBuktiTmb === '') {
                    $linkBuktiTmb = null;
                } elseif ($linkBuktiTmb !== null && !preg_match('/^https?:\/\//i', $linkBuktiTmb)) {
                    $linkBuktiTmb = 'https://' . $linkBuktiTmb;
                }

                // LKH-03 & LKH-04: Validasi capaian dan link bukti untuk tugas tambahan
                if (!$isDraft) {
                    if ($capaianStrTmb === '' || !is_numeric($capaianValNumTmb) || (float)$capaianValNumTmb <= 0) {
                        $msg = 'Jumlah capaian tugas tambahan baris ' . ($index + 1) . ' harus lebih dari 0.';
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', $msg);
                    }
                    if ($linkBuktiTmb === null || !preg_match('/^https?:\/\/[a-z0-9]/i', $linkBuktiTmb)) {
                        $msg = 'Tautan bukti tugas tambahan baris ' . ($index + 1) . ' wajib diisi dengan benar.';
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', $msg);
                    }
                } else {
                    if ($capaianStrTmb === '' || !is_numeric($capaianValNumTmb) || (float)$capaianValNumTmb < 0) {
                        $capaianValNumTmb = 0.0;
                    }
                }

                $rowData = [
                    'user_id'            => $userId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $cleanDesc,
                    'jumlah_capaian'     => (float)$capaianValNumTmb,
                    'satuan'             => $satuanTmb,
                    'link_bukti'         => $linkBuktiTmb,
                    'status'             => $status,
                    'status_approval'    => ($isDirektur ? 'disetujui' : 'menunggu_persetujuan')
                ];

                $candidateId = !empty($rawLogIds[$index]) ? (int)$rawLogIds[$index] : 0;
                if ($candidateId > 0 && isset($validExistingTambahanIds[$candidateId])) {
                    $rowData['id'] = $candidateId;
                    $dataToUpdate[] = $rowData;
                } else {
                    $dataToInsert[$index] = $rowData;
                }
            }
        }

        $insertedIds = [];
        $db = \Config\Database::connect();
        $db->transStart();

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

            $db->transComplete();
        } catch (\Throwable $e) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', '[LogKegiatanController::storeTugasTambahan] ' . $e->getMessage());
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data. Silakan coba lagi.', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
        }

        if ($db->transStatus() === false) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data. Silakan coba lagi.', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
        }

        if (function_exists('log_audit')) {
            log_audit(
                $isDraft ? 'DRAFT_TUGAS_TAMBAHAN' : 'SUBMIT_TUGAS_TAMBAHAN',
                'log_tugas_tambahan',
                $userId,
                null,
                [
                    'tanggal'      => $tanggal,
                    'jumlah_tugas' => count($dataToUpdate) + count($dataToInsert),
                    'mode'         => $status
                ]
            );
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Draf tugas tambahan berhasil disimpan.',
                'new_ids' => $insertedIds,
                'csrf_hash' => csrf_hash()
            ]);
        }

        return redirect()->to('/log-kegiatan')->with('success', 'Tugas tambahan berhasil dikirim.');
    }

    public function hapusTugasTambahan()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $logTambahanModel = new LogTugasTambahan();
            $userId = session()->get('id') ?? session()->get('user_id');
            $row = $logTambahanModel->find($id);
            if ($row && ($row['user_id'] == $userId || hasRole('admin'))) {
                $userModel = new User();
                $currentUser = $userModel->find($userId);
                $isRowDraft = isset($row['status']) && $row['status'] === 'draft';
                $isRevision = $isRowDraft && ($row['tanggal_kegiatan'] <= date('Y-m-d'));
                $lockCheck = $this->checkDateLockStatus($row['tanggal_kegiatan'], $currentUser, $isRowDraft, $isRevision);
                if ($lockCheck['is_locked']) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Laporan terkunci: ' . $lockCheck['reason'], 'csrf_hash' => csrf_hash()]);
                }
                if (!hasRole('admin') && isset($row['status']) && $row['status'] === 'terkirim') {
                    return $this->response->setJSON(['success' => false, 'message' => 'Tugas tambahan yang sudah terkirim tidak dapat dihapus.', 'csrf_hash' => csrf_hash()]);
                }
                $logTambahanModel->delete($id);
                if (function_exists('log_audit')) {
                    log_audit('DELETE_TUGAS_TAMBAHAN', 'log_tugas_tambahan', $id, $row, null);
                }
                return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
            }
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus data tugas tambahan.', 'csrf_hash' => csrf_hash()]);
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

        helper(['notification']);
        $currentUserId = session()->get('id') ?? session()->get('user_id');
        $logModel        = new LogKegiatanHarian();
        $logTambahanModel = new LogTugasTambahan();
        $userModel       = new User();

        $currentUser = $userModel->find($currentUserId);
        if (!$currentUser) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sesi telah berakhir.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        // Verifikasi staf yang akan dibuka kuncinya
        $targetUser = $userModel->find($targetUserId);
        if (!$targetUser) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Staf tidak ditemukan.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $revBulan = (int)date('n', strtotime($tanggal));
        $revTahun = (int)date('Y', strtotime($tanggal));

        // Otorisasi via PenilaianKinerjaPolicy: Admin, Direktur, Kepegawaian (Tugas Belajar), dan Atasan Langsung
        if (!PenilaianKinerjaPolicy::canOpenRevision($currentUser, $targetUser, $revBulan, $revTahun)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Izin revisi hanya untuk atasan langsung atau pimpinan.',
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
                'message' => 'Tidak ada laporan pada tanggal ini.',
                'csrf_hash' => csrf_hash()
            ]);
        }


        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Ubah status log_kegiatan_harian kembali menjadi 'draft'
            $logModel->where('user_id', $targetUserId)
                     ->where('tanggal_kegiatan', $tanggal)
                     ->set(['status' => 'draft'])
                     ->update();

            // Ubah status log_tugas_tambahan kembali menjadi 'draft' dan reset nilai capaiannya (RPK-07 & K-04)
            $logTambahanModel->where('user_id', $targetUserId)
                             ->where('tanggal_kegiatan', $tanggal)
                             ->set([
                                 'status'           => 'draft',
                                 'status_approval'  => 'menunggu_persetujuan',
                                 'nilai_capaian'    => null,
                                 'status_penilaian' => null
                             ])
                             ->update();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memberikan izin revisi. Silakan coba lagi.',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            $stafNama = $targetUser['nama'] ?? $targetUser['nama_lengkap'] ?? 'Staf';

            // Catat ke Audit Log dengan old values dan new values lengkap
            if (function_exists('log_audit')) {
                $oldValues = [
                    'tanggal'                    => $tanggal,
                    'status_pokok_sebelumnya'    => !empty($existingData) ? ($existingData[0]['status'] ?? 'terkirim') : null,
                    'status_tambahan_sebelumnya' => !empty($existingTambahan) ? ($existingTambahan[0]['status'] ?? 'terkirim') : null,
                    'jumlah_pokok'               => count($existingData),
                    'jumlah_tambahan'            => count($existingTambahan)
                ];
                $newValues = [
                    'tanggal'     => $tanggal,
                    'staf'        => $stafNama,
                    'status_baru' => 'draft',
                    'dibuka_oleh' => session()->get('nama') ?? session()->get('nama_lengkap')
                ];
                log_audit('UNLOCK_LAPORAN', 'log_kegiatan_harian', $targetUserId, $oldValues, $newValues);
            }

            // Kirim notifikasi ke staf bersangkutan
            if (function_exists('send_notification')) {
                try {
                    $tanggalFormatted = date('d M Y', strtotime($tanggal));
                    send_notification(
                        $targetUserId,
                        'Laporan Dibuka untuk Revisi',
                        "Laporan harian Anda untuk tanggal {$tanggalFormatted} telah dibuka kembali untuk direvisi. Silakan perbarui dan kirim ulang laporan Anda.",
                        site_url('log-kegiatan')
                    );
                } catch (\Throwable $e) {
                    log_message('error', '[LogKegiatanController::bukaKunci:notification] ' . $e->getMessage() . ' | TargetUser: ' . $targetUserId);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Izin revisi laporan {$stafNama} tanggal " . date('d M Y', strtotime($tanggal)) . " berhasil dibuka.",
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', '[LogKegiatanController::bukaKunci] ' . $e->getMessage() . ' | TargetUser: ' . $targetUserId . ' | Tanggal: ' . $tanggal);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memberikan izin revisi. Silakan coba lagi.',
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Helper untuk mengecek apakah tanggal kegiatan terkunci oleh kebijakan batas waktu sistem
     * @return array ['is_locked' => bool, 'reason' => string]
     */
    private function checkDateLockStatus(string $tanggal, ?array $currentUser = null, bool $isDraft = false, bool $isRevision = false): array
    {
        $today = date('Y-m-d');
        $endOfMonth = date('Y-m-t', strtotime($today));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $maxAllowedDate = ($endOfMonth > $tomorrow) ? $endOfMonth : $tomorrow;

        $userStatus = $currentUser['status_pegawai'] ?? 'aktif';
        $hasDispensasi = !empty($currentUser['dispensasi_lampau_sampai']) && $currentUser['dispensasi_lampau_sampai'] >= $today;

        // 0. Cek Status Pegawai (Mutasi Keluar, Pensiun, Nonaktif) & Batas Akhir Penugasan (TMT)
        if ($userStatus !== 'aktif') {
            if (!empty($currentUser['tmt_selesai'])) {
                if ($tanggal > $currentUser['tmt_selesai']) {
                    $tglTmt = date('d/m/Y', strtotime($currentUser['tmt_selesai']));
                    return [
                        'is_locked' => true,
                        'reason'    => "Tanggal kegiatan melebihi batas akhir penugasan Anda ({$tglTmt})."
                    ];
                }
            } elseif (!$hasDispensasi) {
                return [
                    'is_locked' => true,
                    'reason'    => 'Akun dalam status arsip kepegawaian. Tidak dapat menambah atau mengubah laporan kegiatan.'
                ];
            }
        }

        // Sesuaikan batas maksimal jika akun memiliki tmt_selesai
        if ($userStatus !== 'aktif' && !empty($currentUser['tmt_selesai'])) {
            if ($maxAllowedDate > $currentUser['tmt_selesai']) {
                $maxAllowedDate = $currentUser['tmt_selesai'];
            }
        }

        // 1. Tanggal kegiatan melebihi batas DILARANG KERAS
        if ($tanggal > $maxAllowedDate) {
            $lockMsg = ($userStatus !== 'aktif' && !empty($currentUser['tmt_selesai']))
                ? 'Batas pengisian laporan adalah sampai akhir masa penugasan Anda (' . date('d/m/Y', strtotime($currentUser['tmt_selesai'])) . ').'
                : 'Laporan kegiatan hanya dapat diisi untuk tanggal di bulan ini.';
            return [
                'is_locked' => true,
                'reason'    => $lockMsg
            ];
        }

        // 2. Tanggal di masa depan (tanggal > today) HANYA boleh disimpan sebagai draf, TIDAK boleh dikirim resmi
        if ($tanggal > $today && !$isDraft) {
            return [
                'is_locked' => true,
                'reason'    => 'Kegiatan tanggal mendatang hanya dapat disimpan sebagai draf.'
            ];
        }

        // Jika laporan merupakan revisi yang telah diizinkan, memiliki dispensasi lampau aktif,
        // ATAU pegawai mutasi/pensiun yang mengisi kegiatan dalam masa aktif penugasannya (tanggal <= tmt_selesai)
        $isWithinTmtPenugasan = ($userStatus !== 'aktif' && !empty($currentUser['tmt_selesai']) && $tanggal <= $currentUser['tmt_selesai']);
        if ($isRevision || $hasDispensasi || $isWithinTmtPenugasan) {
            return ['is_locked' => false, 'reason' => ''];
        }

        $settingModel = new SettingModel();

        // 3. Kunci Pengisian Bulan Lalu (End-of-Month Cutoff Deadline) dari Pengaturan Sistem Admin
        $isMonthlyDeadlineActive = $settingModel->getValue('enable_monthly_log_deadline', '1') === '1';
        if ($isMonthlyDeadlineActive) {
            $toleransiDays = (int) $settingModel->getValue('toleransi_hari_bulan_lalu', 0);
            $lastDayOfMonth = date('Y-m-t', strtotime($tanggal));
            $deadlineDate = date('Y-m-d', strtotime("$lastDayOfMonth + {$toleransiDays} days"));

            if ($today > $deadlineDate) {
                $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $mIndex = (int) date('n', strtotime($tanggal)) - 1;
                $tahunKegiatan = date('Y', strtotime($tanggal));
                $namaBulan = ($bulanIndo[$mIndex] ?? '') . " {$tahunKegiatan}";
                $tglBatasIndo = date('d', strtotime($deadlineDate)) . ' ' . ($bulanIndo[(int)date('n', strtotime($deadlineDate)) - 1] ?? '') . ' ' . date('Y', strtotime($deadlineDate));

                return [
                    'is_locked' => true,
                    'reason'    => "Pengisian laporan periode {$namaBulan} telah ditutup sejak {$tglBatasIndo}."
                ];
            }
        }

        // 4. Toleransi Harian Berjalan (Rolling Daily Limit) dari Pengaturan Sistem Admin
        $isDailyDeadlineActive = $settingModel->getValue('enable_log_deadline', '0') === '1';
        if ($isDailyDeadlineActive) {
            $batasLogDays = (int) $settingModel->getValue('batas_input_log', 3);
            $diffDays = (int) floor((strtotime($today) - strtotime($tanggal)) / 86400);
            if ($diffDays > $batasLogDays) {
                return [
                    'is_locked' => true,
                    'reason'    => "Batas pengisian laporan adalah maksimal {$batasLogDays} hari dari tanggal kegiatan."
                ];
            }
        }

        return ['is_locked' => false, 'reason' => ''];
    }
}
