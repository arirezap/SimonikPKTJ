<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\LogKegiatanHarian;
use App\Models\User;

class PenilaianKinerjaController extends BaseController
{
    public function index()
    {
        $logModel = new \App\Models\LogKegiatanHarian();
        $laporanModel = new \App\Models\LaporanHarian();
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
        $isSuper = hasAnyRole(['admin']);

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

        $logTambahanModel = new \App\Models\LogTugasTambahan();

        // Ambil rekap data sebulan penuh untuk diri sendiri (selalu ada)
        $rekapDataSendiri = $laporanModel->getTargetWithRealization($userId, $bulanTerpilih, $tahunTerpilih);
        $rawLogSendiri = $logModel->getLogByMonth($userId, $bulanTerpilih, $tahunTerpilih);
        $tugasTambahanSendiri = $logTambahanModel->getLogByMonth($userId, $bulanTerpilih, $tahunTerpilih);
        
        // Gabungkan Tugas Pokok dan Tugas Tambahan Diri Sendiri untuk Tabel Bagian C
        $combinedSendiri = [];
        foreach ($rawLogSendiri as $l) {
            $l['is_tambahan'] = false;
            $combinedSendiri[] = $l;
        }
        foreach ($tugasTambahanSendiri as $tmb) {
            $tmb['is_tambahan'] = true;
            $tmb['indikator_kinerja'] = 'Tugas Tambahan';
            $combinedSendiri[] = $tmb;
        }
        usort($combinedSendiri, function($a, $b) {
            if ($a['tanggal_kegiatan'] !== $b['tanggal_kegiatan']) {
                return strtotime($a['tanggal_kegiatan']) <=> strtotime($b['tanggal_kegiatan']);
            }
            return ($a['is_tambahan'] ? 1 : 0) <=> ($b['is_tambahan'] ? 1 : 0);
        });
        $logHarianSendiri = $combinedSendiri;
        
        // Ambil rekap data staf terpilih (jika ada)
        $rekapDataStaf = [];
        $logHarianStaf = [];
        $tugasTambahanStaf = [];
        if ($isPenilai) {
            $rekapDataStaf = $laporanModel->getTargetWithRealization($stafIdTerpilih, $bulanTerpilih, $tahunTerpilih, true);
            $rawLogHarian = $logModel->getLogByMonth($stafIdTerpilih, $bulanTerpilih, $tahunTerpilih, true);
            $tugasTambahanStaf = $logTambahanModel->getLogByMonth($stafIdTerpilih, $bulanTerpilih, $tahunTerpilih, true);

            // Gabungkan Tugas Pokok dan Tugas Tambahan untukTabel Bukti & Activity Log Staf (Bagian C)
            $combinedLogs = [];
            foreach ($rawLogHarian as $l) {
                $l['is_tambahan'] = false;
                $combinedLogs[] = $l;
            }
            foreach ($tugasTambahanStaf as $tmb) {
                $tmb['is_tambahan'] = true;
                $tmb['indikator_kinerja'] = 'Tugas Tambahan';
                $combinedLogs[] = $tmb;
            }
            usort($combinedLogs, function($a, $b) {
                if ($a['tanggal_kegiatan'] !== $b['tanggal_kegiatan']) {
                    return strtotime($a['tanggal_kegiatan']) <=> strtotime($b['tanggal_kegiatan']);
                }
                return ($a['is_tambahan'] ? 1 : 0) <=> ($b['is_tambahan'] ? 1 : 0);
            });
            $logHarianStaf = $combinedLogs;
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

        $laporanModel = new \App\Models\LaporanHarian();
        $action = $this->request->getPost('action');
        $statusPenilaian = ($action === 'submit') ? 'terbit' : 'draft';

        $stafPostId = $this->request->getPost('staf_id');
        $bulanPost  = $this->request->getPost('bulan');
        $tahunPost  = $this->request->getPost('tahun');
        $unitPost   = $this->request->getPost('unit_kerja');

        if (!empty($stafPostId)) session()->set('penilaian_staf_id', $stafPostId);
        if (!empty($bulanPost))  session()->set('penilaian_bulan', $bulanPost);
        if (!empty($tahunPost))  session()->set('penilaian_tahun', $tahunPost);
        if (!empty($unitPost))   session()->set('penilaian_unit_kerja', $unitPost);

        $laporan_ids = $this->request->getPost('laporan_id');
        $nilai_capaian_arr = $this->request->getPost('nilai_capaian');

        $dataToUpdate = [];

        if ($laporan_ids) {
            foreach ($laporan_ids as $index => $idLaporan) {
                if (empty($idLaporan)) continue;

                $rowUpdate = [
                    'id' => $idLaporan,
                    'nilai_capaian' => $nilai_capaian_arr[$index] !== '' ? $nilai_capaian_arr[$index] : null,
                    'status_penilaian' => $statusPenilaian,
                ];

                $dataToUpdate[] = $rowUpdate;
            }
        }

        if (!empty($dataToUpdate)) {
            $laporanModel->updateBatch($dataToUpdate, 'id');
        }

        // --- PENILAIAN TUGAS TAMBAHAN ---
        $logTambahanModel = new \App\Models\LogTugasTambahan();
        $log_tambahan_ids = $this->request->getPost('log_tambahan_id');
        $nilai_tambahan_arr = $this->request->getPost('nilai_tambahan');
        $nilai_tambahan_gabungan = $this->request->getPost('nilai_tugas_tambahan_gabungan');

        $dataTambahanToUpdate = [];
        if ($log_tambahan_ids) {
            foreach ($log_tambahan_ids as $index => $idTambahan) {
                if (empty($idTambahan)) continue;

                $valScore = null;
                if ($nilai_tambahan_gabungan !== null && $nilai_tambahan_gabungan !== '') {
                    $valScore = (float)$nilai_tambahan_gabungan;
                } elseif (isset($nilai_tambahan_arr[$index]) && $nilai_tambahan_arr[$index] !== '') {
                    $valScore = (float)$nilai_tambahan_arr[$index];
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
        if (!empty($dataTambahanToUpdate)) {
            $logTambahanModel->updateBatch($dataTambahanToUpdate, 'id');
        }

        // Audit log & Notifikasi hanya jika benar-benar diterbitkan (submit)
        if ($statusPenilaian === 'terbit' && (!empty($dataToUpdate) || !empty($dataTambahanToUpdate))) {
            log_audit('APPROVE', 'laporan_harian/log_tugas_tambahan', 'batch_nilai', null, [$dataToUpdate, $dataTambahanToUpdate]);
            
            $targetUserId = session()->get('penilaian_staf_id');
            if (!$targetUserId && !empty($dataToUpdate)) {
                $firstLaporan = $laporanModel->find($dataToUpdate[0]['id']);
                $targetUserId = $firstLaporan['user_id'] ?? null;
            }
            if (!$targetUserId && !empty($dataTambahanToUpdate)) {
                $firstTambahan = $logTambahanModel->find($dataTambahanToUpdate[0]['id']);
                $targetUserId = $firstTambahan['user_id'] ?? null;
            }

            if (!empty($targetUserId) && $targetUserId != $userId) {
                helper('notification');
                send_notification(
                    $targetUserId,
                    'Penilaian Kinerja Diterbitkan',
                    'Atasan telah menerbitkan Nilai Kinerja Bulanan Anda.',
                    site_url('penilaian-kinerja')
                );
            }
            $pesan = 'Penilaian kinerja staf berhasil diterbitkan.';
        } else {
            $pesan = 'Penilaian kinerja berhasil disimpan sementara (Draf). Penilaian belum dipublikasikan ke staf.';
        }

        return redirect()->to(site_url('penilaian-kinerja') . '#tab-penilaian-atasan')
                         ->with('success', $pesan);
    }

    public function getChartDataApi()
    {
        // if (!$this->request->isAJAX()) return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request']);

        $userId = $this->request->getGet('user_id');
        $bulan = (int)$this->request->getGet('bulan');
        $tahun = (int)$this->request->getGet('tahun');

        if (!$userId || !$bulan || !$tahun) return $this->response->setJSON(['error' => 'Missing parameters']);

        $laporanModel = new \App\Models\LaporanHarian();
        $logTambahanModel = new \App\Models\LogTugasTambahan();
        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $trendBulan = [];
        $trendNilai = [];
        $stafIdTerpilih = session()->get('penilaian_staf_id');
        $bulanTerpilih = session()->get('penilaian_bulan');
        $tahunTerpilih = session()->get('penilaian_tahun');
        
        for ($i = 5; $i >= 0; $i--) {
            $timestamp = mktime(0, 0, 0, $bulan - $i, 1, $tahun);
            $m = (int)date('n', $timestamp);
            $y = (int)date('Y', $timestamp);
            $namaBulan = $bulanIndo[$m - 1] . ' ' . substr($y, 2, 2);
            
            // Hitung rata-rata: (Total Nilai Laporan + Total Nilai Tambahan) / (Jml Laporan Dinilai + Jml Tambahan Dinilai)
            $jmlDinilaiPokok = 0;
            $totalNilaiPokok = 0;
            
            $rekapDataStafAfter = $laporanModel->where('user_id', $userId)
                                          ->where('bulan', $m)
                                          ->where('tahun', $y)
                                          ->findAll();

            foreach ($rekapDataStafAfter as $rd) {
                if ($rd['nilai_capaian'] !== null && $rd['nilai_capaian'] !== '') {
                    $jmlDinilaiPokok++;
                    $totalNilaiPokok += (float)$rd['nilai_capaian'];
                }
            }

            $jmlDinilaiTambahan = 0;
            $totalNilaiTambahan = 0;
            $tugasTambahanStafAfter = $logTambahanModel->getLogByMonth($userId, $m, $y);
            foreach ($tugasTambahanStafAfter as $tt) {
                if ($tt['nilai_capaian'] !== null && $tt['nilai_capaian'] !== '') {
                    $jmlDinilaiTambahan++;
                    $totalNilaiTambahan += (float)$tt['nilai_capaian'];
                }
            }

            $jmlTotal = $jmlDinilaiPokok + $jmlDinilaiTambahan;
            $rataRata = 0;
            if ($jmlTotal > 0) {
                $rataRata = (float)(($totalNilaiPokok + $totalNilaiTambahan) / $jmlTotal);
            }

            $trendBulan[] = $namaBulan;
            $trendNilai[] = round($rataRata, 2);
        }

        $rekapData = $laporanModel->getTargetWithRealization($userId, $bulan, $tahun);
        
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
}
