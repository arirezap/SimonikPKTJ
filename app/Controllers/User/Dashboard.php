<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\RencanaKinerja as RencanaKinerjaModel;
use App\Models\User as UserModel;
use App\Controllers\Traits\EccDataTrait; 
use App\Controllers\Traits\KinerjaBatchTrait;

class Dashboard extends BaseController
{
    use EccDataTrait, KinerjaBatchTrait; 

    public function index()
    {
        $rencanaModel = new RencanaKinerjaModel();
        $userModel = new UserModel();
        $user_id = session()->get('id') ?? session()->get('user_id');

        $ajax_type = $this->request->getGet('ajax_type');
        $tahun_ecc = $this->request->getGet('tahun_ecc') ?? date('Y');
        $tahun_kinerja = $this->request->getGet('tahun_kinerja') ?? date('Y');

        // --- 1. PROSES DATA ECC ---
        $eccData = null;
        if (!$this->request->isAJAX() || $ajax_type === 'ecc') {
            $eccData = $this->getDashboardEccData($tahun_ecc);
            if ($this->request->isAJAX() && $ajax_type === 'ecc') {
                return $this->response->setJSON([
                    'prodiData' => $eccData['prodiData'],
                    'tahun' => $tahun_ecc
                ]);
            }
        }

        // --- 2. PROSES DATA Kinerja Pribadi (Berdasarkan Laporan Harian) ---
        if (!$this->request->isAJAX() || $ajax_type === 'kinerja') {
            // BATCH LOAD: Ambil seluruh data kinerja tahun ini dalam 2 query ringkas (O(1) in-memory)
            [$batchTargets, $batchTambahan] = $this->loadBatchKinerjaData($tahun_kinerja);

            $statPersonal = $this->hitungKinerjaPegawai($user_id, 'all', $tahun_kinerja, $batchTargets, $batchTambahan);
            $rataRataCapaian = $statPersonal['rata_rata'];
            $totalIndikator = $statPersonal['total_laporan'];
            
            $lineChartRealisasiData = [];
            for ($i = 1; $i <= 12; $i++) {
                $lineChartRealisasiData[] = $statPersonal['monthly_averages'][$i] ?? 0;
            }

            $role = session()->get('role');
            $isSuper = hasAnyRole(['admin', 'direktur', 'wadir', 'manajemen']);
            $bulanTerpilih = date('n');
            $daftarStaf = $userModel->getAllStaf($user_id, $role);
            $isAtasan = !empty($daftarStaf);
            
            $me = $userModel->find($user_id);
            $isUnitPeers = false;
            
            if (!$isAtasan && !empty($me['unit'])) {
                $daftarStaf = $userModel->where('unit', $me['unit'])
                                         ->where('id !=', $user_id)
                                         ->where('role !=', 'admin')
                                         ->findAll();
                $isUnitPeers = true;
            }

            $rekapDashboard = [];

            if (!empty($daftarStaf)) {
                foreach ($daftarStaf as $staf) {
                    $statStaf = $this->hitungKinerjaPegawai($staf['id'], $bulanTerpilih, $tahun_kinerja, $batchTargets, $batchTambahan);
                    
                    $rekapDashboard[] = [
                        'staf' => $staf,
                        'total_laporan' => $statStaf['total_laporan'],
                        'dinilai' => $statStaf['dinilai'],
                        'rata_rata' => $statStaf['rata_rata']
                    ];
                }
            }

            $unitStats = [];
            $chartPegawaiUnitLabels = [];
            $chartPegawaiUnitData = [];
            
            if ($isSuper && !empty($rekapDashboard)) {
                foreach ($rekapDashboard as $rekap) {
                    $unitName = trim($rekap['staf']['unit'] ?? '');
                    if (empty($unitName)) $unitName = 'Tanpa Unit';
                    
                    if (!isset($unitStats[$unitName])) {
                        $unitStats[$unitName] = ['total_rata' => 0, 'count' => 0, 'anggota' => []];
                    }
                    $unitStats[$unitName]['total_rata'] += $rekap['rata_rata'];
                    $unitStats[$unitName]['count']++;
                    $unitStats[$unitName]['anggota'][] = [
                        'nama' => $rekap['staf']['nama_lengkap'],
                        'jabatan' => $rekap['staf']['jabatan'] ?? '-',
                        'rata_rata' => $rekap['rata_rata'],
                        'dinilai' => $rekap['dinilai'],
                        'total_laporan' => $rekap['total_laporan']
                    ];
                }
                
                uasort($unitStats, function($a, $b) {
                    $avgA = $a['count'] > 0 ? $a['total_rata'] / $a['count'] : 0;
                    $avgB = $b['count'] > 0 ? $b['total_rata'] / $b['count'] : 0;
                    return $avgB <=> $avgA;
                });
                
                foreach ($unitStats as $unitName => $stat) {
                    $chartPegawaiUnitLabels[] = $unitName;
                    $chartPegawaiUnitData[] = $stat['count'] > 0 ? round($stat['total_rata'] / $stat['count'], 2) : 0;
                }
            }

            if ($this->request->isAJAX() && $ajax_type === 'kinerja') {
                return $this->response->setJSON([
                    'rataRataCapaian' => $rataRataCapaian,
                    'totalIndikator' => $totalIndikator,
                    'cumulative_realisasi' => $lineChartRealisasiData,
                    'isAtasan' => $isAtasan,
                    'isUnitPeers' => $isUnitPeers,
                    'rekapDashboard' => $rekapDashboard,
                    'isSuper' => $isSuper,
                    'chartPegawaiUnitLabels' => $chartPegawaiUnitLabels,
                    'chartPegawaiUnitData' => $chartPegawaiUnitData,
                    'unitStats' => $unitStats,
                    'tahun' => $tahun_kinerja
                ]);
            }
        }

        // --- 3. RENDER FULL VIEW JIKA BUKAN AJAX ---
        $data = [
            'page_title' => 'Dashboard',
            'tahun_ecc' => $tahun_ecc,
            'tahun_kinerja' => $tahun_kinerja,
            'daftar_tahun' => $eccData['daftar_tahun'] ?? [date('Y')],
            
            'prodiData' => $eccData['prodiData'] ?? [],
            
            'totalIndikator' => $totalIndikator ?? 0,
            'rataRataCapaian' => $rataRataCapaian ?? 0,
            
            'lineChartLabels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            'lineChartRealisasiData' => $lineChartRealisasiData ?? [],

            'isAtasan' => $isAtasan ?? false,
            'isUnitPeers' => $isUnitPeers ?? false,
            'rekapDashboard' => $rekapDashboard ?? [],

            'isSuper' => $isSuper ?? false,
            'chartPegawaiUnitLabels' => $chartPegawaiUnitLabels ?? [],
            'chartPegawaiUnitData' => $chartPegawaiUnitData ?? [],
            'unitStats' => $unitStats ?? [],
        ];

        return view('user/dashboard', $data);
    }
}