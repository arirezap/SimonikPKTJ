<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LedCriteria;
use App\Models\LedSubmission;
use App\Models\LedStandar;
use App\Models\LedScore; 
use App\Controllers\Traits\EccDataTrait;

class EccController extends BaseController
{
    use EccDataTrait;
    
    // Definisikan properti $db
    protected $db;

    public function __construct()
    {
        // Inisialisasi koneksi database
        $this->db = \Config\Database::connect();
    }

    /* // FUNGSI INI DIHAPUS KARENA SUDAH TIDAK DIGUNAKAN
    public function index()
    {
        $selectedTahun = $this->request->getGet('tahun') ?? date('Y');
        $eccData = $this->getDashboardEccData($selectedTahun);

        $data = [
            'page_title' => 'Dashboard ECC',
            'prodiData' => $eccData['prodiData'],
            'selectedTahun' => $selectedTahun,
            'daftar_tahun' => $eccData['daftar_tahun'],
        ];
        return view('ecc/ecc_index', $data);
    }
    */

    public function lkps()
    {
        $dataLkps = [
            'RSTJ' => "Konten Laporan Kinerja Program Studi untuk RSTJ...",
            'TRO' => "Konten Laporan Kinerja Program Studi untuk TRO...",
            'TO' => "Konten Laporan Kinerja Program Studi untuk TO...",
        ];
        
        $prodiData = [];
        foreach (config('Simonik')->prodiList as $prodi) {
             $prodiData[$prodi] = [
                'id_prodi' => $prodi, 
                'nama_prodi' => $prodi, 
                'lkps_content' => $dataLkps[$prodi] ?? "Konten untuk {$prodi} belum tersedia."
            ];
        }

        $data = [
            'page_title' => 'Laporan Kinerja Program Studi (LKPS)',
            'prodi_data' => $prodiData
        ];
        return view('ecc/lkps_index', $data);
    }

    public function led()
    {
        $criteriaModel = new LedCriteria();
        $submissionModel = new LedSubmission();

        $selectedTahun = $this->request->getGet('tahun') ?? date('Y');
        $selectedProdi = $this->request->getGet('prodi') ?? config('Simonik')->prodiList[0];
        
        $role = session()->get('role');

        $criteriaQuery = $criteriaModel
            ->select('led_criteria.*, led_standar.nama_standar')
            ->join('led_standar', 'led_standar.id = led_criteria.id_standar', 'left') 
            ->where('led_criteria.prodi', $selectedProdi);
        
        if (in_array($role, ['admin', 'manajemen'])) {
             $all_criteria_raw = $criteriaQuery->orderBy('led_criteria.id', 'ASC')->findAll();
        } 
        elseif ($role === 'kabag_aak') {
            $all_criteria_raw = $criteriaQuery->whereIn('role_assignment', ['aak', 'all'])->orderBy('led_criteria.id', 'ASC')->findAll();
        } elseif ($role === 'kabag_kuk') {
            $all_criteria_raw = $criteriaQuery->whereIn('role_assignment', ['kuk', 'all'])->orderBy('led_criteria.id', 'ASC')->findAll();
        }
        else { 
             $all_criteria_raw = $criteriaQuery->whereIn('role_assignment', [$role, 'all'])
                                               ->orWhere('role_assignment IS NULL') 
                                               ->orWhere('role_assignment', '')
                                               ->orderBy('led_criteria.id', 'ASC')
                                               ->findAll();
        }

        $submissions = $submissionModel
            ->where('tahun', $selectedTahun)
            ->where('prodi', $selectedProdi)
            ->findAll();
        
        $submitted_data = [];
        foreach ($submissions as $sub) {
            $submitted_data[$sub['led_criteria_id']] = $sub;
        }

        $data = [
            'page_title' => 'Laporan Evaluasi Diri (LED)',
            'all_criteria' => $all_criteria_raw,
            'submitted_data' => $submitted_data,
            'selectedTahun' => $selectedTahun,
            'selectedProdi' => $selectedProdi,
            'currentRole' => $role,
            'prodiList' => config('Simonik')->prodiList,
        ];

        return view('ecc/led_index', $data);
    }

