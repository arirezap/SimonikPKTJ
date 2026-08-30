<?php

namespace App\Controllers\Kepegawaian;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\LaporanHarian;
use App\Models\LogTugasTambahan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Dompdf\Dompdf;
use Dompdf\Options;

class DashboardKepegawaian extends BaseController
{
    public function index()
    {
        // Role yang diizinkan: Kepegawaian, Admin, Direktur, Wadir, dan Kabag
        if (!hasAnyRole(['kepegawaian', 'admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk'])) {
            return redirect()->to('/dashboard');
        }

        helper(['avatar']);

        $userModel = new User();
        $laporanModel = new LaporanHarian();
        $logTambahanModel = new LogTugasTambahan();

        $bulanTerpilih = $this->request->getGet('bulan') ?? date('n');
        $tahunTerpilih = $this->request->getGet('tahun') ?? date('Y');
        $unitFilter    = $this->request->getGet('unit') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = ($bulanTerpilih === 'all') ? 'Sepanjang Tahun' : ($bulanIndo[(int)$bulanTerpilih - 1] ?? '');

        // Ambil daftar unit untuk filter
        $units = $userModel->select('unit')->distinct()->where('unit !=', null)->where('unit !=', '')->orderBy('unit', 'ASC')->findAll();
        $daftarUnit = array_column($units, 'unit');

        // Ambil semua user (kecuali admin)
        $builder = $userModel->where('role !=', 'admin');
        if (!empty($unitFilter)) {
            $builder = $builder->where('unit', $unitFilter);
        }
        $semuaPegawai = $builder->orderBy('nama_lengkap', 'ASC')->findAll();

        // Hitung rekap kinerja tiap pegawai
        $rekapKinerja = [];
        foreach ($semuaPegawai as $pegawai) {
            $rekapData = $laporanModel->getTargetWithRealization($pegawai['id'], $bulanTerpilih, $tahunTerpilih);
            
            $jmlDinilai = 0;
            $totalNilai = 0;
            $jmlTarget  = count($rekapData);
            
            $rataRataPerBulan = array_fill(1, 12, null);
            $hasTugasTambahan = false;
            $scoreTambahan = null;

            if ($bulanTerpilih === 'all') {
                $targetsPerBulan = array_fill(1, 12, 0);
                $dinilaiPerBulan = array_fill(1, 12, 0);
                $nilaiPerBulan = array_fill(1, 12, 0);

                foreach ($rekapData as $rd) {
                    $b = (int)$rd['bulan'];
                    $targetsPerBulan[$b]++;
                    
                    if (!empty($rd['nilai_capaian'])) {
                        $dinilaiPerBulan[$b]++;
                        $nilaiPerBulan[$b] += (float)$rd['nilai_capaian'];
                        
                        $jmlDinilai++;
                        $totalNilai += (float)$rd['nilai_capaian'];
                    }
                }

                // Cek Tugas Tambahan per bulan
                for ($m = 1; $m <= 12; $m++) {
                    $tmbBulan = $logTambahanModel->getLogByMonth($pegawai['id'], $m, $tahunTerpilih, true);
                    if (!empty($tmbBulan)) {
                        $targetsPerBulan[$m]++;
                        $scoreM = null;
                        foreach ($tmbBulan as $tmb) {
                            if ($tmb['nilai_capaian'] !== null) {
                                $scoreM = (float)$tmb['nilai_capaian'];
                                break;
                            }
                        }
                        if ($scoreM !== null) {
                            $dinilaiPerBulan[$m]++;
                            $nilaiPerBulan[$m] += $scoreM;
                            $jmlDinilai++;
                            $totalNilai += $scoreM;
                        }
                    }
                }

                for ($i = 1; $i <= 12; $i++) {
                    if ($targetsPerBulan[$i] > 0) {
                        $rataRataPerBulan[$i] = $dinilaiPerBulan[$i] > 0 ? round($nilaiPerBulan[$i] / $dinilaiPerBulan[$i], 2) : 0;
                    }
                }
                $validMonths = array_filter($rataRataPerBulan, fn($v) => $v !== null);
                $rataRata = count($validMonths) > 0 ? round(array_sum($validMonths) / count($validMonths), 2) : 0;
                $jmlTotalKomponen = array_sum($targetsPerBulan);
            } else {
                foreach ($rekapData as $rd) {
                    if (!empty($rd['nilai_capaian'])) {
                        $jmlDinilai++;
                        $totalNilai += (float)$rd['nilai_capaian'];
                    }
                }

                // Cek Tugas Tambahan pada bulan terpilih
                $tugasTambahan = $logTambahanModel->getLogByMonth($pegawai['id'], $bulanTerpilih, $tahunTerpilih, true);
                if (!empty($tugasTambahan)) {
                    $hasTugasTambahan = true;
                    foreach ($tugasTambahan as $tmb) {
                        if ($tmb['nilai_capaian'] !== null) {
                            $scoreTambahan = (float)$tmb['nilai_capaian'];
                            break;
                        }
                    }
                    if ($scoreTambahan !== null) {
                        $jmlDinilai++;
                        $totalNilai += $scoreTambahan;
                    }
                }

                $jmlTotalKomponen = $jmlTarget + ($hasTugasTambahan ? 1 : 0);
                $rataRata = $jmlDinilai > 0 ? round($totalNilai / $jmlDinilai, 2) : 0;
            }

            $rekapKinerja[] = [
                'pegawai'             => $pegawai,
                'jumlah_rhk'          => $jmlTarget,
                'jumlah_komponen'     => $jmlTotalKomponen,
                'rhk_dinilai'         => $jmlDinilai,
                'has_tugas_tambahan'  => $hasTugasTambahan,
                'score_tambahan'      => $scoreTambahan,
                'rata_rata'           => $rataRata,
                'rata_rata_per_bulan' => $rataRataPerBulan
            ];
        }

        // Urutkan dari rata-rata tertinggi
        usort($rekapKinerja, function ($a, $b) {
            return $b['rata_rata'] <=> $a['rata_rata'];
        });

        // Hitung statistik instansi
        $sudahDinilai = 0;
        $belumDinilai = 0;
        $sumRataInstansi = 0;
        foreach ($rekapKinerja as $r) {
            if ($r['rhk_dinilai'] > 0) {
                $sudahDinilai++;
                $sumRataInstansi += $r['rata_rata'];
            } else {
                $belumDinilai++;
            }
        }
        $totalPegawai = count($rekapKinerja);
        $rataRataDinilai = $sudahDinilai > 0 ? round($sumRataInstansi / $sudahDinilai, 2) : 0;
        $rataRataKeseluruhan = $totalPegawai > 0 ? round($sumRataInstansi / $totalPegawai, 2) : 0;

        $data = [
            'title'                 => 'Rekap Kinerja Kepegawaian',
            'rekap_kinerja'         => $rekapKinerja,
            'bulan_terpilih'        => $bulanTerpilih,
            'tahun_terpilih'        => $tahunTerpilih,
            'nama_bulan'            => $namaBulan,
            'bulan_indo'            => $bulanIndo,
            'daftar_unit'           => $daftarUnit,
            'unit_filter'           => $unitFilter,
            'sudah_dinilai'         => $sudahDinilai,
            'belum_dinilai'         => $belumDinilai,
            'rata_rata_dinilai'     => $rataRataDinilai,
            'rata_rata_keseluruhan' => $rataRataKeseluruhan,
            'rata_rata_instansi'    => $rataRataDinilai // Backward compatibility
        ];

        return view('kepegawaian/rekap_kinerja', $data);
    }

