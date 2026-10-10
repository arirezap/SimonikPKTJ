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
use App\Libraries\PenilaianKinerjaPolicy;

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

        $currentUser = $userModel->find($userId);
        if (!$currentUser) {
            return redirect()->to(site_url('login'))->with('error', 'Sesi telah berakhir. Silakan masuk kembali.');
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

        $periodDate = sprintf('%04d-%02d-01', (int)$tahunTerpilih, (int)$bulanTerpilih);
        $periodMonth = date('Y-m', strtotime($periodDate));

        // Cek apakah user punya staf atau punya akses penuh admin
        if ($isSuper) {
            $builder = $userModel->select('id, nama_lengkap, nip, username, jabatan, pangkat, unit, role, status_pegawai, tmt_selesai, foto, atasan_id')
                                 ->where('id !=', $userId)
                                 ->groupStart()
                                     ->where('status_pegawai', 'aktif')
                                     ->orGroupStart()
                                         ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun'])
                                         ->where('tmt_selesai IS NOT NULL')
                                         ->where('tmt_selesai >=', $periodMonth . '-01')
                                     ->groupEnd()
                                 ->groupEnd();
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
            $daftarStaf = $userModel->getStaf($userId, $periodDate);
            // Jika user punya role kepegawaian, sertakan seluruh pegawai Tugas Belajar
            if (hasRole('kepegawaian')) {
                $db = \Config\Database::connect();
                $tubelIds = array_column($db->table('user_roles')->select('user_id')->where('role_name', 'tugas_belajar')->get()->getResultArray(), 'user_id');
                $tubelBuilder = $userModel->select('id, nama_lengkap, nip, username, jabatan, pangkat, unit, role, status_pegawai, tmt_selesai, foto, atasan_id')
                                          ->groupStart()
                                          ->where('role', 'tugas_belajar');
                if (!empty($tubelIds)) {
                    $tubelBuilder->orWhereIn('id', $tubelIds);
                }
                $tubelUsers = $tubelBuilder->groupEnd()
                                           ->groupStart()
                                               ->where('status_pegawai', 'aktif')
                                               ->orGroupStart()
                                                   ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun'])
                                                   ->where('tmt_selesai IS NOT NULL')
                                                   ->where('tmt_selesai >=', $periodMonth . '-01')
                                               ->groupEnd()
                                           ->groupEnd()
                                           ->where('id !=', $userId)
                                           ->findAll();

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
                session()->remove('penilaian_staf_id');
            }
        } else {
            if (!empty($stafIdTerpilih) && !$isAtasan) {
                $stafIdTerpilih = '';
                session()->remove('penilaian_staf_id');
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
            $stafUserSelected = $userModel->select('id, nama_lengkap, role, atasan_id')->find($stafIdTerpilih);
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

        // Ambil tanggal revisi aktif staf jika dalam mode penilai (RPK-07)
        $revisiDatesMap = [];
        if ($isPenilai && !empty($stafIdTerpilih)) {
            $draftDatesPokok = $logModel->select('tanggal_kegiatan')
                                        ->where('user_id', $stafIdTerpilih)
                                        ->where('tanggal_kegiatan >=', $startDate)
                                        ->where('tanggal_kegiatan <=', $endDate)
                                        ->where('status', 'draft')
                                        ->findColumn('tanggal_kegiatan') ?? [];

            $draftDatesTambahan = $logTambahanModel->select('tanggal_kegiatan')
                                                  ->where('user_id', $stafIdTerpilih)
                                                  ->where('tanggal_kegiatan >=', $startDate)
                                                  ->where('tanggal_kegiatan <=', $endDate)
                                                  ->where('status', 'draft')
                                                  ->findColumn('tanggal_kegiatan') ?? [];

            $revisiDatesMap = array_fill_keys(array_merge($draftDatesPokok, $draftDatesTambahan), true);
        }

        // Periksa pengaturan penonaktifan kalender heatmap berbasis tahun
        $settingModel = new SettingModel();
        $rawHiddenYears = $settingModel->getValue('hide_heatmap_years', '[]');
        $hiddenHeatmapYears = json_decode((string)$rawHiddenYears, true);
        if (!is_array($hiddenHeatmapYears)) {
            $hiddenHeatmapYears = [];
        }
        $isHeatmapHidden = in_array((string)$tahunTerpilih, $hiddenHeatmapYears, true);

        // Bangun data kalender heatmap jika tidak disembunyikan
        $heatmapSendiri = !$isHeatmapHidden
            ? $this->buildCalendarHeatmap($bulanTerpilih, $tahunTerpilih, $logHarianSendiri, $holidayMap, $bulanIndo)
            : null;
        $heatmapStaf = (!$isHeatmapHidden && $isPenilai)
            ? $this->buildCalendarHeatmap($bulanTerpilih, $tahunTerpilih, $logHarianStaf, $holidayMap, $bulanIndo, $revisiDatesMap)
            : null;

        $data = [
            'title' => 'Rekap & Penilaian Kinerja',
            'bulan_terpilih' => $bulanTerpilih,
            'tahun_terpilih' => $tahunTerpilih,
            'bulan_indo' => $bulanIndo,
            'daftar_staf' => $daftarStaf,
            'staf_id_terpilih' => $stafIdTerpilih,
            'is_atasan' => $isAtasan,
            'is_penilai' => $isPenilai,
            'is_heatmap_hidden' => $isHeatmapHidden,
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
    private function buildCalendarHeatmap($bulan, $tahun, array $logList, array $holidayMap, array $bulanIndo, array $revisiDatesMap = []): array
    {
        $startDate = sprintf('%04d-%02d-01', $tahun, $bulan);
        $totalDays = (int)date('t', strtotime($startDate));
        $firstDayOfWeek = (int)date('N', strtotime($startDate)); // 1 (Senin) .. 7 (Minggu)
        
        // Group logs by date with sanitized projection (RPK-04 & RPK-08)
        $logsByDate = [];
        foreach ($logList as $item) {
            $tgl = !empty($item['tanggal_kegiatan']) ? $item['tanggal_kegiatan'] : (!empty($item['tanggal']) ? $item['tanggal'] : null);
            if ($tgl) {
                $linkBukti = trim((string)($item['link_bukti'] ?? ''));
                $safeBukti = ($linkBukti !== '' && preg_match('/^https?:\/\//i', $linkBukti)) ? $linkBukti : '';

                // Proyeksi kolom eksplisit yang hanya dibutuhkan modal, singkirkan kolom sensitif nilai_capaian / status_penilaian
                $cleanItem = [
                    'tanggal_kegiatan'   => $tgl,
                    'is_tambahan'        => !empty($item['is_tambahan']),
                    'deskripsi_kegiatan' => (string)($item['deskripsi_kegiatan'] ?? ($item['uraian_kegiatan'] ?? '')),
                    'indikator_kinerja'  => (string)($item['indikator_kinerja'] ?? '-'),
                    'jumlah_capaian'     => $item['jumlah_capaian'] ?? null,
                    'satuan'             => (string)($item['satuan'] ?? ''),
                    'link_bukti'         => $safeBukti,
                    'status'             => (string)($item['status'] ?? 'draft'),
                ];

                $logsByDate[$tgl][] = $cleanItem;
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

            $isRevisiAktif = !empty($revisiDatesMap[$dateStr]);

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
                'has_draft'        => $hasDraft || $isRevisiAktif,
                'has_terkirim'     => $hasTerkirim,
                'is_revisi_aktif'  => $isRevisiAktif,
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
                                ->orWhere('id', $userId) // fallback
                                ->first();
            if ($userDb) {
                $userId = $userDb['id'];
            }
        }

        $currentUser = $userModel->find($userId);
        if (!$currentUser) {
            return redirect()->to(site_url('login'))->with('error', 'Sesi telah berakhir. Silakan masuk kembali.');
        }

        // 1. Validasi Aksi: Hanya terima 'draft', 'submit', atau 'reset'
        $action = (string)$this->request->getPost('action');
        if (!in_array($action, ['draft', 'submit', 'reset'], true)) {
            return redirect()->back()->with('error', 'Aksi penilaian tidak valid.');
        }

        if ($action === 'submit') {
            $statusPenilaian = 'terbit';
        } elseif ($action === 'draft') {
            $statusPenilaian = 'draft';
        } else {
            $statusPenilaian = null; // Reset action
        }

        // 2. Validasi & Normalisasi Periode Bulan dan Tahun
        $bulanPost = $this->request->getPost('bulan');
        $tahunPost = $this->request->getPost('tahun');

        $evalMonth = !empty($bulanPost) && is_numeric($bulanPost) ? (int)$bulanPost : (int)(session()->get('penilaian_bulan') ?? date('n'));
        $evalYear  = !empty($tahunPost) && is_numeric($tahunPost) ? (int)$tahunPost : (int)(session()->get('penilaian_tahun') ?? date('Y'));

        if ($evalMonth < 1 || $evalMonth > 12) {
            return redirect()->back()->with('error', 'Periode bulan penilaian tidak valid.');
        }
        $currentYear = (int)date('Y');
        if ($evalYear < 2020 || $evalYear > ($currentYear + 2)) {
            return redirect()->back()->with('error', 'Periode tahun penilaian tidak valid.');
        }

        $stafPostId = $this->request->getPost('staf_id');
        $unitPost   = $this->request->getPost('unit_kerja');

        $isSelfEval = $this->request->getPost('is_self_eval') === '1' || (empty($stafPostId) || $stafPostId == $userId);

        if ($isSelfEval) {
            // Evaluasi mandiri HANYA diizinkan untuk Direktur (atau user tanpa atasan di sistem)
            if (!PenilaianKinerjaPolicy::canEvaluateSelf($currentUser)) {
                return redirect()->back()->with('error', 'Akses ditolak. Penilaian kinerja staf harus dilakukan oleh Atasan Langsung.');
            }
            $targetUserId = (int)$userId;
            $targetUserRecord = $currentUser;
        } else {
            $stafUser = $userModel->find($stafPostId);
            if (!$stafUser) {
                return redirect()->back()->with('error', 'Staf tidak ditemukan.');
            }
            if (!PenilaianKinerjaPolicy::canEvaluateStaf($currentUser, $stafUser)) {
                return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki izin menilai kinerja staf ini.');
            }
            $targetUserId = (int)$stafPostId;
            $targetUserRecord = $stafUser;
        }

        $laporanModel = new TargetKinerja();

        // 3. Validasi Prasyarat Target Kinerja: Target Staf harus sudah disetujui (kecuali reset atau evaluasi mandiri)
        if (!$isSelfEval && !empty($targetUserId) && $action !== 'reset') {
            $targetUserHasAtasan = $targetUserRecord && !empty($targetUserRecord['atasan_id']) && $targetUserRecord['role'] !== 'direktur';
            if ($targetUserHasAtasan) {
                $checkTargets = $laporanModel->where('user_id', $targetUserId)
                                             ->where('bulan', $evalMonth)
                                             ->where('tahun', $evalYear)
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

        // 4. Cek Batas Waktu Penilaian jika Saklar Batas Waktu Penilaian Aktif
        $settingModel = new SettingModel();
        $isDeadlineActive = $settingModel->getValue('enable_penilaian_deadline', '0') === '1';

        if ($isDeadlineActive && !hasRole('admin') && $action !== 'reset') {
            $batasPenilaian = (int) $settingModel->getValue('batas_penilaian_kinerja', 10);
            $evalMonthStart = new \DateTime(sprintf('%04d-%02d-01', $evalYear, $evalMonth));
            $currentMonthStart = new \DateTime(date('Y-m-01'));
            
            // Jika menilai periode lampau (sebelum bulan berjalan)
            if ($evalMonthStart < $currentMonthStart) {
                $nextMonthOfEval = clone $evalMonthStart;
                $nextMonthOfEval->modify('+1 month');
                $deadlineDay = min((int)$nextMonthOfEval->format('t'), max(1, $batasPenilaian));
                $deadlineDate = new \DateTime($nextMonthOfEval->format('Y-m-') . sprintf('%02d', $deadlineDay) . ' 23:59:59');
                
                $now = new \DateTime();
                if ($now > $deadlineDate) {
                    $bulanIndoList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $namaBulanEval = $bulanIndoList[$evalMonth - 1] ?? $evalMonth;
                    return redirect()->back()->with('error', "Gagal: Batas waktu penilaian kinerja untuk periode {$namaBulanEval} {$evalYear} telah berakhir pada tanggal " . $deadlineDate->format('d M Y') . ".");
                }
            }
        }

        // Sinkronisasi parameter sesi
        if (!$isSelfEval && !empty($targetUserId)) session()->set('penilaian_staf_id', $targetUserId);
        session()->set('penilaian_bulan', $evalMonth);
        session()->set('penilaian_tahun', $evalYear);
        if (!empty($unitPost)) session()->set('penilaian_unit_kerja', $unitPost);

        // 5. Ekstraksi dan Validasi Input Array Target & Tugas Tambahan
        $rawLaporanIds = $this->request->getPost('laporan_id');
        $rawNilaiCapaian = $this->request->getPost('nilai_capaian');

        $laporan_ids = is_array($rawLaporanIds) ? $rawLaporanIds : [];
        $nilai_capaian_arr = is_array($rawNilaiCapaian) ? $rawNilaiCapaian : [];

        $rawTambahanIds = $this->request->getPost('log_tambahan_id');
        $rawNilaiTambahan = $this->request->getPost('nilai_tambahan');
        $nilai_tambahan_gabungan = $this->request->getPost('nilai_tugas_tambahan_gabungan');

        $log_tambahan_ids = is_array($rawTambahanIds) ? $rawTambahanIds : [];
        $nilai_tambahan_arr = is_array($rawNilaiTambahan) ? $rawNilaiTambahan : [];

        // Tolak payload tanpa pekerjaan (mencegah false success)
        if (empty($laporan_ids) && empty($log_tambahan_ids)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada data target atau kegiatan untuk dinilai.');
        }

        // 6. Validasi ID Positif & Anti-Duplikasi Target Kinerja
        $cleanTargetIds = [];
        foreach ($laporan_ids as $lid) {
            if (!is_numeric($lid) || (int)$lid <= 0) {
                return redirect()->back()->withInput()->with('error', 'ID target kinerja tidak valid.');
            }
            $cleanTargetIds[] = (int)$lid;
        }
        if (count($cleanTargetIds) !== count(array_unique($cleanTargetIds))) {
            return redirect()->back()->withInput()->with('error', 'Format data tidak valid: terdapat duplikasi target.');
        }

        // 7. Validasi ID Positif & Anti-Duplikasi Tugas Tambahan
        $cleanTambahanIds = [];
        foreach ($log_tambahan_ids as $tid) {
            if (!is_numeric($tid) || (int)$tid <= 0) {
                return redirect()->back()->withInput()->with('error', 'ID tugas tambahan tidak valid.');
            }
            $cleanTambahanIds[] = (int)$tid;
        }
        if (count($cleanTambahanIds) !== count(array_unique($cleanTambahanIds))) {
            return redirect()->back()->withInput()->with('error', 'Format data tidak valid: terdapat duplikasi tugas tambahan.');
        }

        // 8. Verifikasi Baris Target Kinerja di Database (RPK-01)
        $oldTargetValues = [];
        $dataToUpdate = [];

        if (!empty($cleanTargetIds)) {
            $existingTargets = $laporanModel->whereIn('id', $cleanTargetIds)->findAll();
            if (count($existingTargets) !== count($cleanTargetIds)) {
                return redirect()->back()->withInput()->with('error', 'Sebagian target kinerja tidak ditemukan di sistem.');
            }

            foreach ($existingTargets as $ot) {
                // Validasi kepemilikan
                if ((int)$ot['user_id'] !== $targetUserId) {
                    return redirect()->back()->withInput()->with('error', 'Akses ditolak. Terdapat data penilaian yang tidak sesuai kepemilikan staf.');
                }
                // Validasi periode (RPK-01)
                if ((int)$ot['bulan'] !== $evalMonth || (int)$ot['tahun'] !== $evalYear) {
                    return redirect()->back()->withInput()->with('error', 'Terdapat target kinerja di luar periode yang dipilih.');
                }
                // Validasi status terkirim (kecuali reset)
                if ($action !== 'reset' && ($ot['status'] ?? '') !== 'terkirim') {
                    return redirect()->back()->withInput()->with('error', 'Target kinerja harus berstatus terkirim sebelum dinilai.');
                }

                $oldTargetValues[$ot['id']] = [
                    'id'               => $ot['id'],
                    'nilai_capaian'    => $ot['nilai_capaian'],
                    'status_penilaian' => $ot['status_penilaian']
                ];
            }

            // Proses nilai capaian per baris target
            foreach ($cleanTargetIds as $index => $idLaporan) {
                $valRaw = '';
                if (isset($nilai_capaian_arr[$idLaporan])) {
                    $valRaw = trim((string)$nilai_capaian_arr[$idLaporan]);
                } elseif (isset($nilai_capaian_arr[$index])) {
                    $valRaw = trim((string)$nilai_capaian_arr[$index]);
                }

                $valScore = null;
                $rowStatusPenilaian = $statusPenilaian;

                if ($action !== 'reset' && $valRaw !== '') {
                    $valClean = str_replace(',', '.', $valRaw);
                    if (!is_numeric($valClean) || !is_finite((float)$valClean)) {
                        return redirect()->back()->withInput()->with('error', 'Nilai capaian harus berupa angka antara 0 hingga 150.');
                    }
                    $numVal = (float)$valClean;
                    if ($numVal < 0.0 || $numVal > 150.0) {
                        return redirect()->back()->withInput()->with('error', 'Nilai capaian harus berada pada rentang 0 sampai 150.');
                    }
                    $valScore = $numVal;
                } else {
                    $valScore = null;
                    $rowStatusPenilaian = null;
                }

                $dataToUpdate[] = [
                    'id'               => $idLaporan,
                    'nilai_capaian'    => $valScore,
                    'status_penilaian' => $rowStatusPenilaian,
                ];
            }
        }

        // 9. Verifikasi Baris Tugas Tambahan di Database (RPK-01)
        $logTambahanModel = new LogTugasTambahan();
        $oldTambahanValues = [];
        $dataTambahanToUpdate = [];

        if (!empty($cleanTambahanIds)) {
            $existingTambahans = $logTambahanModel->whereIn('id', $cleanTambahanIds)->findAll();
            if (count($existingTambahans) !== count($cleanTambahanIds)) {
                return redirect()->back()->withInput()->with('error', 'Sebagian data tugas tambahan tidak ditemukan di sistem.');
            }

            foreach ($existingTambahans as $otm) {
                // Validasi kepemilikan
                if ((int)$otm['user_id'] !== $targetUserId) {
                    return redirect()->back()->withInput()->with('error', 'Akses ditolak. Terdapat data tugas tambahan yang tidak sesuai kepemilikan staf.');
                }
                // Validasi periode tanggal kegiatan (RPK-01)
                $tglKegiatan = $otm['tanggal_kegiatan'] ?? '';
                if (empty($tglKegiatan) || !strtotime($tglKegiatan)) {
                    return redirect()->back()->withInput()->with('error', 'Tanggal kegiatan tugas tambahan tidak valid.');
                }
                $tglMonth = (int)date('n', strtotime($tglKegiatan));
                $tglYear  = (int)date('Y', strtotime($tglKegiatan));
                if ($tglMonth !== $evalMonth || $tglYear !== $evalYear) {
                    return redirect()->back()->withInput()->with('error', 'Terdapat tugas tambahan di luar periode yang dipilih.');
                }
                // Validasi status terkirim (kecuali reset)
                if ($action !== 'reset' && ($otm['status'] ?? '') !== 'terkirim') {
                    return redirect()->back()->withInput()->with('error', 'Tugas tambahan harus berstatus terkirim sebelum dinilai.');
                }

                $oldTambahanValues[$otm['id']] = [
                    'id'               => $otm['id'],
                    'nilai_capaian'    => $otm['nilai_capaian'],
                    'status_penilaian' => $otm['status_penilaian']
                ];
            }

            // Validasi nilai tugas tambahan gabungan jika ada
            $sharedTmbScore = null;
            $hasSharedTmbScore = false;
            if ($nilai_tambahan_gabungan !== null && trim((string)$nilai_tambahan_gabungan) !== '') {
                $rawCleanTmb = str_replace(',', '.', trim((string)$nilai_tambahan_gabungan));
                if ($action !== 'reset') {
                    if (!is_numeric($rawCleanTmb) || !is_finite((float)$rawCleanTmb)) {
                        return redirect()->back()->withInput()->with('error', 'Nilai tugas tambahan harus berupa angka antara 0 hingga 150.');
                    }
                    $numTmb = (float)$rawCleanTmb;
                    if ($numTmb < 0.0 || $numTmb > 150.0) {
                        return redirect()->back()->withInput()->with('error', 'Nilai tugas tambahan harus berada pada rentang 0 sampai 150.');
                    }
                    $sharedTmbScore = $numTmb;
                }
                $hasSharedTmbScore = true;
            }

            foreach ($cleanTambahanIds as $index => $idTambahan) {
                $valScore = null;
                $rawScoreTmb = null;

                if ($hasSharedTmbScore) {
                    $rawScoreTmb = $sharedTmbScore;
                } elseif (isset($nilai_tambahan_arr[$idTambahan]) && trim((string)$nilai_tambahan_arr[$idTambahan]) !== '') {
                    $rawScoreTmb = trim((string)$nilai_tambahan_arr[$idTambahan]);
                } elseif (isset($nilai_tambahan_arr[$index]) && trim((string)$nilai_tambahan_arr[$index]) !== '') {
                    $rawScoreTmb = trim((string)$nilai_tambahan_arr[$index]);
                }

                $rowTmbStatus = $statusPenilaian;
                $rowTmbAppr = 'menunggu_persetujuan';

                if ($action !== 'reset' && $rawScoreTmb !== null && $rawScoreTmb !== '') {
                    if (is_numeric($rawScoreTmb)) {
                        $cleanedTmb = (float)$rawScoreTmb;
                    } else {
                        $rawClean = str_replace(',', '.', (string)$rawScoreTmb);
                        if (!is_numeric($rawClean) || !is_finite((float)$rawClean)) {
                            return redirect()->back()->withInput()->with('error', 'Nilai tugas tambahan harus berupa angka antara 0 hingga 150.');
                        }
                        $cleanedTmb = (float)$rawClean;
                    }

                    if ($cleanedTmb < 0.0 || $cleanedTmb > 150.0) {
                        return redirect()->back()->withInput()->with('error', 'Nilai tugas tambahan harus berada pada rentang 0 sampai 150.');
                    }

                    $valScore = $cleanedTmb;
                    $rowTmbStatus = $statusPenilaian;
                    $rowTmbAppr = ($statusPenilaian === 'terbit') ? 'disetujui' : 'menunggu_persetujuan';
                } else {
                    $valScore = null;
                    $rowTmbStatus = null;
                    $rowTmbAppr = 'menunggu_persetujuan';
                }

                $dataTambahanToUpdate[] = [
                    'id'               => $idTambahan,
                    'nilai_capaian'    => $valScore,
                    'status_penilaian' => $rowTmbStatus,
                    'status_approval'  => $rowTmbAppr
                ];
            }
        }

        // Deteksi perubahan nilai & status (RPK-09: Diff detection untuk audit & notifikasi)
        $hasAnyValueChange = false;
        $isFirstPublish = false;

        foreach ($dataToUpdate as $newTgt) {
            $tid = $newTgt['id'];
            $oldTgt = $oldTargetValues[$tid] ?? null;
            if ($oldTgt) {
                $oldScore = $oldTgt['nilai_capaian'] !== null ? (float)$oldTgt['nilai_capaian'] : null;
                $newScore = $newTgt['nilai_capaian'] !== null ? (float)$newTgt['nilai_capaian'] : null;
                $oldStatus = $oldTgt['status_penilaian'] ?? null;
                $newStatus = $newTgt['status_penilaian'] ?? null;

                if ($oldScore !== $newScore || $oldStatus !== $newStatus) {
                    $hasAnyValueChange = true;
                }
                if ($oldStatus !== 'terbit' && $newStatus === 'terbit') {
                    $isFirstPublish = true;
                }
            }
        }

        foreach ($dataTambahanToUpdate as $newTmb) {
            $tmbId = $newTmb['id'];
            $oldTmb = $oldTambahanValues[$tmbId] ?? null;
            if ($oldTmb) {
                $oldScore = $oldTmb['nilai_capaian'] !== null ? (float)$oldTmb['nilai_capaian'] : null;
                $newScore = $newTmb['nilai_capaian'] !== null ? (float)$newTmb['nilai_capaian'] : null;
                $oldStatus = $oldTmb['status_penilaian'] ?? null;
                $newStatus = $newTmb['status_penilaian'] ?? null;

                if ($oldScore !== $newScore || $oldStatus !== $newStatus) {
                    $hasAnyValueChange = true;
                }
                if ($oldStatus !== 'terbit' && $newStatus === 'terbit') {
                    $isFirstPublish = true;
                }
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Validasi ulang: pastikan target staf masih berstatus disetujui jika menerbitkan (RPK-06)
            if ($statusPenilaian === 'terbit' && !$isSelfEval && !empty($cleanTargetIds)) {
                $unapprovedCount = $laporanModel->whereIn('id', $cleanTargetIds)
                                                ->where('status_approval !=', 'disetujui')
                                                ->countAllResults();
                if ($unapprovedCount > 0) {
                    throw new \RuntimeException('Target kinerja staf belum disetujui atau sedang dalam proses revisi. Silakan muat ulang halaman.');
                }
            }

            // Periksa hasil kedua pembaruan batch secara eksplisit (RPK-01)
            if (!empty($dataToUpdate)) {
                $resTarget = $laporanModel->updateBatch($dataToUpdate, 'id');
                if ($resTarget === false) {
                    throw new \RuntimeException('Gagal memperbarui target kinerja.');
                }
            }
            if (!empty($dataTambahanToUpdate)) {
                $resTambahan = $logTambahanModel->updateBatch($dataTambahanToUpdate, 'id');
                if ($resTambahan === false) {
                    throw new \RuntimeException('Gagal memperbarui tugas tambahan.');
                }
            }

            $db->transComplete();
        } catch (\Throwable $e) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', '[PenilaianKinerja::store] Transaksi gagal: ' . $e->getMessage() . ' | Staf: ' . $targetUserId . ' | Bulan: ' . $evalMonth . '/' . $evalYear);
            $errMsg = ($e instanceof \RuntimeException) ? $e->getMessage() : 'Gagal menyimpan penilaian kinerja. Silakan coba lagi.';
            return redirect()->back()->withInput()->with('error', $errMsg);
        }

        if ($db->transStatus() === false) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', '[PenilaianKinerja::store] transStatus false | Staf: ' . $targetUserId . ' | Bulan: ' . $evalMonth . '/' . $evalYear);
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan penilaian kinerja. Silakan coba lagi.');
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
                ? 'Nilai kinerja mandiri berhasil di-reset.' 
                : 'Penilaian kinerja staf berhasil di-reset.';
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
                // Failsafe notifikasi & cegah notifikasi duplikat jika tidak ada perubahan nilai (RPK-09)
                if ($isFirstPublish || $hasAnyValueChange) {
                    helper('notification');
                    try {
                        $bulanIndoArr = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $namaBulanEval = $bulanIndoArr[(int)$evalMonth - 1] ?? "Bulan {$evalMonth}";
                        $atasanName = session()->get('nama_lengkap') ?? 'Atasan Langsung';

                        send_notification(
                            $targetUserId,
                            'Penilaian Kinerja Diterbitkan',
                            "{$atasanName} telah menerbitkan Nilai Kinerja Bulanan Anda untuk periode {$namaBulanEval} {$evalYear}. Silakan periksa rekapitulasi nilai dan capaian Anda.",
                            site_url('penilaian-kinerja?bulan=' . $evalMonth . '&tahun=' . $evalYear . '&active_tab=individu')
                        );
                    } catch (\Throwable $e) {
                        log_message('error', '[PenilaianKinerjaController::store:notification] ' . $e->getMessage() . ' | Staf: ' . $targetUserId);
                    }
                }
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
                ? 'Nilai kinerja mandiri berhasil disimpan sebagai draf.' 
                : 'Penilaian kinerja staf berhasil disimpan sebagai draf.';
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
                                ->orWhere('id', $currentUserId) // fallback
                                ->first();
            if ($userDb) {
                $currentUserId = $userDb['id'];
            }
        }

        $currentUser = $userModel->select('id, nama_lengkap, nip, username, role, atasan_id, unit')->find($currentUserId);
        if (!$currentUser) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Sesi pengguna tidak valid.']);
        }

        $userId = (int)$this->request->getGet('user_id');
        $bulan  = (int)$this->request->getGet('bulan');
        $tahun  = (int)$this->request->getGet('tahun');

        if (!$userId || !$bulan || !$tahun) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Parameter tidak lengkap']);
        }

        $targetUser = $userModel->select('id, nama_lengkap, nip, username, role, atasan_id, unit')->find($userId);
        if (!$targetUser) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Pegawai tidak ditemukan.']);
        }

        // Proteksi IDOR & Otorisasi Hierarki via PenilaianKinerjaPolicy
        if (!PenilaianKinerjaPolicy::canViewStafRekap($currentUser, $targetUser)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak. Anda tidak berhak melihat data kinerja pegawai ini.']);
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
            $trendNilai[] = ($stat['dinilai'] > 0) ? round((float)$stat['rata_rata'], 2) : null;
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
