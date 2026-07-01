<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\LaporanHarian;
use App\Models\Satuan;

class LaporanHarianController extends BaseController
{
    public function index()
    {
        $laporanModel = new LaporanHarian();
        $satuanModel = new Satuan();

        $userId = session()->get('id') ?? session()->get('user_id');
        
        // Gunakan session agar URL tetap bersih
        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            if ($this->request->getPost('bulan')) session()->set('laporan_harian_bulan', $this->request->getPost('bulan'));
            if ($this->request->getPost('tahun')) session()->set('laporan_harian_tahun', $this->request->getPost('tahun'));
        }

        $bulanTerpilih = session()->get('laporan_harian_bulan') ?? date('n');
        $tahunTerpilih = session()->get('laporan_harian_tahun') ?? date('Y');

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = $bulanIndo[$bulanTerpilih - 1];

        // Ambil data laporan/target pada bulan tersebut untuk user ini
        $rekapData = $laporanModel->where('user_id', $userId)
                                  ->where('bulan', $bulanTerpilih)
                                  ->where('tahun', $tahunTerpilih)
                                  ->findAll();

        // Logika Kunci Waktu
        $settingModel = new \App\Models\SettingModel();
        $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);
        $isLocked = false;
        
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');
        $currentDay = (int) date('j');

        if ($tahunTerpilih == $currentYear && $bulanTerpilih == $currentMonth) {
            if ($currentDay > $batasTarget) {
                $isLocked = true;
            }
        } elseif (($tahunTerpilih < $currentYear) || ($tahunTerpilih == $currentYear && $bulanTerpilih < $currentMonth)) {
            $isLocked = true; // Bulan sebelumnya otomatis terkunci
        }

        $data = [
            'title' => 'Target Laporan Bulanan',
            'bulan_terpilih' => $bulanTerpilih,
            'tahun_terpilih' => $tahunTerpilih,
            'nama_bulan' => $namaBulan,
            'rekap_data' => $rekapData,
            'daftar_satuan' => $satuanModel->findAll(),
            'bulan_indo' => $bulanIndo,
            'batas_target' => $batasTarget,
            'is_locked' => $isLocked
        ];

        return view('user/laporan_harian/index', $data);
    }

    public function store()
    {
        $userId = session()->get('id') ?? session()->get('user_id');

        $rules = [
            'bulan'               => 'required|numeric',
            'tahun'               => 'required|numeric',
            'sasaran_program.*'   => 'required',
            'indikator_kinerja.*' => 'required',
            'target_bulanan.*'    => 'required|numeric',
            'satuan.*'            => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan. Pastikan semua kolom terisi dengan benar (target harus angka).');
        }

        $laporanModel = new LaporanHarian();

        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');

        $settingModel = new \App\Models\SettingModel();
        $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');
        $currentDay = (int) date('j');
        if ($tahun == $currentYear && $bulan == $currentMonth && $currentDay > $batasTarget) {
            return redirect()->back()->with('error', 'Gagal menyimpan. Batas waktu pengisian target bulan ini sudah ditutup.');
        } elseif (($tahun < $currentYear) || ($tahun == $currentYear && $bulan < $currentMonth)) {
            return redirect()->back()->with('error', 'Gagal menyimpan. Target untuk bulan sebelumnya tidak dapat diubah.');
        }

        $laporan_ids = $this->request->getPost('laporan_id');
        $sasaran_program_arr = $this->request->getPost('sasaran_program');
        $indikator_kinerja_arr = $this->request->getPost('indikator_kinerja');
        $target_bulanan_arr = $this->request->getPost('target_bulanan');
        $satuan_arr = $this->request->getPost('satuan');

        $dataToUpdate = [];
        $dataToInsert = [];

        if ($sasaran_program_arr) {
            foreach ($sasaran_program_arr as $index => $sasaran) {
                if (empty($sasaran)) continue;

                $rowData = [
                    'user_id'           => $userId,
                    'tanggal'           => null,
                    'bulan'             => $bulan,
                    'tahun'             => $tahun,
                    'sasaran_program'   => $sasaran,
                    'indikator_kinerja' => $indikator_kinerja_arr[$index] ?? '',
                    'target_bulanan'    => $target_bulanan_arr[$index] ?? 0,
                    'satuan'            => $satuan_arr[$index] ?? '',
                ];

                if (!empty($laporan_ids[$index])) {
                    $rowData['id'] = $laporan_ids[$index];
                    $dataToUpdate[] = $rowData;
                } else {
                    $dataToInsert[] = $rowData;
                }
            }
        }

        if (!empty($dataToUpdate)) {
            $laporanModel->updateBatch($dataToUpdate, 'id');
        }
        if (!empty($dataToInsert)) {
            $laporanModel->insertBatch($dataToInsert);
        }

        return redirect()->to('/laporan-harian')
                         ->with('success', 'Target Bulanan berhasil disimpan.');
    }
    
    public function hapus()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $laporanModel = new LaporanHarian();
            $laporan = $laporanModel->find($id);
            if ($laporan) {
                $settingModel = new \App\Models\SettingModel();
                $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);
                $currentMonth = (int) date('n');
                $currentYear = (int) date('Y');
                $currentDay = (int) date('j');
                $tahun = (int) $laporan['tahun'];
                $bulan = (int) $laporan['bulan'];

                if (($tahun == $currentYear && $bulan == $currentMonth && $currentDay > $batasTarget) || 
                    ($tahun < $currentYear) || ($tahun == $currentYear && $bulan < $currentMonth)) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Terkunci']);
                }

                $laporanModel->delete($id);
                return $this->response->setJSON(['success' => true]);
            }
        }
        return $this->response->setJSON(['success' => false]);
    }
}
