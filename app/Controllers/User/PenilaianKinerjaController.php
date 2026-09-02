<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Controllers\Traits\KinerjaBatchTrait;
use App\Models\HolidayModel;
use App\Models\LogKegiatanHarian;
use App\Models\LogTugasTambahan;
use App\Models\SettingModel;
use App\Models\TargetKinerja;
use App\Models\User;

class PenilaianKinerjaController extends BaseController
{
    use KinerjaBatchTrait;

    public function index()
    {
        $logModel = new LogKegiatanHarian();
        $laporanModel = new TargetKinerja();
        $userModel = new User();

        $userId = session()->get('id') ?? session()->get('user_id');
        
        // Failsafe: Jika user menggunakan session versi lama (dimana id berisi NIP/Username)
        if (!is_numeric($userId) || strlen((string)$userId) > 10) {
            $userDb = $userModel->where('username', $userId)
                                ->orWhere('nip', $userId)
                                ->orWhere('id', $userId) // fallback
                                ->first();
            if ($userDb) {
                $userId = $userDb['id'];
            }
        }
        
        // Sinkronisasi parameter filter via GET, POST, maupun navigasi URL
        $reqBulan = $this->request->getVar('bulan');
        $reqTahun = $this->request->getVar('tahun');
        $reqStafId = $this->request->getVar('staf_id');
        $reqUnitKerja = $this->request->getVar('unit_kerja');

        if (!empty($reqBulan) && is_numeric($reqBulan)) session()->set('penilaian_bulan', (int)$reqBulan);
        if (!empty($reqTahun) && is_numeric($reqTahun)) session()->set('penilaian_tahun', (int)$reqTahun);
        if ($reqStafId !== null) {
            if (!empty($reqStafId)) {
                session()->set('penilaian_staf_id', $reqStafId);
            } else {
                session()->remove('penilaian_staf_id');
            }
        }
        if ($reqUnitKerja !== null) {
            if (!empty($reqUnitKerja)) {
                session()->set('penilaian_unit_kerja', $reqUnitKerja);
            } else {
                session()->remove('penilaian_unit_kerja');
            }
        }

        // Gunakan PRG pattern agar terhindar dari form resubmission dan error 403 saat POST non-AJAX
        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            $hash = '';
            if ($this->request->getPost('active_tab')) {
                $activeTab = $this->request->getPost('active_tab');
                $hash = '#' . $activeTab;
            }
            return redirect()->to(site_url('penilaian-kinerja') . $hash);
        }

        $bulanTerpilih = session()->get('penilaian_bulan') ?? date('n');
        $tahunTerpilih = session()->get('penilaian_tahun') ?? date('Y');
        $stafIdTerpilih = session()->get('penilaian_staf_id') ?? '';
        $unitKerjaTerpilih = session()->get('penilaian_unit_kerja') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $role = session()->get('role');
        // Catatan: Role Wadir tidak memiliki akses menilai kinerja staf
        $isSuper = hasAnyRole(['admin', 'direktur']);

        // Ambil daftar unit kerja untuk filter (Hanya untuk Admin Utama dan Direktur)
        $daftarUnit = [];
        if ($isSuper) {
            $units = $userModel->select('unit')->distinct()->where('unit !=', null)->where('unit !=', '')->orderBy('unit', 'ASC')->findAll();
            foreach ($units as $u) {
                $daftarUnit[] = $u['unit'];
            }
        }

        // Cek apakah user punya staf atau punya akses penuh admin
        if ($isSuper) {
            $builder = $userModel->where('id !=', $userId);
            if (!empty($unitKerjaTerpilih)) {
                $builder = $builder->where('unit', $unitKerjaTerpilih);
            }
            $daftarStaf = $builder->orderBy('nama_lengkap', 'ASC')->findAll();
            $isAtasan = true;
        } elseif (hasRole('wadir')) {
            // Role Wakil Direktur tidak menilai staf (hanya mengelola rekap Target Saya)
            $daftarStaf = [];
            $daftarUnit = [];
            $isAtasan = false;
        } else {
            $daftarStaf = $userModel->getStaf($userId);
            // Jika user punya role kepegawaian, sertakan seluruh pegawai Tugas Belajar
            if (hasRole('kepegawaian')) {
                $db = \Config\Database::connect();
                $tubelIds = array_column($db->table('user_roles')->select('user_id')->where('role_name', 'tugas_belajar')->get()->getResultArray(), 'user_id');
                $builder = $userModel->where('role', 'tugas_belajar');
                if (!empty($tubelIds)) {
                    $builder->orWhereIn('id', $tubelIds);
                }
                $tubelUsers = $builder->where('id !=', $userId)->findAll();

                $existingIds = array_column($daftarStaf, 'id');
                foreach ($tubelUsers as $tb) {
                    if (!in_array($tb['id'], $existingIds)) {
                        $daftarStaf[] = $tb;
                    }
                }
            }
            $isAtasan = !empty($daftarStaf);
        }