    /**
     * Helper untuk menentukan predikat kinerja
     */
    private function getPredikatKinerja($dinilaiCount, $score)
    {
        if ($dinilaiCount == 0 && (float)$score == 0) {
            return ['text' => 'Belum Dinilai', 'fill' => 'F1F5F9', 'color' => '475569'];
        }
        $score = (float)$score;
        if ($score >= 100) {
            return ['text' => 'Sangat Baik', 'fill' => 'D1FAE5', 'color' => '065F46'];
        } elseif ($score >= 80) {
            return ['text' => 'Baik', 'fill' => 'DBEAFE', 'color' => '1E40AF'];
        } elseif ($score >= 60) {
            return ['text' => 'Butuh Perbaikan', 'fill' => 'F1F5F9', 'color' => '334155'];
        } elseif ($score >= 50) {
            return ['text' => 'Kurang', 'fill' => 'FEF3C7', 'color' => '92400E'];
        } else {
            return ['text' => 'Sangat Kurang', 'fill' => 'FEE2E2', 'color' => '991B1B'];
        }
    }

    /**
     * Helper Bulk Fetching Data Kinerja Pegawai (Ultra Fast Batch Query)
     * Menggantikan N+1 query loop menjadi 2 query efisien dalam satu roundtrip.
     */
    private function getBulkRekapKinerja($semuaPegawai, $userMap, $bulanTerpilih, $tahunTerpilih, $bulanIndo, $namaBulan, $includeRincianDetail = false)
    {
        $userIds = array_column($semuaPegawai, 'id');
        if (empty($userIds)) {
            return ['rekapKinerja' => [], 'rincianDetail' => []];
        }

        $laporanModel = new LaporanHarian();
        $logTambahanModel = new LogTugasTambahan();

        // 1. Batch Query Target Kinerja + Total Realisasi
        $targetBuilder = $laporanModel->select('target_kinerja_bulanan.*, IFNULL(SUM(log_kegiatan_harian.jumlah_capaian), 0) as total_realisasi')
            ->join('log_kegiatan_harian', "log_kegiatan_harian.target_id = target_kinerja_bulanan.id AND log_kegiatan_harian.status = 'terkirim'", 'left')
            ->whereIn('target_kinerja_bulanan.user_id', $userIds)
            ->where('target_kinerja_bulanan.tahun', $tahunTerpilih)
            ->where('target_kinerja_bulanan.status', 'terkirim');

        if ($bulanTerpilih !== 'all' && $bulanTerpilih !== '') {
            $targetBuilder->where('target_kinerja_bulanan.bulan', $bulanTerpilih);
        }
        $allTargets = $targetBuilder->groupBy('target_kinerja_bulanan.id')->findAll();

        $targetsByUser = [];
        foreach ($allTargets as $tr) {
            $targetsByUser[$tr['user_id']][] = $tr;
        }

        // 2. Batch Query Tugas Tambahan
        $tmbBuilder = $logTambahanModel->whereIn('user_id', $userIds)
            ->where('status', 'terkirim');

        if ($bulanTerpilih !== 'all' && $bulanTerpilih !== '') {
            $startDate = sprintf('%04d-%02d-01', (int)$tahunTerpilih, (int)$bulanTerpilih);
            $endDate   = date('Y-m-t', strtotime($startDate));
        } else {
            $startDate = sprintf('%04d-01-01', (int)$tahunTerpilih);
            $endDate   = sprintf('%04d-12-31', (int)$tahunTerpilih);
        }
        $tmbBuilder->where('tanggal_kegiatan >=', $startDate)
                   ->where('tanggal_kegiatan <=', $endDate);

        $allTmb = $tmbBuilder->findAll();

        $tmbByUser = [];
        foreach ($allTmb as $tmb) {
            $m = (int)date('n', strtotime($tmb['tanggal_kegiatan']));
            $tmbByUser[$tmb['user_id']][$m][] = $tmb;
        }

        // 3. Proses Agregasi Kinerja di Memori (O(N) Execution)
        $rekapKinerja = [];
        $rincianDetail = [];

        foreach ($semuaPegawai as $pegawai) {
            $uId = $pegawai['id'];
            $atasanNama = isset($pegawai['atasan_id']) && isset($userMap[$pegawai['atasan_id']]) 
                ? $userMap[$pegawai['atasan_id']]['nama_lengkap'] 
                : '-';
            $pegawai['atasan_nama'] = $atasanNama;

            $userTargets = $targetsByUser[$uId] ?? [];
            $jmlDinilai = 0;
            $totalNilai = 0;
            $jmlTarget  = count($userTargets);
            $rataRataPerBulan = array_fill(1, 12, null);
            $hasTugasTambahan = false;
            $scoreTambahan = null;

            if ($bulanTerpilih === 'all') {
                $targetsPerBulan = array_fill(1, 12, 0);
                $dinilaiPerBulan = array_fill(1, 12, 0);
                $nilaiPerBulan   = array_fill(1, 12, 0);

                foreach ($userTargets as $rd) {
                    $b = (int)$rd['bulan'];
                    $targetsPerBulan[$b]++;
                    
                    if (!empty($rd['nilai_capaian'])) {
                        $dinilaiPerBulan[$b]++;
                        $nilaiPerBulan[$b] += (float)$rd['nilai_capaian'];
                        $jmlDinilai++;
                        $totalNilai += (float)$rd['nilai_capaian'];
                    }

                    if ($includeRincianDetail) {
                        $targetVal = round((float)$rd['target_bulanan'], 4);
                        $realVal = round((float)$rd['total_realisasi'], 4);
                        $gapVal = round($realVal - $targetVal, 4);
                        $nilaiRhk = ($rd['nilai_capaian'] !== null && $rd['nilai_capaian'] !== '') ? (float)$rd['nilai_capaian'] : null;

                        $rincianDetail[] = [
                            'nama'       => $pegawai['nama_lengkap'],
                            'nip'        => $pegawai['nip'] ?? '-',
                            'unit'       => $pegawai['unit'] ?? '-',
                            'bulan'      => $bulanIndo[$b - 1] ?? "Bulan {$b}",
                            'tipe'       => 'RHK Pokok',
                            'sasaran'    => $rd['sasaran_program'] ?? '-',
                            'indikator'  => $rd['indikator_kinerja'] ?? '-',
                            'target'     => $targetVal,
                            'realisasi'  => $realVal,
                            'satuan'     => $rd['satuan'] ?? '-',
                            'gap'        => $gapVal,
                            'nilai'      => $nilaiRhk,
                            'predikat'   => $this->getPredikatKinerja($nilaiRhk !== null ? 1 : 0, $nilaiRhk ?? 0)['text'],
                            'bukti'      => $rd['link_bukti'] ?? '-',
                            'status_app' => $rd['status_penilaian'] ?? 'menunggu'
                        ];
                    }
                }

                // Cek Tugas Tambahan per bulan
                for ($m = 1; $m <= 12; $m++) {
                    $tmbBulan = $tmbByUser[$uId][$m] ?? [];
                    if (!empty($tmbBulan)) {
                        $targetsPerBulan[$m]++;
                        $scoreM = null;
                        foreach ($tmbBulan as $tmb) {
                            if ($tmb['nilai_capaian'] !== null) {
                                $scoreM = (float)$tmb['nilai_capaian'];
                                break;
                            }
                        }
                        if ($scoreM !== null) {
                            $dinilaiPerBulan[$m]++;
                            $nilaiPerBulan[$m] += $scoreM;
                            $jmlDinilai++;
                            $totalNilai += $scoreM;
                        }

                        if ($includeRincianDetail) {
                            foreach ($tmbBulan as $tmbItem) {
                                $tmbNilai = ($tmbItem['nilai_capaian'] !== null) ? (float)$tmbItem['nilai_capaian'] : null;
                                $rincianDetail[] = [
                                    'nama'       => $pegawai['nama_lengkap'],
                                    'nip'        => $pegawai['nip'] ?? '-',
                                    'unit'       => $pegawai['unit'] ?? '-',
                                    'bulan'      => $bulanIndo[$m - 1] ?? "Bulan {$m}",
                                    'tipe'       => 'Tugas Tambahan',
                                    'sasaran'    => 'Penugasan Tambahan Institusi',
                                    'indikator'  => $tmbItem['deskripsi_kegiatan'] ?? ($tmbItem['uraian_tugas'] ?? '-'),
                                    'target'     => 1,
                                    'realisasi'  => 1,
                                    'satuan'     => 'Kegiatan',
                                    'gap'        => 0,
                                    'nilai'      => $tmbNilai,
                                    'predikat'   => $this->getPredikatKinerja($tmbNilai !== null ? 1 : 0, $tmbNilai ?? 0)['text'],
                                    'bukti'      => $tmbItem['link_bukti'] ?? '-',
                                    'status_app' => $tmbItem['status_approval'] ?? 'disetujui'
                                ];
                            }
                        }
                    }
                }

                for ($i = 1; $i <= 12; $i++) {
                    if ($targetsPerBulan[$i] > 0) {
                        $rataRataPerBulan[$i] = $dinilaiPerBulan[$i] > 0 ? round($nilaiPerBulan[$i] / $dinilaiPerBulan[$i], 2) : 0;
                    }
                }
                $validMonths = array_filter($rataRataPerBulan, fn($v) => $v !== null);
                $rataRata = count($validMonths) > 0 ? round(array_sum($validMonths) / count($validMonths), 2) : 0;
                $jmlTotalKomponen = array_sum($targetsPerBulan);
            } else {
                foreach ($userTargets as $rd) {
                    if (!empty($rd['nilai_capaian'])) {
                        $jmlDinilai++;
                        $totalNilai += (float)$rd['nilai_capaian'];
                    }

                    if ($includeRincianDetail) {
                        $targetVal = round((float)$rd['target_bulanan'], 4);
                        $realVal = round((float)$rd['total_realisasi'], 4);
                        $gapVal = round($realVal - $targetVal, 4);
                        $nilaiRhk = ($rd['nilai_capaian'] !== null && $rd['nilai_capaian'] !== '') ? (float)$rd['nilai_capaian'] : null;

                        $rincianDetail[] = [
                            'nama'       => $pegawai['nama_lengkap'],
                            'nip'        => $pegawai['nip'] ?? '-',
                            'unit'       => $pegawai['unit'] ?? '-',
                            'bulan'      => $namaBulan,
                            'tipe'       => 'RHK Pokok',
                            'sasaran'    => $rd['sasaran_program'] ?? '-',
                            'indikator'  => $rd['indikator_kinerja'] ?? '-',
                            'target'     => $targetVal,
                            'realisasi'  => $realVal,
                            'satuan'     => $rd['satuan'] ?? '-',
                            'gap'        => $gapVal,
                            'nilai'      => $nilaiRhk,
                            'predikat'   => $this->getPredikatKinerja($nilaiRhk !== null ? 1 : 0, $nilaiRhk ?? 0)['text'],
                            'bukti'      => $rd['link_bukti'] ?? '-',
                            'status_app' => $rd['status_penilaian'] ?? 'menunggu'
                        ];
                    }
                }

                // Cek Tugas Tambahan
                $tugasTambahan = $tmbByUser[$uId][(int)$bulanTerpilih] ?? [];
                if (!empty($tugasTambahan)) {
                    $hasTugasTambahan = true;
                    foreach ($tugasTambahan as $tmb) {
                        if ($tmb['nilai_capaian'] !== null && $scoreTambahan === null) {
                            $scoreTambahan = (float)$tmb['nilai_capaian'];
                        }

                        if ($includeRincianDetail) {
                            $tmbNilai = ($tmb['nilai_capaian'] !== null) ? (float)$tmb['nilai_capaian'] : null;
                            $rincianDetail[] = [
                                'nama'       => $pegawai['nama_lengkap'],
                                'nip'        => $pegawai['nip'] ?? '-',
                                'unit'       => $pegawai['unit'] ?? '-',
                                'bulan'      => $namaBulan,
                                'tipe'       => 'Tugas Tambahan',
                                'sasaran'    => 'Penugasan Tambahan Institusi',
                                'indikator'  => $tmb['deskripsi_kegiatan'] ?? ($tmb['uraian_tugas'] ?? '-'),
                                'target'     => 1,
                                'realisasi'  => 1,
                                'satuan'     => 'Kegiatan',
                                'gap'        => 0,
                                'nilai'      => $tmbNilai,
                                'predikat'   => $this->getPredikatKinerja($tmbNilai !== null ? 1 : 0, $tmbNilai ?? 0)['text'],
                                'bukti'      => $tmb['link_bukti'] ?? '-',
                                'status_app' => $tmb['status_approval'] ?? 'disetujui'
                            ];
                        }
                    }
                    if ($scoreTambahan !== null) {
                        $jmlDinilai++;
                        $totalNilai += $scoreTambahan;
                    }
                }

                $jmlTotalKomponen = $jmlTarget + ($hasTugasTambahan ? 1 : 0);
                $rataRata = $jmlDinilai > 0 ? round($totalNilai / $jmlDinilai, 2) : 0;
            }

            $rekapKinerja[] = [
                'pegawai'             => $pegawai,
                'jumlah_rhk'          => $jmlTarget,
                'jumlah_komponen'     => $jmlTotalKomponen,
                'rhk_dinilai'         => $jmlDinilai,
                'has_tugas_tambahan'  => $hasTugasTambahan,
                'score_tambahan'      => $scoreTambahan,
                'rata_rata'           => $rataRata,
                'rata_rata_per_bulan' => $rataRataPerBulan
            ];
        }

        // Urutkan rekap dari nilai tertinggi
        usort($rekapKinerja, fn($a, $b) => $b['rata_rata'] <=> $a['rata_rata']);

        return [
            'rekapKinerja'  => $rekapKinerja,
            'rincianDetail' => $rincianDetail
        ];
    }

