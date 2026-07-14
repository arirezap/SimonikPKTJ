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
    
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function lkps()
    {
        $dataLkps = [
            'RSTJ' => "Konten Laporan Kinerja Program Studi untuk RSTJ...",
            'TRO' => "Konten Laporan Kinerja Program Studi untuk TRO...",
            'TO' => "Konten Laporan Kinerja Program Studi untuk TO...",
        ];
        
        $prodiData = [];
        foreach (config('Ecc')->prodiList as $prodi) {
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
        $selectedProdi = $this->request->getGet('prodi') ?? config('Ecc')->prodiList[0];
        
        $currentRole = session()->get('role');

        // 1. Definisikan Role Flags
        $is_staf     = in_array($currentRole, ['aak', 'kuk', 'user']); 
        $is_kabag    = in_array($currentRole, ['kabag_aak', 'kabag_kuk']);
        $is_wadir    = in_array($currentRole, ['admin', 'manajemen', 'direktur', 'spm']);

        // 2. Ambil Semua Kriteria (Master) untuk prodi ini
        $all_criteria = $criteriaModel
            ->select('led_criteria.*, led_standar.nama_standar')
            ->join('led_standar', 'led_standar.id = led_criteria.id_standar', 'left') 
            ->where('led_criteria.prodi', $selectedProdi)
            ->orderBy('led_criteria.id', 'ASC')
            ->findAll();
            
        // 3. Filter Kriteria berdasarkan Role
        $filtered_criteria = [];
        if (!empty($all_criteria)) {
            foreach ($all_criteria as $c) {
                $show = true;
                if ($is_staf && $currentRole !== 'spm') {
                    if ($c['role_assignment'] !== $currentRole && $c['role_assignment'] !== 'all') {
                        $show = false;
                    }
                } elseif ($is_kabag) {
                    $targetRole = str_replace('kabag_', '', $currentRole);
                    if ($c['role_assignment'] !== $targetRole && $c['role_assignment'] !== 'all') {
                        $show = false;
                    }
                }
                if ($show) {
                    $filtered_criteria[] = $c;
                }
            }
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
            'all_criteria' => $all_criteria,
            'filtered_criteria' => $filtered_criteria,
            'submitted_data' => $submitted_data,
            'selectedTahun' => $selectedTahun,
            'selectedProdi' => $selectedProdi,
            'currentRole' => $currentRole,
            'is_staf' => $is_staf,
            'is_kabag' => $is_kabag,
            'is_wadir' => $is_wadir,
            'prodiList' => config('Ecc')->prodiList,
        ];

        return view('ecc/led_index', $data);
    }

    public function storeLed()
    {
        $db = \Config\Database::connect();
        $submissionModel = new LedSubmission();
        
        $user_id = session()->get('id');
        $role = session()->get('role');
        
        $tahun = $this->request->getPost('tahun');
        $prodi = $this->request->getPost('prodi');
        
        $statuses = $this->request->getPost('status');
        $catatan = $this->request->getPost('catatan'); 
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

                if (in_array($role, ['admin', 'manajemen', 'direktur', 'wadir', 'spm'])) { // Wadir / Admin / SPM
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
                } else { // Staf
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
                ->with('success', 'Data LED berhasil disimpan.');
                
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('ecc/led?prodi=' . $prodi . '&tahun=' . $tahun)
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // --- FITUR HAPUS LINK & RESET APPROVAL (DIPERBARUI) ---
    public function deleteLedLink($id)
    {
        $submissionModel = new LedSubmission();
        $submission = $submissionModel->find($id);

        if (!$submission) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $role = strtolower(session()->get('role')); // Normalisasi role ke huruf kecil
        $allowed_roles = ['admin', 'manajemen', 'direktur', 'kabag_aak', 'kabag_kuk', 'spm'];

        if (!in_array($role, $allowed_roles)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus data ini.');
        }

        // PERBAIKAN: Reset semua field terkait
        $updateData = [
            'catatan' => null,       // Hapus link/bukti
            'kabag_approved' => 0,   // Reset approval
            'status' => null,        // Reset status wadir (Ada/Tidak Ada)
            'catatan_kabag' => null, // Hapus catatan review kabag
            'catatan_wadir' => null  // Hapus catatan review wadir
        ];

        if ($submissionModel->update($id, $updateData)) {
            return redirect()->back()->with('success', 'Link dan seluruh riwayat review berhasil dihapus (reset).');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus link.');
        }
    }

    public function simulasi()
    {
        $criteriaModel = new LedCriteria();
        $scoreModel = new LedScore();
        $submissionModel = new LedSubmission();

        $selectedTahun = $this->request->getGet('tahun') ?? date('Y');
        $selectedProdi = $this->request->getGet('prodi') ?? config('Ecc')->prodiList[0];

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
            'prodiList' => config('Ecc')->prodiList,
        ];

        return view('ecc/simulasi_index', $data);
    }

    public function storeSimulasi()
    {
        $db = \Config\Database::connect();
        $scoreModel = new LedScore();
        
        $user_id = session()->get('id');
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
                ->with('success', 'Skor simulasi berhasil disimpan.');
                
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('ecc/simulasi?prodi=' . $prodi . '&tahun=' . $tahun)
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
    
    public function detailStandar($standar_id, $prodi, $tahun)
    {
        $standarModel = new LedStandar();
        $criteriaModel = new LedCriteria();

        $standar = $standarModel->find($standar_id);
        if (!$standar) {
            return redirect()->to('/ecc')->with('error', 'Standar tidak ditemukan.');
        }

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
                    $skor_alasan = 'Item belum disetujui.';
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
        
        $from_page = $this->request->getGet('from') ?? 'user'; 

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