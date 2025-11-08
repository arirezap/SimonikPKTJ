<?php

namespace App\Controllers\Traits;

use App\Models\LedCriteria;
use App\Models\LedStandar;
use App\Models\LedScore; 
use App\Models\LedSubmission; 
use App\Models\RencanaKinerja as RencanaKinerjaModel;

trait EccDataTrait
{
    /**
     * Mengambil data ECC dan daftar tahun gabungan untuk dashboard.
     * Logika ini mengambil skor HANYA JIKA kriteria sudah diapprove
     * oleh Kabag DAN Wadir/Manajemen.
     */
    private function getDashboardEccData($selectedTahun)
    {
        $scoreModel = new LedScore();
        $criteriaModel = new LedCriteria();
        $standarModel = new LedStandar();
        $submissionModel = new LedSubmission(); 

        // 1. Ambil semua Standar (Label Grafik)
        $standar_raw = $standarModel->orderBy('nama_standar', 'ASC')->findAll();
        $chart_labels = array_column($standar_raw, 'nama_standar');
        
        $prodiList = config('Simonik')->prodiList;
        $prodiData = [];
        $scores = [];

        // 2. Inisialisasi struktur skor untuk SEMUA prodi
        foreach ($prodiList as $prodi) {
            foreach ($chart_labels as $label) {
                $scores[$prodi][$label] = ['total_skor' => 0, 'jumlah_item' => 0];
            }
        }
        
        // 3. Ambil SEMUA Kriteria (untuk pemetaan dan penghitungan)
        $all_criteria = $criteriaModel
            ->select('led_criteria.id, led_criteria.prodi, led_standar.nama_standar')
            ->join('led_standar', 'led_standar.id = led_criteria.id_standar', 'left') // Menggunakan id_standar
            ->findAll();
            
        $criteriaMap = []; // Peta untuk [kriteria_id => [nama_standar, prodi]]
        foreach ($all_criteria as $item) {
            $criteriaMap[$item['id']] = [
                'standar' => $item['nama_standar'], // Menggunakan nama_standar
                'prodi' => $item['prodi']
            ];
            
            // 4. Hitung 'total' item per standar per prodi
            $prodi = $item['prodi'];
            $standar_nama = $item['nama_standar'];
            if (!empty($standar_nama) && in_array($standar_nama, $chart_labels) && isset($scores[$prodi])) {
                $scores[$prodi][$standar_nama]['jumlah_item']++;
            }
        }

        // 5. Ambil data SKOR untuk SEMUA prodi di TAHUN TERPILIH
        $scores_data = $scoreModel
            ->where('tahun', $selectedTahun)
            ->findAll();
        
        // 6. Ambil data APPROVAL untuk SEMUA prodi di TAHUN TERPILIH
        $submissions_data = $submissionModel
            ->where('tahun', $selectedTahun)
            ->findAll();
        
        // Buat Peta Persetujuan (Approval Map) untuk pengecekan cepat
        $approvalMap = [];
        foreach ($submissions_data as $sub) {
            $is_kabag_approved = ($sub['kabag_approved'] ?? 0) == 1;
            $is_wadir_approved = !empty($sub['status']);
            
            if ($is_kabag_approved && $is_wadir_approved) {
                // Kunci unik: 'prodi-kriteria_id'
                $key = $sub['prodi'] . '-' . $sub['led_criteria_id'];
                $approvalMap[$key] = true;
            }
        }

        // 7. Akumulasi 'total_skor' HANYA JIKA SUDAH DISETUJUI
        foreach ($scores_data as $score) {
            $prodi = $score['prodi'];
            $kriteria_id = $score['led_criteria_id'];
            
            if (isset($criteriaMap[$kriteria_id])) {
                $standar = $criteriaMap[$kriteria_id]['standar'];
                
                // Cek Peta Persetujuan
                $approvalKey = $prodi . '-' . $kriteria_id;
                $is_approved = isset($approvalMap[$approvalKey]);

                if(isset($scores[$prodi][$standar]) && $criteriaMap[$kriteria_id]['prodi'] == $prodi) {
                     // Jika disetujui, tambahkan skor. Jika tidak, skornya dianggap 0 (tidak menambah total_skor).
                     if ($is_approved) {
                        $scores[$prodi][$standar]['total_skor'] += (float)$score['skor'];
                     }
                }
            }
        }

        // 8. Hitung Rata-rata skor per standar dan siapkan data untuk view
        foreach ($prodiList as $prodi) {
            $chart_data = [];
            foreach ($chart_labels as $label) {
                $total_skor = $scores[$prodi][$label]['total_skor'];
                $jumlah_item = $scores[$prodi][$label]['jumlah_item'];
                
                // Rata-rata dihitung berdasarkan total skor (yang sudah disetujui) dibagi jumlah total item
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

        // Ambil daftar tahun unik
        $rencanaModel = new RencanaKinerjaModel();
        
        $ecc_tahun_raw = $scoreModel->select('tahun')->distinct()->orderBy('tahun', 'DESC')->findAll();
        $kinerja_tahun_raw = $rencanaModel->select('tahun_anggaran AS tahun')->distinct()->orderBy('tahun_anggaran', 'DESC')->findAll();
        
        $ecc_tahun = array_column($ecc_tahun_raw, 'tahun');
        $kinerja_tahun = array_column($kinerja_tahun_raw, 'tahun');
        
        $daftar_tahun = array_unique(array_merge($ecc_tahun, $kinerja_tahun));
        rsort($daftar_tahun); // Urutkan dari terbaru
        
        if (empty($daftar_tahun)) {
             $daftar_tahun[] = date('Y');
        }

        return [
            'prodiData' => $prodiData,
            'daftar_tahun' => $daftar_tahun,
        ];
    }
}