<?php

namespace App\Controllers\Kepegawaian;

use App\Controllers\BaseController;
use App\Models\User;

class DashboardKepegawaian extends BaseController
{
    public function index()
    {
        // Hanya role kepegawaian atau admin yang boleh akses
        if (!hasAnyRole(['kepegawaian', 'admin'])) {
            return redirect()->to('/dashboard');
        }

        $userModel = new User();
        $laporanModel = new \App\Models\LaporanHarian();

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

                for ($i = 1; $i <= 12; $i++) {
                    if ($targetsPerBulan[$i] > 0) {
                        $rataRataPerBulan[$i] = $dinilaiPerBulan[$i] > 0 ? round($nilaiPerBulan[$i] / $dinilaiPerBulan[$i], 2) : 0;
                    }
                }
            } else {
                foreach ($rekapData as $rd) {
                    if (!empty($rd['nilai_capaian'])) {
                        $jmlDinilai++;
                        $totalNilai += (float)$rd['nilai_capaian'];
                    }
                }
            }

            $rataRata = $jmlDinilai > 0 ? round($totalNilai / $jmlDinilai, 2) : 0;

            $rekapKinerja[] = [
                'pegawai'             => $pegawai,
                'jumlah_rhk'          => $jmlTarget,
                'rhk_dinilai'         => $jmlDinilai,
                'rata_rata'           => $rataRata,
                'rata_rata_per_bulan' => $rataRataPerBulan
            ];
        }

        // Urutkan dari rata-rata tertinggi
        usort($rekapKinerja, function ($a, $b) {
            return $b['rata_rata'] <=> $a['rata_rata'];
        });

        $data = [
            'title'           => 'Rekap Kinerja Kepegawaian',
            'rekap_kinerja'   => $rekapKinerja,
            'bulan_terpilih'  => $bulanTerpilih,
            'tahun_terpilih'  => $tahunTerpilih,
            'nama_bulan'      => $namaBulan,
            'bulan_indo'      => $bulanIndo,
            'daftar_unit'     => $daftarUnit,
            'unit_filter'     => $unitFilter,
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
        $laporanModel = new \App\Models\LaporanHarian();

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

                for ($i = 1; $i <= 12; $i++) {
                    if ($targetsPerBulan[$i] > 0) {
                        $rataRataPerBulan[$i] = $dinilaiPerBulan[$i] > 0 ? round($nilaiPerBulan[$i] / $dinilaiPerBulan[$i], 2) : 0;
                    }
                }
            } else {
                foreach ($rekapData as $rd) {
                    if (!empty($rd['nilai_capaian'])) {
                        $jmlDinilai++;
                        $totalNilai += (float)$rd['nilai_capaian'];
                    }
                }
            }

            $rataRata = $jmlDinilai > 0 ? round($totalNilai / $jmlDinilai, 2) : 0;

            $rows[] = [
                'nama'                => $pegawai['nama_lengkap'],
                'nip'                 => $pegawai['nip'] ?? '-',
                'jabatan'             => $pegawai['jabatan'] ?? '-',
                'unit'                => $pegawai['unit'] ?? '-',
                'jumlah_rhk'          => $jmlTarget,
                'dinilai'             => $jmlDinilai,
                'rata_rata'           => $rataRata,
                'rata_rata_per_bulan' => $rataRataPerBulan
            ];
        }

        // Urutkan dari rata-rata tertinggi
        usort($rows, fn($a, $b) => $b['rata_rata'] <=> $a['rata_rata']);

        // Generate CSV (Universal Excel-compatible)
        $filename = "Rekap_Kinerja_{$namaBulan}_{$tahunTerpilih}.csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM untuk UTF-8 Excel compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        // Header
        fputcsv($output, ['REKAP KINERJA PEGAWAI - ' . strtoupper($namaBulan) . ' ' . $tahunTerpilih], ';');
        fputcsv($output, ['Diekspor pada: ' . date('d/m/Y H:i:s')], ';');
        fputcsv($output, [], ';');

        if ($bulanTerpilih === 'all') {
            fputcsv($output, [
                'No', 'Nama Lengkap', 'NIP', 'Jabatan', 'Unit Kerja', 
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
                'Jumlah RHK Total', 'RHK Dinilai Total', 'Rata-rata Nilai Keseluruhan'
            ], ';');
        } else {
            fputcsv($output, ['No', 'Nama Lengkap', 'NIP', 'Jabatan', 'Unit Kerja', 'Jumlah RHK', 'RHK Dinilai', 'Rata-rata Nilai Kinerja'], ';');
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
}
