<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\LogKegiatanHarian;
use App\Models\LaporanHarian;

class LogKegiatanController extends BaseController
{
    public function index()
    {
        $logModel = new LogKegiatanHarian();
        $targetModel = new LaporanHarian();

        $userId = session()->get('id') ?? session()->get('user_id');
        
        // Gunakan session agar URL tetap bersih
        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            if ($this->request->getPost('tanggal')) session()->set('log_kegiatan_tanggal', $this->request->getPost('tanggal'));
        }

        $tanggalTerpilih = session()->get('log_kegiatan_tanggal') ?? date('Y-m-d');
        
        $bulanTerpilih = date('n', strtotime($tanggalTerpilih));
        $tahunTerpilih = date('Y', strtotime($tanggalTerpilih));

        // Ambil daftar target yang sudah dibuat user pada bulan tersebut
        $daftarTarget = $targetModel->where('user_id', $userId)
                                    ->where('bulan', $bulanTerpilih)
                                    ->where('tahun', $tahunTerpilih)
                                    ->findAll();

        // Ambil log kegiatan harian yang sudah diinput pada tanggal tersebut
        $rekapData = $logModel->getLogWithTarget($userId, $tanggalTerpilih);

        // Logika Batas Waktu
        $settingModel = new \App\Models\SettingModel();
        $batasLog = (int) $settingModel->getValue('batas_input_log', 3);

        $isPastDeadline = false;
        $tanggalTerpilihObj = new \DateTime($tanggalTerpilih);
        $todayObj = new \DateTime(date('Y-m-d'));
        $diff = $todayObj->diff($tanggalTerpilihObj)->days;
        
        // Jika hari ini sudah melewati tanggal terpilih + batas toleransi hari
        if ($todayObj > $tanggalTerpilihObj && $diff > $batasLog) {
            $isPastDeadline = true;
        }

        $data = [
            'title' => 'Laporan Kegiatan Harian',
            'tanggal_terpilih' => $tanggalTerpilih,
            'daftar_target' => $daftarTarget,
            'rekap_data' => $rekapData,
            'batas_log' => $batasLog,
            'is_past_deadline' => $isPastDeadline
        ];

        return view('user/log_kegiatan/index', $data);
    }

    public function store()
    {
        $userId = session()->get('id') ?? session()->get('user_id');

        $rules = [
            'tanggal'              => 'required',
            'target_id.*'          => 'required|numeric',
            'deskripsi_kegiatan.*' => 'required',
            'jumlah_capaian.*'     => 'required|numeric',
            'link_bukti.*'         => 'required|valid_url',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan. Pastikan semua form terisi dengan benar (URL Bukti Pekerjaan harus valid).');
        }

        $logModel = new LogKegiatanHarian();
        $tanggal = $this->request->getPost('tanggal');

        $settingModel = new \App\Models\SettingModel();
        $batasLog = (int) $settingModel->getValue('batas_input_log', 3);
        $tanggalTerpilihObj = new \DateTime($tanggal);
        $todayObj = new \DateTime(date('Y-m-d'));
        $diff = $todayObj->diff($tanggalTerpilihObj)->days;
        if ($todayObj > $tanggalTerpilihObj && $diff > $batasLog) {
            return redirect()->back()->with('error', 'Gagal menyimpan. Batas waktu pelaporan harian untuk tanggal ini sudah ditutup.');
        }

        $log_ids = $this->request->getPost('log_id');
        $target_id_arr = $this->request->getPost('target_id');
        $deskripsi_kegiatan_arr = $this->request->getPost('deskripsi_kegiatan');
        $jumlah_capaian_arr = $this->request->getPost('jumlah_capaian');
        $link_bukti_arr = $this->request->getPost('link_bukti');

        $dataToUpdate = [];
        $dataToInsert = [];

        if ($target_id_arr) {
            foreach ($target_id_arr as $index => $targetId) {
                if (empty($targetId)) continue;

                $rowData = [
                    'user_id'            => $userId,
                    'target_id'          => $targetId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $deskripsi_kegiatan_arr[$index] ?? '',
                    'jumlah_capaian'     => $jumlah_capaian_arr[$index] ?? 0,
                    'link_bukti'         => !empty($link_bukti_arr[$index]) ? $link_bukti_arr[$index] : null,
                ];

                if (!empty($log_ids[$index])) {
                    $rowData['id'] = $log_ids[$index];
                    $dataToUpdate[] = $rowData;
                } else {
                    $dataToInsert[] = $rowData;
                }
            }
        }

        if (!empty($dataToUpdate)) {
            $logModel->updateBatch($dataToUpdate, 'id');
        }
        if (!empty($dataToInsert)) {
            $logModel->insertBatch($dataToInsert);
        }

        return redirect()->to('/log-kegiatan')
                         ->with('success', 'Kegiatan harian berhasil disimpan.');
    }
    
    public function hapus()
    {
        // Pegawai tidak diizinkan menghapus log yang sudah tersimpan
        return $this->response->setJSON(['success' => false, 'message' => 'Laporan yang sudah disimpan tidak dapat dihapus.']);
    }
}
