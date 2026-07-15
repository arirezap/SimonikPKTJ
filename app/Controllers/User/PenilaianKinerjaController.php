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
        
        // Gunakan session agar URL tetap bersih
        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            if ($this->request->getPost('bulan')) session()->set('penilaian_bulan', $this->request->getPost('bulan'));
            if ($this->request->getPost('tahun')) session()->set('penilaian_tahun', $this->request->getPost('tahun'));
            if (isset($_POST['bawahan_id'])) session()->set('penilaian_bawahan_id', $this->request->getPost('bawahan_id'));
            if (isset($_POST['unit_kerja'])) session()->set('penilaian_unit_kerja', $this->request->getPost('unit_kerja'));
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
            // Samakan logika dengan TimController: Ambil bawahan berdasarkan atasan_id ATAU unit yang sama
            $me = $userModel->find($userId);
            $myUnit = $me['unit'] ?? null;
            
            $bawahanQuery = $userModel->where('id !=', $userId);
            if (!empty($myUnit)) {
                $bawahanQuery->groupStart()
                             ->where('atasan_id', $userId)
                             ->orWhere('unit', $myUnit)
                             ->groupEnd();
            } else {
                $bawahanQuery->where('atasan_id', $userId);
            }
            $daftarBawahan = $bawahanQuery->orderBy('nama_lengkap', 'ASC')->findAll();
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
        $rekapDataSendiri = $logModel->getLogByMonth($userId, $bulanTerpilih, $tahunTerpilih);
        
        // Ambil rekap data bawahan terpilih (jika ada)
        $rekapDataBawahan = [];
        if ($isPenilai) {
            $rekapDataBawahan = $logModel->getLogByMonth($bawahanIdTerpilih, $bulanTerpilih, $tahunTerpilih);
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
            'rekap_data_bawahan' => $rekapDataBawahan,
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

    public function getChartDataApi()
    {
        // if (!$this->request->isAJAX()) return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request']);

        $userId = $this->request->getGet('user_id');
        $bulan = (int)$this->request->getGet('bulan');
        $tahun = (int)$this->request->getGet('tahun');

        if (!$userId || !$bulan || !$tahun) return $this->response->setJSON(['error' => 'Missing parameters']);

        $logModel = new \App\Models\LogKegiatanHarian();
        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $trendBulan = [];
        $trendNilai = [];
        for ($i = 5; $i >= 0; $i--) {
            $timestamp = mktime(0, 0, 0, $bulan - $i, 1, $tahun);
            $m = (int)date('n', $timestamp);
            $y = (int)date('Y', $timestamp);
            $namaBulan = $bulanIndo[$m - 1] . ' ' . substr($y, 2, 2);
            
            $logsBulan = $logModel->getLogByMonth($userId, $m, $y);
            $jmlDinilai = 0;
            $totalNilai = 0;
            foreach ($logsBulan as $l) {
                if (!empty($l['nilai_harian'])) {
                    $jmlDinilai++;
                    $totalNilai += (float)$l['nilai_harian'];
                }
            }
            $rata = $jmlDinilai > 0 ? round($totalNilai / $jmlDinilai, 2) : 0;
            $trendBulan[] = $namaBulan;
            $trendNilai[] = $rata;
        }

        $rekapData = $logModel->getLogByMonth($userId, $bulan, $tahun);
        
        $tepat = 0; $lambat = 0;
        $sumDisiplin = 0; $sumKerjasama = 0;
        $dinilaiCount = 0;
        
        $targets = [];
        $totalRealisasi = 0;

        foreach ($rekapData as $row) {
            if (!empty($row['nilai_harian'])) {
                $dinilaiCount++;
                $sumDisiplin += (float)$row['disiplin'];
                $sumKerjasama += (float)$row['kerjasama'];
                if ($row['waktu_penyelesaian'] === 'Tepat waktu') $tepat++;
                else if ($row['waktu_penyelesaian'] === 'Terlambat') $lambat++;
            }
            $targets[$row['target_id']] = (int)$row['target_bulanan'];
            $totalRealisasi += (int)$row['jumlah_capaian'];
        }
        
        $totalTarget = array_sum($targets);
        $avgDisiplin = $dinilaiCount > 0 ? round($sumDisiplin / $dinilaiCount, 1) : 0;
        $avgKerjasama = $dinilaiCount > 0 ? round($sumKerjasama / $dinilaiCount, 1) : 0;

        return $this->response->setJSON([
            'trend_labels' => $trendBulan,
            'trend_data' => $trendNilai,
            'kualitas' => ['tepat' => $tepat, 'lambat' => $lambat],
            'sikap' => ['disiplin' => $avgDisiplin, 'kerjasama' => $avgKerjasama],
            'produktivitas' => ['realisasi' => $totalRealisasi, 'sisa' => max(0, $totalTarget - $totalRealisasi)]
        ]);
    }
}
