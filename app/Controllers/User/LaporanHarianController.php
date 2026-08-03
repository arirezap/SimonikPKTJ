<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\LaporanHarian;
use App\Models\Satuan;
use App\Models\User;

class LaporanHarianController extends BaseController
{
    public function index()
    {
        $laporanModel = new LaporanHarian();
        $satuanModel = new Satuan();
        $userModel = new User();

        $userId = session()->get('id') ?? session()->get('user_id');
        $role = session()->get('role');
        
        // Gunakan session agar URL tetap bersih, dan gunakan PRG (Post-Redirect-Get) untuk mencegah Form Resubmission (403)
        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            if ($this->request->getPost('bulan')) session()->set('laporan_harian_bulan', $this->request->getPost('bulan'));
            if ($this->request->getPost('tahun')) session()->set('laporan_harian_tahun', $this->request->getPost('tahun'));
            
            $sourceTab = $this->request->getPost('source_tab');
            if ($sourceTab === 'sendiri') {
                session()->remove('laporan_harian_staf_id');
            } elseif ($sourceTab === 'staf') {
                session()->set('laporan_harian_staf_id', $this->request->getPost('staf_id'));
            }

            return redirect()->to(site_url('laporan-harian'));
        }

        $bulanTerpilih = session()->get('laporan_harian_bulan') ?? date('n');
        $tahunTerpilih = session()->get('laporan_harian_tahun') ?? date('Y');
        $stafIdTerpilih = session()->get('laporan_harian_staf_id') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = $bulanIndo[$bulanTerpilih - 1];

        // Ambil data laporan/target pada bulan tersebut untuk user ini (Target Saya)
        $rekapDataSendiri = $laporanModel->where('user_id', $userId)
                                  ->where('bulan', $bulanTerpilih)
                                  ->where('tahun', $tahunTerpilih)
                                  ->findAll();

        // Cek apakah user punya staf/staf (Hanya Admin yang punya opsi lihat semua)
        $isSuper = hasAnyRole(['admin']);
        if ($isSuper) {
            $daftarStaf = $userModel->where('id !=', $userId)->orderBy('nama_lengkap', 'ASC')->findAll();
            $isAtasan = true;
        } else {
            $daftarStaf = $userModel->getStaf($userId);
            $isAtasan = !empty($daftarStaf);
        }

        $rekapDataStaf = [];
        $isPenyetuju = false;