        $rekapDashboard = [];

        // Tentukan data siapa yang akan ditampilkan
        $isPenilai = false; // Mode form penilaian aktif?

        if ($isAtasan && !empty($stafIdTerpilih)) {
            // Validasi apakah benar stafnya
            $isValidStaf = false;
            foreach ($daftarStaf as $bwh) {
                if ($bwh['id'] == $stafIdTerpilih) {
                    $isValidStaf = true;
                    break;
                }
            }
            if ($isValidStaf) {
                $isPenilai = true; // Atasan bisa menilai
            } else {
                $stafIdTerpilih = ''; // Reset jika ternyata tidak valid (misal pindah unit)
            }
        }

        $logTambahanModel = new LogTugasTambahan();

        // Ambil rekap data sebulan penuh untuk diri sendiri (selalu ada)
        $rekapDataSendiri = $laporanModel->getTargetWithRealization($userId, $bulanTerpilih, $tahunTerpilih);
        $rawLogSendiri = $logModel->getLogByMonth($userId, $bulanTerpilih, $tahunTerpilih);
        $tugasTambahanSendiri = $logTambahanModel->getLogByMonth($userId, $bulanTerpilih, $tahunTerpilih);
        $logHarianSendiri = $this->combineAndSortLogs($rawLogSendiri, $tugasTambahanSendiri);
        
        // Ambil rekap data staf terpilih (jika ada)
        $rekapDataStaf = [];
        $logHarianStaf = [];
        $tugasTambahanStaf = [];
        $isTargetStafDisetujui = true;
        $targetStafBelumDisetujuiCount = 0;

        if ($isPenilai) {
            $rekapDataStaf = $laporanModel->getTargetWithRealization($stafIdTerpilih, $bulanTerpilih, $tahunTerpilih, true);
            $rawLogHarian = $logModel->getLogByMonth($stafIdTerpilih, $bulanTerpilih, $tahunTerpilih, true);
            $tugasTambahanStaf = $logTambahanModel->getLogByMonth($stafIdTerpilih, $bulanTerpilih, $tahunTerpilih, true);
            $logHarianStaf = $this->combineAndSortLogs($rawLogHarian, $tugasTambahanStaf);

            // Validasi apakah seluruh target staf pada periode ini sudah disetujui atasan
            $stafUserSelected = $userModel->find($stafIdTerpilih);
            $stafHasAtasan = $stafUserSelected && !empty($stafUserSelected['atasan_id']) && $stafUserSelected['role'] !== 'direktur';

            if (!empty($rekapDataStaf) && $stafHasAtasan) {
                foreach ($rekapDataStaf as $targetRow) {
                    if (($targetRow['status_approval'] ?? '') !== 'disetujui') {
                        $isTargetStafDisetujui = false;
                        $targetStafBelumDisetujuiCount++;
                    }
                }
            }
        }

        // Ambil data hari libur nasional untuk kalender heatmap
        $holidayModel = new HolidayModel();
        $startDate = sprintf('%04d-%02d-01', $tahunTerpilih, $bulanTerpilih);
        $endDate = date('Y-m-t', strtotime($startDate));
        $holidaysList = $holidayModel->where('holiday_date >=', $startDate)
                                     ->where('holiday_date <=', $endDate)
                                     ->findAll();
        $holidayMap = [];
        foreach ($holidaysList as $h) {
            $holidayMap[$h['holiday_date']] = $h['holiday_name'];
        }

        // Bangun data kalender heatmap untuk diri sendiri dan staf
        $heatmapSendiri = $this->buildCalendarHeatmap($bulanTerpilih, $tahunTerpilih, $logHarianSendiri, $holidayMap, $bulanIndo);
        $heatmapStaf = $isPenilai ? $this->buildCalendarHeatmap($bulanTerpilih, $tahunTerpilih, $logHarianStaf, $holidayMap, $bulanIndo) : null;