    /**
     * Export data rekap kinerja ke Excel (.xlsx Multi-Sheet Data Sangat Lengkap - Optimized)
     */
    public function exportExcel()
    {
        if (!hasAnyRole(['kepegawaian', 'admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk'])) {
            return redirect()->to('/dashboard');
        }

        $userModel = new User();

        $bulanTerpilih = $this->request->getGet('bulan') ?? date('n');
        $tahunTerpilih = $this->request->getGet('tahun') ?? date('Y');
        $unitFilter    = $this->request->getGet('unit') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = ($bulanTerpilih === 'all') ? 'Sepanjang Tahun' : ($bulanIndo[(int)$bulanTerpilih - 1] ?? '');

        // Mapping atasan
        $allUsers = $userModel->select('id, nama_lengkap, nip, unit, jabatan')->findAll();
        $userMap = [];
        foreach ($allUsers as $u) {
            $userMap[$u['id']] = $u;
        }

        // Ambil semua user (kecuali admin)
        $builder = $userModel->where('role !=', 'admin');
        if (!empty($unitFilter)) {
            $builder = $builder->where('unit', $unitFilter);
        }
        $semuaPegawai = $builder->orderBy('nama_lengkap', 'ASC')->findAll();

        // Ambil data rekap secara bulk (Super Fast Batch Fetching)
        $bulkData = $this->getBulkRekapKinerja($semuaPegawai, $userMap, $bulanTerpilih, $tahunTerpilih, $bulanIndo, $namaBulan, true);
        $rekapKinerja = $bulkData['rekapKinerja'];
        $rincianDetail = $bulkData['rincianDetail'];

        // Hitung statistik instansi
        $sudahDinilai = 0;
        $belumDinilai = 0;
        $sumRataInstansi = 0;
        foreach ($rekapKinerja as $r) {
            if ($r['rhk_dinilai'] > 0) {
                $sudahDinilai++;
                $sumRataInstansi += $r['rata_rata'];
            } else {
                $belumDinilai++;
            }
        }
        $totalPegawai = count($rekapKinerja);
        $rataRataDinilai = $sudahDinilai > 0 ? round($sumRataInstansi / $sudahDinilai, 2) : 0;
        $rataRataKeseluruhan = $totalPegawai > 0 ? round($sumRataInstansi / $totalPegawai, 2) : 0;

        // INSIALISASI SPREADSHEET
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Evidence Command Center (ECC) - PKTJ')
            ->setTitle("Rekap Kinerja {$namaBulan} {$tahunTerpilih}")
            ->setSubject('Laporan Rekapitulasi Capaian Kinerja Pegawai');

        // ==========================================
        // SHEET 1: REKAP EKSEKUTIF PEGAWAI
        // ==========================================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Rekap Kinerja Pegawai');

        // Header Title
        $sheet1->setCellValue('A1', 'EVIDENCE COMMAND CENTER (ECC) - POLITEKNIK KESELAMATAN TRANSPORTASI JALAN');
        $sheet1->setCellValue('A2', 'LAPORAN REKAPITULASI CAPAIAN KINERJA PEGAWAI');
        $sheet1->setCellValue('A3', "Periode: {$namaBulan} {$tahunTerpilih} | Unit Kerja: " . (!empty($unitFilter) ? $unitFilter : 'Semua Unit Kerja'));
        $sheet1->setCellValue('A4', 'Diekspor pada: ' . date('d/m/Y H:i:s') . ' WIB | Status Dokumen: Resmi');

        $sheet1->getStyle('A1')->getFont()->setSize(14)->setBold(true)->getColor()->setRGB('1E3A8A');
        $sheet1->getStyle('A2')->getFont()->setSize(12)->setBold(true)->getColor()->setRGB('0F172A');
        $sheet1->getStyle('A3:A4')->getFont()->setSize(10)->getColor()->setRGB('475569');

        // KPI Summary Box (Row 6 - 7)
        $kpiItems = [
            ['Total Pegawai', $totalPegawai . ' Pegawai', 'F1F5F9', '0F172A'],
            ['Sudah Dinilai', $sudahDinilai . ' (' . ($totalPegawai > 0 ? round(($sudahDinilai / $totalPegawai) * 100) : 0) . '%)', 'D1FAE5', '065F46'],
            ['Belum Dinilai', $belumDinilai . ' (' . ($totalPegawai > 0 ? round(($belumDinilai / $totalPegawai) * 100) : 0) . '%)', 'FEE2E2', '991B1B'],
            ['Rata Dinilai', number_format($rataRataDinilai, 2, ',', '.') . '%', 'DBEAFE', '1E40AF'],
            ['Rata Total', number_format($rataRataKeseluruhan, 2, ',', '.') . '%', 'F8FAFC', '334155'],
        ];

        $colChar = 'B';
        foreach ($kpiItems as $kpi) {
            $nextCol = chr(ord($colChar) + 1);
            $cellRangeTitle = "{$colChar}6:{$nextCol}6";
            $cellRangeVal   = "{$colChar}7:{$nextCol}7";
            
            $sheet1->mergeCells($cellRangeTitle);
            $sheet1->mergeCells($cellRangeVal);
            
            $sheet1->setCellValue("{$colChar}6", $kpi[0]);
            $sheet1->setCellValue("{$colChar}7", $kpi[1]);

            $sheet1->getStyle("{$colChar}6:{$nextCol}7")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($kpi[2]);
            $sheet1->getStyle("{$colChar}6")->getFont()->setSize(9)->setBold(true)->getColor()->setRGB('475569');
            $sheet1->getStyle("{$colChar}7")->getFont()->setSize(12)->setBold(true)->getColor()->setRGB($kpi[3]);
            $sheet1->getStyle("{$colChar}6:{$nextCol}7")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet1->getStyle("{$colChar}6:{$nextCol}7")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

            $colChar = chr(ord($nextCol) + 1);
        }

        // Table Headers (Row 9)
        $headerRow = 9;
        if ($bulanTerpilih === 'all') {
            $headers = [
                'No', 'Nama Lengkap Pegawai', 'NIP', 'Jabatan', 'Unit Kerja', 'Nama Atasan Penilai',
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
                'Total Komponen', 'Komponen Dinilai', 'Nilai Akhir Tahunan (%)', 'Predikat Kinerja'
            ];
        } else {
            $headers = [
                'No', 'Nama Lengkap Pegawai', 'NIP', 'Jabatan', 'Unit Kerja', 'Nama Atasan Penilai',
                'Jumlah Target RHK', 'Komponen Dinilai', 'Tugas Tambahan', 'Nilai Kinerja (%)', 'Predikat Kinerja', 'Status Evaluasi'
            ];
        }

        $colIdx = 1;
        foreach ($headers as $h) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheet1->setCellValue("{$colLetter}{$headerRow}", $h);
            $colIdx++;
        }

