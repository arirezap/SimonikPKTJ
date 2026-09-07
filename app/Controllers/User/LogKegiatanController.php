<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\HolidayModel;
use App\Models\LogKegiatanHarian;
use App\Models\LogTugasTambahan;
use App\Models\SettingModel;
use App\Models\TargetKinerja;
use App\Models\User;

class LogKegiatanController extends BaseController
{
    public function index()
    {
        $logModel = new LogKegiatanHarian();
        $targetModel = new TargetKinerja();

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

        $userModel = new User();
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
        $logTambahanModel = new LogTugasTambahan();
        $rekapDataTambahan = $logTambahanModel->getLogByDate($userId, $tanggalTerpilih);

        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $endOfMonth = date('Y-m-t', strtotime($today));
        $maxAllowedDate = ($endOfMonth > $tomorrow) ? $endOfMonth : $tomorrow;

        $isFutureDate = ($tanggalTerpilih > $today);
        $isTomorrow = ($tanggalTerpilih === $tomorrow);

        $isLocked = false;
        $lockReason = '';

        // 1. Aturan Khusus: Tanggal di luar batas masa depan bulan berjalan DILARANG KERAS
        if ($tanggalTerpilih > $maxAllowedDate) {
            $isLocked = true;
            $lockReason = 'Laporan kegiatan hanya dapat diisi untuk tanggal di bulan ini.';
        } else {
            // 2. Cek pembatasan deadline masa lalu / bulan sebelumnya dari Pengaturan Sistem Admin
            $lockCheck = $this->checkDateLockStatus($tanggalTerpilih, $currentUser, $isFutureDate);
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

        // Ambil semua log kegiatan harian & tugas tambahan user pada tahun ini (memanfaatkan index idx_user_tgl)
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

        // 1. Cek apakah tanggal kegiatan terkunci oleh kebijakan batas waktu sistem (Pengaturan Admin & Masa Depan)
        $lockCheck = $this->checkDateLockStatus($tanggal, $currentUser, $isDraft);
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

        $logModel = new LogKegiatanHarian();
        $tanggal = $this->request->getPost('tanggal');
        $existingData = $logModel->getLogWithTarget($userId, $tanggal);
        $existingTambahanData = (new LogTugasTambahan())->getLogByDate($userId, $tanggal);

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


        $log_ids = $this->request->getPost('log_id');
        $target_id_arr = $this->request->getPost('target_id');
        $deskripsi_kegiatan_arr = $this->request->getPost('deskripsi_kegiatan');
        $jumlah_capaian_arr = $this->request->getPost('jumlah_capaian');
        $link_bukti_arr = $this->request->getPost('link_bukti');

        // Validasi IDOR Kepemilikan Record Tugas Pokok
        $validTargetIds = array_map('intval', array_column($allTargets, 'id'));
        if (!empty($log_ids) && is_array($log_ids)) {
            $cleanLogIds = array_filter(array_map('intval', $log_ids));
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

                $capaianStr = trim((string)($jumlah_capaian_arr[$index] ?? ''));
                $capaianValNum = str_replace(',', '.', $capaianStr);
                if ($capaianStr === '' || !is_numeric($capaianValNum) || (float)$capaianValNum <= 0) {
                    if (!$isDraft) {
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => 'Jumlah capaian harus lebih dari 0.', 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', 'Jumlah capaian harus lebih dari 0.');
                    }
                    if ($capaianStr !== '' && (float)$capaianValNum <= 0) {
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => 'Jumlah capaian harus lebih dari 0.', 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', 'Jumlah capaian harus lebih dari 0.');
                    }
                    $capaianValNum = null;
                }
                if ($capaianValNum === null && $isDraft) {
                    continue; // Lewati jika draf dan capaian belum diisi
                }

                $linkBukti = !empty($link_bukti_arr[$index]) ? trim((string)$link_bukti_arr[$index]) : null;
                if ($linkBukti === 'https://...' || $linkBukti === 'http://...' || $linkBukti === '') {
                    $linkBukti = null;
                } elseif ($linkBukti !== null && !preg_match('/^https?:\/\//i', $linkBukti)) {
                    $linkBukti = 'https://' . $linkBukti;
                }

                $rowData = [
                    'user_id'            => $userId,
                    'target_id'          => (int)$targetId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $deskripsi_kegiatan_arr[$index] ?? '',
                    'jumlah_capaian'     => (float)$capaianValNum,
                    'link_bukti'         => $linkBukti,
                    'status'             => $status
                ];

                if (!empty($log_ids[$index])) {
                    $rowData['id'] = (int)$log_ids[$index];
                    $dataToUpdate[] = $rowData;
                    $allPokokIds[$index] = (int)$log_ids[$index];
                } else {
                    $dataToInsert[$index] = $rowData;
                }
            }
        }

        // --- OLAP SIMULTAN UNTUK TUGAS TAMBAHAN ---
        $logTambahanModel = new LogTugasTambahan();
        $log_tambahan_ids = $this->request->getPost('log_tambahan_id');
        $deskripsi_kegiatan_tambahan_arr = $this->request->getPost('deskripsi_kegiatan_tambahan');
        $jumlah_capaian_tambahan_arr = $this->request->getPost('jumlah_capaian_tambahan');
        $satuan_tambahan_arr = $this->request->getPost('satuan_tambahan');
        $link_bukti_tambahan_arr = $this->request->getPost('link_bukti_tambahan');

        // Validasi IDOR Kepemilikan Record Tugas Tambahan
        if (!empty($log_tambahan_ids) && is_array($log_tambahan_ids)) {
            $cleanTambahanIds = array_filter(array_map('intval', $log_tambahan_ids));
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
                }
            }
        }

        $dataTambahanToUpdate = [];
        $dataTambahanToInsert = [];
        $allTambahanIds = []; // Semua ID tugas tambahan (existing + baru) untuk sinkronisasi UI

        if ($deskripsi_kegiatan_tambahan_arr) {
            foreach ($deskripsi_kegiatan_tambahan_arr as $index => $deskripsiTmb) {
                if (empty($deskripsiTmb)) continue;

                $existingTambahanId = !empty($log_tambahan_ids[$index]) ? (int)$log_tambahan_ids[$index] : null;

                // Skip Tambahan yang sudah terkirim — tidak boleh diubah
                if ($existingTambahanId && in_array($existingTambahanId, $lockedTambahanIds, true)) {
                    continue;
                }
                
                $capaianStrTmb = trim((string)($jumlah_capaian_tambahan_arr[$index] ?? ''));
                $capaianValNumTmb = str_replace(',', '.', $capaianStrTmb);
                if ($capaianStrTmb === '' || !is_numeric($capaianValNumTmb) || (float)$capaianValNumTmb <= 0) {
                    if (!$isDraft) {
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => 'Jumlah capaian tugas tambahan harus lebih dari 0.', 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', 'Jumlah capaian tugas tambahan harus lebih dari 0.');
                    }
                    if ($capaianStrTmb !== '' && (float)$capaianValNumTmb <= 0) {
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => 'Jumlah capaian tugas tambahan harus lebih dari 0.', 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->with('error', 'Jumlah capaian tugas tambahan harus lebih dari 0.');
                    }
                    $capaianValNumTmb = null;
                }
                if ($capaianValNumTmb === null && $isDraft) {
                    continue; // Lewati jika draf dan capaian belum diisi
                }

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
                $lockCheck = $this->checkDateLockStatus($row['tanggal_kegiatan'], $currentUser);
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
        $tanggal = $this->request->getPost('tanggal');
        $userModel = new User();
        $currentUser = $userModel->find($userId);
        $isDirektur = ($currentUser && $currentUser['role'] === 'direktur');

        $isDraft = $this->request->isAJAX() || $this->request->getPost('action') === 'draft';
        $status = $isDraft ? 'draft' : 'terkirim';

        // Cek apakah tanggal kegiatan terkunci oleh kebijakan batas waktu sistem (Pengaturan Admin & Masa Depan)
        $lockCheck = $this->checkDateLockStatus($tanggal, $currentUser, $isDraft);
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

        $log_ids = $this->request->getPost('log_tambahan_id');
        $deskripsi_kegiatan_arr = $this->request->getPost('deskripsi_kegiatan_tambahan');
        $link_bukti_arr = $this->request->getPost('link_bukti_tambahan');
        $jumlah_capaian_tambahan_arr = $this->request->getPost('jumlah_capaian_tambahan');

        // Validasi IDOR Kepemilikan Record Tugas Tambahan
        if (!empty($log_ids) && is_array($log_ids)) {
            $cleanTambahanIds = array_filter(array_map('intval', $log_ids));
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
                }
            }
        }

        $dataToUpdate = [];
        $dataToInsert = [];

        if ($deskripsi_kegiatan_arr) {
            foreach ($deskripsi_kegiatan_arr as $index => $deskripsi) {
                if (empty($deskripsi)) continue;

                $capaianStrTmb = trim((string)($jumlah_capaian_tambahan_arr[$index] ?? ''));
                $capaianValNumTmb = str_replace(',', '.', $capaianStrTmb);
                if ($capaianStrTmb === '' || !is_numeric($capaianValNumTmb) || (float)$capaianValNumTmb <= 0) {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Jumlah capaian tugas tambahan harus lebih dari 0.', 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', 'Jumlah capaian tugas tambahan harus lebih dari 0.');
                }

                $linkBuktiTmb = !empty($link_bukti_arr[$index]) ? trim((string)$link_bukti_arr[$index]) : null;
                if ($linkBuktiTmb === 'https://...' || $linkBuktiTmb === 'http://...' || $linkBuktiTmb === '') {
                    $linkBuktiTmb = null;
                } elseif ($linkBuktiTmb !== null && !preg_match('/^https?:\/\//i', $linkBuktiTmb)) {
                    $linkBuktiTmb = 'https://' . $linkBuktiTmb;
                }

                $rowData = [
                    'user_id'            => $userId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $deskripsi,
                    'jumlah_capaian'     => (float)$capaianValNumTmb,
                    'link_bukti'         => $linkBuktiTmb,
                    'status'             => $status,
                    'status_approval'    => 'menunggu_persetujuan'
                ];

                if (!empty($log_ids[$index])) {
                    $rowData['id'] = (int)$log_ids[$index];
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
                $lockCheck = $this->checkDateLockStatus($row['tanggal_kegiatan'], $currentUser);
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

        $currentUserId = session()->get('id') ?? session()->get('user_id');
        $logModel        = new LogKegiatanHarian();
        $logTambahanModel = new LogTugasTambahan();
        $userModel       = new User();
        // Verifikasi staf yang akan dibuka kuncinya
        $targetUser = $userModel->find($targetUserId);
        if (!$targetUser) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Staf tidak ditemukan.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        // Otorisasi: Superadmin, Kepegawaian (Tugas Belajar), dan Atasan Langsung
        $isAtasanLangsung = !empty($targetUser['atasan_id']) && ($targetUser['atasan_id'] == $currentUserId);
        if (!hasAnyRole(['admin', 'kepegawaian']) && !$isAtasanLangsung) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Izin revisi hanya untuk atasan atau admin.',
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

            // Ubah status log_tugas_tambahan kembali menjadi 'draft'
            $logTambahanModel->where('user_id', $targetUserId)
                             ->where('tanggal_kegiatan', $tanggal)
                             ->set(['status' => 'draft', 'status_approval' => 'menunggu_persetujuan'])
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
    private function checkDateLockStatus(string $tanggal, ?array $currentUser = null, bool $isDraft = false): array
    {
        $today = date('Y-m-d');
        $endOfMonth = date('Y-m-t', strtotime($today));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $maxAllowedDate = ($endOfMonth > $tomorrow) ? $endOfMonth : $tomorrow;

        // 1. Tanggal kegiatan melebihi batas bulan berjalan DILARANG KERAS
        if ($tanggal > $maxAllowedDate) {
            return [
                'is_locked' => true,
                'reason'    => 'Laporan kegiatan hanya dapat diisi untuk tanggal di bulan ini.'
            ];
        }

        // 2. Tanggal di masa depan (tanggal > today) HANYA boleh disimpan sebagai draf, TIDAK boleh dikirim resmi
        if ($tanggal > $today && !$isDraft) {
            return [
                'is_locked' => true,
                'reason'    => 'Kegiatan tanggal mendatang hanya dapat disimpan sebagai draf.'
            ];
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