        $data = [
            'title' => 'Rekap & Penilaian Kinerja',
            'bulan_terpilih' => $bulanTerpilih,
            'tahun_terpilih' => $tahunTerpilih,
            'bulan_indo' => $bulanIndo,
            'daftar_staf' => $daftarStaf,
            'staf_id_terpilih' => $stafIdTerpilih,
            'is_atasan' => $isAtasan,
            'is_penilai' => $isPenilai,
            'rekap_data_sendiri' => $rekapDataSendiri,
            'log_harian_sendiri' => $logHarianSendiri,
            'tugas_tambahan_sendiri' => $tugasTambahanSendiri,
            'rekap_data_staf' => $rekapDataStaf,
            'log_harian_staf' => $logHarianStaf,
            'tugas_tambahan_staf' => $tugasTambahanStaf,
            'heatmap_sendiri' => $heatmapSendiri,
            'heatmap_staf' => $heatmapStaf,
            'rekap_dashboard' => $rekapDashboard,
            'is_super' => $isSuper,
            'daftar_unit' => $daftarUnit,
            'unit_kerja_terpilih' => $unitKerjaTerpilih,
            'is_target_staf_disetujui' => $isTargetStafDisetujui,
            'target_staf_unapproved_count' => $targetStafBelumDisetujuiCount
        ];