        // Merge Header Title across columns
        $lastHeaderCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet1->mergeCells("A1:{$lastHeaderCol}1");
        $sheet1->mergeCells("A2:{$lastHeaderCol}2");
        $sheet1->mergeCells("A3:{$lastHeaderCol}3");
        $sheet1->mergeCells("A4:{$lastHeaderCol}4");

        $headerRange = "A{$headerRow}:{$lastHeaderCol}{$headerRow}";
        $sheet1->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
        $sheet1->getStyle($headerRange)->getFont()->setSize(10)->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet1->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet1->getRowDimension($headerRow)->setRowHeight(28);

        // Populate Rows
        $rowNum = 10;
        $no = 1;
        foreach ($rekapKinerja as $row) {
            $p = $row['pegawai'];
            $score = (float)$row['rata_rata'];
            $pred = $this->getPredikatKinerja($row['rhk_dinilai'], $score);

            if ($bulanTerpilih === 'all') {
                $sheet1->setCellValue("A{$rowNum}", $no++);
                $sheet1->setCellValue("B{$rowNum}", $p['nama_lengkap']);
                $sheet1->setCellValueExplicit("C{$rowNum}", (string)($p['nip'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet1->setCellValue("D{$rowNum}", $p['jabatan'] ?? '-');
                $sheet1->setCellValue("E{$rowNum}", $p['unit'] ?? '-');
                $sheet1->setCellValue("F{$rowNum}", $p['atasan_nama'] ?? '-');

                // 12 Bulan (Col G - R)
                for ($m = 1; $m <= 12; $m++) {
                    $mCol = Coordinate::stringFromColumnIndex(7 + ($m - 1));
                    $mVal = $row['rata_rata_per_bulan'][$m];
                    if ($mVal !== null) {
                        $sheet1->setCellValue("{$mCol}{$rowNum}", (float)$mVal);
                        $sheet1->getStyle("{$mCol}{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');
                    } else {
                        $sheet1->setCellValue("{$mCol}{$rowNum}", '-');
                    }
                    $sheet1->getStyle("{$mCol}{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $sheet1->setCellValue("S{$rowNum}", $row['jumlah_komponen']);
                $sheet1->setCellValue("T{$rowNum}", $row['rhk_dinilai']);
                $sheet1->setCellValue("U{$rowNum}", $score);
                $sheet1->setCellValue("V{$rowNum}", $pred['text']);

                $sheet1->getStyle("U{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet1->getStyle("V{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($pred['fill']);
                $sheet1->getStyle("V{$rowNum}")->getFont()->setBold(true)->getColor()->setRGB($pred['color']);
                $sheet1->getStyle("S{$rowNum}:V{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            } else {
                $statusEval = ($row['rhk_dinilai'] > 0) ? 'Selesai Dinilai' : 'Belum Dinilai';

                $sheet1->setCellValue("A{$rowNum}", $no++);
                $sheet1->setCellValue("B{$rowNum}", $p['nama_lengkap']);
                $sheet1->setCellValueExplicit("C{$rowNum}", (string)($p['nip'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet1->setCellValue("D{$rowNum}", $p['jabatan'] ?? '-');
                $sheet1->setCellValue("E{$rowNum}", $p['unit'] ?? '-');
                $sheet1->setCellValue("F{$rowNum}", $p['atasan_nama'] ?? '-');
                $sheet1->setCellValue("G{$rowNum}", $row['jumlah_rhk']);
                $sheet1->setCellValue("H{$rowNum}", $row['rhk_dinilai']);
                $sheet1->setCellValue("I{$rowNum}", $row['has_tugas_tambahan'] ? 'Ada' : 'Tidak');
                $sheet1->setCellValue("J{$rowNum}", $score);
                $sheet1->setCellValue("K{$rowNum}", $pred['text']);
                $sheet1->setCellValue("L{$rowNum}", $statusEval);

                $sheet1->getStyle("J{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet1->getStyle("K{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($pred['fill']);
                $sheet1->getStyle("K{$rowNum}")->getFont()->setBold(true)->getColor()->setRGB($pred['color']);
                $sheet1->getStyle("G{$rowNum}:L{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Alignments & styling
            $sheet1->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle("C{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle("A{$rowNum}:{$lastHeaderCol}{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
            $sheet1->getRowDimension($rowNum)->setRowHeight(20);

            $rowNum++;
        }

        // Freeze Panes & Auto-filter
        $sheet1->setAutoFilter("A{$headerRow}:{$lastHeaderCol}" . ($rowNum - 1));
        $sheet1->freezePane('C10');

        // Optimasi Lebar Kolom Sheet 1 (Fixed Presets Cepat)
        $sheet1->getColumnDimension('A')->setWidth(6);
        $sheet1->getColumnDimension('B')->setWidth(28);
        $sheet1->getColumnDimension('C')->setWidth(22);
        $sheet1->getColumnDimension('D')->setWidth(24);
        $sheet1->getColumnDimension('E')->setWidth(22);
        $sheet1->getColumnDimension('F')->setWidth(26);
        if ($bulanTerpilih === 'all') {
            for ($m = 1; $m <= 12; $m++) {
                $mCol = Coordinate::stringFromColumnIndex(7 + ($m - 1));
                $sheet1->getColumnDimension($mCol)->setWidth(10);
            }
            $sheet1->getColumnDimension('S')->setWidth(16);
            $sheet1->getColumnDimension('T')->setWidth(16);
            $sheet1->getColumnDimension('U')->setWidth(18);
            $sheet1->getColumnDimension('V')->setWidth(18);
        } else {
            $sheet1->getColumnDimension('G')->setWidth(16);
            $sheet1->getColumnDimension('H')->setWidth(16);
            $sheet1->getColumnDimension('I')->setWidth(16);
            $sheet1->getColumnDimension('J')->setWidth(18);
            $sheet1->getColumnDimension('K')->setWidth(18);
            $sheet1->getColumnDimension('L')->setWidth(18);
        }

        // ==========================================
        // SHEET 2: RINCIAN RHK & TUGAS TAMBAHAN
        // ==========================================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Rincian RHK & Tugas');

        // Sheet 2 Title
        $sheet2->setCellValue('A1', 'RINCIAN DETAIL RENCANA HASIL KERJA (RHK) & TUGAS TAMBAHAN SELURUH PEGAWAI');
        $sheet2->setCellValue('A2', "Periode: {$namaBulan} {$tahunTerpilih} | Dokumen Audit Kinerja Pegawai");
        $sheet2->getStyle('A1')->getFont()->setSize(12)->setBold(true)->getColor()->setRGB('1E3A8A');
        $sheet2->getStyle('A2')->getFont()->setSize(10)->getColor()->setRGB('475569');

        $sheet2Headers = [
            'No', 'Nama Pegawai', 'NIP', 'Unit Kerja', 'Bulan', 'Tipe Komponen',
            'Sasaran Program / Rencana Kerja', 'Indikator Kinerja / Uraian Tugas',
            'Target', 'Realisasi', 'Satuan', 'Selisih (Gap)', 'Nilai Capaian (%)',
            'Predikat', 'Tautan Bukti Kegiatan', 'Status Persetujuan'
        ];

        $s2HeaderRow = 4;
        $s2ColIdx = 1;
        foreach ($sheet2Headers as $s2h) {
            $colLetter = Coordinate::stringFromColumnIndex($s2ColIdx);
            $sheet2->setCellValue("{$colLetter}{$s2HeaderRow}", $s2h);
            $s2ColIdx++;
        }

        $s2LastCol = Coordinate::stringFromColumnIndex(count($sheet2Headers));
        $sheet2->mergeCells("A1:{$s2LastCol}1");
        $sheet2->mergeCells("A2:{$s2LastCol}2");

        $sheet2->getStyle("A{$s2HeaderRow}:{$s2LastCol}{$s2HeaderRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('334155');
        $sheet2->getStyle("A{$s2HeaderRow}:{$s2LastCol}{$s2HeaderRow}")->getFont()->setSize(10)->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet2->getStyle("A{$s2HeaderRow}:{$s2LastCol}{$s2HeaderRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet2->getRowDimension($s2HeaderRow)->setRowHeight(24);

        $s2RowNum = 5;
        $s2No = 1;
        foreach ($rincianDetail as $rd) {
            $sheet2->setCellValue("A{$s2RowNum}", $s2No++);
            $sheet2->setCellValue("B{$s2RowNum}", $rd['nama']);
            $sheet2->setCellValueExplicit("C{$s2RowNum}", (string)$rd['nip'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet2->setCellValue("D{$s2RowNum}", $rd['unit']);
            $sheet2->setCellValue("E{$s2RowNum}", $rd['bulan']);
            $sheet2->setCellValue("F{$s2RowNum}", $rd['tipe']);
            $sheet2->setCellValue("G{$s2RowNum}", $rd['sasaran']);
            $sheet2->setCellValue("H{$s2RowNum}", $rd['indikator']);
            $sheet2->setCellValue("I{$s2RowNum}", $rd['target']);
            $sheet2->setCellValue("J{$s2RowNum}", $rd['realisasi']);
            $sheet2->setCellValue("K{$s2RowNum}", $rd['satuan']);
            $sheet2->setCellValue("L{$s2RowNum}", $rd['gap']);
            
            if ($rd['nilai'] !== null) {
                $sheet2->setCellValue("M{$s2RowNum}", (float)$rd['nilai']);
                $sheet2->getStyle("M{$s2RowNum}")->getNumberFormat()->setFormatCode('#,##0.00');
            } else {
                $sheet2->setCellValue("M{$s2RowNum}", '-');
            }
            
            $sheet2->setCellValue("N{$s2RowNum}", $rd['predikat']);
            $sheet2->setCellValue("O{$s2RowNum}", $rd['bukti']);
            $sheet2->setCellValue("P{$s2RowNum}", strtoupper((string)$rd['status_app']));

            $sheet2->getStyle("A{$s2RowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle("C{$s2RowNum}:F{$s2RowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle("I{$s2RowNum}:N{$s2RowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle("P{$s2RowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle("A{$s2RowNum}:{$s2LastCol}{$s2RowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');

            $s2RowNum++;
        }

        $sheet2->setAutoFilter("A{$s2HeaderRow}:{$s2LastCol}" . ($s2RowNum - 1));
        $sheet2->freezePane('C5');

        // Optimasi Lebar Kolom Sheet 2 (Fixed Presets Cepat)
        $s2Widths = [
            'A' => 6,  'B' => 26, 'C' => 22, 'D' => 20,
            'E' => 14, 'F' => 18, 'G' => 32, 'H' => 32,
            'I' => 12, 'J' => 12, 'K' => 14, 'L' => 14,
            'M' => 16, 'N' => 16, 'O' => 30, 'P' => 18
        ];
        foreach ($s2Widths as $colL => $w) {
            $sheet2->getColumnDimension($colL)->setWidth($w);
        }

        // Set active sheet back to Sheet 1
        $spreadsheet->setActiveSheetIndex(0);

        // STREAM XLSX
        $cleanNamaBulan = str_replace(' ', '_', $namaBulan);
        $fileName = "Rekap_Kinerja_ECC_{$cleanNamaBulan}_{$tahunTerpilih}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Access-Control-Expose-Headers: Content-Disposition');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export data rekap kinerja ke format PDF (A4 Landscape Berstandar Instansi - Optimized)
     */
    public function exportPdf()
    {
        if (!hasAnyRole(['kepegawaian', 'admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk'])) {
            return redirect()->to('/dashboard');
        }

        $userModel = new User();

        $bulanTerpilih = $this->request->getGet('bulan') ?? date('n');
        $tahunTerpilih = $this->request->getGet('tahun') ?? date('Y');
        $unitFilter    = $this->request->getGet('unit') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = ($bulanTerpilih === 'all') ? 'Sepanjang Tahun' : ($bulanIndo[(int)$bulanTerpilih - 1] ?? '');

        // Mapping atasan
        $allUsers = $userModel->select('id, nama_lengkap, nip, unit, jabatan')->findAll();
        $userMap = [];
        foreach ($allUsers as $u) {
            $userMap[$u['id']] = $u;
        }

        // Ambil semua user (kecuali admin)
        $builder = $userModel->where('role !=', 'admin');
        if (!empty($unitFilter)) {
            $builder = $builder->where('unit', $unitFilter);
        }
        $semuaPegawai = $builder->orderBy('nama_lengkap', 'ASC')->findAll();

        // Bulk Fetch Data Kinerja (Super Fast Batch Query)
        $bulkData = $this->getBulkRekapKinerja($semuaPegawai, $userMap, $bulanTerpilih, $tahunTerpilih, $bulanIndo, $namaBulan, false);
        $rekapKinerja = $bulkData['rekapKinerja'];

        // Hitung statistik instansi
        $sudahDinilai = 0;
        $belumDinilai = 0;
        $sumRataInstansi = 0;
        foreach ($rekapKinerja as $r) {
            if ($r['rhk_dinilai'] > 0) {
                $sudahDinilai++;
                $sumRataInstansi += $r['rata_rata'];
            } else {
                $belumDinilai++;
            }
        }
        $totalPegawai = count($rekapKinerja);
        $rataRataDinilai = $sudahDinilai > 0 ? round($sumRataInstansi / $sudahDinilai, 2) : 0;
        $rataRataKeseluruhan = $totalPegawai > 0 ? round($sumRataInstansi / $totalPegawai, 2) : 0;

        $data = [
            'title'                 => "Laporan Rekapitulasi Kinerja {$namaBulan} {$tahunTerpilih}",
            'rekap_kinerja'         => $rekapKinerja,
            'bulan_terpilih'        => $bulanTerpilih,
            'tahun_terpilih'        => $tahunTerpilih,
            'nama_bulan'            => $namaBulan,
            'bulan_indo'            => $bulanIndo,
            'unit_filter'           => $unitFilter,
            'sudah_dinilai'         => $sudahDinilai,
            'belum_dinilai'         => $belumDinilai,
            'rata_rata_dinilai'     => $rataRataDinilai,
            'rata_rata_keseluruhan' => $rataRataKeseluruhan,
            'rata_rata_instansi'    => $rataRataDinilai
        ];

        $html = view('kepegawaian/rekap_kinerja_pdf', $data);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $cleanNamaBulan = str_replace(' ', '_', $namaBulan);
        $fileName = "Laporan_Rekapitulasi_Kinerja_ECC_{$cleanNamaBulan}_{$tahunTerpilih}.pdf";
        header('Access-Control-Expose-Headers: Content-Disposition');
        $dompdf->stream($fileName, ['Attachment' => true]);
        exit;
    }

    /**
     * AJAX endpoint untuk mendapatkan rincian detail kinerja pegawai (RHK & Tugas Tambahan)
     */
    public function getDetailPegawai()
    {
        if (!hasAnyRole(['kepegawaian', 'admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        $userId = $this->request->getGet('user_id');
        $bulan = $this->request->getGet('bulan') ?? date('n');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        if (empty($userId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter pegawai tidak valid']);
        }

        $userModel = new User();
        $pegawai = $userModel->find($userId);
        if (!$pegawai) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pegawai tidak ditemukan']);
        }

        // Atasan Info
        $atasan = null;
        if (!empty($pegawai['atasan_id'])) {
            $atasan = $userModel->find($pegawai['atasan_id']);
        }

        $laporanModel = new LaporanHarian();
        $logTambahanModel = new LogTugasTambahan();

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = ($bulan === 'all') ? 'Sepanjang Tahun' : ($bulanIndo[(int)$bulan - 1] ?? '');

        $rekapRhk = $laporanModel->getTargetWithRealization($userId, $bulan, $tahun, true);
        if ($bulan !== 'all') {
            $tugasTambahan = $logTambahanModel->getLogByMonth($userId, $bulan, $tahun, true);
        } else {
            $startDate = sprintf('%04d-01-01', (int)$tahun);
            $endDate   = sprintf('%04d-12-31', (int)$tahun);
            $tugasTambahan = $logTambahanModel->where('user_id', $userId)
                ->where('tanggal_kegiatan >=', $startDate)
                ->where('tanggal_kegiatan <=', $endDate)
                ->where('status', 'terkirim')
                ->orderBy('tanggal_kegiatan', 'ASC')
                ->findAll();
        }

        // Hitung rata-rata & predikat
        $totalNilai = 0;
        $jmlDinilai = 0;
        $formattedRhk = [];
        foreach ($rekapRhk as $rhk) {
            $target = round((float)$rhk['target_bulanan'], 4);
            $realisasi = round((float)$rhk['total_realisasi'], 4);
            $selisih = round($realisasi - $target, 4);
            $nilai = ($rhk['nilai_capaian'] !== null && $rhk['nilai_capaian'] !== '') ? round((float)$rhk['nilai_capaian'], 2) : null;

            if ($nilai !== null) {
                $totalNilai += $nilai;
                $jmlDinilai++;
            }

            $formattedRhk[] = [
                'id' => $rhk['id'],
                'indikator' => $rhk['indikator_kinerja'],
                'target' => $target,
                'realisasi' => $realisasi,
                'selisih' => $selisih,
                'satuan' => $rhk['satuan'],
                'nilai_capaian' => $nilai,
                'status_penilaian' => $rhk['status_penilaian'] ?? null
            ];
        }

        $scoreTambahan = null;
        $formattedTambahan = [];
        if (!empty($tugasTambahan)) {
            foreach ($tugasTambahan as $tmb) {
                if ($tmb['nilai_capaian'] !== null && $scoreTambahan === null) {
                    $scoreTambahan = (float)$tmb['nilai_capaian'];
                }
                $formattedTambahan[] = [
                    'id' => $tmb['id'],
                    'deskripsi' => $tmb['deskripsi_kegiatan'],
                    'tanggal' => $tmb['tanggal_kegiatan'],
                    'capaian' => $tmb['jumlah_capaian'],
                    'satuan' => $tmb['satuan'] ?? '',
                    'link_bukti' => $tmb['link_bukti'] ?? null
                ];
            }
            if ($scoreTambahan !== null) {
                $totalNilai += $scoreTambahan;
                $jmlDinilai++;
            }
        }

        $totalKomponen = count($rekapRhk) + (!empty($tugasTambahan) ? 1 : 0);
        $rataRata = $jmlDinilai > 0 ? round($totalNilai / $jmlDinilai, 2) : 0;

        $predikatLabel = 'Belum Dinilai';
        $badgeClass = 'bg-secondary';
        if ($jmlDinilai > 0) {
            if ($rataRata >= 100) {
                $predikatLabel = 'Sangat Baik';
                $badgeClass = 'bg-success';
            } elseif ($rataRata >= 80) {
                $predikatLabel = 'Baik';
                $badgeClass = 'bg-primary';
            } elseif ($rataRata >= 60) {
                $predikatLabel = 'Butuh Perbaikan';
                $badgeClass = 'bg-secondary';
            } elseif ($rataRata >= 50) {
                $predikatLabel = 'Kurang';
                $badgeClass = 'bg-warning text-dark';
            } else {
                $predikatLabel = 'Sangat Kurang';
                $badgeClass = 'bg-danger';
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'pegawai' => [
                'id' => $pegawai['id'],
                'nama' => $pegawai['nama_lengkap'],
                'nip' => $pegawai['nip'] ?? '-',
                'jabatan' => $pegawai['jabatan'] ?? '-',
                'unit' => $pegawai['unit'] ?? '-',
                'atasan_nama' => $atasan ? $atasan['nama_lengkap'] : '-'
            ],
            'periode' => [
                'bulan' => $bulan,
                'nama_bulan' => $namaBulan,
                'tahun' => $tahun
            ],
            'rekap_rhk' => $formattedRhk,
            'tugas_tambahan' => $formattedTambahan,
            'score_tambahan' => $scoreTambahan,
            'total_rhk' => count($formattedRhk),
            'jml_dinilai' => $jmlDinilai,
            'total_komponen' => $totalKomponen,
            'rata_rata' => $rataRata,
            'predikat' => $predikatLabel,
            'badge_class' => $badgeClass
        ]);
    }
}
