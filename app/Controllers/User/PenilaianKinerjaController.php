<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\LogKegiatanHarian;
use App\Models\User;

class PenilaianKinerjaController extends BaseController
{
    public function index()
    {
        $logModel = new LogKegiatanHarian();
        $userModel = new User();

        $userId = session()->get('id') ?? session()->get('user_id');
        
        // Gunakan session agar URL tetap bersih
        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            if ($this->request->getPost('bulan')) session()->set('penilaian_bulan', $this->request->getPost('bulan'));
            if ($this->request->getPost('tahun')) session()->set('penilaian_tahun', $this->request->getPost('tahun'));
            if (isset($_POST['bawahan_id'])) session()->set('penilaian_bawahan_id', $this->request->getPost('bawahan_id'));
        }

        $bulanTerpilih = session()->get('penilaian_bulan') ?? date('n');
        $tahunTerpilih = session()->get('penilaian_tahun') ?? date('Y');
        $bawahanIdTerpilih = session()->get('penilaian_bawahan_id') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Cek apakah user punya bawahan
        $daftarBawahan = $userModel->getBawahan($userId);
        $isAtasan = !empty($daftarBawahan);

        $rekapDashboard = [];
        if ($isAtasan) {
            foreach ($daftarBawahan as $bawahan) {
                $logs = $logModel->getLogByMonth($bawahan['id'], $bulanTerpilih, $tahunTerpilih);
                $total_laporan = count($logs);
                
                $dinilai = 0;
                $total_nilai = 0;
                foreach ($logs as $l) {
                    if (!empty($l['nilai_harian'])) {
                        $dinilai++;
                        $total_nilai += (float)$l['nilai_harian'];
                    }
                }
                
                $rata_rata = $dinilai > 0 ? round($total_nilai / $dinilai, 2) : 0;
                
                $rekapDashboard[] = [
                    'bawahan' => $bawahan,
                    'total_laporan' => $total_laporan,
                    'dinilai' => $dinilai,
                    'belum_dinilai' => $total_laporan - $dinilai,
                    'rata_rata' => $rata_rata
                ];
            }
        }

        // Tentukan data siapa yang akan ditampilkan
        $targetUserId = $userId; // Default lihat sendiri
        $isPenilai = false; // Mode read-only

        if ($isAtasan && !empty($bawahanIdTerpilih)) {
            // Validasi apakah benar bawahannya
            $isValidBawahan = false;
            foreach ($daftarBawahan as $bwh) {
                if ($bwh['id'] == $bawahanIdTerpilih) {
                    $isValidBawahan = true;
                    break;
                }
            }
            if ($isValidBawahan) {
                $targetUserId = $bawahanIdTerpilih;
                $isPenilai = true; // Atasan bisa menilai
            }
        }

        // Ambil rekap data sebulan penuh
        $rekapData = $logModel->getLogByMonth($targetUserId, $bulanTerpilih, $tahunTerpilih);

        $data = [
            'title' => 'Rekap & Penilaian Kinerja',
            'bulan_terpilih' => $bulanTerpilih,
            'tahun_terpilih' => $tahunTerpilih,
            'bulan_indo' => $bulanIndo,
            'daftar_bawahan' => $daftarBawahan,
            'bawahan_id_terpilih' => $bawahanIdTerpilih,
            'is_atasan' => $isAtasan,
            'is_penilai' => $isPenilai,
            'rekap_data' => $rekapData,
            'rekap_dashboard' => $rekapDashboard
        ];

        return view('user/penilaian_kinerja/index', $data);
    }

    public function store()
    {
        // Hanya yang mensubmit form penilaian (Atasan) yang sampai ke sini
        $logModel = new LogKegiatanHarian();

        $log_ids = $this->request->getPost('log_id');
        $waktu_penyelesaian_arr = $this->request->getPost('waktu_penyelesaian');
        $kualitas_hasil_arr = $this->request->getPost('kualitas_hasil');
        $disiplin_arr = $this->request->getPost('disiplin');
        $kerjasama_arr = $this->request->getPost('kerjasama');
        $nilai_harian_arr = $this->request->getPost('nilai_harian'); // Diisi via JS di frontend

        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        $bawahan_id = $this->request->getPost('bawahan_id');

        $dataToUpdate = [];

        if ($log_ids) {
            foreach ($log_ids as $index => $idLog) {
                if (empty($idLog)) continue;

                $rowUpdate = [
                    'id' => $idLog,
                    'waktu_penyelesaian' => $waktu_penyelesaian_arr[$index] ?? null,
                    'kualitas_hasil' => $kualitas_hasil_arr[$index] ?? null,
                    'disiplin' => $disiplin_arr[$index] ?? null,
                    'kerjasama' => $kerjasama_arr[$index] ?? null,
                    'nilai_harian' => $nilai_harian_arr[$index] ?? null,
                ];

                $dataToUpdate[] = $rowUpdate;
            }
        }

        if (!empty($dataToUpdate)) {
            $logModel->updateBatch($dataToUpdate, 'id');
        }

        return redirect()->to('/penilaian-kinerja')
                         ->with('success', 'Penilaian kinerja berhasil disimpan.');
    }
}
