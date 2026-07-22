<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\LogKegiatanHarian;
use App\Models\User;

class PenilaianKinerjaController extends BaseController
{
    public function index()
    {
        $logModel = new \App\Models\LogKegiatanHarian();
        $laporanModel = new \App\Models\LaporanHarian();
        $userModel = new User();

        $userId = session()->get('id') ?? session()->get('user_id');
        
        // Failsafe: Jika user menggunakan session versi lama (dimana id berisi NIP/Username)
        if (!is_numeric($userId) || strlen((string)$userId) > 10) {
            $userDb = $userModel->where('username', $userId)
                                ->orWhere('nip', $userId)
                                ->orWhere('id', $userId) // fallback
                                ->first();
            if ($userDb) {
                $userId = $userDb['id'];
            }
        }
        
        // Gunakan PRG pattern agar terhindar dari form resubmission dan error 403
        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            if ($this->request->getPost('bulan')) session()->set('penilaian_bulan', $this->request->getPost('bulan'));
            if ($this->request->getPost('tahun')) session()->set('penilaian_tahun', $this->request->getPost('tahun'));
            if (isset($_POST['bawahan_id'])) session()->set('penilaian_bawahan_id', $this->request->getPost('bawahan_id'));
            if (isset($_POST['unit_kerja'])) session()->set('penilaian_unit_kerja', $this->request->getPost('unit_kerja'));
            
            if ($this->request->getPost('active_tab') === 'individu') {
                session()->remove('penilaian_bawahan_id');
            }
            
            return redirect()->to(site_url('penilaian-kinerja'));
        }

        $bulanTerpilih = session()->get('penilaian_bulan') ?? date('n');
        $tahunTerpilih = session()->get('penilaian_tahun') ?? date('Y');
        $bawahanIdTerpilih = session()->get('penilaian_bawahan_id') ?? '';
        $unitKerjaTerpilih = session()->get('penilaian_unit_kerja') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $role = session()->get('role');
        $isSuper = in_array($role, ['admin', 'direktur', 'wadir']);

        // Ambil daftar unit kerja untuk filter (Hanya untuk Super)
        $daftarUnit = [];
        if ($isSuper) {
            $units = $userModel->select('unit')->distinct()->where('unit !=', null)->where('unit !=', '')->orderBy('unit', 'ASC')->findAll();
            foreach ($units as $u) {
                $daftarUnit[] = $u['unit'];
            }
        }

        // Cek apakah user punya bawahan atau punya akses penuh
        if ($isSuper) {
            $builder = $userModel->where('id !=', $userId);
            if (!empty($unitKerjaTerpilih)) {
                $builder = $builder->where('unit', $unitKerjaTerpilih);
            }
            $daftarBawahan = $builder->orderBy('nama_lengkap', 'ASC')->findAll();
            $isAtasan = true;
        } else {
            $daftarBawahan = $userModel->getBawahan($userId);
            $isAtasan = !empty($daftarBawahan);
        }

        $rekapDashboard = [];