    public function storeLed()
    {
        $db = \Config\Database::connect();
        $submissionModel = new LedSubmission();
        
        $user_id = session()->get('user_id');
        $role = session()->get('role');
        
        $tahun = $this->request->getPost('tahun');
        $prodi = $this->request->getPost('prodi');
        
        $statuses = $this->request->getPost('status');
        $catatan = $this->request->getPost('catatan'); // Link bukti dari staf
        $kabag_approvals = $this->request->getPost('kabag_approved');
        $catatan_kabag = $this->request->getPost('catatan_kabag');
        $catatan_wadir = $this->request->getPost('catatan_wadir');
        
        if (empty($tahun) || empty($prodi)) {
             return redirect()->to('ecc/led?prodi=' . $prodi . '&tahun=' . $tahun)
                ->with('error', 'Data tidak lengkap. Pastikan tahun dan prodi dipilih.');
        }
        
        $loop_data = $catatan ?? $statuses ?? $kabag_approvals ?? $catatan_kabag ?? $catatan_wadir;
        if (empty($loop_data)) {
             return redirect()->to('ecc/led?prodi=' . $prodi . '&tahun=' . $tahun)
                ->with('error', 'Tidak ada data untuk disimpan.');
        }

        $db->transStart();
        try {
            foreach ($loop_data as $kriteria_id => $value) {
                
                $existing = $submissionModel
                    ->where('tahun', $tahun)
                    ->where('prodi', $prodi)
                    ->where('led_criteria_id', $kriteria_id)
                    ->first();

                $data = [
                    'user_id'         => $user_id,
                    'prodi'           => $prodi,
                    'tahun'           => $tahun,
                    'led_criteria_id' => $kriteria_id,
                ];

                if (in_array($role, ['admin', 'manajemen'])) { // Wadir / Admin
                    if(isset($statuses[$kriteria_id])) {
                        $data['status'] = $statuses[$kriteria_id];
                    }
                    if(isset($catatan_wadir[$kriteria_id])) {
                        $data['catatan_wadir'] = $catatan_wadir[$kriteria_id];
                    }
                } elseif (in_array($role, ['kabag_aak', 'kabag_kuk'])) { // Kabag
                    if(isset($kabag_approvals[$kriteria_id])) {
                        $data['kabag_approved'] = $kabag_approvals[$kriteria_id];
                    }
                    if(isset($catatan_kabag[$kriteria_id])) {
                        $data['catatan_kabag'] = $catatan_kabag[$kriteria_id];
                    }
                } else { // Staf (aak, kuk, spm)
                    if(isset($catatan[$kriteria_id])) {
                        $data['catatan'] = $catatan[$kriteria_id]; // Ini adalah link bukti
                    }
                }

                if ($existing) {
                    $data = array_merge($existing, $data);
                    $submissionModel->update($existing['id'], $data);
                } else {
                    $submissionModel->insert($data);
                }
            }
            
            $db->transComplete();

            if ($db->transStatus() === false) {
                 return redirect()->to('ecc/led?prodi=' . $prodi . '&tahun=' . $tahun)
                    ->with('error', 'Terjadi kesalahan pada database saat menyimpan.');
            }

            return redirect()->to('ecc/led?prodi=' . $prodi . '&tahun=' . $tahun)
                ->with('success', 'Data LED untuk prodi ' . $prodi . ' tahun ' . $tahun . ' berhasil disimpan.');
                
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('ecc/led?prodi=' . $prodi . '&tahun=' . $tahun)
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // ==========================================================
    // FUNGSI-FUNGSI SIMULASI PENILAIAN
    // ==========================================================
    
    public function simulasi()
    {
        $criteriaModel = new LedCriteria();
        $scoreModel = new LedScore();
        $submissionModel = new LedSubmission();

        $selectedTahun = $this->request->getGet('tahun') ?? date('Y');
        $selectedProdi = $this->request->getGet('prodi') ?? config('Simonik')->prodiList[0];

        $all_criteria_raw = $criteriaModel
            ->select('led_criteria.*, led_standar.nama_standar')
            ->join('led_standar', 'led_standar.id = led_criteria.id_standar', 'left') 
            ->where('led_criteria.prodi', $selectedProdi)
            ->orderBy('led_criteria.id', 'ASC')
            ->findAll();

        $scores = $scoreModel
            ->where('tahun', $selectedTahun)
            ->where('prodi', $selectedProdi)
            ->findAll();
        
        $submitted_scores = [];
        foreach ($scores as $score) {
            $submitted_scores[$score['led_criteria_id']] = $score;
        }

        $submissions = $submissionModel
            ->where('tahun', $selectedTahun)
            ->where('prodi', $selectedProdi)
            ->findAll();

        $submitted_submissions = [];
        foreach ($submissions as $sub) {
            $submitted_submissions[$sub['led_criteria_id']] = $sub;
        }

        $data = [
            'page_title' => 'Simulasi Penilaian LED',
            'all_criteria' => $all_criteria_raw,
            'submitted_scores' => $submitted_scores,
            'submitted_submissions' => $submitted_submissions,
            'selectedTahun' => $selectedTahun,
            'selectedProdi' => $selectedProdi,
            'prodiList' => config('Simonik')->prodiList,
        ];

        return view('ecc/simulasi_index', $data);
    }

    public function storeSimulasi()
    {
        $db = \Config\Database::connect();
        $scoreModel = new LedScore();
        
        $user_id = session()->get('user_id');
        $tahun = $this->request->getPost('tahun');
        $prodi = $this->request->getPost('prodi');
        $scores = $this->request->getPost('skor');
        
        if (empty($tahun) || empty($prodi) || empty($scores)) {
             return redirect()->to('ecc/simulasi?prodi=' . $prodi . '&tahun=' . $tahun)
                ->with('error', 'Data tidak lengkap.');
        }

        $db->transStart();
        try {
            foreach ($scores as $kriteria_id => $skor) {
                $existing = $scoreModel
                    ->where('tahun', $tahun)
                    ->where('prodi', $prodi)
                    ->where('led_criteria_id', $kriteria_id)
                    ->first();

                $data = [
                    'user_id'         => $user_id,
                    'prodi'           => $prodi,
                    'tahun'           => $tahun,
                    'led_criteria_id' => $kriteria_id,
                    'skor'            => $skor ?: 0, 
                ];

                if ($existing) {
                    $scoreModel->update($existing['id'], $data);
                } else {
                    $scoreModel->insert($data);
                }
            }
            
            $db->transComplete();

            if ($db->transStatus() === false) {
                 return redirect()->to('ecc/simulasi?prodi=' . $prodi . '&tahun=' . $tahun)
                    ->with('error', 'Terjadi kesalahan pada database saat menyimpan.');
            }

            return redirect()->to('ecc/simulasi?prodi=' . $prodi . '&tahun=' . $tahun)
                ->with('success', 'Skor simulasi untuk prodi ' . $prodi . ' tahun ' . $tahun . ' berhasil disimpan.');
                
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('ecc/simulasi?prodi=' . $prodi . '&tahun=' . $tahun)
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
    
    // ==========================================================
    // FUNGSI UNTUK HALAMAN DETAIL STANDAR
    // ==========================================================
    public function detailStandar($standar_id, $prodi, $tahun)
    {
        $standarModel = new LedStandar();
        $criteriaModel = new LedCriteria();

        // 1. Ambil nama standar
        $standar = $standarModel->find($standar_id);
        if (!$standar) {
            return redirect()->to('/ecc')->with('error', 'Standar tidak ditemukan.');
        }

        // 2. Ambil semua data terkait untuk standar, prodi, dan tahun ini
        $criteria_data = $criteriaModel
            ->select('
                led_criteria.id, 
                led_criteria.nama_kriteria, 
                led_criteria.role_assignment,
                s.status, 
                s.catatan, 
                s.kabag_approved,
                s.catatan_kabag, 
                s.catatan_wadir, 
                sc.skor
            ')
            ->join('led_submissions s', 's.led_criteria_id = led_criteria.id AND s.prodi = led_criteria.prodi AND s.tahun = ' . $this->db->escape($tahun), 'left')
            ->join('led_scores sc', 'sc.led_criteria_id = led_criteria.id AND sc.prodi = led_criteria.prodi AND sc.tahun = ' . $this->db->escape($tahun), 'left')
            ->where('led_criteria.id_standar', $standar_id)
            ->where('led_criteria.prodi', $prodi)
            ->orderBy('led_criteria.id', 'ASC')
            ->findAll();

        // 3. Siapkan data for Bar Chart and Table
        $barChartLabels = [];
        $barChartScores = [];
        $barChartTooltips = []; 
        $tableData = []; 
        $no = 1;

        foreach ($criteria_data as $item) {
            $isApproved = ($item['kabag_approved'] == 1 && !empty($item['status']));
            $raw_skor = (float)($item['skor'] ?? 0);
            $skor_display = $isApproved ? $raw_skor : 0;
            
            $skor_alasan = ''; 
            if (!$isApproved) {
                 if (empty($item['catatan'])) {
                    $skor_alasan = 'Bukti (link) belum diunggah.';
                 } else if ($item['kabag_approved'] == 0) {
                    $skor_alasan = 'Menunggu persetujuan Kabag.';
                } elseif (empty($item['status'])) {
                    $skor_alasan = 'Menunggu penilaian Wadir.';
                } else {
                    $skor_alasan = 'Item belum disetujui.'; // Fallback
                }
            }

            $tableRow = $item;
            $tableRow['no'] = $no++;
            $tableRow['skor_display'] = $skor_display;
            $tableRow['skor_alasan_text'] = $skor_alasan; 
            $tableRow['is_approved'] = $isApproved;
            $tableData[] = $tableRow;

            $barChartLabels[] = "Kriteria " . $tableRow['no']; 
            $barChartScores[] = $skor_display; 
            
            $barChartTooltips[] = [
                'nama_kriteria' => $item['nama_kriteria'],
                'skor_alasan' => $skor_alasan 
            ];
        }
        
        $from_page = $this->request->getGet('from') ?? 'user'; // Default ke user jika tidak diset

        $data = [
            'page_title' => 'Detail Standar: ' . $standar['nama_standar'],
            'standar' => $standar,
            'prodi' => $prodi,
            'tahun' => $tahun,
            'criteria_data' => $tableData, 
            'barChartLabels' => json_encode($barChartLabels),
            'barChartScores' => json_encode($barChartScores),
            'barChartTooltips' => json_encode($barChartTooltips),
            'from_page' => $from_page 
        ];

        return view('ecc/detail_standar', $data);
    }
}