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
            // Jika user punya role kepegawaian, sertakan seluruh pegawai Tugas Belajar
            if (hasRole('kepegawaian')) {
                $db = \Config\Database::connect();
                $tubelIds = array_column($db->table('user_roles')->select('user_id')->where('role_name', 'tugas_belajar')->get()->getResultArray(), 'user_id');
                $builder = $userModel->where('role', 'tugas_belajar');
                if (!empty($tubelIds)) {
                    $builder->orWhereIn('id', $tubelIds);
                }
                $tubelUsers = $builder->where('id !=', $userId)->findAll();

                $existingIds = array_column($daftarStaf, 'id');
                foreach ($tubelUsers as $tb) {
                    if (!in_array($tb['id'], $existingIds)) {
                        $daftarStaf[] = $tb;
                    }
                }
            }
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

        // Logika Batas Waktu Berdasarkan Pengaturan Sistem
        $settingModel = new \App\Models\SettingModel();
        $isDeadlineActive = $settingModel->getValue('enable_target_deadline', '0') === '1';
        $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);
        $isLocked = false;

        if ($isDeadlineActive && !hasAnyRole(['admin', 'direktur'])) {
            $now = new \DateTime();
            $targetMonth = new \DateTime(sprintf('%04d-%02d-01', $tahunTerpilih, $bulanTerpilih));
            $currentMonth = new \DateTime(date('Y-m-01'));

            if ($targetMonth < $currentMonth) {
                // Bulan lalu terkunci jika pembatasan deadline aktif
                $isLocked = true;
            } elseif ($targetMonth == $currentMonth) {
                // Bulan berjalan: terkunci jika melewati tanggal batas
                if ((int)$now->format('j') > $batasTarget) {
                    $isLocked = true;
                }
            }
            // Bulan ke depan ($targetMonth > $currentMonth) DIPERBOLEHKAN (tidak dikunci)
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

        $bulan = (int)$this->request->getPost('bulan');
        $tahun = (int)$this->request->getPost('tahun');
        
        $isEditingStaf = $this->request->getPost('is_editing_staf') == '1';
        $targetUserId = $isEditingStaf ? $this->request->getPost('staf_id') : $userId;

        // Otorisasi: Jika menyunting target milik staf lain, pastikan user adalah Atasan Langsung, Admin, atau Direktur
        if ($isEditingStaf && $targetUserId != $userId) {
            $targetUser = (new \App\Models\User())->find($targetUserId);
            $isAtasan = $targetUser && !empty($targetUser['atasan_id']) && ($targetUser['atasan_id'] == $userId);
            if (!hasAnyRole(['admin', 'direktur']) && !$isAtasan) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak. Anda tidak memiliki izin menyunting target staf ini.', 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki izin menyunting target staf ini.');
            }
        }

        $isDraft = $this->request->isAJAX() || $this->request->getPost('action') === 'draft';
        $targetStatus = $isDraft ? 'draft' : 'terkirim';

        // Validasi Kunci Waktu jika pembatasan deadline target diaktifkan oleh Admin
        $settingModel = new \App\Models\SettingModel();
        $isDeadlineActive = $settingModel->getValue('enable_target_deadline', '0') === '1';
        $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);

        if ($isDeadlineActive && !hasAnyRole(['admin', 'direktur']) && !$isEditingStaf) {
            $now = new \DateTime();
            $targetMonth = new \DateTime(sprintf('%04d-%02d-01', $tahun, $bulan));
            $currentMonth = new \DateTime(date('Y-m-01'));
            $isLocked = false;

            if ($targetMonth < $currentMonth) {
                $isLocked = true;
            } elseif ($targetMonth == $currentMonth) {
                if ((int)$now->format('j') > $batasTarget) {
                    $isLocked = true;
                }
            }

            if ($isLocked) {
                $msg = "Gagal menyimpan: Batas waktu pengisian target kinerja untuk periode {$bulan}/{$tahun} telah berakhir (Maksimal tanggal {$batasTarget}).";
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->with('error', $msg);
            }
        }

        // Ambil data dari form
        $laporan_ids = $this->request->getPost('laporan_id');
        $sasaran_program_arr = $this->request->getPost('sasaran_program');
        $indikator_kinerja_arr = $this->request->getPost('indikator_kinerja');
        $target_bulanan_arr = $this->request->getPost('target_bulanan');
        $satuan_arr = $this->request->getPost('satuan');

        $dataToUpdate = [];
        $dataToInsert = [];
        $hasValidRow = false;
        $hasError = false;
        
        // Jika diedit oleh atasan atau pemilik akun adalah Direktur, langsung disetujui
        $isDirektur = (session()->get('role') === 'direktur');
        $status_approval = ($isEditingStaf || $isDirektur) ? 'disetujui' : 'menunggu_persetujuan';

        if ($sasaran_program_arr) {
            foreach ($sasaran_program_arr as $index => $sasaran) {
                $indikator = $indikator_kinerja_arr[$index] ?? '';
                $targetVal = $target_bulanan_arr[$index] ?? '';
                $satuanVal = $satuan_arr[$index] ?? '';
                
                $sasaranStr = trim((string)$sasaran);
                $indikatorStr = trim((string)$indikator);
                $targetStr = trim((string)$targetVal);
                $satuanStr = trim((string)$satuanVal);

                // Abaikan baris yang benar-benar kosong
                if ($sasaranStr === '' && $indikatorStr === '' && $targetStr === '' && $satuanStr === '') {
                    continue; 
                }

                // Jika Simpan & Kirim, pastikan tidak ada sel yang kosong dalam baris yang terisi
                if (!$isDraft) {
                    if ($sasaranStr === '' || $indikatorStr === '' || $targetStr === '' || $satuanStr === '') {
                        $hasError = true;
                        break;
                    }
                }

                // Konversi koma ke titik untuk desimal
                $targetValNum = str_replace(',', '.', $targetStr);
                
                // Pastikan target adalah angka yang valid (jika sudah diisi)
                if ($targetStr !== '' && !is_numeric($targetValNum)) {
                    $hasError = true;
                    break;
                }

                $hasValidRow = true;

                $rowData = [
                    'user_id'           => $targetUserId,
                    'tanggal'           => null,
                    'bulan'             => $bulan,
                    'tahun'             => $tahun,
                    'sasaran_program'   => $sasaranStr,
                    'indikator_kinerja' => $indikatorStr,
                    'target_bulanan'    => is_numeric($targetValNum) ? (float)$targetValNum : 0.00,
                    'satuan'            => $satuanStr,
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

        // Jika Simpan & Kirim, harus ada minimal 1 baris yang valid
        // Jika draf, boleh disimpan dalam keadaan kosong sama sekali (hasValidRow = false) tapi tidak boleh ada error format
        if ((!$isDraft && !$hasValidRow) || $hasError) {
            $msg = $hasError ? 'Gagal menyimpan. Pastikan angka target menggunakan format yang benar dan seluruh sel dalam baris terisi (jika Simpan & Kirim).' : 'Gagal menyimpan. Minimal harus ada 1 target.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $msg,
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        $insertedIds = [];
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if (!empty($dataToUpdate)) {
                $laporanModel->updateBatch($dataToUpdate, 'id');
            }
            if (!empty($dataToInsert)) {
                foreach ($dataToInsert as $origIndex => $insertRow) {
                    $laporanModel->insert($insertRow);
                    $insertedIds[$origIndex] = $laporanModel->getInsertID();
                }
            }

            $db->transComplete();
        } catch (\Throwable $e) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal terhubung ke database. Coba lagi atau hubungi admin.',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data ke database.');
        }

        // Jika Simpan & Kirim, update semua target bulan ini yang sebelumnya draf menjadi terkirim
        if (!$isDraft) {
            $updateData = ['status' => 'terkirim'];
            if ($isDirektur) {
                $updateData['status_approval'] = 'disetujui';
            }
            $laporanModel->where('user_id', $targetUserId)
                         ->where('bulan', $bulan)
                         ->where('tahun', $tahun)
                         ->set($updateData)
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
        $currentUserId = session()->get('id') ?? session()->get('user_id');

        if ($id) {
            $laporanModel = new LaporanHarian();
            $laporan = $laporanModel->find($id);
            if ($laporan) {
                $targetUser = (new \App\Models\User())->find($laporan['user_id']);
                $isAtasan = $targetUser && !empty($targetUser['atasan_id']) && ($targetUser['atasan_id'] == $currentUserId);
                if (!hasAnyRole(['admin', 'direktur']) && !$isAtasan) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Akses ditolak. Anda tidak memiliki izin menyetujui target ini.',
                        'csrf_hash' => csrf_hash()
                    ]);
                }
                $laporanModel->update($id, ['status_approval' => 'disetujui']);
                log_audit('APPROVE', 'laporan_harian', $id, null, ['status_approval' => 'disetujui']);
                return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
            }
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Data target tidak ditemukan.', 'csrf_hash' => csrf_hash()]);
    }
    
    public function approveAll()
    {
        $staf_id = $this->request->getPost('staf_id');
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        $currentUserId = session()->get('id') ?? session()->get('user_id');

        if ($staf_id && $bulan && $tahun) {
            // Otorisasi: Pastikan penilai adalah Atasan Langsung dari staf atau Superadmin
            $targetUser = (new \App\Models\User())->find($staf_id);
            $isAtasan = $targetUser && !empty($targetUser['atasan_id']) && ($targetUser['atasan_id'] == $currentUserId);
            if (!hasAnyRole(['admin', 'direktur']) && !$isAtasan) {
                return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki izin menyetujui target staf ini.');
            }

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
        $currentUserId = session()->get('id') ?? session()->get('user_id');

        if ($id) {
            $laporanModel = new LaporanHarian();
            $laporan = $laporanModel->find($id);
            if ($laporan) {
                // Otorisasi: Pastikan target milik user bersangkutan atau user adalah Admin / Atasan
                if ($laporan['user_id'] != $currentUserId && !hasAnyRole(['admin', 'kepegawaian', 'direktur'])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Akses ditolak. Anda tidak memiliki izin menghapus target ini.',
                        'csrf_hash' => csrf_hash()
                    ]);
                }

                $isDirektur = (session()->get('role') === 'direktur');
                if (!$isDirektur && !hasRole('admin')) {
                    // Jangan izinkan hapus jika sudah disetujui
                    if ($laporan['status_approval'] == 'disetujui') {
                        return $this->response->setJSON([
                            'success' => false, 
                            'message' => 'Terkunci. Target sudah disetujui oleh atasan.',
                            'csrf_hash' => csrf_hash()
                        ]);
                    }
                }

                $laporanModel->delete($id);
                log_audit('DELETE', 'laporan_harian', $id, $laporan, null);
                return $this->response->setJSON([
                    'success' => true,
                    'csrf_hash' => csrf_hash()
                ]);
            }
        }
        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Data target tidak ditemukan.',
            'csrf_hash' => csrf_hash()
        ]);
    }

    /**
     * [SUPERADMIN ONLY] Membatalkan persetujuan Target Bulanan pegawai
     * agar pegawai dapat merevisi target dan mengajukannya kembali ke atasan langsung.
     */
    public function cancelApprove()
    {
        if (!hasRole('admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Fitur ini hanya untuk Superadmin.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $stafId = $this->request->getPost('staf_id') ?? $this->request->getPost('target_user_id');
        $bulan  = $this->request->getPost('bulan');
        $tahun  = $this->request->getPost('tahun');

        if (!$stafId || !$bulan || !$tahun) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap (staf_id, bulan, dan tahun diperlukan).',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $laporanModel = new LaporanHarian();
        $userModel    = new User();

        $targetUser = $userModel->find($stafId);
        if (!$targetUser) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Staf tidak ditemukan.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $existingTargets = $laporanModel->where('user_id', $stafId)
                                        ->where('bulan', $bulan)
                                        ->where('tahun', $tahun)
                                        ->findAll();

        if (empty($existingTargets)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada target bulanan untuk staf tersebut pada periode ini.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $hasApproved = false;
        foreach ($existingTargets as $t) {
            if ($t['status_approval'] === 'disetujui') {
                $hasApproved = true;
                break;
            }
        }

        if (!$hasApproved) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Target bulanan pada periode ini belum berstatus disetujui.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Revert status_approval ke 'menunggu_persetujuan' dan status ke 'draft' agar form dapat diedit kembali oleh staf
            $laporanModel->where('user_id', $stafId)
                         ->where('bulan', $bulan)
                         ->where('tahun', $tahun)
                         ->set([
                             'status_approval' => 'menunggu_persetujuan',
                             'status'          => 'draft'
                         ])
                         ->update();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membatalkan persetujuan target. Terjadi kesalahan database.',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // Audit log
            log_audit('CANCEL_APPROVE_TARGET', 'laporan_harian', $stafId, null, [
                'bulan'        => $bulan,
                'tahun'        => $tahun,
                'staf'         => $targetUser['nama_lengkap'],
                'dibatal_oleh' => session()->get('nama_lengkap')
            ]);

            // Kirim notifikasi ke staf
            if (function_exists('send_notification')) {
                $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $namaBulan = $bulanIndo[(int)$bulan - 1] ?? $bulan;

                send_notification(
                    $stafId,
                    'Persetujuan Target Dibatalkan',
                    "Persetujuan Target Bulanan ({$namaBulan} {$tahun}) Anda telah dibatalkan oleh Superadmin. Silakan lakukan revisi dan ajukan kembali ke atasan langsung.",
                    site_url('laporan-harian')
                );
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Persetujuan Target Bulanan {$targetUser['nama_lengkap']} berhasil dibatalkan. Target kini berstatus draf.",
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal membatalkan persetujuan: ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }
}

