<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LedCriteria;
use App\Models\LedSubmission;
use App\Models\LedCategory; 
use App\Models\LedScore; 

class EccController extends BaseController
{
    /**
     * Menampilkan halaman Dashboard ECC (Diagram Jaring Laba-laba)
     * Menggunakan data nyata dari led_scores
     */
    public function index()
    {
        $scoreModel = new LedScore();
        $criteriaModel = new LedCriteria();
        $categoryModel = new LedCategory();

        $selectedTahun = $this->request->getGet('tahun') ?? date('Y');

        // 1. Ambil semua Kategori dari Master Kategori (Label Grafik)
        $categories_raw = $categoryModel->orderBy('nama_kategori', 'ASC')->findAll();
        $chart_labels = array_column($categories_raw, 'nama_kategori');
        
        // 2. Ambil semua Kriteria (untuk pemetaan)
        $all_criteria = $criteriaModel->findAll();
        $criteriaMap = []; // Peta untuk [kriteria_id => kategori]
        foreach ($all_criteria as $item) {
            $criteriaMap[$item['id']] = $item['kategori'];
        }

        // 3. Ambil data SKOR untuk SEMUA prodi di TAHUN TERPILIH
        $scores_data = $scoreModel
            ->where('tahun', $selectedTahun)
            ->findAll();

        // 4. Inisialisasi data prodi
        $prodiList = ['RSTJ', 'TRO', 'TO'];
        $prodiData = [];

        // 5. Inisialisasi struktur skor
        $scores = [];
        foreach ($prodiList as $prodi) {
            foreach ($chart_labels as $label) {
                $scores[$prodi][$label] = ['total_skor' => 0, 'jumlah_item' => 0];
            }
        }

        // 6. Hitung 'total' item per kategori dari Master Kriteria
        foreach ($all_criteria as $item) {
            if (!empty($item['kategori']) && in_array($item['kategori'], $chart_labels)) {
                foreach ($prodiList as $prodi) {
                    if (isset($scores[$prodi][$item['kategori']])) {
                        $scores[$prodi][$item['kategori']]['jumlah_item']++;
                    }
                }
            }
        }

        // 7. Akumulasi 'total_skor' dari data Skor yang sudah diinput
        foreach ($scores_data as $score) {
            $prodi = $score['prodi'];
            $kriteria_id = $score['led_criteria_id'];
            
            if (isset($criteriaMap[$kriteria_id]) && !empty($criteriaMap[$kriteria_id])) {
                $kategori = $criteriaMap[$kriteria_id];
                if(isset($scores[$prodi][$kategori])) {
                     $scores[$prodi][$kategori]['total_skor'] += (float)$score['skor'];
                }
            }
        }

        // 8. Hitung Rata-rata skor per kategori dan siapkan data untuk view
        foreach ($prodiList as $prodi) {
            $chart_data = [];
            foreach ($chart_labels as $label) {
                $total_skor = $scores[$prodi][$label]['total_skor'];
                $jumlah_item = $scores[$prodi][$label]['jumlah_item'];
                
                // Rata-rata skor untuk kategori tsb
                $avg_score = ($jumlah_item > 0) ? ($total_skor / $jumlah_item) : 0; 
                $chart_data[] = round($avg_score, 2);
            }
            
            $prodiData[$prodi] = [
                'id_prodi' => $prodi,
                'nama_prodi' => $prodi,
                'chart_labels' => $chart_labels,
                'chart_data' => $chart_data
            ];
        }

        // Ambil daftar tahun unik dari submissions untuk filter
        $daftar_tahun_raw = $scoreModel->select('tahun')->distinct()->orderBy('tahun', 'DESC')->findAll();
        $daftar_tahun = array_column($daftar_tahun_raw, 'tahun');
        if (!in_array(date('Y'), $daftar_tahun)) {
            $daftar_tahun[] = date('Y');
            rsort($daftar_tahun);
        }

        $data = [
            'page_title' => 'Dashboard ECC',
            'prodiData' => $prodiData,
            'selectedTahun' => $selectedTahun,
            'daftar_tahun' => $daftar_tahun,
        ];
        return view('ecc/ecc_index', $data);
    }

    /**
     * Menampilkan halaman LKPS
     */
    public function lkps()
    {
        $dataLkps = [
            'RSTJ' => "Konten Laporan Kinerja Program Studi untuk RSTJ...",
            'TRO' => "Konten Laporan Kinerja Program Studi untuk TRO...",
            'TO' => "Konten Laporan Kinerja Program Studi untuk TO...",
        ];
        $data = [
            'page_title' => 'Laporan Kinerja Program Studi (LKPS)',
            'dataLkps' => $dataLkps
        ];
        return view('ecc/lkps_index', $data);
    }