        return view('user/penilaian_kinerja/index', $data);
    }

    /**
     * Membangun dataset kalender heatmap bulanan untuk visualisasi aktivitas harian
     */
    private function buildCalendarHeatmap($bulan, $tahun, array $logList, array $holidayMap, array $bulanIndo): array
    {
        $startDate = sprintf('%04d-%02d-01', $tahun, $bulan);
        $totalDays = (int)date('t', strtotime($startDate));
        $firstDayOfWeek = (int)date('N', strtotime($startDate)); // 1 (Senin) .. 7 (Minggu)
        
        // Group logs by date
        $logsByDate = [];
        foreach ($logList as $item) {
            $tgl = !empty($item['tanggal_kegiatan']) ? $item['tanggal_kegiatan'] : (!empty($item['tanggal']) ? $item['tanggal'] : null);
            if ($tgl) {
                $logsByDate[$tgl][] = $item;
            }
        }

        $hariIndoArr = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $days = [];
        $totalHariTerisi = 0;
        $totalLogItems = 0;

        for ($d = 1; $d <= $totalDays; $d++) {
            $dateStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
            $dayOfWeek = (int)date('N', strtotime($dateStr)); // 1 (Sen) .. 7 (Min)
            $isWeekend = ($dayOfWeek >= 6);
            $isHoliday = isset($holidayMap[$dateStr]);
            $holidayName = $isHoliday ? $holidayMap[$dateStr] : null;
            $isTanggalMerah = ($isWeekend || $isHoliday);

            $dayLogs = $logsByDate[$dateStr] ?? [];
            $countLogs = count($dayLogs);
            $totalLogItems += $countLogs;

            if ($countLogs > 0) {
                $totalHariTerisi++;
            }

            // Hitung level intensitas berdasarkan aktivitas riil (berlaku untuk semua unit kerja & shift):
            // 0: 0 log (netral)
            // 1: 1-2 log
            // 2: 3-4 log
            // 3: 5-6 log
            // 4: >6 log
            $level = 0;
            if ($countLogs === 0) {
                $level = 0;
            } elseif ($countLogs <= 2) {
                $level = 1;
            } elseif ($countLogs <= 4) {
                $level = 2;
            } elseif ($countLogs <= 6) {
                $level = 3;
            } else {
                $level = 4;
            }

            $wIdx = (int)date('w', strtotime($dateStr));
            $namaHari = $hariIndoArr[$wIdx] ?? '';
            $tglFormatted = $namaHari . ', ' . $d . ' ' . ($bulanIndo[$bulan - 1] ?? '') . ' ' . $tahun;

            $hasDraft = false;
            $hasTerkirim = false;
            foreach ($dayLogs as $dl) {
                $st = $dl['status'] ?? 'draft';
                if ($st === 'terkirim' || $st === 'disetujui') {
                    $hasTerkirim = true;
                } else {
                    $hasDraft = true;
                }
            }

            $days[] = [
                'day_num'          => $d,
                'date_str'         => $dateStr,
                'date_formatted'   => $tglFormatted,
                'day_of_week'      => $dayOfWeek, // 1 = Senin .. 7 = Minggu
                'is_weekend'       => $isWeekend,
                'is_holiday'       => $isHoliday,
                'is_tanggal_merah' => $isTanggalMerah,
                'holiday_name'     => $holidayName,
                'count_logs'       => $countLogs,
                'level'            => $level,
                'has_draft'        => $hasDraft,
                'has_terkirim'     => $hasTerkirim,
                'logs'             => $dayLogs,
            ];
        }

        $rataRataPerHariAktif = $totalHariTerisi > 0 ? round($totalLogItems / $totalHariTerisi, 1) : 0;

        return [
            'total_days'        => $totalDays,
            'first_day_of_week' => $firstDayOfWeek, // 1 (Senin) s/d 7 (Minggu)
            'days'              => $days,
            'summary'           => [
                'total_hari_terisi'        => $totalHariTerisi,
                'total_log_items'          => $totalLogItems,
                'rata_rata_per_hari_aktif' => $rataRataPerHariAktif,
            ],
        ];
    }

    public function store()
    {
        // Hanya yang mensubmit form penilaian (Atasan) yang sampai ke sini
        $userModel = new User();
        $userId = session()->get('id') ?? session()->get('user_id');

        // Failsafe: Jika user menggunakan session versi lama (dimana id berisi NIP/Username)
        if (!is_numeric($userId) || strlen((string)$userId) > 10) {
            $userDb = $userModel->where('username', $userId)
                                ->orWhere('nip', $userId)
                                ->orWhere('id', $userId)
                                ->first();
            if ($userDb) {
                $userId = $userDb['id'];
            }
        }

        $laporanModel = new TargetKinerja();
        $action = $this->request->getPost('action');
        if ($action === 'submit') {
            $statusPenilaian = 'terbit';
        } elseif ($action === 'draft') {
            $statusPenilaian = 'draft';
        } else {
            $statusPenilaian = null; // Reset action
        }

        $stafPostId = $this->request->getPost('staf_id');
        $bulanPost  = $this->request->getPost('bulan');
        $tahunPost  = $this->request->getPost('tahun');
        $unitPost   = $this->request->getPost('unit_kerja');

        $isSelfEval = $this->request->getPost('is_self_eval') === '1' || (empty($stafPostId) || $stafPostId == $userId);

        if ($isSelfEval) {
            $currentUser = $userModel->find($userId);
            // Evaluasi mandiri HANYA diizinkan untuk Direktur (atau user tanpa atasan di sistem)
            if (!($currentUser && ($currentUser['role'] === 'direktur' || empty($currentUser['atasan_id'])))) {
                return redirect()->back()->with('error', 'Akses ditolak. Penilaian kinerja staf harus dilakukan oleh Atasan Langsung.');
            }
            $targetUserId = $userId;
            $targetUserRecord = $currentUser;
        } else {
            // Otorisasi: Pastikan penilai adalah Atasan Langsung dari staf atau Pimpinan/Superadmin/Kepegawaian (Wadir tidak diizinkan)
            if (hasRole('wadir')) {
                return redirect()->back()->with('error', 'Akses ditolak. Role Wakil Direktur tidak memiliki wewenang memberikan penilaian kinerja staf.');
            }
            $stafUser = $userModel->find($stafPostId);
            if (!$stafUser) {
                return redirect()->back()->with('error', 'Staf tidak ditemukan.');
            }
            $isAtasan = !empty($stafUser['atasan_id']) && ($stafUser['atasan_id'] == $userId);
            if (!hasAnyRole(['admin', 'direktur', 'kepegawaian']) && !$isAtasan) {
                return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki izin menilai kinerja staf ini.');
            }
            $targetUserId = $stafPostId;
            $targetUserRecord = $stafUser;
        }

        // Validasi: Atasan hanya dapat memberikan penilaian jika seluruh Target Kinerja Bulanan staf sudah disetujui (kecuali reset)
        if (!$isSelfEval && !empty($targetUserId) && $action !== 'reset') {
            $targetUserHasAtasan = $targetUserRecord && !empty($targetUserRecord['atasan_id']) && $targetUserRecord['role'] !== 'direktur';
            if ($targetUserHasAtasan) {
                $evalMonthCheck = !empty($bulanPost) ? (int)$bulanPost : (int)(session()->get('penilaian_bulan') ?? date('n'));
                $evalYearCheck  = !empty($tahunPost) ? (int)$tahunPost : (int)(session()->get('penilaian_tahun') ?? date('Y'));
                
                $checkTargets = $laporanModel->where('user_id', $targetUserId)
                                             ->where('bulan', $evalMonthCheck)
                                             ->where('tahun', $evalYearCheck)
                                             ->where('status', 'terkirim')
                                             ->findAll();
                if (empty($checkTargets)) {
                    return redirect()->back()->with('error', 'Gagal: Staf belum memiliki Target Kinerja Bulanan untuk periode ini.');
                }
                foreach ($checkTargets as $ct) {
                    if (($ct['status_approval'] ?? '') !== 'disetujui') {
                        return redirect()->back()->with('error', 'Gagal menyimpan penilaian: Target Kinerja Bulanan staf untuk periode ini belum disetujui oleh Atasan Langsung. Harap setujui target terlebih dahulu pada menu Target Kinerja.');
                    }
                }
            }
        }

        // Cek Batas Waktu Penilaian jika Saklar Batas Waktu Penilaian Aktif
        $settingModel = new SettingModel();
        $isDeadlineActive = $settingModel->getValue('enable_penilaian_deadline', '0') === '1';
        $evalYear  = !empty($tahunPost) ? (int)$tahunPost : (int)(session()->get('penilaian_tahun') ?? date('Y'));
        $evalMonth = !empty($bulanPost) ? (int)$bulanPost : (int)(session()->get('penilaian_bulan') ?? date('n'));

        if ($isDeadlineActive && !hasRole('admin') && $action !== 'reset') {
            $batasPenilaian = (int) $settingModel->getValue('batas_penilaian_kinerja', 10);
            $evalMonthStart = new \DateTime(sprintf('%04d-%02d-01', $evalYear, $evalMonth));
            $currentMonthStart = new \DateTime(date('Y-m-01'));
            
            // Jika menilai periode lampau (sebelum bulan berjalan)
            if ($evalMonthStart < $currentMonthStart) {
                $nextMonthOfEval = clone $evalMonthStart;
                $nextMonthOfEval->modify('+1 month');
                $deadlineDay = min(28, max(1, $batasPenilaian));
                $deadlineDate = new \DateTime($nextMonthOfEval->format('Y-m-') . sprintf('%02d', $deadlineDay) . ' 23:59:59');
                
                $now = new \DateTime();
                if ($now > $deadlineDate) {
                    $bulanIndoList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $namaBulanEval = $bulanIndoList[$evalMonth - 1] ?? $evalMonth;
                    return redirect()->back()->with('error', "Gagal: Batas waktu penilaian kinerja untuk periode {$namaBulanEval} {$evalYear} telah berakhir pada tanggal " . $deadlineDate->format('d M Y') . ".");
                }
            }
        }

        if (!$isSelfEval && !empty($stafPostId)) session()->set('penilaian_staf_id', $stafPostId);
        if (!empty($bulanPost))  session()->set('penilaian_bulan', $bulanPost);
        if (!empty($tahunPost))  session()->set('penilaian_tahun', $tahunPost);
        if (!empty($unitPost))   session()->set('penilaian_unit_kerja', $unitPost);

        $laporan_ids = $this->request->getPost('laporan_id');
        $nilai_capaian_arr = $this->request->getPost('nilai_capaian');

        $dataToUpdate = [];

        if ($laporan_ids) {
            foreach ($laporan_ids as $index => $idLaporan) {
                if (empty($idLaporan)) continue;

                $valRaw = isset($nilai_capaian_arr[$index]) ? trim((string)$nilai_capaian_arr[$index]) : '';
                $valScore = null;
                $rowStatusPenilaian = $statusPenilaian;

                if ($action !== 'reset' && $valRaw !== '') {
                    $valClean = str_replace(',', '.', $valRaw);
                    if (is_numeric($valClean)) {
                        $valScore = min(150.0, max(0.0, (float)$valClean));
                    }
                } else {
                    // Nilai kosong atau action reset: kosongkan nilai dan status penilaian seperti belum pernah dinilai
                    $valScore = null;
                    $rowStatusPenilaian = null;
                }

                $rowUpdate = [
                    'id' => $idLaporan,
                    'nilai_capaian' => $valScore,
                    'status_penilaian' => $rowStatusPenilaian,
                ];

                $dataToUpdate[] = $rowUpdate;
            }
        }

        $oldTargetValues = [];
        if (!empty($dataToUpdate)) {
            $targetIds = array_column($dataToUpdate, 'id');
            $oldTargets = $laporanModel->whereIn('id', $targetIds)->findAll();
            foreach ($oldTargets as $ot) {
                // Validasi ownership: pastikan laporan_id yang dikirim memang milik targetUserId
                if ((int)$ot['user_id'] !== (int)$targetUserId) {
                    return redirect()->back()->with('error', 'Akses ditolak. Terdapat data penilaian yang tidak sesuai kepemilikan staf.');
                }
                $oldTargetValues[$ot['id']] = [
                    'id' => $ot['id'],
                    'nilai_capaian' => $ot['nilai_capaian'],
                    'status_penilaian' => $ot['status_penilaian']
                ];
            }
        }

        // --- PENILAIAN TUGAS TAMBAHAN ---
        $logTambahanModel = new LogTugasTambahan();
        $log_tambahan_ids = $this->request->getPost('log_tambahan_id');
        $nilai_tambahan_arr = $this->request->getPost('nilai_tambahan');
        $nilai_tambahan_gabungan = $this->request->getPost('nilai_tugas_tambahan_gabungan');

        $dataTambahanToUpdate = [];
        if ($log_tambahan_ids) {
            foreach ($log_tambahan_ids as $index => $idTambahan) {
                if (empty($idTambahan)) continue;

                $valScore = null;
                $rawScoreTmb = null;
                if ($nilai_tambahan_gabungan !== null && trim((string)$nilai_tambahan_gabungan) !== '') {
                    $rawScoreTmb = trim((string)$nilai_tambahan_gabungan);
                } elseif (isset($nilai_tambahan_arr[$index]) && trim((string)$nilai_tambahan_arr[$index]) !== '') {
                    $rawScoreTmb = trim((string)$nilai_tambahan_arr[$index]);
                }

                $rowTmbStatus = $statusPenilaian;
                $rowTmbAppr = 'menunggu_persetujuan';

                if ($action !== 'reset' && $rawScoreTmb !== null && $rawScoreTmb !== '') {
                    $cleanedTmb = str_replace(',', '.', $rawScoreTmb);
                    if (is_numeric($cleanedTmb)) {
                        $valScore = min(150.0, max(0.0, (float)$cleanedTmb));
                    }
                    $rowTmbStatus = $statusPenilaian;
                    $rowTmbAppr = ($statusPenilaian === 'terbit') ? 'disetujui' : 'menunggu_persetujuan';
                } else {
                    // Nilai tugas tambahan kosong atau reset: kosongkan nilai dan status_penilaian
                    $valScore = null;
                    $rowTmbStatus = null;
                    $rowTmbAppr = 'menunggu_persetujuan';
                }

                $rowUpdate = [
                    'id' => $idTambahan,
                    'nilai_capaian' => $valScore,
                    'status_penilaian' => $rowTmbStatus,
                    'status_approval' => $rowTmbAppr
                ];
                $dataTambahanToUpdate[] = $rowUpdate;
            }
        }

        $oldTambahanValues = [];
        if (!empty($dataTambahanToUpdate)) {
            $tambahanIds = array_column($dataTambahanToUpdate, 'id');
            $oldTambahans = $logTambahanModel->whereIn('id', $tambahanIds)->findAll();
            foreach ($oldTambahans as $otm) {
                // Validasi ownership: pastikan log_tambahan_id yang dikirim memang milik targetUserId
                if ((int)$otm['user_id'] !== (int)$targetUserId) {
                    return redirect()->back()->with('error', 'Akses ditolak. Terdapat data tugas tambahan yang tidak sesuai kepemilikan staf.');
                }
                $oldTambahanValues[$otm['id']] = [
                    'id' => $otm['id'],
                    'nilai_capaian' => $otm['nilai_capaian'],
                    'status_penilaian' => $otm['status_penilaian']
                ];
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if (!empty($dataToUpdate)) {
                $laporanModel->updateBatch($dataToUpdate, 'id');
            }
            if (!empty($dataTambahanToUpdate)) {
                $logTambahanModel->updateBatch($dataTambahanToUpdate, 'id');
            }

            $db->transComplete();
        } catch (\Throwable $e) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', '[PenilaianKinerja::store] Transaksi gagal: ' . $e->getMessage() . ' | Staf: ' . $targetUserId . ' | Bulan: ' . $evalMonth . '/' . $evalYear);
            return redirect()->back()->with('error', 'Gagal menyimpan penilaian kinerja ke database: ' . $e->getMessage());
        }

        if ($db->transStatus() === false) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', '[PenilaianKinerja::store] transStatus false | Staf: ' . $targetUserId . ' | Bulan: ' . $evalMonth . '/' . $evalYear);
            return redirect()->back()->with('error', 'Gagal menyimpan penilaian kinerja karena kesalahan basis data.');
        }

        $stafName = $targetUserRecord['nama_lengkap'] ?? $targetUserRecord['nama'] ?? 'Pegawai';

        // Audit log & Notifikasi
        if ($action === 'reset') {
            if (function_exists('log_audit') && (!empty($dataToUpdate) || !empty($dataTambahanToUpdate))) {
                log_audit(
                    'RESET_PENILAIAN_KINERJA',
                    'target_kinerja_bulanan',
                    $targetUserId,
                    ['target' => $oldTargetValues, 'tambahan' => $oldTambahanValues],
                    [
                        'staf'  => $stafName,
                        'bulan' => $evalMonth,
                        'tahun' => $evalYear,
                        'action'=> 'reset'
                    ]
                );
            }
            $pesan = $isSelfEval 
                ? 'Nilai kinerja mandiri berhasil dikosongkan (di-reset kembali ke status belum dinilai).' 
                : 'Penilaian kinerja staf berhasil dikosongkan (di-reset kembali ke status belum dinilai).';
        } elseif ($statusPenilaian === 'terbit' && (!empty($dataToUpdate) || !empty($dataTambahanToUpdate))) {
            if (function_exists('log_audit')) {
                log_audit(
                    'APPROVE_PENILAIAN_KINERJA',
                    'target_kinerja_bulanan',
                    $targetUserId,
                    ['target' => $oldTargetValues, 'tambahan' => $oldTambahanValues],
                    [
                        'target'           => $dataToUpdate,
                        'tambahan'         => $dataTambahanToUpdate,
                        'staf'             => $stafName,
                        'bulan'            => $evalMonth,
                        'tahun'            => $evalYear,
                        'status_penilaian' => 'terbit'
                    ]
                );
            }
            
            if (!$isSelfEval && !empty($targetUserId) && $targetUserId != $userId) {
                helper('notification');
                $bulanIndoArr = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $namaBulanEval = $bulanIndoArr[(int)$evalMonth - 1] ?? "Bulan {$evalMonth}";
                $atasanName = session()->get('nama_lengkap') ?? 'Atasan Langsung';

                send_notification(
                    $targetUserId,
                    'Penilaian Kinerja Diterbitkan',
                    "{$atasanName} telah menerbitkan Nilai Kinerja Bulanan Anda untuk periode {$namaBulanEval} {$evalYear}. Silakan periksa rekapitulasi nilai dan capaian Anda.",
                    site_url('penilaian-kinerja?bulan=' . $evalMonth . '&tahun=' . $evalYear . '&active_tab=individu')
                );
                $pesan = 'Penilaian kinerja staf berhasil diterbitkan.';
            } else {
                $pesan = 'Nilai capaian kinerja mandiri Anda berhasil diterbitkan.';
            }
        } else {
            if (function_exists('log_audit') && (!empty($dataToUpdate) || !empty($dataTambahanToUpdate))) {
                log_audit(
                    'DRAFT_PENILAIAN_KINERJA',
                    'target_kinerja_bulanan',
                    $targetUserId,
                    ['target' => $oldTargetValues, 'tambahan' => $oldTambahanValues],
                    [
                        'target'           => $dataToUpdate,
                        'tambahan'         => $dataTambahanToUpdate,
                        'staf'             => $stafName,
                        'bulan'            => $evalMonth,
                        'tahun'            => $evalYear,
                        'status_penilaian' => 'draft'
                    ]
                );
            }

            $pesan = $isSelfEval 
                ? 'Nilai kinerja mandiri berhasil disimpan sementara (Draf).' 
                : 'Penilaian kinerja berhasil disimpan sementara (Draf). Penilaian belum dipublikasikan ke staf.';
        }

        $redirectHash = $isSelfEval ? '#individu' : (!empty($stafPostId) ? '#staf' : '#individu');
        return redirect()->to(site_url('penilaian-kinerja') . $redirectHash)
                         ->with('success', $pesan);
    }

    public function getChartDataApi()
    {
        $currentUserId = session()->get('id') ?? session()->get('user_id');
        $userModel = new User();

        // Failsafe session id
        if (!is_numeric($currentUserId) || strlen((string)$currentUserId) > 10) {
            $userDb = $userModel->where('username', $currentUserId)
                                ->orWhere('nip', $currentUserId)
                                ->orWhere('id', $currentUserId)
                                ->first();
            if ($userDb) {
                $currentUserId = $userDb['id'];
            }
        }

        $userId = (int)$this->request->getGet('user_id');
        $bulan  = (int)$this->request->getGet('bulan');
        $tahun  = (int)$this->request->getGet('tahun');

        if (!$userId || !$bulan || !$tahun) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Parameter tidak lengkap']);
        }

        // Proteksi IDOR: Batasi akses sesuai hierarki jabatan
        // - Admin, Direktur, Kepegawaian: bypass penuh
        // - Manajemen/Kabag: hanya ke staf langsung di bawahnya
        // - Wadir, SPM, dan role lain: TIDAK boleh akses data pegawai lain
        if ($userId != $currentUserId) {
            if (hasAnyRole(['admin', 'direktur', 'kepegawaian'])) {
                // Bypass penuh — diizinkan
            } elseif (hasAnyRole(['manajemen', 'kabag', 'kabag_aak', 'kabag_kuk'])) {
                // Hanya bisa lihat staf langsung di bawahnya
                $daftarStaf = $userModel->getStaf($currentUserId);
                $allowedIds = array_column($daftarStaf, 'id');
                if (!in_array($userId, $allowedIds)) {
                    return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak. Anda tidak berhak melihat data kinerja pegawai ini.']);
                }
            } else {
                // Wadir, SPM, staf biasa, dan role lain tidak bisa melihat data pegawai lain
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak. Anda tidak berhak melihat data kinerja pegawai ini.']);
            }
        }

        $targetModel = new TargetKinerja();
        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $trendBulan = [];
        $trendNilai = [];
        
        // Kumpulkan tahun yang terlibat dalam 6 bulan terakhir untuk batch loading efisien
        $yearsInvolved = [];
        $monthsToFetch = [];
        for ($i = 5; $i >= 0; $i--) {
            $timestamp = mktime(0, 0, 0, $bulan - $i, 1, $tahun);
            $m = (int)date('n', $timestamp);
            $y = (int)date('Y', $timestamp);
            $yearsInvolved[$y] = true;
            $monthsToFetch[] = ['bulan' => $m, 'tahun' => $y, 'label' => $bulanIndo[$m - 1] . ' ' . substr($y, 2, 2)];
        }

        // Batch fetch data kinerja per tahun (O(1) in-memory)
        $batchDataPerYear = [];
        foreach (array_keys($yearsInvolved) as $yVal) {
            $batchDataPerYear[$yVal] = $this->loadBatchKinerjaData($yVal, [$userId]);
        }

        foreach ($monthsToFetch as $item) {
            $m = $item['bulan'];
            $y = $item['tahun'];
            [$bTargets, $bTambahan] = $batchDataPerYear[$y] ?? [[], []];

            $stat = $this->hitungKinerjaPegawai($userId, $m, $y, $bTargets, $bTambahan);

            $trendBulan[] = $item['label'];
            $trendNilai[] = round((float)($stat['rata_rata'] ?? 0), 2);
        }

        $rekapData = $targetModel->getTargetWithRealization($userId, $bulan, $tahun);
        
        $totalRealisasi = 0;
        $totalTarget = 0;

        foreach ($rekapData as $row) {
            $totalTarget += (float)$row['target_bulanan'];
            $totalRealisasi += (float)$row['total_realisasi'];
        }

        return $this->response->setJSON([
            'trend_labels' => $trendBulan,
            'trend_data' => $trendNilai,
            'kualitas' => ['tepat' => 0, 'lambat' => 0],
            'sikap' => ['disiplin' => 0, 'kerjasama' => 0],
            'produktivitas' => ['realisasi' => $totalRealisasi, 'sisa' => max(0, $totalTarget - $totalRealisasi)]
        ]);
    }

    /**
     * Helper untuk menggabungkan log kegiatan utama dan tugas tambahan, lalu mengurutkannya secara kronologis.
     */
    private function combineAndSortLogs(array $rawLogs, array $tugasTambahan): array
    {
        $combined = [];
        foreach ($rawLogs as $l) {
            $l['is_tambahan'] = false;
            $combined[] = $l;
        }
        foreach ($tugasTambahan as $tmb) {
            $tmb['is_tambahan'] = true;
            $tmb['indikator_kinerja'] = 'Tugas Tambahan';
            $combined[] = $tmb;
        }
        usort($combined, function($a, $b) {
            if (($a['tanggal_kegiatan'] ?? '') !== ($b['tanggal_kegiatan'] ?? '')) {
                return strtotime($a['tanggal_kegiatan'] ?? '') <=> strtotime($b['tanggal_kegiatan'] ?? '');
            }
            return (!empty($a['is_tambahan']) ? 1 : 0) <=> (!empty($b['is_tambahan']) ? 1 : 0);
        });
        return $combined;
    }
}
