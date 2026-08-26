<?php

namespace App\Controllers\Kepegawaian;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\LaporanHarian;
use App\Models\LogTugasTambahan;

class DashboardKepegawaian extends BaseController
{
    public function index()
    {
        // Hanya role kepegawaian atau admin yang boleh akses
        if (!hasAnyRole(['kepegawaian', 'admin'])) {
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
        $rataRataInstansi = $sudahDinilai > 0 ? round($sumRataInstansi / $sudahDinilai, 2) : 0;

        $data = [
            'title'              => 'Rekap Kinerja Kepegawaian',
            'rekap_kinerja'      => $rekapKinerja,
            'bulan_terpilih'     => $bulanTerpilih,
            'tahun_terpilih'     => $tahunTerpilih,
            'nama_bulan'         => $namaBulan,
            'bulan_indo'         => $bulanIndo,
            'daftar_unit'        => $daftarUnit,
            'unit_filter'        => $unitFilter,
            'sudah_dinilai'      => $sudahDinilai,
            'belum_dinilai'      => $belumDinilai,
            'rata_rata_instansi' => $rataRataInstansi
        ];

        return view('kepegawaian/rekap_kinerja', $data);
    }

    /**
     * Export data rekap kinerja ke Excel
     */
    public function exportExcel()
    {
        if (!hasAnyRole(['kepegawaian', 'admin'])) {
            return redirect()->to('/dashboard');
        }

        $userModel = new User();
        $laporanModel = new LaporanHarian();
        $logTambahanModel = new LogTugasTambahan();

        $bulanTerpilih = $this->request->getGet('bulan') ?? date('n');
        $tahunTerpilih = $this->request->getGet('tahun') ?? date('Y');
        $unitFilter    = $this->request->getGet('unit') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = ($bulanTerpilih === 'all') ? 'Sepanjang Tahun' : ($bulanIndo[(int)$bulanTerpilih - 1] ?? '');

        // Ambil semua user (kecuali admin)
        $builder = $userModel->where('role !=', 'admin');
        if (!empty($unitFilter)) {
            $builder = $builder->where('unit', $unitFilter);
        }
        $semuaPegawai = $builder->orderBy('nama_lengkap', 'ASC')->findAll();

        // Hitung rekap kinerja
        $rows = [];
        foreach ($semuaPegawai as $pegawai) {
            $rekapData = $laporanModel->getTargetWithRealization($pegawai['id'], $bulanTerpilih, $tahunTerpilih);
            
            $jmlDinilai = 0;
            $totalNilai = 0;
            $jmlTarget  = count($rekapData);
            
            $rataRataPerBulan = array_fill(1, 12, null);
            $hasTugasTambahan = false;

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

                // Cek Tugas Tambahan
                $tugasTambahan = $logTambahanModel->getLogByMonth($pegawai['id'], $bulanTerpilih, $tahunTerpilih, true);
                if (!empty($tugasTambahan)) {
                    $hasTugasTambahan = true;
                    $scoreTambahan = null;
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

            $rows[] = [
                'nama'                => $pegawai['nama_lengkap'],
                'nip'                 => $pegawai['nip'] ?? '-',
                'jabatan'             => $pegawai['jabatan'] ?? '-',
                'unit'                => $pegawai['unit'] ?? '-',
                'jumlah_rhk'          => $jmlTotalKomponen,
                'dinilai'             => $jmlDinilai,
                'rata_rata'           => $rataRata,
                'rata_rata_per_bulan' => $rataRataPerBulan
            ];
        }

        // Urutkan dari rata-rata tertinggi
        usort($rows, fn($a, $b) => $b['rata_rata'] <=> $a['rata_rata']);

        // Generate CSV (Universal Excel-compatible)
        $cleanNamaBulan = str_replace(' ', '_', $namaBulan);
        $filename = "Rekap_Kinerja_ECC_{$cleanNamaBulan}_{$tahunTerpilih}.csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM untuk UTF-8 Excel compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        // Header
        fputcsv($output, ['REKAP KINERJA PEGAWAI (ECC) - ' . strtoupper($namaBulan) . ' ' . $tahunTerpilih], ';');
        fputcsv($output, ['Diekspor pada: ' . date('d/m/Y H:i:s')], ';');
        fputcsv($output, [], ';');

        if ($bulanTerpilih === 'all') {
            fputcsv($output, [
                'No', 'Nama Lengkap', 'NIP', 'Jabatan', 'Unit Kerja', 
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
                'Jumlah Komponen Total', 'Komponen Dinilai Total', 'Rata-rata Nilai Keseluruhan'
            ], ';');
        } else {
            fputcsv($output, ['No', 'Nama Lengkap', 'NIP', 'Jabatan', 'Unit Kerja', 'Jumlah Komponen', 'Komponen Dinilai', 'Rata-rata Nilai Kinerja'], ';');
        }
        
        $no = 1;
        foreach ($rows as $row) {
            $nipFormatted = ($row['nip'] !== '-') ? '="' . $row['nip'] . '"' : '-';

            if ($bulanTerpilih === 'all') {
                $exportRow = [
                    $no++,
                    $row['nama'],
                    $nipFormatted,
                    $row['jabatan'],
                    $row['unit']
                ];
                
                // Tambahkan nilai per bulan
                for ($i = 1; $i <= 12; $i++) {
                    $val = $row['rata_rata_per_bulan'][$i];
                    $exportRow[] = ($val !== null) ? str_replace('.', ',', $val) : '-';
                }

                // Tambahkan total akhir
                $exportRow[] = $row['jumlah_rhk'];
                $exportRow[] = $row['dinilai'];
                $exportRow[] = str_replace('.', ',', $row['rata_rata']);

                fputcsv($output, $exportRow, ';');
            } else {
                fputcsv($output, [
                    $no++,
                    $row['nama'],
                    $nipFormatted,
                    $row['jabatan'],
                    $row['unit'],
                    $row['jumlah_rhk'],
                    $row['dinilai'],
                    str_replace('.', ',', $row['rata_rata']),
                ], ';');
            }
        }
        
        fclose($output);
        exit;
    }

    /**
     * AJAX endpoint untuk mendapatkan rincian detail kinerja pegawai (RHK & Tugas Tambahan)
     */
    public function getDetailPegawai()
    {
        if (!hasAnyRole(['kepegawaian', 'admin', 'direktur'])) {
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
        $tugasTambahan = ($bulan !== 'all') ? $logTambahanModel->getLogByMonth($userId, $bulan, $tahun, true) : [];

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

        $predikatLabel = '-';
        $badgeClass = 'bg-secondary';
        if ($jmlDinilai > 0) {
            if ($rataRata <= 25) { $predikatLabel = 'Sangat Kurang'; $badgeClass = 'bg-danger'; }
            elseif ($rataRata <= 75) { $predikatLabel = 'Kurang'; $badgeClass = 'bg-warning text-dark'; }
            elseif ($rataRata <= 90) { $predikatLabel = 'Butuh Perbaikan'; $badgeClass = 'bg-secondary'; }
            elseif ($rataRata <= 100) { $predikatLabel = 'Baik'; $badgeClass = 'bg-primary'; }
            else { $predikatLabel = 'Sangat Baik'; $badgeClass = 'bg-success'; }
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
