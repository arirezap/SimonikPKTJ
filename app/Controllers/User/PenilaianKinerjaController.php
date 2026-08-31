<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Controllers\Traits\KinerjaBatchTrait;
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
        
        // Gunakan PRG pattern agar terhindar dari form resubmission dan error 403
        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            if ($this->request->getPost('bulan')) session()->set('penilaian_bulan', $this->request->getPost('bulan'));
            if ($this->request->getPost('tahun')) session()->set('penilaian_tahun', $this->request->getPost('tahun'));
            if (isset($_POST['staf_id'])) session()->set('penilaian_staf_id', $this->request->getPost('staf_id'));
            if (isset($_POST['unit_kerja'])) session()->set('penilaian_unit_kerja', $this->request->getPost('unit_kerja'));
            
            $hash = '';
            if ($this->request->getPost('active_tab')) {
                $activeTab = $this->request->getPost('active_tab');
                $hash = '#' . $activeTab;
                // Jangan hapus penilaian_staf_id agar jika user kembali ke tab staf, stafnya masih terpilih
            }
            
            return redirect()->to(site_url('penilaian-kinerja') . $hash);
        }

        $bulanTerpilih = session()->get('penilaian_bulan') ?? date('n');
        $tahunTerpilih = session()->get('penilaian_tahun') ?? date('Y');
        $stafIdTerpilih = session()->get('penilaian_staf_id') ?? '';
        $unitKerjaTerpilih = session()->get('penilaian_unit_kerja') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $role = session()->get('role');
        $isSuper = hasAnyRole(['admin', 'direktur', 'wadir']);

        // Ambil daftar unit kerja untuk filter (Hanya untuk Admin Utama)
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
        if ($isPenilai) {
            $rekapDataStaf = $laporanModel->getTargetWithRealization($stafIdTerpilih, $bulanTerpilih, $tahunTerpilih, true);
            $rawLogHarian = $logModel->getLogByMonth($stafIdTerpilih, $bulanTerpilih, $tahunTerpilih, true);
            $tugasTambahanStaf = $logTambahanModel->getLogByMonth($stafIdTerpilih, $bulanTerpilih, $tahunTerpilih, true);
            $logHarianStaf = $this->combineAndSortLogs($rawLogHarian, $tugasTambahanStaf);
        }

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
            'rekap_dashboard' => $rekapDashboard,
            'is_super' => $isSuper,
            'daftar_unit' => $daftarUnit,
            'unit_kerja_terpilih' => $unitKerjaTerpilih
        ];

        return view('user/penilaian_kinerja/index', $data);
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
        $statusPenilaian = ($action === 'submit') ? 'terbit' : 'draft';

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
            // Otorisasi: Pastikan penilai adalah Atasan Langsung dari staf atau Pimpinan/Superadmin/Kepegawaian
            $stafUser = $userModel->find($stafPostId);
            if (!$stafUser) {
                return redirect()->back()->with('error', 'Staf tidak ditemukan.');
            }
            $isAtasan = !empty($stafUser['atasan_id']) && ($stafUser['atasan_id'] == $userId);
            if (!hasAnyRole(['admin', 'direktur', 'wadir', 'kepegawaian']) && !$isAtasan) {
                return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki izin menilai kinerja staf ini.');
            }
            $targetUserId = $stafPostId;
            $targetUserRecord = $stafUser;
        }

        // Cek Batas Waktu Penilaian jika Saklar Batas Waktu Penilaian Aktif
        $settingModel = new SettingModel();
        $isDeadlineActive = $settingModel->getValue('enable_penilaian_deadline', '0') === '1';
        $evalYear  = !empty($tahunPost) ? (int)$tahunPost : (int)(session()->get('penilaian_tahun') ?? date('Y'));
        $evalMonth = !empty($bulanPost) ? (int)$bulanPost : (int)(session()->get('penilaian_bulan') ?? date('n'));

        if ($isDeadlineActive && !hasRole('admin')) {
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
                if ($valRaw !== '') {
                    $valClean = str_replace(',', '.', $valRaw);
                    if (is_numeric($valClean)) {
                        $valScore = min(150.0, max(0.0, (float)$valClean));
                    }
                } elseif ($statusPenilaian === 'terbit') {
                    // Jika diterbitkan tapi nilai kosong, atasan telah mengonfirmasi untuk menghitung dengan nilai 0
                    $valScore = 0.0;
                }

                $rowUpdate = [
                    'id' => $idLaporan,
                    'nilai_capaian' => $valScore,
                    'status_penilaian' => $statusPenilaian,
                ];

                $dataToUpdate[] = $rowUpdate;
            }
        }

        $oldTargetValues = [];
        if (!empty($dataToUpdate)) {
            $targetIds = array_column($dataToUpdate, 'id');
            $oldTargets = $laporanModel->whereIn('id', $targetIds)->findAll();
            foreach ($oldTargets as $ot) {
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

                if ($rawScoreTmb !== null && $rawScoreTmb !== '') {
                    $cleanedTmb = str_replace(',', '.', $rawScoreTmb);
                    if (is_numeric($cleanedTmb)) {
                        $valScore = min(150.0, max(0.0, (float)$cleanedTmb));
                    }
                } elseif ($statusPenilaian === 'terbit') {
                    // Jika diterbitkan tapi nilai tugas tambahan kosong, dihitung dengan nilai 0
                    $valScore = 0.0;
                }

                $rowUpdate = [
                    'id' => $idTambahan,
                    'nilai_capaian' => $valScore,
                    'status_penilaian' => $statusPenilaian,
                    'status_approval' => ($statusPenilaian === 'terbit') ? 'disetujui' : 'menunggu_persetujuan'
                ];
                $dataTambahanToUpdate[] = $rowUpdate;
            }
        }

        $oldTambahanValues = [];
        if (!empty($dataTambahanToUpdate)) {
            $tambahanIds = array_column($dataTambahanToUpdate, 'id');
            $oldTambahans = $logTambahanModel->whereIn('id', $tambahanIds)->findAll();
            foreach ($oldTambahans as $otm) {
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
            return redirect()->back()->with('error', 'Gagal menyimpan penilaian kinerja ke database: ' . $e->getMessage());
        }

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan penilaian kinerja karena kesalahan basis data.');
        }

        $stafName = $targetUserRecord['nama_lengkap'] ?? $targetUserRecord['nama'] ?? 'Pegawai';

        // Audit log & Notifikasi
        if ($statusPenilaian === 'terbit' && (!empty($dataToUpdate) || !empty($dataTambahanToUpdate))) {
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
                send_notification(
                    $targetUserId,
                    'Penilaian Kinerja Diterbitkan',
                    'Atasan telah menerbitkan Nilai Kinerja Bulanan Anda.',
                    site_url('penilaian-kinerja')
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

        // Proteksi IDOR: Pastikan user hanya dapat mengakses datanya sendiri, atau requester adalah atasan langsung/manajemen
        if ($userId != $currentUserId && !hasAnyRole(['admin', 'direktur', 'wadir', 'manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kepegawaian'])) {
            $daftarStaf = $userModel->getAllStaf($currentUserId);
            $allowedIds = array_column($daftarStaf, 'id');
            if (!in_array($userId, $allowedIds)) {
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
