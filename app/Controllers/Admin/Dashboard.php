<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\RencanaKinerja;
use App\Models\Sasaran;
use App\Models\Indikator;
use App\Models\LedScore;
use App\Models\LedCriteria;

class Dashboard extends BaseController
{
    protected $db;
    protected $userModel;
    protected $rencanaModel;
    protected $sasaranModel;
    protected $indikatorModel;
    protected $ledScoreModel;
    protected $ledCriteriaModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->userModel = new User();
        $this->rencanaModel = new RencanaKinerja();
        $this->sasaranModel = new Sasaran();
        $this->indikatorModel = new Indikator();
        $this->ledScoreModel = new LedScore();
        $this->ledCriteriaModel = new LedCriteria();
    }

    // Helper private untuk menangani format data realisasi (String JSON vs Array)
    private function parseRealisasi($data)
    {
        if (is_string($data)) {
            // Decode JSON jika string, jika gagal kembalikan array kosong
            return json_decode($data, true) ?? [];
        }
        // Jika sudah array kembalikan, jika null/lainnya kembalikan array kosong
        return is_array($data) ? $data : [];
    }

    public function index()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? 'all'; // all, 1, 2, ...

        // ----------------------------------------------------------------
        // 1. DATA UNTUK SUMMARY CARDS & AGREGASI INDIKATOR
        // ----------------------------------------------------------------
        // Ambil SEMUA rencana kinerja tahun ini (Hanya dari user yang dipantau / bukan admin)
        $allRencana = $this->rencanaModel
            ->select('rencana_kinerja.*')
            ->join('users', 'users.id = rencana_kinerja.user_id')
            ->where('users.role !=', 'admin') // Filter: Hanya user yang dipantau
            ->where('rencana_kinerja.tahun_anggaran', $tahun)
            ->findAll();

        $totalPersen = 0;
        $countRencana = 0;
        
        // Array untuk menampung data agregat indikator (untuk Grafik Bar 2)
        $indikatorData = []; 

        foreach ($allRencana as $row) {
            $target = (float) $row['target_utama'];
            
            // Gunakan Helper agar aman dari error json_decode
            $realisasi_bulanan = $this->parseRealisasi($row['realisasi_bulanan'] ?? []);

            $realisasi = 0;
            if ($bulan === 'all') {
                // Total Tahunan
                $realisasi = array_sum(array_map('floatval', $realisasi_bulanan));
            } else {
                // Per Bulan
                $idx = (int)$bulan - 1;
                $realisasi = isset($realisasi_bulanan[$idx]) ? (float)$realisasi_bulanan[$idx] : 0;
            }

            // Hitung capaian (hindari division by zero)
            $capaian = ($target > 0) ? ($realisasi / $target) * 100 : 0;
            
            $totalPersen += $capaian;
            $countRencana++;

            // AGREGASI DATA PER INDIKATOR
            $namaIndikator = $row['indikator_kinerja'];
            $satuan = $row['satuan'] ?? ''; // Ambil satuan untuk tooltip

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
        $totalIndikator = count($indikatorData); // Hitung jumlah indikator unik

        // ----------------------------------------------------------------
        // 2. DATA UNTUK GRAFIK PERBANDINGAN USER (TIM/UNIT)
        // ----------------------------------------------------------------
        $users = $this->userModel->where('role !=', 'admin')->findAll();
        $chartUserLabels = [];
        $chartUserData = [];
        $kinerja_per_user = [];

        foreach ($users as $u) {
            // Cari rencana milik user ini dari data $allRencana yang sudah diambil
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
        // 3. SIAPKAN DATA GRAFIK INDIKATOR (PERSENTASE & URUT ABJAD NATURAL)
        // ----------------------------------------------------------------
        
        // PERBAIKAN: Urutkan array indikator berdasarkan key (nama indikator) secara Ascending Natural
        ksort($indikatorData, SORT_NATURAL);

        $chartIndikatorLabels = [];
        $chartIndikatorPersen = [];
        $chartIndikatorMeta = []; // Untuk Tooltip (Target & Realisasi Asli)

        foreach ($indikatorData as $k => $v) {
            $chartIndikatorLabels[] = $k; // Nama Indikator
            
            // Hitung Persentase untuk grafik
            $persen = ($v['target'] > 0) ? ($v['realisasi'] / $v['target'] * 100) : 0;
            $chartIndikatorPersen[] = round($persen, 2);

            // Simpan Data Asli untuk Tooltip
            $chartIndikatorMeta[] = [
                'target' => $v['target'],
                'realisasi' => $v['realisasi'],
                'satuan' => $v['satuan']
            ];
        }

        // ----------------------------------------------------------------
        // 4. DATA UNTUK GRAFIK RADAR (ECC/LED)
        // ----------------------------------------------------------------
        $prodiList = config('Simonik')->prodiList;
        $prodiData = [];

        foreach ($prodiList as $prodi) {
            $scores = $this->ledScoreModel
                ->select('led_standar.nama_standar, SUM(led_scores.skor) as total_skor, COUNT(led_scores.id) as jumlah_item')
                ->join('led_criteria', 'led_criteria.id = led_scores.led_criteria_id')
                ->join('led_standar', 'led_standar.id = led_criteria.id_standar')
                ->where('led_scores.tahun', $tahun)
                ->where('led_scores.prodi', $prodi)
                ->groupBy('led_standar.nama_standar')
                ->findAll();

            $radarLabels = [];
            $radarData = [];
            $radarLabelIds = [];
            
            foreach ($scores as $s) {
                $radarLabels[] = $s['nama_standar'];
                $avg = ($s['jumlah_item'] > 0) ? $s['total_skor'] / $s['jumlah_item'] : 0;
                $radarData[] = ($avg / 4) * 100; 
                $radarLabelIds[] = 0; 
            }

            if (empty($radarLabels)) {
                $standarMaster = $this->db->table('led_standar')->orderBy('id', 'ASC')->get()->getResultArray();
                foreach($standarMaster as $sm) {
                    $radarLabels[] = $sm['nama_standar'];
                    $radarData[] = 0;
                    $radarLabelIds[] = $sm['id'];
                }
            }

            $prodiData[$prodi] = [
                'id_prodi' => $prodi,
                'nama_prodi' => $prodi,
                'chart_labels' => $radarLabels,
                'chart_data' => $radarData,
                'chart_label_ids' => $radarLabelIds
            ];
        }

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
            
            // Data Grafik Indikator Baru (Persentase + Meta Tooltip)
            'chartIndikatorLabels' => $chartIndikatorLabels,
            'chartIndikatorPersen' => $chartIndikatorPersen,
            'chartIndikatorMeta' => $chartIndikatorMeta,

            'kinerja_per_user' => $kinerja_per_user,
            'prodiData' => $prodiData
        ];

        // PERBAIKAN DISINI: Ubah 'Admin/dashboard' menjadi 'admin/dashboard' (huruf kecil)
        return view('admin/dashboard', $data);
    }
}