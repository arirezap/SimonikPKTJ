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
            
            // --- PERBAIKAN SEBENARNYA ADA DI SINI ---
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
        $catatan = $this->request->getPost('catatan');
        $kabag_approvals = $this->request->getPost('kabag_approved');
        
        if (empty($tahun) || empty($prodi)) {
             return redirect()->to('ecc/led?prodi=' . $prodi . '&tahun=' . $tahun)
                ->with('error', 'Data tidak lengkap. Pastikan tahun dan prodi dipilih.');
        }
        
        $loop_data = $catatan ?? $statuses ?? $kabag_approvals;
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

                if (in_array($role, ['admin', 'manajemen'])) {
                    if(isset($statuses[$kriteria_id])) {
                        $data['status'] = $statuses[$kriteria_id];
                    }
                } elseif (in_array($role, ['kabag_aak', 'kabag_kuk'])) {
                    if(isset($kabag_approvals[$kriteria_id])) {
                        $data['kabag_approved'] = $kabag_approvals[$kriteria_id];
                    }
                } else {
                    if(isset($catatan[$kriteria_id])) {
                        $data['catatan'] = $catatan[$kriteria_id];
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
            
            // --- PERBAIKAN SEBENARNYA ADA DI SINI ---
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
}