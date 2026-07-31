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
        $allTargets = $targetModel->where('user_id', $userId)
                                  ->where('bulan', $bulanTerpilih)
                                  ->where('tahun', $tahunTerpilih)
                                  ->findAll();

        $userModel = new \App\Models\User();
        $currentUser = $userModel->find($userId);
        $hasAtasan = $currentUser && !empty($currentUser['atasan_id']) && $currentUser['role'] !== 'direktur';

        $targetStatus = 'disetujui';
        if (empty($allTargets)) {
            $targetStatus = 'belum_ada';
        } else if ($hasAtasan) {
            foreach ($allTargets as $t) {
                if ($t['status_approval'] !== 'disetujui') {
                    $targetStatus = 'belum_disetujui';
                    break;
                }
            }
        }

        $daftarTarget = ($targetStatus === 'disetujui') ? $allTargets : [];

        // Ambil log kegiatan harian yang sudah diinput pada tanggal tersebut
        $rekapData = $logModel->getLogWithTarget($userId, $tanggalTerpilih);

        $isLocked = false;
        if ($targetStatus !== 'disetujui') {
            $isLocked = true;
        } elseif (!empty($rekapData)) {
            foreach ($rekapData as $row) {
                if (isset($row['status']) && $row['status'] === 'terkirim') {
                    $isLocked = true;
                    break;
                }
            }
        }

        $logTambahanModel = new \App\Models\LogTugasTambahan();
        $rekapDataTambahan = $logTambahanModel->getLogByDate($userId, $tanggalTerpilih);

        $data = [
            'title' => 'Lapor Kegiatan Harian',
            'tanggal_terpilih' => $tanggalTerpilih,
            'daftar_target' => $daftarTarget,
            'rekap_data' => $rekapData,
            'rekap_data_tambahan' => $rekapDataTambahan,
            'is_locked' => $isLocked,
            'target_status' => $targetStatus
        ];

        return view('user/log_kegiatan/index', $data);
    }

    public function store()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        $tanggal = $this->request->getPost('tanggal');
        $bulanTerpilih = date('n', strtotime($tanggal));
        $tahunTerpilih = date('Y', strtotime($tanggal));

        // Cek persetujuan target bulanan
        $targetModel = new LaporanHarian();
        $allTargets = $targetModel->where('user_id', $userId)
                                  ->where('bulan', $bulanTerpilih)
                                  ->where('tahun', $tahunTerpilih)
                                  ->findAll();

        $userModel = new \App\Models\User();
        $currentUser = $userModel->find($userId);
        $hasAtasan = $currentUser && !empty($currentUser['atasan_id']) && $currentUser['role'] !== 'direktur';

        if (empty($allTargets)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan. Anda belum membuat Target Kinerja Bulanan untuk bulan ini.', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan. Anda belum membuat Target Kinerja Bulanan untuk bulan ini.');
        }

        if ($hasAtasan) {
            foreach ($allTargets as $t) {
                if ($t['status_approval'] !== 'disetujui') {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan. Target Kinerja Bulanan Anda untuk bulan ini belum disetujui oleh atasan langsung.', 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', 'Gagal menyimpan. Target Kinerja Bulanan Anda untuk bulan ini belum disetujui oleh atasan langsung.');
                }
            }
        }

        $rules = [
            'tanggal'              => 'required',
            'target_id.*'          => 'required|numeric',
            'deskripsi_kegiatan.*' => 'required',
            'jumlah_capaian.*'     => 'required|numeric',
            'link_bukti.*'         => 'required|valid_url',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan. Pastikan semua form terisi dengan benar (URL Bukti Pekerjaan harus valid).',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan. Pastikan semua form terisi dengan benar (URL Bukti Pekerjaan harus valid).');
        }

        $logModel = new LogKegiatanHarian();
        $tanggal = $this->request->getPost('tanggal');

        // Pengecekan keamanan: Apakah tanggal sudah dikunci?
        $existingData = $logModel->getLogWithTarget($userId, $tanggal);
        foreach ($existingData as $row) {
            if (isset($row['status']) && $row['status'] === 'terkirim') {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Laporan hari ini telah dikunci.', 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->with('error', 'Laporan hari ini telah dikunci dan tidak dapat diedit.');
            }
        }

        // Tentukan status berdasarkan jenis submit
        $status = $this->request->isAJAX() ? 'draft' : 'terkirim';

        // Khusus untuk submit "Simpan & Kirim" yang mungkin tidak mengirim data baru, tapi hanya mengupdate status yang sudah ada (draft -> terkirim)
        if (!$this->request->isAJAX() && empty($this->request->getPost('target_id'))) {
            if (!empty($existingData)) {
                $logModel->where('user_id', $userId)
                         ->where('tanggal_kegiatan', $tanggal)
                         ->set(['status' => 'terkirim'])
                         ->update();
                         
                $user = (new \App\Models\User())->find($userId);
                if ($user && !empty($user['atasan_id'])) {
                    helper('notification');
                    send_notification(
                        $user['atasan_id'], 
                        'Laporan Harian Baru', 
                        $user['nama_lengkap'] . " mengirimkan Laporan Harian untuk tanggal $tanggal.",
                        site_url('penilaian-staf')
                    );
                }
                
                return redirect()->to('/log-kegiatan')->with('success', 'Kegiatan harian berhasil dikirim.');
            } else {
                return redirect()->back()->with('error', 'Tidak ada data untuk dikirim.');
            }
        }

        $tanggalTerpilihObj = new \DateTime($tanggal);
        $todayObj = new \DateTime(date('Y-m-d'));
        
        if ($tanggalTerpilihObj > $todayObj) {
            return redirect()->back()->with('error', 'Gagal menyimpan. Anda tidak dapat melaporkan kegiatan untuk tanggal di masa depan.');
        }

        // Fitur batas pelaporan masa lalu dihapus atas permintaan user


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
                    'status'             => $status
                ];

                if (!empty($log_ids[$index])) {
                    $rowData['id'] = $log_ids[$index];
                    $dataToUpdate[] = $rowData;
                } else {
                    $dataToInsert[$index] = $rowData;
                }
            }
        }

        $insertedIds = [];
        if (!empty($dataToUpdate)) {
            $logModel->updateBatch($dataToUpdate, 'id');
        }
        if (!empty($dataToInsert)) {
            foreach ($dataToInsert as $origIndex => $insertRow) {
                $logModel->insert($insertRow);
                $insertedIds[$origIndex] = $logModel->getInsertID();
            }
        }

        // --- OLAP SIMULTAN UNTUK TUGAS TAMBAHAN ---
        $logTambahanModel = new \App\Models\LogTugasTambahan();
        $log_tambahan_ids = $this->request->getPost('log_tambahan_id');
        $deskripsi_kegiatan_tambahan_arr = $this->request->getPost('deskripsi_kegiatan_tambahan');
        $jumlah_capaian_tambahan_arr = $this->request->getPost('jumlah_capaian_tambahan');
        $satuan_tambahan_arr = $this->request->getPost('satuan_tambahan');
        $link_bukti_tambahan_arr = $this->request->getPost('link_bukti_tambahan');

        $dataTambahanToUpdate = [];
        $dataTambahanToInsert = [];

        if ($deskripsi_kegiatan_tambahan_arr) {
            foreach ($deskripsi_kegiatan_tambahan_arr as $index => $deskripsiTmb) {
                if (empty($deskripsiTmb)) continue;

                $rowTmbData = [
                    'user_id'            => $userId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $deskripsiTmb,
                    'jumlah_capaian'     => !empty($jumlah_capaian_tambahan_arr[$index]) ? (float)$jumlah_capaian_tambahan_arr[$index] : 1.00,
                    'satuan'             => !empty($satuan_tambahan_arr[$index]) ? $satuan_tambahan_arr[$index] : 'Kegiatan',
                    'link_bukti'         => !empty($link_bukti_tambahan_arr[$index]) ? $link_bukti_tambahan_arr[$index] : null,
                    'status'             => $status,
                    'status_approval'    => 'menunggu_persetujuan'
                ];

                if (!empty($log_tambahan_ids[$index])) {
                    $rowTmbData['id'] = $log_tambahan_ids[$index];
                    $dataTambahanToUpdate[] = $rowTmbData;
                } else {
                    $dataTambahanToInsert[$index] = $rowTmbData;
                }
            }
        }

        $insertedTambahanIds = [];
        if (!empty($dataTambahanToUpdate)) {
            $logTambahanModel->updateBatch($dataTambahanToUpdate, 'id');
        }
        if (!empty($dataTambahanToInsert)) {
            foreach ($dataTambahanToInsert as $origIndex => $insertRow) {
                $logTambahanModel->insert($insertRow);
                $insertedTambahanIds[$origIndex] = $logTambahanModel->getInsertID();
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Laporan harian & tugas tambahan berhasil disimpan sementara.',
                'new_ids' => $insertedIds,
                'new_tambahan_ids' => $insertedTambahanIds,
                'csrf_hash' => csrf_hash()
            ]);
        }

        // Jika submit normal (Simpan & Kirim), ubah status semua log tanggal ini menjadi terkirim
        if (!$this->request->isAJAX()) {
            $logModel->where('user_id', $userId)
                     ->where('tanggal_kegiatan', $tanggal)
                     ->set(['status' => 'terkirim'])
                     ->update();

            $logTambahanModel->where('user_id', $userId)
                             ->where('tanggal_kegiatan', $tanggal)
                             ->set(['status' => 'terkirim'])
                             ->update();
                     
            $user = (new \App\Models\User())->find($userId);
            if ($user && !empty($user['atasan_id'])) {
                helper('notification');
                send_notification(
                    $user['atasan_id'], 
                    'Laporan Harian Baru', 
                    $user['nama_lengkap'] . " mengirimkan Laporan Harian untuk tanggal $tanggal.",
                    site_url('penilaian-staf')
                );
            }
        }

        return redirect()->to('/log-kegiatan')
                         ->with('success', 'Laporan harian dan tugas tambahan berhasil dikirim.');
    }
    
    public function hapus()
    {
        // Pegawai tidak diizinkan menghapus log yang sudah tersimpan
        return $this->response->setJSON(['success' => false, 'message' => 'Laporan yang sudah disimpan tidak dapat dihapus.']);
    }

    public function storeTugasTambahan()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        
        $rules = [
            'tanggal'              => 'required',
            'deskripsi_kegiatan_tambahan.*' => 'required',
            'link_bukti_tambahan.*'         => 'required|valid_url',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan. Pastikan semua form tugas tambahan terisi dengan benar (URL Bukti Pekerjaan harus valid).',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan. Pastikan semua form tugas tambahan terisi dengan benar (URL Bukti Pekerjaan harus valid).');
        }

        $logTambahanModel = new \App\Models\LogTugasTambahan();
        $tanggal = $this->request->getPost('tanggal');
        $status = $this->request->isAJAX() ? 'draft' : 'terkirim';

        $log_ids = $this->request->getPost('log_tambahan_id');
        $deskripsi_kegiatan_arr = $this->request->getPost('deskripsi_kegiatan_tambahan');
        $link_bukti_arr = $this->request->getPost('link_bukti_tambahan');

        $dataToUpdate = [];
        $dataToInsert = [];

        if ($deskripsi_kegiatan_arr) {
            foreach ($deskripsi_kegiatan_arr as $index => $deskripsi) {
                if (empty($deskripsi)) continue;

                $rowData = [
                    'user_id'            => $userId,
                    'tanggal_kegiatan'   => $tanggal,
                    'deskripsi_kegiatan' => $deskripsi,
                    'link_bukti'         => $link_bukti_arr[$index] ?? '',
                    'status'             => $status,
                    'status_approval'    => 'menunggu_persetujuan'
                ];

                if (!empty($log_ids[$index])) {
                    $rowData['id'] = $log_ids[$index];
                    $dataToUpdate[] = $rowData;
                } else {
                    $dataToInsert[$index] = $rowData;
                }
            }
        }

        $insertedIds = [];
        if (!empty($dataToUpdate)) {
            $logTambahanModel->updateBatch($dataToUpdate, 'id');
        }
        if (!empty($dataToInsert)) {
            foreach ($dataToInsert as $origIndex => $insertRow) {
                $logTambahanModel->insert($insertRow);
                $insertedIds[$origIndex] = $logTambahanModel->getInsertID();
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data tugas tambahan berhasil disimpan sementara.',
                'new_ids' => $insertedIds,
                'csrf_hash' => csrf_hash()
            ]);
        }

        return redirect()->to('/log-kegiatan')->with('success', 'Tugas Tambahan berhasil disimpan dan dikirim.');
    }

    public function hapusTugasTambahan()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $logTambahanModel = new \App\Models\LogTugasTambahan();
            $userId = session()->get('id') ?? session()->get('user_id');
            $row = $logTambahanModel->find($id);
            // Boleh hapus asalkan belum disetujui atasan (jika mau) atau statusnya draft
            if ($row && $row['user_id'] == $userId) {
                $logTambahanModel->delete($id);
                return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
            }
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus.', 'csrf_hash' => csrf_hash()]);
    }
}