    /**
     * Menampilkan halaman LED (Checklist Kriteria)
     */
    public function led()
    {
        $criteriaModel = new LedCriteria();
        $submissionModel = new LedSubmission();

        $selectedTahun = $this->request->getGet('tahun') ?? date('Y');
        $selectedProdi = $this->request->getGet('prodi') ?? 'RSTJ';
        
        $role = session()->get('role');

        // Ambil semua data master kriteria, filter berdasarkan peran
        $criteriaQuery = $criteriaModel;
        
        if (in_array($role, ['admin', 'manajemen'])) {
             // Admin & Manajemen bisa melihat semua kriteria
             $all_criteria_raw = $criteriaModel->findAll();
        } 
        // Kabag melihat semua kriteria yang ditugaskan ke unitnya (AAK/KUK) atau 'all'
        elseif ($role === 'kabag_aak') {
            $all_criteria_raw = $criteriaQuery->whereIn('role_assignment', ['aak', 'all'])->findAll();
        } elseif ($role === 'kabag_kuk') {
            $all_criteria_raw = $criteriaQuery->whereIn('role_assignment', ['kuk', 'all'])->findAll();
        }
        // Staf AAK/KUK hanya melihat yang ditugaskan ke mereka atau 'all'
        else { 
             $all_criteria_raw = $criteriaQuery->whereIn('role_assignment', [$role, 'all'])
                                               ->orWhere('role_assignment IS NULL') 
                                               ->orWhere('role_assignment', '')
                                               ->findAll();
        }

        usort($all_criteria_raw, function($a, $b) {
            return strnatcmp($a['nomor_kriteria'], $b['nomor_kriteria']);
        });

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
            'currentRole' => $role // Kirim data peran ke view
        ];

        return view('ecc/led_index', $data);
    }

    /**
     * Menyimpan data checklist LED
     */
    public function storeLed()
    {
        $db = \Config\Database::connect();
        $submissionModel = new LedSubmission();
        
        $user_id = session()->get('user_id');
        $role = session()->get('role'); // Ambil peran
        
        $tahun = $this->request->getPost('tahun');
        $prodi = $this->request->getPost('prodi');
        
        // Ambil data dari form
        $statuses = $this->request->getPost('status');
        $catatan = $this->request->getPost('catatan');
        $kabag_approvals = $this->request->getPost('kabag_approved');
        
        if (empty($tahun) || empty($prodi)) {
             return redirect()->to('ecc/led?prodi=' . $prodi . '&tahun=' . $tahun)
                ->with('error', 'Data tidak lengkap. Pastikan tahun dan prodi dipilih.');
        }
        
        // Tentukan data mana yang akan di-loop
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

                // Siapkan data dasar
                $data = [
                    'user_id'         => $user_id,
                    'prodi'           => $prodi,
                    'tahun'           => $tahun,
                    'led_criteria_id' => $kriteria_id,
                ];

                // --- LOGIKA PENYIMPANAN BERDASARKAN PERAN ---
                if (in_array($role, ['admin', 'manajemen'])) {
                    // Wadir/Manajemen: Hanya bisa mengubah 'status'
                    if(isset($statuses[$kriteria_id])) {
                        $data['status'] = $statuses[$kriteria_id];
                    }
                } elseif (in_array($role, ['kabag_aak', 'kabag_kuk'])) {
                    // Kabag: Hanya bisa mengubah 'kabag_approved'
                    if(isset($kabag_approvals[$kriteria_id])) {
                        $data['kabag_approved'] = $kabag_approvals[$kriteria_id];
                    }
                } else {
                    // AAK/KUK (Staf): Hanya bisa mengubah 'catatan'
                    if(isset($catatan[$kriteria_id])) {
                        $data['catatan'] = $catatan[$kriteria_id];
                    }
                }
                // --- Akhir Logika Peran ---

                if ($existing) {
                    // Gabungkan data baru dengan data lama yang tidak diubah
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
        $submissionModel = new LedSubmission(); // TAMBAHKAN MODEL SUBMISSION

        $selectedTahun = $this->request->getGet('tahun') ?? date('Y');
        $selectedProdi = $this->request->getGet('prodi') ?? 'RSTJ';

        $all_criteria_raw = $criteriaModel->findAll();
        usort($all_criteria_raw, function($a, $b) {
            return strnatcmp($a['nomor_kriteria'], $b['nomor_kriteria']);
        });

        // Ambil data skor yang sudah diinput
        $scores = $scoreModel
            ->where('tahun', $selectedTahun)
            ->where('prodi', $selectedProdi)
            ->findAll();
        
        $submitted_scores = [];
        foreach ($scores as $score) {
            $submitted_scores[$score['led_criteria_id']] = $score;
        }

        // BARU: Ambil data submission (link & status) yang sudah diinput
        $submissions = $submissionModel
            ->where('tahun', $selectedTahun)
            ->where('prodi', $selectedProdi)
            ->findAll();

        $submitted_submissions = [];
        foreach ($submissions as $sub) {
            $submitted_submissions[$sub['led_criteria_id']] = $sub;
        }
        // AKHIR BARU

        $data = [
            'page_title' => 'Simulasi Penilaian LED',
            'all_criteria' => $all_criteria_raw,
            'submitted_scores' => $submitted_scores,
            'submitted_submissions' => $submitted_submissions, // Kirim data submission ke view
            'selectedTahun' => $selectedTahun,
            'selectedProdi' => $selectedProdi
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
