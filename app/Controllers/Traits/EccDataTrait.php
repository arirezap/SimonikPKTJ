<?php

namespace App\Controllers\Traits;

use App\Models\LedCriteria;
use App\Models\LedStandar; // <-- GANTI
use App\Models\LedScore; 
use App\Models\RencanaKinerja as RencanaKinerjaModel;

trait EccDataTrait
{
    /**
     * Mengambil data ECC dan daftar tahun gabungan untuk dashboard.
     */
    private function getDashboardEccData($selectedTahun)
    {
        $scoreModel = new LedScore();
        $criteriaModel = new LedCriteria();
        $standarModel = new LedStandar(); // <-- GANTI

        // 1. Ambil semua Standar dari Master Standar (Label Grafik)
        $standar_raw = $standarModel->orderBy('nama_standar', 'ASC')->findAll(); // <-- GANTI
        $chart_labels = array_column($standar_raw, 'nama_standar'); // <-- GANTI
        
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
            ->select('led_criteria.id, led_criteria.prodi, led_standar.nama_standar') // <-- GANTI
            ->join('led_standar', 'led_standar.id = led_criteria.id_kategori', 'left') // <-- GANTI
            ->findAll();
            
        $criteriaMap = []; // Peta untuk [kriteria_id => [nama_standar, prodi]]
        foreach ($all_criteria as $item) {
            $criteriaMap[$item['id']] = [
                'kategori' => $item['nama_standar'], // <-- GANTI
                'prodi' => $item['prodi']
            ];
            
            // 4. Hitung 'total' item per standar per prodi
            $prodi = $item['prodi'];
            $kategori_nama = $item['nama_standar']; // <-- GANTI
            if (!empty($kategori_nama) && in_array($kategori_nama, $chart_labels) && isset($scores[$prodi])) {
                $scores[$prodi][$kategori_nama]['jumlah_item']++;
            }
        }

        // 5. Ambil data SKOR untuk SEMUA prodi di TAHUN TERPILIH
        $scores_data = $scoreModel
            ->where('tahun', $selectedTahun)
            ->findAll();

        // 6. Akumulasi 'total_skor' dari data Skor yang sudah diinput
        foreach ($scores_data as $score) {
            $prodi = $score['prodi'];
            $kriteria_id = $score['led_criteria_id'];
            
            if (isset($criteriaMap[$kriteria_id])) {
                $kategori = $criteriaMap[$kriteria_id]['kategori'];
                // Pastikan prodi-nya cocok
                if(isset($scores[$prodi][$kategori]) && $criteriaMap[$kriteria_id]['prodi'] == $prodi) {
                     $scores[$prodi][$kategori]['total_skor'] += (float)$score['skor'];
                }
            }
        }

        // 7. Hitung Rata-rata skor per standar dan siapkan data untuk view
        foreach ($prodiList as $prodi) {
            $chart_data = [];
            foreach ($chart_labels as $label) {
                $total_skor = $scores[$prodi][$label]['total_skor'];
                $jumlah_item = $scores[$prodi][$label]['jumlah_item'];
                
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