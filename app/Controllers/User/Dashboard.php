<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\User as UserModel;
use App\Controllers\Traits\EccDataTrait; 
use App\Controllers\Traits\KinerjaBatchTrait;

class Dashboard extends BaseController
{
    use EccDataTrait, KinerjaBatchTrait; 

    public function index()
    {
        $userModel = new UserModel();
        $db = \Config\Database::connect();
        $user_id = session()->get('id') ?? session()->get('user_id');

        $ajax_type = $this->request->getGet('ajax_type');
        $tahun_ecc = $this->request->getGet('tahun_ecc') ?? date('Y');
        $tahun_kinerja = $this->request->getGet('tahun_kinerja') ?? (string)date('Y');
        $bulanTerpilih = $this->request->getGet('bulan_kinerja') ?? 'all';

        // --- 1. PROSES DATA ECC ---
        $eccData = null;
        if (!$this->request->isAJAX() || $ajax_type === 'ecc') {
            $eccData = $this->getDashboardEccData($tahun_ecc);
            if ($this->request->isAJAX() && $ajax_type === 'ecc') {
                return $this->response->setJSON([
                    'prodiData' => $eccData['prodiData'],
                    'tahun'     => $tahun_ecc
                ]);
            }
        }

        // --- 2. PROSES DATA Kinerja Pribadi & Staf / Rekan Unit ---
        if (!$this->request->isAJAX() || $ajax_type === 'kinerja') {
            // BATCH LOAD: Ambil seluruh data kinerja tahun ini dalam 2 query ringkas (O(1) in-memory)
            [$batchTargets, $batchTambahan] = $this->loadBatchKinerjaData($tahun_kinerja);

            $statPersonal = $this->hitungKinerjaPegawai($user_id, $bulanTerpilih, $tahun_kinerja, $batchTargets, $batchTambahan);
            $rataRataCapaian = $statPersonal['rata_rata'];
            $totalIndikator = $statPersonal['total_laporan'];
            
            $statYear = ($bulanTerpilih === 'all') ? $statPersonal : $this->hitungKinerjaPegawai($user_id, 'all', $tahun_kinerja, $batchTargets, $batchTambahan);
            $lineChartRealisasiData = [];
            for ($i = 1; $i <= 12; $i++) {
                $lineChartRealisasiData[] = $statYear['monthly_averages'][$i] ?? 0;
            }

            $role = session()->get('role');
            $bulanCek = ($bulanTerpilih === 'all' || empty($bulanTerpilih)) ? 12 : (int)$bulanTerpilih;
            $periodDate = sprintf('%04d-%02d-01', (int)$tahun_kinerja, $bulanCek);

            $daftarStaf = $userModel->getAllStaf($user_id, $role, $periodDate);
            $isAtasan = !empty($daftarStaf);
            
            $userSelect = 'id, nama_lengkap, nip, username, jabatan, unit, role, status_pegawai, tmt_selesai, foto';
            $me = $userModel->select($userSelect)->find($user_id);
            if ($me) {
                $penugasanMe = $userModel->getPenugasanPeriode($user_id, $bulanCek, (int)$tahun_kinerja);
                if ($penugasanMe) {
                    $me['unit'] = $penugasanMe['unit'] ?? $me['unit'];
                    $me['jabatan'] = $penugasanMe['jabatan'] ?? $me['jabatan'];
                }
            }
            $isUnitPeers = false;
            
            // Jika bukan atasan langsung, tampilkan data rekan kerja aktif dalam 1 unit
            if (!$isAtasan && !empty($me['unit'])) {
                $peerQuery = $userModel->select($userSelect)
                    ->where('unit', $me['unit'])
                    ->where('id !=', $user_id)
                    ->where('role !=', 'admin');
                
                if ($db->fieldExists('status_pegawai', 'users')) {
                    if ($db->fieldExists('tmt_selesai', 'users')) {
                        $peerQuery->groupStart()
                            ->where('status_pegawai', 'aktif')
                            ->orGroupStart()
                                ->whereIn('status_pegawai', ['mutasi', 'pensiun'])
                                ->where('tmt_selesai >=', $periodDate)
                            ->groupEnd()
                        ->groupEnd();
                    } else {
                        $peerQuery->where('status_pegawai', 'aktif');
                    }
                }
                $daftarStaf = $peerQuery->findAll();
                $isUnitPeers = true;
            }

            $rekapDashboard = [];

            if (!empty($daftarStaf)) {
                foreach ($daftarStaf as $staf) {
                    $penugasanStaf = $userModel->getPenugasanPeriode($staf['id'], $bulanCek, (int)$tahun_kinerja);
                    if ($penugasanStaf) {
                        $staf['unit'] = $penugasanStaf['unit'] ?? $staf['unit'];
                        $staf['jabatan'] = $penugasanStaf['jabatan'] ?? $staf['jabatan'];
                    }

                    $statStaf = $this->hitungKinerjaPegawai($staf['id'], $bulanTerpilih, $tahun_kinerja, $batchTargets, $batchTambahan);
                    
                    $rekapDashboard[] = [
                        'staf'           => $staf,
                        'total_pokok'    => $statStaf['total_pokok'],
                        'total_tambahan' => $statStaf['total_tambahan'],
                        'total_laporan'  => $statStaf['total_laporan'],
                        'dinilai'        => $statStaf['dinilai'],
                        'rata_rata'      => $statStaf['rata_rata']
                    ];
                }
            }

            if ($this->request->isAJAX() && $ajax_type === 'kinerja') {
                return $this->response->setJSON([
                    'rataRataCapaian'      => $rataRataCapaian,
                    'totalIndikator'       => $totalIndikator,
                    'cumulative_realisasi' => $lineChartRealisasiData,
                    'isAtasan'             => $isAtasan,
                    'isUnitPeers'          => $isUnitPeers,
                    'rekapDashboard'       => $rekapDashboard,
                    'tahun'                => $tahun_kinerja,
                    'bulan'                => $bulanTerpilih
                ]);
            }
        }

        // --- 3. RENDER FULL VIEW JIKA BUKAN AJAX ---
        $data = [
            'page_title'             => 'Dashboard',
            'tahun_ecc'              => $tahun_ecc,
            'tahun_kinerja'          => $tahun_kinerja,
            'bulan_kinerja'          => $bulanTerpilih,
            'daftar_tahun'           => $eccData['daftar_tahun'] ?? [date('Y')],
            'prodiData'              => $eccData['prodiData'] ?? [],
            'totalIndikator'         => $totalIndikator ?? 0,
            'rataRataCapaian'        => $rataRataCapaian ?? 0,
            'lineChartLabels'        => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            'lineChartRealisasiData' => $lineChartRealisasiData ?? [],
            'isAtasan'               => $isAtasan ?? false,
            'isUnitPeers'            => $isUnitPeers ?? false,
            'rekapDashboard'         => $rekapDashboard ?? [],
        ];

        return view('user/dashboard', $data);
    }
}