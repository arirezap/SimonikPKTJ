<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\TargetKinerja;
use App\Models\LaporanHarian;
use App\Models\Satuan;
use App\Models\User;

/**
 * Class LaporanHarianController
 * 
 * Mengelola siklus penetapan, revisi, penyalinan, dan persetujuan
 * Target Kinerja Bulanan (Rencana Hasil Kerja / RHK) bagi Staf, Atasan Langsung,
 * Direktur, dan Superadmin di lingkungan Evidence Command Center (ECC).
 */
class LaporanHarianController extends BaseController
{
    /**
     * Batas kuota maksimum baris target yang dapat disimpan dalam satu bulan.
     */
    public const MAX_TARGET_ROWS = 30;

    public function index()
    {
        $laporanModel = new TargetKinerja();
        $satuanModel = new Satuan();
        $userModel = new User();

        $userId = session()->get('id') ?? session()->get('user_id');
        $role = session()->get('role');
        
        // Gunakan session dan getVar agar filter via GET, POST, maupun navigasi URL tetap sinkron
        $reqBulan = $this->request->getVar('bulan');
        $reqTahun = $this->request->getVar('tahun');
        $reqStafId = $this->request->getVar('staf_id');
        $sourceTab = $this->request->getVar('source_tab');

        if (!empty($reqBulan) && is_numeric($reqBulan)) session()->set('laporan_harian_bulan', (int)$reqBulan);
        if (!empty($reqTahun) && is_numeric($reqTahun)) session()->set('laporan_harian_tahun', (int)$reqTahun);
        
        if ($sourceTab === 'sendiri') {
            session()->remove('laporan_harian_staf_id');
        } elseif ($sourceTab === 'staf' || $reqStafId !== null) {
            if (!empty($reqStafId)) {
                session()->set('laporan_harian_staf_id', $reqStafId);
            } else {
                session()->remove('laporan_harian_staf_id');
            }
        }

        if ($this->request->is('post') && ($sourceTab === 'sendiri' || $sourceTab === 'staf')) {
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
        $laporanModel = new TargetKinerja();

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

        // Server-Side Two-Tier Lock Check: Jika target bulan tersebut sudah disetujui, staf tidak boleh mengubahnya
        $isDirektur = (session()->get('role') === 'direktur');
        if (!$isEditingStaf && !$isDirektur && !hasRole('admin')) {
            $alreadyApprovedCount = $laporanModel->where('user_id', $targetUserId)
                                                 ->where('bulan', $bulan)
                                                 ->where('tahun', $tahun)
                                                 ->where('status_approval', 'disetujui')
                                                 ->countAllResults();
            if ($alreadyApprovedCount > 0) {
                $msg = "Gagal menyimpan: Target Kinerja Bulanan untuk periode {$bulan}/{$tahun} telah disetujui oleh atasan dan terkunci dari perubahan.";
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

        // Validasi batas kuota baris target kinerja
        if (!empty($sasaran_program_arr) && count($sasaran_program_arr) > self::MAX_TARGET_ROWS) {
            $msg = 'Gagal menyimpan: Jumlah baris target kinerja melebihi batas maksimum (' . self::MAX_TARGET_ROWS . ' baris per bulan).';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $msg,
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        $dataToUpdate = [];
        $dataToInsert = [];
        $hasValidRow = false;
        $hasError = false;
        
        // Jika diedit oleh atasan atau pemilik akun adalah Direktur, langsung disetujui
        $isDirektur = (session()->get('role') === 'direktur');
        $status_approval = ($isEditingStaf || $isDirektur) ? 'disetujui' : 'menunggu_persetujuan';

        $seenPairs = [];
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

                // Cek duplikasi pasangan sasaran program & indikator kinerja
                if ($sasaranStr !== '' || $indikatorStr !== '') {
                    $pairKey = mb_strtolower($sasaranStr . '|||' . $indikatorStr);
                    if (isset($seenPairs[$pairKey])) {
                        $rowA = $seenPairs[$pairKey] + 1;
                        $rowB = $index + 1;
                        $dupMsg = "Gagal menyimpan: Terdapat duplikasi target (RHK & Indikator yang sama) pada baris ke-{$rowA} dan baris ke-{$rowB}. Harap sesuaikan indikator agar tidak tercatat ganda.";
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON([
                                'success' => false,
                                'message' => $dupMsg,
                                'csrf_hash' => csrf_hash()
                            ]);
                        }
                        return redirect()->back()->withInput()->with('error', $dupMsg);
                    }
                    $seenPairs[$pairKey] = $index;
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
                
                // Pastikan target adalah angka yang valid dan bernilai lebih dari 0 (jika sudah diisi)
                if ($targetStr !== '') {
                    if (!is_numeric($targetValNum) || (float)$targetValNum <= 0) {
                        $hasError = true;
                        $errorCustomMsg = "Gagal menyimpan: Nilai Target Bulanan pada baris ke-" . ($index + 1) . " harus berupa angka lebih besar dari 0 (tidak boleh 0 atau bernilai negatif).";
                        break;
                    }
                }

                $hasValidRow = true;

                $rowData = [
                    'user_id'           => $targetUserId,
                    'tanggal'           => null,
                    'bulan'             => $bulan,
                    'tahun'             => $tahun,
                    'sasaran_program'   => $sasaranStr,
                    'indikator_kinerja' => $indikatorStr,
                    'target_bulanan'    => is_numeric($targetValNum) && (float)$targetValNum > 0 ? (float)$targetValNum : null,
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
            $msg = $errorCustomMsg ?? ($hasError ? 'Gagal menyimpan. Pastikan angka target menggunakan format yang benar (lebih dari 0) dan seluruh sel dalam baris terisi (jika Simpan & Kirim).' : 'Gagal menyimpan. Minimal harus ada 1 target.');
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

        // Catat Audit Trail aktivitas penetapan / pembaruan target
        $targetUser = (new \App\Models\User())->find($targetUserId);
        $targetUserName = $targetUser['nama_lengkap'] ?? "User #{$targetUserId}";
        $actionType = $isEditingStaf ? 'UPDATE_TARGET_STAF' : ($isDraft ? 'DRAFT_TARGET' : 'SUBMIT_TARGET');

        log_audit($actionType, 'target_kinerja_bulanan', $targetUserId, null, [
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'target_staf'   => $targetUserName,
            'jumlah_target' => count($dataToUpdate) + count($dataToInsert),
            'status'        => $targetStatus,
            'is_draft'      => $isDraft ? 1 : 0,
            'disimpan_oleh' => session()->get('nama_lengkap')
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil disimpan sementara.',
                'new_ids' => $insertedIds,
                'csrf_hash' => csrf_hash()
            ]);
        }

        // Kirim notifikasi in-app
        helper('notification');
        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = $bulanIndo[(int)$bulan - 1] ?? "Bulan {$bulan}";
        $currentUserName = session()->get('nama_lengkap') ?? 'Atasan';

        if ($isEditingStaf && $targetUserId != $userId) {
            // Notifikasi ke staf jika targetnya disunting/disetujui langsung oleh atasan
            send_notification(
                $targetUserId,
                'Target Kinerja Diperbarui Atasan',
                "Target Kinerja Bulanan Anda ({$namaBulan} {$tahun}) telah diperbarui dan disetujui oleh {$currentUserName}.",
                site_url('laporan-harian?bulan=' . $bulan . '&tahun=' . $tahun)
            );
        } elseif (!$isEditingStaf && !$isDraft) {
            // Notifikasi ke atasan langsung jika staf mengajukan target
            if ($targetUser && !empty($targetUser['atasan_id'])) {
                send_notification(
                    $targetUser['atasan_id'], 
                    'Persetujuan Target Bulanan', 
                    $targetUser['nama_lengkap'] . " mengirimkan Target Bulanan ({$namaBulan} {$tahun}) untuk diperiksa.",
                    site_url('laporan-harian?source_tab=staf&staf_id=' . $targetUserId)
                );
            }
        }

        return redirect()->to('/laporan-harian')
                         ->with('success', $isDraft ? 'Target Bulanan berhasil disimpan sementara.' : ($isEditingStaf ? 'Target Bulanan staf berhasil diperbarui dan disetujui.' : 'Target Bulanan berhasil dikirim ke atasan langsung.'));
    }
    
    public function approve()
    {
        $id = $this->request->getPost('id');
        $currentUserId = session()->get('id') ?? session()->get('user_id');

        if ($id) {
            $laporanModel = new TargetKinerja();
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
                $laporanModel->update($id, [
                    'status_approval' => 'disetujui',
                    'status'          => 'terkirim'
                ]);

                log_audit('APPROVE', 'target_kinerja_bulanan', $id, $laporan, [
                    'approved_by' => session()->get('nama_lengkap'),
                    'approver_id' => $currentUserId
                ]);

                // Kirim notifikasi ke staf bahwa targetnya telah disetujui
                if ($laporan['user_id'] != $currentUserId) {
                    helper('notification');
                    $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $namaBulan = $bulanIndo[(int)$laporan['bulan'] - 1] ?? "Bulan {$laporan['bulan']}";
                    $atasanName = session()->get('nama_lengkap') ?? 'Atasan Langsung';
                    $targetDesc = !empty($laporan['sasaran_program']) ? (' ("' . mb_strimwidth($laporan['sasaran_program'], 0, 40, '...') . '")') : '';

                    send_notification(
                        $laporan['user_id'],
                        'Target Kinerja Disetujui',
                        "Target Kinerja{$targetDesc} ({$namaBulan} {$laporan['tahun']}) telah disetujui oleh {$atasanName}.",
                        site_url('laporan-harian?bulan=' . $laporan['bulan'] . '&tahun=' . $laporan['tahun'])
                    );
                }

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

            $laporanModel = new TargetKinerja();
            $laporanModel->where('user_id', $staf_id)
                         ->where('bulan', $bulan)
                         ->where('tahun', $tahun)
                         ->set([
                             'status_approval' => 'disetujui',
                             'status'          => 'terkirim'
                         ])
                         ->update();

            log_audit('APPROVE_ALL', 'target_kinerja_bulanan', $staf_id, null, [
                'bulan'       => $bulan,
                'tahun'       => $tahun,
                'staf'        => $targetUser['nama_lengkap'] ?? "User #{$staf_id}",
                'approved_by' => session()->get('nama_lengkap'),
                'approver_id' => $currentUserId
            ]);
            
            helper('notification');
            $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $namaBulan = $bulanIndo[(int)$bulan - 1] ?? "Bulan {$bulan}";
            $atasanName = session()->get('nama_lengkap') ?? 'Atasan Langsung';

            send_notification(
                $staf_id,
                'Target Kinerja Disetujui',
                "Seluruh Target Kinerja Bulanan Anda ({$namaBulan} {$tahun}) telah disetujui oleh {$atasanName}.",
                site_url('laporan-harian?bulan=' . $bulan . '&tahun=' . $tahun)
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
            $laporanModel = new TargetKinerja();
            $laporan = $laporanModel->find($id);
            if ($laporan) {
                $targetUser = (new \App\Models\User())->find($laporan['user_id']);
                $isAtasan = $targetUser && !empty($targetUser['atasan_id']) && ($targetUser['atasan_id'] == $currentUserId);

                // Otorisasi: Pastikan target milik user bersangkutan atau user adalah Admin / Direktur / Kepegawaian / Atasan Langsung
                if ($laporan['user_id'] != $currentUserId && !hasAnyRole(['admin', 'kepegawaian', 'direktur']) && !$isAtasan) {
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

                $db = \Config\Database::connect();
                $db->transStart();

                $logModel = new \App\Models\LogKegiatanHarian();
                $affectedLogs = $logModel->where('target_id', $id)->countAllResults();
                if ($affectedLogs > 0) {
                    // Lepaskan target_id dan kembalikan status laporan harian ke 'draft'
                    $logModel->where('target_id', $id)
                             ->set([
                                 'target_id' => null,
                                 'status'    => 'draft'
                             ])
                             ->update();
                }

                $laporanModel->delete($id);
                log_audit('DELETE', 'target_kinerja_bulanan', $id, $laporan, [
                    'affected_logs_reset_to_draft' => $affectedLogs
                ]);

                $db->transComplete();

                if ($db->transStatus() === false) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal menghapus data target.',
                        'csrf_hash' => csrf_hash()
                    ]);
                }

                $msg = ($affectedLogs > 0)
                    ? "Target RHK berhasil dihapus. Sebanyak {$affectedLogs} catatan kegiatan harian yang sebelumnya menggunakan target ini telah kembali berstatus Draf agar targetnya dapat Anda sesuaikan."
                    : "Target RHK berhasil dihapus.";

                return $this->response->setJSON([
                    'success' => true,
                    'message' => $msg,
                    'affected_logs' => $affectedLogs,
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

        $laporanModel = new TargetKinerja();
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
            log_audit('CANCEL_APPROVE_TARGET', 'target_kinerja_bulanan', $stafId, null, [
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

    /**
     * AJAX endpoint untuk mengambil target kinerja dari bulan/periode tertentu
     * untuk disalin ke formulir target kinerja bulan berjalan.
     */
    public function getPreviousTargets()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        $bulanSumber = (int)$this->request->getVar('bulan');
        $tahunSumber = (int)$this->request->getVar('tahun');
        $stafId = $this->request->getVar('staf_id');

        $targetUserId = (!empty($stafId) && is_numeric($stafId)) ? (int)$stafId : $userId;

        // Validasi Otorisasi jika mengakses data staf lain
        if ($targetUserId !== $userId) {
            $userModel = new User();
            $targetUser = $userModel->find($targetUserId);
            $isAtasan = $targetUser && !empty($targetUser['atasan_id']) && ($targetUser['atasan_id'] == $userId);
            if (!hasAnyRole(['admin', 'direktur']) && !$isAtasan) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Akses ditolak: Anda tidak memiliki wewenang untuk mengambil data target staf ini.',
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(403);
            }
        }

        if (!$bulanSumber || !$tahunSumber) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Parameter bulan dan tahun sumber wajib dipilih.',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(400);
        }

        $laporanModel = new TargetKinerja();
        $targets = $laporanModel->where('user_id', $targetUserId)
                                ->where('bulan', $bulanSumber)
                                ->where('tahun', $tahunSumber)
                                ->orderBy('id', 'ASC')
                                ->findAll();

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulanSumber = $bulanIndo[$bulanSumber - 1] ?? "Bulan {$bulanSumber}";

        if (empty($targets)) {
            return $this->response->setJSON([
                'status' => 'empty',
                'message' => "Tidak ditemukan target kinerja pada periode {$namaBulanSumber} {$tahunSumber}.",
                'data' => [],
                'csrf_hash' => csrf_hash()
            ]);
        }

        $cleanData = [];
        foreach ($targets as $t) {
            $cleanData[] = [
                'sasaran_program'   => $t['sasaran_program'] ?? '',
                'indikator_kinerja' => $t['indikator_kinerja'] ?? '',
                'target_bulanan'    => isset($t['target_bulanan']) && $t['target_bulanan'] !== null ? (float)$t['target_bulanan'] : '',
                'satuan'            => $t['satuan'] ?? 'Kegiatan'
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => "Berhasil memuat " . count($cleanData) . " target dari {$namaBulanSumber} {$tahunSumber}.",
            'count' => count($cleanData),
            'nama_bulan_sumber' => $namaBulanSumber,
            'tahun_sumber' => $tahunSumber,
            'data' => $cleanData,
            'csrf_hash' => csrf_hash()
        ]);
    }
}