        // Tentukan data siapa yang akan ditampilkan
        $isPenilai = false; // Mode form penilaian aktif?

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
                $isPenilai = true; // Atasan bisa menilai
            } else {
                $bawahanIdTerpilih = ''; // Reset jika ternyata tidak valid (misal pindah unit)
            }
        }

        // Ambil rekap data sebulan penuh untuk diri sendiri (selalu ada)
        $rekapDataSendiri = $laporanModel->getTargetWithRealization($userId, $bulanTerpilih, $tahunTerpilih);
        $logHarianSendiri = $logModel->getLogByMonth($userId, $bulanTerpilih, $tahunTerpilih);
        
        // Ambil rekap data bawahan terpilih (jika ada)
        $rekapDataBawahan = [];
        $logHarianBawahan = [];
        if ($isPenilai) {
            $rekapDataBawahan = $laporanModel->getTargetWithRealization($bawahanIdTerpilih, $bulanTerpilih, $tahunTerpilih);
            $logHarianBawahan = $logModel->getLogByMonth($bawahanIdTerpilih, $bulanTerpilih, $tahunTerpilih);
        }

        $data = [
            'title' => 'Rekap & Penilaian Kinerja',
            'bulan_terpilih' => $bulanTerpilih,
            'tahun_terpilih' => $tahunTerpilih,
            'bulan_indo' => $bulanIndo,
            'daftar_bawahan' => $daftarBawahan,
            'bawahan_id_terpilih' => $bawahanIdTerpilih,
            'is_atasan' => $isAtasan,
            'is_penilai' => $isPenilai,
            'rekap_data_sendiri' => $rekapDataSendiri,
            'log_harian_sendiri' => $logHarianSendiri,
            'rekap_data_bawahan' => $rekapDataBawahan,
            'log_harian_bawahan' => $logHarianBawahan,
            'rekap_dashboard' => $rekapDashboard,
            'is_super' => $isSuper,
            'daftar_unit' => $daftarUnit,
            'unit_kerja_terpilih' => $unitKerjaTerpilih
        ];

        return view('user/penilaian_kinerja/index', $data);
    }

    public function store()
    {
        // Hanya yang mensubmit form penilaian (Atasan) yang sampai ke sini
        $laporanModel = new \App\Models\LaporanHarian();

        $laporan_ids = $this->request->getPost('laporan_id');
        $nilai_capaian_arr = $this->request->getPost('nilai_capaian');

        $dataToUpdate = [];

        if ($laporan_ids) {
            foreach ($laporan_ids as $index => $idLaporan) {
                if (empty($idLaporan)) continue;

                $rowUpdate = [
                    'id' => $idLaporan,
                    'nilai_capaian' => $nilai_capaian_arr[$index] ?? null,
                ];

                $dataToUpdate[] = $rowUpdate;
            }
        }

        if (!empty($dataToUpdate)) {
            $laporanModel->updateBatch($dataToUpdate, 'id');
        }

        return redirect()->to('/penilaian-kinerja')
                         ->with('success', 'Penilaian kinerja berhasil disimpan.');
    }

    public function getChartDataApi()
    {
        // if (!$this->request->isAJAX()) return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request']);

        $userId = $this->request->getGet('user_id');
        $bulan = (int)$this->request->getGet('bulan');
        $tahun = (int)$this->request->getGet('tahun');

        if (!$userId || !$bulan || !$tahun) return $this->response->setJSON(['error' => 'Missing parameters']);

        $laporanModel = new \App\Models\LaporanHarian();
        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $trendBulan = [];
        $trendNilai = [];
        for ($i = 5; $i >= 0; $i--) {
            $timestamp = mktime(0, 0, 0, $bulan - $i, 1, $tahun);
            $m = (int)date('n', $timestamp);
            $y = (int)date('Y', $timestamp);
            $namaBulan = $bulanIndo[$m - 1] . ' ' . substr($y, 2, 2);
            
            $targetsBulan = $laporanModel->getTargetWithRealization($userId, $m, $y);
            $jmlDinilai = 0;
            $totalNilai = 0;
            foreach ($targetsBulan as $t) {
                if (!empty($t['nilai_capaian'])) {
                    $jmlDinilai++;
                    $totalNilai += (float)$t['nilai_capaian'];
                }
            }
            $rata = $jmlDinilai > 0 ? round($totalNilai / $jmlDinilai, 2) : 0;
            $trendBulan[] = $namaBulan;
            $trendNilai[] = $rata;
        }

        $rekapData = $laporanModel->getTargetWithRealization($userId, $bulan, $tahun);
        
        $totalRealisasi = 0;
        $totalTarget = 0;

        foreach ($rekapData as $row) {
            $totalTarget += (float)$row['target_bulanan'];
            $totalRealisasi += (float)$row['total_realisasi'];
        }

        return $this->response->setJSON([
            'trend_labels' => $trendBulan,
            'trend_data' => $trendNilai,
            'kualitas' => ['tepat' => 0, 'lambat' => 0],
            'sikap' => ['disiplin' => 0, 'kerjasama' => 0],
            'produktivitas' => ['realisasi' => $totalRealisasi, 'sisa' => max(0, $totalTarget - $totalRealisasi)]
        ]);
    }
}