        if ($isAtasan && !empty($stafIdTerpilih)) {
            $isValidStaf = false;
            foreach ($daftarStaf as $staf) {
                if ($staf['id'] == $stafIdTerpilih) {
                    $isValidStaf = true;
                    break;
                }
            }
            if ($isValidStaf) {
                $isPenyetuju = true;
                $rekapDataStaf = $laporanModel->where('user_id', $stafIdTerpilih)
                                              ->where('bulan', $bulanTerpilih)
                                              ->where('tahun', $tahunTerpilih)
                                              ->where('status', 'terkirim')
                                              ->findAll();
            } else {
                $stafIdTerpilih = ''; 
            }
        }

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
            'title' => 'Target Kinerja Bulanan',
            'bulan_terpilih' => $bulanTerpilih,
            'tahun_terpilih' => $tahunTerpilih,
            'nama_bulan' => $namaBulan,
            'rekap_data_sendiri' => $rekapDataSendiri,
            'rekap_data_staf' => $rekapDataStaf,
            'daftar_satuan' => $satuanModel->findAll(),
            'bulan_indo' => $bulanIndo,
            'batas_target' => $batasTarget,
            'is_locked' => $isLocked,
            'is_atasan' => $isAtasan,
            'is_penyetuju' => $isPenyetuju,
            'daftar_staf' => $daftarStaf,
            'staf_id_terpilih' => $stafIdTerpilih
        ];

        return view('user/laporan_harian/index', $data);
    }

    public function store()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        $laporanModel = new LaporanHarian();

        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        
        $isEditingStaf = $this->request->getPost('is_editing_staf') == '1';
        $targetUserId = $isEditingStaf ? $this->request->getPost('staf_id') : $userId;
        $isDraft = $this->request->isAJAX() || $this->request->getPost('action') === 'draft';
        $targetStatus = $isDraft ? 'draft' : 'terkirim';

        // Validasi Kunci Waktu HANYA jika yang edit adalah staf itu sendiri
        if (!$isEditingStaf) {
            $settingModel = new \App\Models\SettingModel();
            $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);
            $currentMonth = (int) date('n');
            $currentYear = (int) date('Y');
            $currentDay = (int) date('j');
            if ($tahun == $currentYear && $bulan == $currentMonth && $currentDay > $batasTarget) {
                if ($this->request->isAJAX()) return $this->response->setJSON(['success' => false, 'message' => 'Batas waktu pengisian ditutup.', 'csrf_hash' => csrf_hash()]);
                return redirect()->back()->with('error', 'Gagal menyimpan. Batas waktu pengisian target bulan ini sudah ditutup.');
            } elseif (($tahun < $currentYear) || ($tahun == $currentYear && $bulan < $currentMonth)) {
                if ($this->request->isAJAX()) return $this->response->setJSON(['success' => false, 'message' => 'Target untuk bulan sebelumnya tidak dapat diubah.', 'csrf_hash' => csrf_hash()]);
                return redirect()->back()->with('error', 'Gagal menyimpan. Target untuk bulan sebelumnya tidak dapat diubah.');
            }
        }

        // Jika Simpan & Kirim (Bukan Draft), lakukan validasi ketat
        if (!$isDraft) {
            $rules = [
                'bulan'               => 'required|numeric',
                'tahun'               => 'required|numeric',
                'sasaran_program.*'   => 'required',
                'indikator_kinerja.*' => 'required',
                'target_bulanan.*'    => 'required|numeric',
                'satuan.*'            => 'required',
            ];

            if (!$this->validate($rules)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal mengirim. Pastikan semua kolom target terisi dengan lengkap.',
                        'csrf_hash' => csrf_hash()
                    ]);
                }
                return redirect()->back()->withInput()->with('error', 'Gagal mengirim. Pastikan semua kolom target terisi dengan lengkap.');
            }
        }

        $laporan_ids = $this->request->getPost('laporan_id');
        $sasaran_program_arr = $this->request->getPost('sasaran_program');
        $indikator_kinerja_arr = $this->request->getPost('indikator_kinerja');
        $target_bulanan_arr = $this->request->getPost('target_bulanan');
        $satuan_arr = $this->request->getPost('satuan');

        $dataToUpdate = [];
        $dataToInsert = [];
        
        // Jika diedit oleh atasan, langsung jadi disetujui & terkirim
        $status_approval = $isEditingStaf ? 'disetujui' : 'menunggu_persetujuan';

        if ($sasaran_program_arr) {
            foreach ($sasaran_program_arr as $index => $sasaran) {
                $indikator = $indikator_kinerja_arr[$index] ?? '';
                $targetVal = $target_bulanan_arr[$index] ?? '';
                $satuanVal = $satuan_arr[$index] ?? '';

                // Untuk draft, jika seluruh baris kosong, abaikan
                if ($isDraft && empty($sasaran) && empty($indikator) && empty($targetVal) && empty($satuanVal)) {
                    continue;
                }

                $rowData = [
                    'user_id'           => $targetUserId,
                    'tanggal'           => null,
                    'bulan'             => $bulan,
                    'tahun'             => $tahun,
                    'sasaran_program'   => $sasaran,
                    'indikator_kinerja' => $indikator,
                    'target_bulanan'    => is_numeric($targetVal) ? (float)$targetVal : null,
                    'satuan'            => $satuanVal,
                    'status_approval'   => $status_approval,
                    'status'            => $targetStatus
                ];

                if (!empty($laporan_ids[$index])) {
                    $rowData['id'] = $laporan_ids[$index];
                    $dataToUpdate[] = $rowData;
                } else {
                    $dataToInsert[$index] = $rowData;
                }
            }
        }

        $insertedIds = [];
        if (!empty($dataToUpdate)) {
            $laporanModel->updateBatch($dataToUpdate, 'id');
        }
        if (!empty($dataToInsert)) {
            foreach ($dataToInsert as $origIndex => $insertRow) {
                $laporanModel->insert($insertRow);
                $insertedIds[$origIndex] = $laporanModel->getInsertID();
            }
        }

        // Jika Simpan & Kirim, update semua target bulan ini yang sebelumnya draf menjadi terkirim
        if (!$isDraft) {
            $laporanModel->where('user_id', $targetUserId)
                         ->where('bulan', $bulan)
                         ->where('tahun', $tahun)
                         ->set(['status' => 'terkirim'])
                         ->update();
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil disimpan sementara.',
                'new_ids' => $insertedIds,
                'csrf_hash' => csrf_hash()
            ]);
        }

        // Send notification to boss if staff is submitting
        if (!$isEditingStaf && !$isDraft) {
            $user = (new \App\Models\User())->find($targetUserId);
            if ($user && !empty($user['atasan_id'])) {
                helper('notification');
                send_notification(
                    $user['atasan_id'], 
                    'Persetujuan Target Bulanan', 
                    $user['nama_lengkap'] . ' mengirimkan Target Bulanan untuk diperiksa.',
                    site_url('penilaian-staf')
                );
            }
        }

        return redirect()->to('/laporan-harian')
                         ->with('success', $isDraft ? 'Target Bulanan berhasil disimpan sementara.' : 'Target Bulanan berhasil dikirim ke atasan langsung.');
    }
    
    public function approve()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $laporanModel = new LaporanHarian();
            $laporanModel->update($id, ['status_approval' => 'disetujui']);
            log_audit('APPROVE', 'laporan_harian', $id, null, ['status_approval' => 'disetujui']);
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false]);
    }
    
    public function approveAll()
    {
        $staf_id = $this->request->getPost('staf_id');
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        
        if ($staf_id && $bulan && $tahun) {
            $laporanModel = new LaporanHarian();
            $laporanModel->where('user_id', $staf_id)
                         ->where('bulan', $bulan)
                         ->where('tahun', $tahun)
                         ->set(['status_approval' => 'disetujui'])
                         ->update();
            log_audit('APPROVE_ALL', 'laporan_harian', $staf_id, null, ['bulan' => $bulan, 'tahun' => $tahun]);
            
            helper('notification');
            send_notification(
                $staf_id,
                'Target Disetujui',
                "Target Bulanan (Bulan: $bulan, Tahun: $tahun) telah disetujui oleh atasan.",
                site_url('laporan-harian')
            );

            return redirect()->to('/laporan-harian')->with('success', 'Semua target milik staf berhasil disetujui.');
        }
        return redirect()->back()->with('error', 'Data tidak valid.');
    }

    public function hapus()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $laporanModel = new LaporanHarian();
            $laporan = $laporanModel->find($id);
            if ($laporan) {
                // Jangan izinkan hapus jika sudah disetujui
                if ($laporan['status_approval'] == 'disetujui') {
                    return $this->response->setJSON(['success' => false, 'message' => 'Terkunci. Target sudah disetujui oleh atasan.']);
                }
                
                $settingModel = new \App\Models\SettingModel();
                $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);
                $currentMonth = (int) date('n');
                $currentYear = (int) date('Y');
                $currentDay = (int) date('j');
                $tahun = (int) $laporan['tahun'];
                $bulan = (int) $laporan['bulan'];

                if (($tahun == $currentYear && $bulan == $currentMonth && $currentDay > $batasTarget) || 
                    ($tahun < $currentYear) || ($tahun == $currentYear && $bulan < $currentMonth)) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Terkunci. Batas waktu terlewat.']);
                }

                $laporanModel->delete($id);
                return $this->response->setJSON(['success' => true]);
            }
        }
        return $this->response->setJSON(['success' => false]);
    }
}
