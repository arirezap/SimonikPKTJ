<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\RencanaKinerja;
use App\Models\Sasaran;
use App\Models\Indikator;
// Model LED tidak perlu di-load manual lagi karena sudah dihandle oleh Trait
use App\Controllers\Traits\EccDataTrait; // 1. Import Trait

class Dashboard extends BaseController
{
    use EccDataTrait; // 2. Gunakan Trait
    
    protected $db;
    protected $userModel;
    protected $rencanaModel;
    protected $sasaranModel;
    protected $indikatorModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->userModel = new User();
        $this->rencanaModel = new RencanaKinerja();
        $this->sasaranModel = new Sasaran();
        $this->indikatorModel = new Indikator();
        // Model LED dihapus dari constructor karena ada di Trait
    }

    // Helper private untuk menangani format data realisasi
    private function parseRealisasi($data)
    {
        if (is_string($data)) {
            return json_decode($data, true) ?? [];
        }
        return is_array($data) ? $data : [];
    }

    public function index()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? 'all'; 

        // ----------------------------------------------------------------
        // 1. DATA UNTUK SUMMARY CARDS & AGREGASI INDIKATOR
        // ----------------------------------------------------------------
        $allRencana = $this->rencanaModel
            ->select('rencana_kinerja.*')
            ->join('users', 'users.id = rencana_kinerja.user_id')
            ->where('users.role !=', 'admin') 
            ->where('rencana_kinerja.tahun_anggaran', $tahun)
            ->findAll();

        $totalPersen = 0;
        $countRencana = 0;
        
        $indikatorData = []; 

        foreach ($allRencana as $row) {
            $target = (float) $row['target_utama'];
            $realisasi_bulanan = $this->parseRealisasi($row['realisasi_bulanan'] ?? []);

            $realisasi = 0;
            if ($bulan === 'all') {
                $realisasi = array_sum(array_map('floatval', $realisasi_bulanan));
            } else {
                $idx = (int)$bulan - 1;
                $realisasi = isset($realisasi_bulanan[$idx]) ? (float)$realisasi_bulanan[$idx] : 0;
            }

            $capaian = ($target > 0) ? ($realisasi / $target) * 100 : 0;
            
            $totalPersen += $capaian;
            $countRencana++;

            // AGREGASI DATA PER INDIKATOR
            $namaIndikator = $row['indikator_kinerja'];
            $satuan = $row['satuan'] ?? ''; 

            if (!isset($indikatorData[$namaIndikator])) {
                $indikatorData[$namaIndikator] = [
                    'target' => 0, 
                    'realisasi' => 0,
                    'satuan' => $satuan
                ];
            }
            $indikatorData[$namaIndikator]['target'] += $target;
            $indikatorData[$namaIndikator]['realisasi'] += $realisasi;
        }

        $rataRataCapaianGlobal = ($countRencana > 0) ? $totalPersen / $countRencana : 0;
        $totalIndikator = count($indikatorData);

        // ----------------------------------------------------------------
        // 2. DATA UNTUK GRAFIK PERBANDINGAN USER (TIM/UNIT)
        // ----------------------------------------------------------------
        $users = $this->userModel->where('role !=', 'admin')->findAll();
        $chartUserLabels = [];
        $chartUserData = [];
        $kinerja_per_user = [];

        foreach ($users as $u) {
            $rencanaUser = array_filter($allRencana, function($item) use ($u) {
                return $item['user_id'] == $u['id'];
            });

            if (empty($rencanaUser)) continue;

            $userTotalCapaian = 0;
            $userItemCount = 0;
            $userTotalTarget = 0;
            $userTotalRealisasi = 0;

            foreach ($rencanaUser as $r) {
                $t = (float) $r['target_utama'];
                $rb = $this->parseRealisasi($r['realisasi_bulanan'] ?? []);
                
                $real = 0;
                if ($bulan === 'all') {
                    $real = array_sum(array_map('floatval', $rb));
                } else {
                    $idx = (int)$bulan - 1;
                    $real = isset($rb[$idx]) ? (float)$rb[$idx] : 0;
                }

                $c = ($t > 0) ? ($real / $t) * 100 : 0;
                
                $userTotalCapaian += $c;
                $userItemCount++;
                $userTotalTarget += $t;
                $userTotalRealisasi += $real;
            }

            $avgCapaian = ($userItemCount > 0) ? $userTotalCapaian / $userItemCount : 0;

            $chartUserLabels[] = $u['nama_lengkap'];
            $chartUserData[] = round($avgCapaian, 2);

            $kinerja_per_user[] = [
                'nama' => $u['nama_lengkap'],
                'jumlah_indikator' => $userItemCount,
                'total_target' => $userTotalTarget,
                'total_realisasi' => $userTotalRealisasi,
                'persentase_capaian' => $avgCapaian
            ];
        }

        // ----------------------------------------------------------------
        // 3. SIAPKAN DATA GRAFIK INDIKATOR
        // ----------------------------------------------------------------
        ksort($indikatorData, SORT_NATURAL);

        $chartIndikatorLabels = [];
        $chartIndikatorPersen = [];
        $chartIndikatorMeta = [];

        foreach ($indikatorData as $k => $v) {
            $chartIndikatorLabels[] = $k;
            $persen = ($v['target'] > 0) ? ($v['realisasi'] / $v['target'] * 100) : 0;
            $chartIndikatorPersen[] = round($persen, 2);
            $chartIndikatorMeta[] = [
                'target' => $v['target'],
                'realisasi' => $v['realisasi'],
                'satuan' => $v['satuan']
            ];
        }

        // ----------------------------------------------------------------
        // 4. DATA ECC (MENGGUNAKAN TRAIT AGAR SAMA DENGAN USER)
        // ----------------------------------------------------------------
        // Ini menggantikan kode manual yang panjang sebelumnya
        $eccData = $this->getDashboardEccData($tahun);
        $prodiData = $eccData['prodiData']; // Hasilnya sudah format standar (label, data, ids)

        // ----------------------------------------------------------------
        
        $currentYear = date('Y');
        $daftar_tahun = range($currentYear, $currentYear - 4);

        $data = [
            'page_title' => 'Dashboard Admin',
            'tahun_terpilih' => $tahun,
            'bulan_terpilih' => $bulan,
            'daftar_tahun' => $daftar_tahun,
            
            'totalIndikator' => $totalIndikator,
            'rataRataCapaianGlobal' => $rataRataCapaianGlobal,
            
            'chartLabels' => $chartUserLabels,
            'chartData' => $chartUserData,
            
            'chartIndikatorLabels' => $chartIndikatorLabels,
            'chartIndikatorPersen' => $chartIndikatorPersen,
            'chartIndikatorMeta' => $chartIndikatorMeta,

            'kinerja_per_user' => $kinerja_per_user,
            
            // Data Radar Chart dari Trait
            'prodiData' => $prodiData
        ];

        return view('admin/dashboard', $data);
    }
}