<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Libraries\PenilaianKinerjaPolicy;
use App\Models\LogKegiatanHarian;
use App\Models\Satuan;
use App\Models\SettingModel;
use App\Models\TargetKinerja;
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

    /**
     * Memeriksa apakah pegawai memiliki izin revisi yang sah dari Superadmin untuk periode tertentu.
     * Izin revisi diverifikasi melalui jejak audit CANCEL_APPROVE_TARGET dalam 14 hari terakhir
     * dan target pada periode tersebut masih berstatus belum disetujui (draf revisi).
     */
    public static function hasRevisionPermission(int $userId, int $bulan, int $tahun): bool
    {
        if ($userId <= 0 || $bulan < 1 || $bulan > 12 || $tahun < 2020) {
            return false;
        }

        $db = \Config\Database::connect();
        $fourteenDaysAgo = date('Y-m-d H:i:s', strtotime('-14 days'));
        $auditLogs = $db->table('audit_logs')
                        ->where('action', 'CANCEL_APPROVE_TARGET')
                        ->where('entity', 'target_kinerja_bulanan')
                        ->where('entity_id', (string)$userId)
                        ->where('created_at >=', $fourteenDaysAgo)
                        ->orderBy('id', 'DESC')
                        ->get()
                        ->getResultArray();

        if (empty($auditLogs)) {
            return false;
        }

        $hasValidAudit = false;
        foreach ($auditLogs as $log) {
            $newVals = !empty($log['new_values']) ? json_decode($log['new_values'], true) : null;
            if (is_array($newVals) && isset($newVals['bulan'], $newVals['tahun'])) {
                if ((int)$newVals['bulan'] === (int)$bulan && (int)$newVals['tahun'] === (int)$tahun) {
                    $hasValidAudit = true;
                    break;
                }
            }
        }

        if (!$hasValidAudit) {
            return false;
        }

        $targetModel = new TargetKinerja();
        $targets = $targetModel->where('user_id', $userId)
                               ->where('bulan', $bulan)
                               ->where('tahun', $tahun)
                               ->findAll();

        if (empty($targets)) {
            return false;
        }

        $allApproved = true;
        foreach ($targets as $t) {
            if (($t['status_approval'] ?? '') !== 'disetujui') {
                $allApproved = false;
                break;
            }
        }

        return !$allApproved;
    }

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
        $reqUnitKerja = $this->request->getVar('unit_kerja');
        $sourceTab = $this->request->getVar('source_tab');

        if (!empty($reqBulan) && is_numeric($reqBulan)) session()->set('laporan_harian_bulan', (int)$reqBulan);
        if (!empty($reqTahun) && is_numeric($reqTahun)) session()->set('laporan_harian_tahun', (int)$reqTahun);
        
        if ($reqUnitKerja !== null) {
            if ($reqUnitKerja !== session()->get('laporan_harian_unit_kerja')) {
                session()->remove('laporan_harian_staf_id');
            }
            if (!empty($reqUnitKerja)) {
                session()->set('laporan_harian_unit_kerja', $reqUnitKerja);
            } else {
                session()->remove('laporan_harian_unit_kerja');
            }
        }

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
        $unitKerjaTerpilih = session()->get('laporan_harian_unit_kerja') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = $bulanIndo[$bulanTerpilih - 1];

        // Ambil data laporan/target pada bulan tersebut untuk user ini (Target Saya)
        $rekapDataSendiri = $laporanModel->select('target_kinerja_bulanan.id, target_kinerja_bulanan.user_id, target_kinerja_bulanan.sasaran_program, target_kinerja_bulanan.indikator_kinerja, target_kinerja_bulanan.target_bulanan, target_kinerja_bulanan.satuan, target_kinerja_bulanan.status_approval, target_kinerja_bulanan.status, target_kinerja_bulanan.nilai_capaian, target_kinerja_bulanan.status_penilaian, target_kinerja_bulanan.approved_by, target_kinerja_bulanan.approved_at, approver.nama_lengkap as nama_penyetuju')
                                         ->join('users as approver', 'approver.id = target_kinerja_bulanan.approved_by', 'left')
                                         ->where('target_kinerja_bulanan.user_id', $userId)
                                         ->where('target_kinerja_bulanan.bulan', $bulanTerpilih)
                                         ->where('target_kinerja_bulanan.tahun', $tahunTerpilih)
                                         ->orderBy('target_kinerja_bulanan.id', 'ASC')
                                         ->findAll();

        // Cek apakah user punya staf atau akses pimpinan eksekutif (Admin, Direktur)
        // Catatan: Role Wadir tidak memiliki akses menyetujui / merevisi target staf
        $isSuper = hasAnyRole(['admin', 'direktur']);
        $daftarUnit = [];
        if ($isSuper) {
            $units = $userModel->select('unit')->distinct()->where('unit !=', null)->where('unit !=', '')->orderBy('unit', 'ASC')->findAll();
            foreach ($units as $u) {
                $daftarUnit[] = $u['unit'];
            }

            $periodDate = sprintf('%04d-%02d-01', (int)$tahunTerpilih, (int)$bulanTerpilih);
            $periodMonth = date('Y-m', strtotime($periodDate));

            $builder = $userModel->select('id, nama_lengkap, jabatan, unit, role, status_pegawai, tmt_selesai, atasan_id')
                                 ->where('id !=', $userId)
                                 ->groupStart()
                                     ->where('status_pegawai', 'aktif')
                                     ->orGroupStart()
                                         ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun'])
                                         ->where('tmt_selesai IS NOT NULL')
                                         ->where('tmt_selesai >=', $periodMonth . '-01')
                                     ->groupEnd()
                                 ->groupEnd();
            if (!empty($unitKerjaTerpilih)) {
                $builder = $builder->where('unit', $unitKerjaTerpilih);
            }
            $daftarStaf = $builder->orderBy('nama_lengkap', 'ASC')->findAll();
            $isAtasan = true;
        } elseif (hasRole('wadir')) {
            // Role Wakil Direktur tidak mengelola / menyetujui target staf (hanya mengelola Target Saya)
            $daftarStaf = [];
            $isAtasan = false;
        } else {
            $periodDate = sprintf('%04d-%02d-01', (int)$tahunTerpilih, (int)$bulanTerpilih);
            $periodMonth = date('Y-m', strtotime($periodDate));

            $daftarStaf = $userModel->getStaf($userId, $periodDate);
            // Jika user punya role kepegawaian, sertakan seluruh pegawai Tugas Belajar
            if (hasRole('kepegawaian')) {
                $db = \Config\Database::connect();
                $tubelIds = array_column($db->table('user_roles')->select('user_id')->where('role_name', 'tugas_belajar')->get()->getResultArray(), 'user_id');
                $tubelBuilder = $userModel->select('id, nama_lengkap, jabatan, unit, role, status_pegawai, tmt_selesai, atasan_id')
                                          ->groupStart()
                                          ->where('role', 'tugas_belajar');
                if (!empty($tubelIds)) {
                    $tubelBuilder->orWhereIn('id', $tubelIds);
                }
                $tubelUsers = $tubelBuilder->groupEnd()
                                           ->groupStart()
                                               ->where('status_pegawai', 'aktif')
                                               ->orGroupStart()
                                                   ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun'])
                                                   ->where('tmt_selesai IS NOT NULL')
                                                   ->where('tmt_selesai >=', $periodMonth . '-01')
                                               ->groupEnd()
                                           ->groupEnd()
                                           ->where('id !=', $userId)
                                           ->findAll();

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
                $rekapDataStaf = $laporanModel->select('target_kinerja_bulanan.id, target_kinerja_bulanan.user_id, target_kinerja_bulanan.sasaran_program, target_kinerja_bulanan.indikator_kinerja, target_kinerja_bulanan.target_bulanan, target_kinerja_bulanan.satuan, target_kinerja_bulanan.status_approval, target_kinerja_bulanan.status, target_kinerja_bulanan.nilai_capaian, target_kinerja_bulanan.status_penilaian, target_kinerja_bulanan.approved_by, target_kinerja_bulanan.approved_at, approver.nama_lengkap as nama_penyetuju')
                                              ->join('users as approver', 'approver.id = target_kinerja_bulanan.approved_by', 'left')
                                              ->where('target_kinerja_bulanan.user_id', $stafIdTerpilih)
                                              ->where('target_kinerja_bulanan.bulan', $bulanTerpilih)
                                              ->where('target_kinerja_bulanan.tahun', $tahunTerpilih)
                                              ->where('target_kinerja_bulanan.status', 'terkirim')
                                              ->orderBy('target_kinerja_bulanan.id', 'ASC')
                                              ->findAll();
            } else {
                $stafIdTerpilih = '';
                session()->remove('laporan_staf_id');
            }
        }

        // Logika Batas Waktu Berdasarkan Pengaturan Sistem
        $settingModel = new SettingModel();
        $isDeadlineActive = $settingModel->getValue('enable_target_deadline', '0') === '1';
        $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);
        $isLocked = false;

        // Cek apakah periode ini memiliki izin revisi sah yang terverifikasi (K-02 & TKB-11)
        $targetUserIdContext = !empty($stafIdTerpilih) ? (int)$stafIdTerpilih : (int)$userId;
        $isRevision = self::hasRevisionPermission($targetUserIdContext, $bulanTerpilih, $tahunTerpilih);

        if ($isDeadlineActive && !hasRole('admin') && !hasRole('direktur')) {
            $now = new \DateTime();
            $targetMonth = new \DateTime(sprintf('%04d-%02d-01', $tahunTerpilih, $bulanTerpilih));
            $currentMonth = new \DateTime(date('Y-m-01'));

            if ($targetMonth < $currentMonth) {
                // Bulan lalu terkunci jika pembatasan deadline aktif (kecuali jika dalam izin revisi sah)
                $isLocked = !$isRevision;
            } elseif ($targetMonth == $currentMonth) {
                // Bulan berjalan: terkunci jika melewati tanggal batas (kecuali jika dalam izin revisi sah)
                if ((int)$now->format('j') > $batasTarget) {
                    $isLocked = !$isRevision;
                }
            }
            // Bulan ke depan ($targetMonth > $currentMonth) DIPERBOLEHKAN (tidak dikunci)
        }

        // Cek Pembatasan Batas Penugasan (TMT Selesai) untuk Pegawai Mutasi, Pensiun, atau Nonaktif
        $targetUserContext = $userModel->select('id, nama_lengkap, nip, username, role, status_pegawai, tmt_selesai, dispensasi_lampau_sampai')->find($targetUserIdContext);
        $targetUserStatus = $targetUserContext['status_pegawai'] ?? 'aktif';
        $targetTmtSelesai = $targetUserContext['tmt_selesai'] ?? null;
        
        $isPostTmt = false;
        $postTmtReason = '';
        if ($targetUserStatus !== 'aktif' && !empty($targetTmtSelesai)) {
            $selectedPeriodStr = sprintf('%04d-%02d-01', (int)$tahunTerpilih, (int)$bulanTerpilih);
            $tmtPeriodStr = date('Y-m-01', strtotime($targetTmtSelesai));
            if ($selectedPeriodStr > $tmtPeriodStr) {
                $isLocked = true;
                $isPostTmt = true;
                $tmtMonthFormatted = ($bulanIndo[(int)date('n', strtotime($targetTmtSelesai)) - 1] ?? '') . ' ' . date('Y', strtotime($targetTmtSelesai));
                $tmtDateFormatted = date('d/m/Y', strtotime($targetTmtSelesai));
                $labelStatus = ($targetUserStatus === 'pensiun') ? 'Pensiun' : (($targetUserStatus === 'nonaktif') ? 'Nonaktif' : 'Mutasi Keluar');
                if (!empty($stafIdTerpilih)) {
                    $postTmtReason = "Pegawai telah berstatus {$labelStatus} per {$tmtDateFormatted}. Pengisian target kinerja hanya dapat dilakukan sampai periode {$tmtMonthFormatted}.";
                } else {
                    $postTmtReason = "Akun Anda telah berstatus {$labelStatus} per {$tmtDateFormatted}. Pengisian target kinerja hanya dapat dilakukan sampai periode {$tmtMonthFormatted}.";
                }
            }
        }

        $data = [
            'title' => 'Target Kinerja Bulanan',
            'bulan_terpilih' => $bulanTerpilih,
            'tahun_terpilih' => $tahunTerpilih,
            'nama_bulan' => $namaBulan,
            'rekap_data_sendiri' => $rekapDataSendiri,
            'rekap_data_staf' => $rekapDataStaf,
            'daftar_satuan' => $satuanModel->orderBy('nama_satuan', 'ASC')->findAll(),
            'bulan_indo' => $bulanIndo,
            'batas_target' => $batasTarget,
            'is_locked' => $isLocked,
            'is_revision' => $isRevision,
            'is_post_tmt' => $isPostTmt,
            'post_tmt_reason' => $postTmtReason,
            'is_atasan' => $isAtasan,
            'is_penyetuju' => $isPenyetuju,
            'daftar_staf' => $daftarStaf,
            'staf_id_terpilih' => $stafIdTerpilih,
            'is_super' => $isSuper,
            'daftar_unit' => $daftarUnit,
            'unit_kerja_terpilih' => $unitKerjaTerpilih
        ];

        return view('user/laporan_harian/index', $data);
    }

    public function store()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        $laporanModel = new TargetKinerja();
        $userModel = new User();

        $currentUser = $userModel->select('id, nama_lengkap, nip, username, role, unit, atasan_id')->find($userId);
        if (!$currentUser) {
            $msg = 'Sesi pengguna tidak valid.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->to('/login')->with('error', $msg);
        }

        // Validasi parameter bulan dan tahun (TKB-10)
        $rawBulan = $this->request->getPost('bulan');
        $rawTahun = $this->request->getPost('tahun');
        if (!is_numeric($rawBulan) || (int)$rawBulan < 1 || (int)$rawBulan > 12) {
            $msg = 'Bulan tidak valid.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', $msg);
        }
        if (!is_numeric($rawTahun) || (int)$rawTahun < 2020 || (int)$rawTahun > 2099) {
            $msg = 'Tahun tidak valid.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', $msg);
        }

        $bulan = (int)$rawBulan;
        $tahun = (int)$rawTahun;

        $isEditingStafInput = $this->request->getPost('is_editing_staf') == '1';
        $stafIdInput = $this->request->getPost('staf_id');
        $targetUserId = (!empty($stafIdInput) && is_numeric($stafIdInput)) ? (int)$stafIdInput : (int)$userId;

        // Anti-Spoofing (TKB-01): Jika target adalah diri sendiri, dilarang memakai mode is_editing_staf
        if ((int)$targetUserId === (int)$userId) {
            $isEditingStaf = false;
            $targetUserId = (int)$userId;
            $targetUser = $currentUser;
        } else {
            // Target adalah staf lain: wajib divalidasi dengan PenilaianKinerjaPolicy
            $targetUser = $userModel->select('id, nama_lengkap, nip, username, role, unit, status_pegawai, tmt_selesai, dispensasi_lampau_sampai, atasan_id')->find($targetUserId);
            if (!$targetUser) {
                $msg = 'Pengguna target tidak ditemukan.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->with('error', $msg);
            }

            if (!PenilaianKinerjaPolicy::canEditTarget($currentUser, $targetUser, $bulan, $tahun)) {
                $msg = 'Akses ditolak. Anda tidak memiliki izin menyunting target staf ini.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->with('error', $msg);
            }

            $isEditingStaf = true;
        }

        // Penentuan Aksi Eksplisit (TKB-10)
        $action = strtolower(trim((string)$this->request->getPost('action')));
        $isDraft = ($action === 'draft');
        $targetStatus = $isDraft ? 'draft' : 'terkirim';

        // Validasi Status Kepegawaian & Mode Arsip (Read-Only)
        $targetUserStatus = $targetUser['status_pegawai'] ?? 'aktif';
        $hasDispensasi = !empty($targetUser['dispensasi_lampau_sampai']) && $targetUser['dispensasi_lampau_sampai'] >= date('Y-m-d');
        $targetPeriodeStr = sprintf('%04d-%02d-01', (int)$tahun, (int)$bulan);
        $tmtSelesaiStr = !empty($targetUser['tmt_selesai']) ? date('Y-m-01', strtotime($targetUser['tmt_selesai'])) : null;

        if ($targetUserStatus !== 'aktif') {
            // Batas mutlak: periode target tidak boleh melampaui bulan dan tahun masa penugasan (TMT)
            if ($tmtSelesaiStr !== null && $targetPeriodeStr > $tmtSelesaiStr) {
                $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $tmtMonthFormatted = ($bulanIndo[(int)date('n', strtotime($targetUser['tmt_selesai'])) - 1] ?? '') . ' ' . date('Y', strtotime($targetUser['tmt_selesai']));
                $msg = "Target kinerja tidak dapat dibuat untuk periode setelah akhir masa tugas pegawai ({$tmtMonthFormatted}).";
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->with('error', $msg);
            }

            // Jika status non-aktif tanpa TMT dan tidak ada dispensasi
            if (empty($targetUser['tmt_selesai']) && !$hasDispensasi) {
                $msg = 'Akun dalam status arsip kepegawaian. Tidak dapat menambah atau mengubah target kinerja.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->with('error', $msg);
            }
        }

        // Validasi Kunci Waktu jika pembatasan deadline target diaktifkan oleh Admin
        $settingModel = new SettingModel();
        $isDeadlineActive = $settingModel->getValue('enable_target_deadline', '0') === '1';
        $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);

        $isRevision = self::hasRevisionPermission($targetUserId, $bulan, $tahun);

        if ($isDeadlineActive && !hasRole('admin') && !hasRole('direktur') && !$isEditingStaf && !$isRevision && !$hasDispensasi) {
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

        $isTargetDirektur = (strtolower((string)($targetUser['role'] ?? '')) === 'direktur');

        // Ambil data array dari form (TKB-10, TKB-08, TKB-07)
        $laporan_ids = $this->request->getPost('laporan_id');
        $sasaran_program_arr = $this->request->getPost('sasaran_program');
        $indikator_kinerja_arr = $this->request->getPost('indikator_kinerja');
        $target_bulanan_arr = $this->request->getPost('target_bulanan');
        $satuan_arr = $this->request->getPost('satuan');
        $client_row_ids = $this->request->getPost('client_row_id');
        $deleted_ids = $this->request->getPost('deleted_ids');

        // Validasi tipe array satu tingkat (menolak input scalar jika field terkirim)
        foreach ([
            'laporan_id' => $laporan_ids, 
            'sasaran_program' => $sasaran_program_arr, 
            'indikator_kinerja' => $indikator_kinerja_arr, 
            'target_bulanan' => $target_bulanan_arr, 
            'satuan' => $satuan_arr,
            'client_row_id' => $client_row_ids,
            'deleted_ids' => $deleted_ids
        ] as $paramName => $paramVal) {
            if ($paramVal !== null && !is_array($paramVal)) {
                $msg = 'Format data tidak valid.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->with('error', $msg);
            }
        }

        $laporan_ids = is_array($laporan_ids) ? $laporan_ids : [];
        $sasaran_program_arr = is_array($sasaran_program_arr) ? $sasaran_program_arr : [];
        $indikator_kinerja_arr = is_array($indikator_kinerja_arr) ? $indikator_kinerja_arr : [];
        $target_bulanan_arr = is_array($target_bulanan_arr) ? $target_bulanan_arr : [];
        $satuan_arr = is_array($satuan_arr) ? $satuan_arr : [];
        $client_row_ids = is_array($client_row_ids) ? $client_row_ids : [];
        $deleted_ids = is_array($deleted_ids) ? $deleted_ids : [];

        // Validasi batas kuota baris dalam payload (maksimal 30 baris per request)
        if (count($sasaran_program_arr) > self::MAX_TARGET_ROWS) {
            $msg = 'Jumlah baris target melebihi batas maksimal (' . self::MAX_TARGET_ROWS . ' baris).';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        // Ambil data target yang sudah tersimpan di database untuk pengguna dan periode ini (TKB-02 & TKB-06)
        $existingDbTargets = $laporanModel->getTargetsByUserAndPeriod($targetUserId, $bulan, $tahun);
        $existingDbMap = [];
        foreach ($existingDbTargets as $dbt) {
            $existingDbMap[(int)$dbt['id']] = $dbt;
        }

        // Validasi dan siapkan daftar ID yang dihapus secara eksplisit (TKB-07 / K-03)
        $dataToDelete = [];
        foreach ($deleted_ids as $delId) {
            if ($delId === null || trim((string)$delId) === '') continue;
            $intDelId = (int)$delId;
            if ($intDelId <= 0) continue;

            if (!isset($existingDbMap[$intDelId])) {
                continue; // Bukan ID periode ini / bukan milik user ini
            }

            if ($existingDbMap[$intDelId]['status_approval'] === 'disetujui' && !$isTargetDirektur) {
                $msg = 'Target yang sudah disetujui tidak dapat diganti atau dihapus.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->withInput()->with('error', $msg);
            }

            $dataToDelete[] = $intDelId;
        }

        // Validasi ID Target & Anti-Pindah Periode (TKB-02)
        $submittedIds = [];
        foreach ($laporan_ids as $lid) {
            if ($lid !== null && trim((string)$lid) !== '') {
                $lidStr = trim((string)$lid);
                if (!is_numeric($lidStr) || (int)$lidStr <= 0) {
                    $msg = 'ID target tidak valid.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
                $intId = (int)$lidStr;
                if (in_array($intId, $submittedIds, true)) {
                    $msg = 'Terdeteksi duplikasi ID target.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
                $submittedIds[] = $intId;
            }
        }

        $oldTargetValues = [];
        if (!empty($submittedIds)) {
            $foundDbRows = $laporanModel->whereIn('id', $submittedIds)->findAll();
            if (count($foundDbRows) !== count($submittedIds)) {
                $msg = 'Data target tidak ditemukan.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->with('error', $msg);
            }
            foreach ($foundDbRows as $fRow) {
                // Anti-IDOR: ID harus milik pengguna target yang sedang dikelola
                if ((int)$fRow['user_id'] !== (int)$targetUserId) {
                    $msg = 'Akses ditolak. ID target tidak sesuai.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
                // Anti-Pindah Periode (TKB-02): ID target harus berasal dari bulan & tahun yang sama
                if ((int)$fRow['bulan'] !== $bulan || (int)$fRow['tahun'] !== $tahun) {
                    $msg = 'Target tidak sesuai dengan periode yang dipilih.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->with('error', $msg);
                }
                $oldTargetValues[(int)$fRow['id']] = $fRow;
            }
        }

        // Parsing & Validasi Tiap Baris (TKB-10)
        $status_approval = ($isEditingStaf || $isTargetDirektur) ? 'disetujui' : 'menunggu_persetujuan';
        $dataToUpdate = [];
        $dataToInsert = [];
        $updatedIdSet = [];
        $hasValidRow = false;

        $resetScoreTargetIds = [];
        $maxIndex = max(
            count($sasaran_program_arr),
            count($indikator_kinerja_arr),
            count($target_bulanan_arr),
            count($satuan_arr),
            count($laporan_ids)
        );

        for ($index = 0; $index < $maxIndex; $index++) {
            $sasaranRaw = $sasaran_program_arr[$index] ?? '';
            $indikatorRaw = $indikator_kinerja_arr[$index] ?? '';
            $targetRaw = $target_bulanan_arr[$index] ?? '';
            $satuanRaw = $satuan_arr[$index] ?? '';
            $rowIdRaw = $laporan_ids[$index] ?? '';

            // Pastikan elemen berupa nilai skalar
            if (is_array($sasaranRaw) || is_array($indikatorRaw) || is_array($targetRaw) || is_array($satuanRaw) || is_array($rowIdRaw)) {
                $msg = 'Format data baris tidak valid.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->with('error', $msg);
            }

            $sasaranStr = trim((string)$sasaranRaw);
            $indikatorStr = trim((string)$indikatorRaw);
            $targetStr = trim((string)$targetRaw);
            $satuanStr = trim((string)$satuanRaw);
            $rowId = (!empty($rowIdRaw) && is_numeric($rowIdRaw)) ? (int)$rowIdRaw : null;

            // Abaikan baris yang seluruh isinya kosong tanpa ID
            if ($sasaranStr === '' && $indikatorStr === '' && $targetStr === '' && $satuanStr === '' && $rowId === null) {
                continue;
            }

            // Validasi panjang string teks (TKB-10)
            $rowNumber = $index + 1;
            if (mb_strlen($sasaranStr) > 255) {
                $msg = "Rencana Hasil Kerja pada baris ke-{$rowNumber} melebihi 255 karakter.";
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->withInput()->with('error', $msg);
            }
            if (mb_strlen($indikatorStr) > 255) {
                $msg = "Indikator kinerja pada baris ke-{$rowNumber} melebihi 255 karakter.";
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->withInput()->with('error', $msg);
            }
            if (mb_strlen($satuanStr) > 255) {
                $msg = "Satuan pada baris ke-{$rowNumber} melebihi 255 karakter.";
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->withInput()->with('error', $msg);
            }

            // Validasi angka target (desimal koma Indonesia & batas kapasitas)
            $targetValNum = null;
            if ($targetStr !== '') {
                $targetNormalized = str_replace(',', '.', $targetStr);
                if (!is_numeric($targetNormalized) || !is_finite((float)$targetNormalized) || (float)$targetNormalized <= 0 || (float)$targetNormalized >= 10000000) {
                    $msg = "Nilai target pada baris ke-{$rowNumber} harus berupa angka positif yang valid.";
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->withInput()->with('error', $msg);
                }
                $targetValNum = round((float)$targetNormalized, 2);
            }

            // Validasi kelengkapan jika aksi bukan draf (Simpan & Kirim wajib lengkap)
            if (!$isDraft) {
                if ($sasaranStr === '' || $indikatorStr === '' || $targetValNum === null || $satuanStr === '') {
                    $msg = "Lengkapi seluruh kolom pada baris ke-{$rowNumber}.";
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->withInput()->with('error', $msg);
                }
            }

            // Tandai baris valid jika memiliki isi minimal
            if ($sasaranStr !== '' || $indikatorStr !== '' || $targetValNum !== null || $satuanStr !== '') {
                $hasValidRow = true;
            }

            $rowData = [
                'user_id'           => $targetUserId,
                'tanggal'           => null,
                'bulan'             => $bulan,
                'tahun'             => $tahun,
                'sasaran_program'   => $sasaranStr,
                'indikator_kinerja' => $indikatorStr,
                'target_bulanan'    => $targetValNum,
                'satuan'            => $satuanStr,
                'status_approval'   => $status_approval,
                'status'            => $targetStatus
            ];

            if ($rowId !== null && isset($oldTargetValues[$rowId])) {
                $oldRow = $oldTargetValues[$rowId];
                $isOldApproved = (($oldRow['status_approval'] ?? '') === 'disetujui');

                $isSubstantiveChanged = (
                    $sasaranStr !== trim((string)$oldRow['sasaran_program']) ||
                    $indikatorStr !== trim((string)$oldRow['indikator_kinerja']) ||
                    $satuanStr !== trim((string)$oldRow['satuan']) ||
                    (float)$targetValNum !== (float)$oldRow['target_bulanan']
                );

                // TKB-05: Penyelarasan penguncian per baris (row-level lock)
                if ($isOldApproved && !$isTargetDirektur) {
                    if ($isSubstantiveChanged) {
                        $msg = "Target pada baris ke-{$rowNumber} sudah disetujui oleh atasan dan tidak dapat diubah.";
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->withInput()->with('error', $msg);
                    }

                    // Baris tidak diubah: pertahankan data lama dan status disetujui tanpa menimpanya
                    $updatedIdSet[$rowId] = true;
                    continue;
                }

                // K-06: Jika isi substantif berubah pada revisi sah, invalidasi nilai penilaian lama
                if ($isSubstantiveChanged && ($oldRow['nilai_capaian'] !== null || $oldRow['status_penilaian'] !== null)) {
                    $rowData['nilai_capaian'] = null;
                    $rowData['status_penilaian'] = null;
                    $resetScoreTargetIds[] = $rowId;
                }

                $rowData['id'] = $rowId;
                $dataToUpdate[] = $rowData;
                $updatedIdSet[$rowId] = true;
            } else {
                $clientKey = (!empty($client_row_ids[$index])) ? (string)$client_row_ids[$index] : "c_row_{$index}";
                $dataToInsert[$clientKey] = $rowData;
            }
        }

        // Kunci periode penuh jika seluruh target di basis data telah disetujui dan tidak ada baris baru/perubahan
        $allApprovedDbCount = 0;
        foreach ($existingDbTargets as $dbt) {
            if ($dbt['status_approval'] === 'disetujui') {
                $allApprovedDbCount++;
            }
        }
        $isAllDbApproved = (!empty($existingDbTargets) && $allApprovedDbCount === count($existingDbTargets));
        if ($isAllDbApproved && empty($dataToInsert) && empty($dataToUpdate) && empty($dataToDelete) && !$isTargetDirektur && !hasRole('admin')) {
            $msg = "Semua target kinerja untuk periode {$bulan}/{$tahun} telah disetujui oleh atasan dan terkunci.";
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Jika Simpan & Kirim, harus ada minimal 1 baris yang valid
        if (!$isDraft && !$hasValidRow && empty($existingDbTargets)) {
            $msg = 'Isi minimal 1 target kinerja.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        // Perhitungan Keadaan Akhir (Final State) & Kuota 30 (TKB-06)
        $finalState = [];
        // 1. Data target yang diperbarui dalam request ini
        foreach ($dataToUpdate as $upRow) {
            $finalState[] = $upRow;
        }
        // 2. Data target tersimpan di DB yang tidak dikirim dalam request (dipertahankan, kecuali yang dihapus eksplisit)
        foreach ($existingDbMap as $dbId => $dbRow) {
            if (!isset($updatedIdSet[$dbId]) && !in_array($dbId, $dataToDelete, true)) {
                // TKB-03: Jika diajukan, pastikan draf lama yang tersimpan juga sudah lengkap
                if (!$isDraft) {
                    if (trim((string)$dbRow['sasaran_program']) === '' || trim((string)$dbRow['indikator_kinerja']) === '' || empty($dbRow['target_bulanan']) || (float)$dbRow['target_bulanan'] <= 0 || trim((string)$dbRow['satuan']) === '') {
                        $msg = 'Terdapat draf target tersimpan yang belum lengkap. Silakan lengkapi atau hapus sebelum mengajukan.';
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
                        }
                        return redirect()->back()->withInput()->with('error', $msg);
                    }
                }
                $finalState[] = $dbRow;
            }
        }
        // 3. Data target baru yang ditambahkan
        foreach ($dataToInsert as $insRow) {
            $finalState[] = $insRow;
        }

        // Penegakan kuota maksimum 30 target pada keadaan akhir
        if (count($finalState) > self::MAX_TARGET_ROWS) {
            $msg = 'Total target kinerja untuk periode ini melebihi batas maksimal ' . self::MAX_TARGET_ROWS . ' target.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        // Penegakan keunikan RHK + Indikator pada seluruh keadaan akhir
        $seenPairs = [];
        foreach ($finalState as $fsRow) {
            $sKey = trim((string)($fsRow['sasaran_program'] ?? ''));
            $iKey = trim((string)($fsRow['indikator_kinerja'] ?? ''));
            if ($sKey !== '' || $iKey !== '') {
                $pairHash = mb_strtolower($sKey . '|||' . $iKey);
                if (isset($seenPairs[$pairHash])) {
                    $dupMsg = 'Terdapat duplikasi target (Rencana Hasil Kerja dan Indikator yang sama). Harap sesuaikan indikator agar tidak tercatat ganda.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $dupMsg, 'csrf_hash' => csrf_hash()]);
                    }
                    return redirect()->back()->withInput()->with('error', $dupMsg);
                }
                $seenPairs[$pairHash] = true;
            }
        }

        // Simpan ke Basis Data dalam Transaksi Atomik
        $insertedIds = [];
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Hapus baris yang diminta secara eksplisit (TKB-07 / K-03)
            if (!empty($dataToDelete)) {
                $logModel = new LogKegiatanHarian();
                $logModel->where('user_id', $targetUserId)
                         ->whereIn('target_id', $dataToDelete)
                         ->set(['target_id' => null, 'status' => 'draft'])
                         ->update();

                $laporanModel->where('user_id', $targetUserId)
                             ->whereIn('id', $dataToDelete)
                             ->delete();
            }

            if (!empty($dataToUpdate)) {
                foreach ($dataToUpdate as $upRow) {
                    $targetId = (int)$upRow['id'];
                    unset($upRow['id']);
                    $laporanModel->update($targetId, $upRow);
                }
            }
            if (!empty($dataToInsert)) {
                foreach ($dataToInsert as $clientKey => $insertRow) {
                    $laporanModel->insert($insertRow);
                    $insertedIds[$clientKey] = $laporanModel->getInsertID();
                }
            }

            // Jika Simpan & Kirim, update target bulan ini yang belum disetujui menjadi terkirim
            if (!$isDraft) {
                $updateData = ['status' => 'terkirim'];
                if ($isEditingStaf || $isTargetDirektur) {
                    $updateData['status_approval'] = 'disetujui';
                }
                $laporanModel->where('user_id', $targetUserId)
                             ->where('bulan', $bulan)
                             ->where('tahun', $tahun)
                             ->where('status_approval !=', 'disetujui')
                             ->set($updateData)
                             ->update();
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Gagal menyimpan target ke basis data.');
            }
        } catch (\Throwable $e) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', 'LaporanHarianController::store failed for user #' . $targetUserId . ': ' . $e->getMessage());
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data. Silakan coba lagi.',
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
        }

        // Catat Audit Trail aktivitas penetapan / pembaruan target
        $targetUserName = $targetUser['nama_lengkap'] ?? "User #{$targetUserId}";
        $actionType = $isEditingStaf ? 'UPDATE_TARGET_STAF' : ($isDraft ? 'DRAFT_TARGET' : 'SUBMIT_TARGET');

        log_audit($actionType, 'target_kinerja_bulanan', $targetUserId, !empty($oldTargetValues) ? $oldTargetValues : null, [
            'bulan'                  => $bulan,
            'tahun'                  => $tahun,
            'target_staf'            => $targetUserName,
            'jumlah_target'          => count($dataToUpdate) + count($dataToInsert),
            'status'                 => $targetStatus,
            'is_draft'               => $isDraft ? 1 : 0,
            'disimpan_oleh'          => session()->get('nama_lengkap'),
            'updated_rows'           => $dataToUpdate,
            'inserted_rows'          => $dataToInsert,
            'reset_score_target_ids' => !empty($resetScoreTargetIds) ? $resetScoreTargetIds : null
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
            try {
                send_notification(
                    $targetUserId,
                    'Target Kinerja Diperbarui Atasan',
                    "Target Kinerja Bulanan Anda ({$namaBulan} {$tahun}) telah diperbarui dan disetujui oleh {$currentUserName}.",
                    site_url('laporan-harian?bulan=' . $bulan . '&tahun=' . $tahun)
                );
            } catch (\Throwable $e) {
                log_message('error', 'Gagal mengirim notifikasi target ke staf #' . $targetUserId . ': ' . $e->getMessage());
            }
        } elseif (!$isEditingStaf && !$isDraft && !$isTargetDirektur) {
            if (!empty($targetUser['atasan_id'])) {
                try {
                    $hadSubmittedBefore = false;
                    if (!empty($oldTargetValues)) {
                        foreach ($oldTargetValues as $ot) {
                            if (($ot['status'] ?? '') === 'terkirim') {
                                $hadSubmittedBefore = true;
                                break;
                            }
                        }
                    }

                    $notifJudul = $hadSubmittedBefore ? 'Pembaruan Target Bulanan' : 'Persetujuan Target Bulanan';
                    $notifPesan = $hadSubmittedBefore
                        ? ($targetUser['nama_lengkap'] ?? 'Staf') . " memperbarui Target Bulanan ({$namaBulan} {$tahun}) untuk diperiksa."
                        : ($targetUser['nama_lengkap'] ?? 'Staf') . " mengirimkan Target Bulanan ({$namaBulan} {$tahun}) untuk diperiksa.";

                    send_notification(
                        $targetUser['atasan_id'], 
                        $notifJudul, 
                        $notifPesan,
                        site_url('laporan-harian?source_tab=staf&staf_id=' . $targetUserId . '&bulan=' . $bulan . '&tahun=' . $tahun)
                    );
                } catch (\Throwable $e) {
                    log_message('error', 'Gagal mengirim notifikasi target ke atasan #' . $targetUser['atasan_id'] . ': ' . $e->getMessage());
                }
            }
        }

        $successMsg = $isDraft 
            ? 'Target Bulanan berhasil disimpan sementara.' 
            : ($isEditingStaf 
                ? 'Target Bulanan staf berhasil diperbarui dan disetujui.' 
                : ($isTargetDirektur 
                    ? 'Target Bulanan berhasil disimpan dan disetujui.' 
                    : (!empty($hadSubmittedBefore) ? 'Target Bulanan berhasil diperbarui dan diajukan kembali ke atasan langsung.' : 'Target Bulanan berhasil diajukan ke atasan langsung.')));

        $redirectUrl = $isEditingStaf
            ? site_url('laporan-harian?source_tab=staf&staf_id=' . $targetUserId . '&bulan=' . $bulan . '&tahun=' . $tahun)
            : site_url('laporan-harian?bulan=' . $bulan . '&tahun=' . $tahun);

        return redirect()->to($redirectUrl)
                         ->with('success', $successMsg);
    }
    
    public function approve()
    {
        $id = (int)$this->request->getPost('id');
        $currentUserId = (int)(session()->get('id') ?? session()->get('user_id'));
        $userModel = new User();
        $currentUser = $userModel->select('id, nama_lengkap, nip, username, role, unit, atasan_id')->find($currentUserId);

        if (!$currentUser || !$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak valid atau sesi telah berakhir.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $laporanModel = new TargetKinerja();
        $laporan = $laporanModel->find($id);
        if (!$laporan) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data target tidak ditemukan.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $targetUser = $userModel->select('id, nama_lengkap, nip, username, role, unit, atasan_id')->find($laporan['user_id']);
        if (!$targetUser) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pengguna pemilik target tidak ditemukan.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        if (!PenilaianKinerjaPolicy::canApproveTarget($currentUser, $targetUser, (int)($laporan['bulan'] ?? null), (int)($laporan['tahun'] ?? null))) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki izin menyetujui target ini.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        // TKB-03 & TKB-04: Idempotensi, validasi status dan kelengkapan
        if ($laporan['status_approval'] === 'disetujui') {
            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Target sudah disetujui sebelumnya.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        if ($laporan['status'] !== 'terkirim') {
            return $this->response->setJSON([
                'success'   => false,
                'message'   => 'Target belum diajukan oleh pegawai.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        if (!$laporanModel->isComplete($laporan)) {
            return $this->response->setJSON([
                'success'   => false,
                'message'   => 'Data target belum lengkap untuk disetujui.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $laporanModel->update($id, [
                'status_approval' => 'disetujui',
                'status'          => 'terkirim',
                'approved_by'     => $currentUserId,
                'approved_at'     => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success'   => false,
                    'message'   => 'Gagal menyetujui target. Silakan coba lagi.',
                    'csrf_hash' => csrf_hash()
                ]);
            }
        } catch (\Throwable $e) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', 'Error approving target: ' . $e->getMessage());
            return $this->response->setJSON([
                'success'   => false,
                'message'   => 'Gagal menyetujui target. Silakan coba lagi.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        log_audit('APPROVE', 'target_kinerja_bulanan', $id, [
            'status_approval' => $laporan['status_approval'],
            'status'          => $laporan['status'],
            'sasaran_program' => $laporan['sasaran_program']
        ], [
            'status_approval' => 'disetujui',
            'status'          => 'terkirim',
            'approved_by'     => session()->get('nama_lengkap'),
            'approver_id'     => $currentUserId,
            'bulan'           => $laporan['bulan'],
            'tahun'           => $laporan['tahun'],
            'target_user'     => $targetUser['nama_lengkap'] ?? "User #{$laporan['user_id']}"
        ]);

        // Kirim notifikasi ke staf bahwa targetnya telah disetujui (jika bukan akun sendiri)
        if ($laporan['user_id'] != $currentUserId) {
            try {
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
            } catch (\Throwable $e) {
                log_message('error', 'Gagal mengirim notifikasi target: ' . $e->getMessage());
            }
        }

        return $this->response->setJSON([
            'success'   => true,
            'message'   => 'Target berhasil disetujui.',
            'csrf_hash' => csrf_hash()
        ]);
    }
    
    public function approveAll()
    {
        $staf_id = (int)$this->request->getPost('staf_id');
        $bulan = (int)$this->request->getPost('bulan');
        $tahun = (int)$this->request->getPost('tahun');
        $currentUserId = (int)(session()->get('id') ?? session()->get('user_id'));

        $userModel = new User();
        $currentUser = $userModel->select('id, nama_lengkap, nip, username, role, unit, atasan_id')->find($currentUserId);

        if (!$currentUser || !$staf_id || !$bulan || !$tahun) {
            return redirect()->back()->with('error', 'Parameter tidak lengkap atau sesi telah berakhir.');
        }

        $targetUser = $userModel->select('id, nama_lengkap, nip, username, role, unit, atasan_id')->find($staf_id);
        if (!$targetUser) {
            return redirect()->back()->with('error', 'Pegawai target tidak ditemukan.');
        }

        if (!PenilaianKinerjaPolicy::canApproveTarget($currentUser, $targetUser, (int)$bulan, (int)$tahun)) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki izin menyetujui target staf ini.');
        }

        $laporanModel = new TargetKinerja();
        $targetsToApprove = $laporanModel->getPendingApprovalTargets($staf_id, $bulan, $tahun);

        if (empty($targetsToApprove)) {
            return redirect()->back()->with('error', 'Tidak ada target kinerja berstatus diajukan yang menunggu persetujuan pada periode ini.');
        }

        // TKB-03: Validasi kelengkapan semua target yang akan disetujui
        foreach ($targetsToApprove as $tRow) {
            if (!$laporanModel->isComplete($tRow)) {
                return redirect()->back()->with('error', 'Terdapat target yang belum lengkap. Pastikan seluruh sasaran, indikator, target, dan satuan telah terisi.');
            }
        }

        $targetIdsToApprove = array_column($targetsToApprove, 'id');
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $laporanModel->whereIn('id', $targetIdsToApprove)
                         ->set([
                             'status_approval' => 'disetujui',
                             'approved_by'     => $currentUserId,
                             'approved_at'     => date('Y-m-d H:i:s'),
                         ])
                         ->update();

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', "approveAll transaction failed for staf_id: {$staf_id}, periode: {$bulan}/{$tahun}");
                return redirect()->back()->with('error', 'Gagal menyetujui target staf. Silakan coba lagi.');
            }
        } catch (\Throwable $e) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', "approveAll exception: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyetujui target staf.');
        }

        $oldSnapshots = [];
        foreach ($targetsToApprove as $tRow) {
            $oldSnapshots[] = [
                'id'              => $tRow['id'],
                'sasaran_program' => $tRow['sasaran_program'],
                'status_approval' => $tRow['status_approval'],
                'status'          => $tRow['status']
            ];
        }

        log_audit('APPROVE_ALL', 'target_kinerja_bulanan', $staf_id, $oldSnapshots, [
            'bulan'            => $bulan,
            'tahun'            => $tahun,
            'staf'             => $targetUser['nama_lengkap'] ?? "User #{$staf_id}",
            'target_ids'       => $targetIdsToApprove,
            'jumlah_disetujui' => count($targetsToApprove),
            'approved_by'      => session()->get('nama_lengkap'),
            'approver_id'      => $currentUserId
        ]);
        
        try {
            helper('notification');
            $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $namaBulan = $bulanIndo[(int)$bulan - 1] ?? "Bulan {$bulan}";
            $atasanName = session()->get('nama_lengkap') ?? 'Atasan Langsung';

            send_notification(
                $staf_id,
                'Target Kinerja Disetujui',
                "Seluruh Target Kinerja Bulanan Anda (" . count($targetsToApprove) . " target pada {$namaBulan} {$tahun}) telah disetujui oleh {$atasanName}.",
                site_url('laporan-harian?bulan=' . $bulan . '&tahun=' . $tahun)
            );
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mengirim notifikasi approveAll staf #' . $staf_id . ': ' . $e->getMessage());
        }

        return redirect()->to(site_url('laporan-harian?source_tab=staf&staf_id=' . $staf_id . '&bulan=' . $bulan . '&tahun=' . $tahun))
                         ->with('success', 'Semua target milik staf berhasil disetujui (' . count($targetsToApprove) . ' target).');
    }

    public function hapus()
    {
        $id = (int)$this->request->getPost('id');
        $currentUserId = (int)(session()->get('id') ?? session()->get('user_id'));
        $userModel = new User();
        $currentUser = $userModel->select('id, nama_lengkap, nip, username, role, unit, atasan_id')->find($currentUserId);

        if (!$currentUser || !$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak valid atau sesi telah berakhir.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $laporanModel = new TargetKinerja();
        $laporan = $laporanModel->find($id);
        if (!$laporan) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Data target tidak ditemukan.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $targetUser = $userModel->select('id, nama_lengkap, nip, username, role, unit, status_pegawai, tmt_selesai, dispensasi_lampau_sampai, atasan_id')->find($laporan['user_id']);
        if (!$targetUser) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Pemilik target tidak ditemukan.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        if (!PenilaianKinerjaPolicy::canDeleteTarget($currentUser, $targetUser, $laporan)) {
            $msg = (($laporan['status_approval'] ?? '') === 'disetujui')
                ? 'Terkunci. Target sudah disetujui oleh atasan.'
                : 'Akses ditolak. Anda tidak memiliki izin menghapus target ini.';
            return $this->response->setJSON([
                'success' => false,
                'message' => $msg,
                'csrf_hash' => csrf_hash()
            ]);
        }

        // K-07: Batasi penghapusan setelah tenggat kecuali memiliki izin revisi sah yang terverifikasi
        $isEditingStafHapus = ($currentUserId !== (int)$laporan['user_id']);
        $targetUserStatusHapus = $targetUser['status_pegawai'] ?? 'aktif';
        $hasDispensasiHapus = !empty($targetUser['dispensasi_lampau_sampai']) && $targetUser['dispensasi_lampau_sampai'] >= date('Y-m-d');

        if ($targetUserStatusHapus !== 'aktif' && !$hasDispensasiHapus) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akun dalam status arsip kepegawaian. Tidak dapat menghapus target kinerja.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $settingModelHapus = new SettingModel();
        $isDeadlineActiveHapus = $settingModelHapus->getValue('enable_target_deadline', '0') === '1';
        $batasTargetHapus = (int)$settingModelHapus->getValue('batas_input_target', 5);
        $isRevisionHapus = self::hasRevisionPermission((int)$laporan['user_id'], (int)$laporan['bulan'], (int)$laporan['tahun']);

        if ($isDeadlineActiveHapus && !hasRole('admin') && !hasRole('direktur') && !$isEditingStafHapus && !$isRevisionHapus && !$hasDispensasiHapus) {
            $now = new \DateTime();
            $targetMonth = new \DateTime(sprintf('%04d-%02d-01', (int)$laporan['tahun'], (int)$laporan['bulan']));
            $currentMonth = new \DateTime(date('Y-m-01'));
            $isLockedHapus = false;
            if ($targetMonth < $currentMonth) {
                $isLockedHapus = true;
            } elseif ($targetMonth == $currentMonth) {
                if ((int)$now->format('j') > $batasTargetHapus) {
                    $isLockedHapus = true;
                }
            }
            if ($isLockedHapus) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Batas waktu pengisian target kinerja untuk periode ini telah berakhir.',
                    'csrf_hash' => csrf_hash()
                ]);
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $logModel = new LogKegiatanHarian();
            $affectedLogs = $logModel->where('user_id', $targetUser['id'])
                                     ->where('target_id', $id)
                                     ->countAllResults();
            if ($affectedLogs > 0) {
                // Lepaskan target_id dan kembalikan status laporan harian ke 'draft'
                $logModel->where('user_id', $targetUser['id'])
                         ->where('target_id', $id)
                         ->set([
                             'target_id' => null,
                             'status'    => 'draft'
                         ])
                         ->update();
            }

            $laporanModel->where('user_id', $targetUser['id'])->where('id', $id)->delete();
            log_audit('DELETE', 'target_kinerja_bulanan', $id, $laporan, [
                'affected_logs_reset_to_draft' => $affectedLogs
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', "hapus target failed for target_id: {$id}");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus data target.',
                    'csrf_hash' => csrf_hash()
                ]);
            }
        } catch (\Throwable $e) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', 'hapus target exception: ' . $e->getMessage() . ' for target_id: ' . $id);
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

    /**
     * [SUPERADMIN ONLY] Membatalkan persetujuan Target Bulanan pegawai
     * agar pegawai dapat merevisi target dan mengajukannya kembali ke atasan langsung.
     */
    public function cancelApprove()
    {
        $currentUserId = (int)(session()->get('id') ?? session()->get('user_id'));
        $userModel = new User();
        $currentUser = $userModel->select('id, nama_lengkap, nip, username, role, unit, atasan_id')->find($currentUserId);

        if (!$currentUser || !PenilaianKinerjaPolicy::canCancelTargetApproval($currentUser)) {
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
            // Snapshot data sebelum reset untuk audit trail lengkap
            $oldTargetSnapshots = [];
            foreach ($existingTargets as $t) {
                $oldTargetSnapshots[] = [
                    'id'                => $t['id'],
                    'sasaran_program'   => $t['sasaran_program'],
                    'indikator_kinerja' => $t['indikator_kinerja'],
                    'target_bulanan'    => $t['target_bulanan'],
                    'satuan'            => $t['satuan'],
                    'status_approval'   => $t['status_approval'],
                    'status'            => $t['status'],
                    'nilai_capaian'     => $t['nilai_capaian'],
                    'status_penilaian'  => $t['status_penilaian']
                ];
            }

            $startDate = sprintf('%04d-%02d-01', (int)$tahun, (int)$bulan);
            $endDate   = date('Y-m-t', strtotime($startDate));
            $logTambahanModel = new \App\Models\LogTugasTambahan();
            $existingTambahan = $logTambahanModel->where('user_id', $stafId)
                                                 ->where('tanggal_kegiatan >=', $startDate)
                                                 ->where('tanggal_kegiatan <=', $endDate)
                                                 ->findAll();
            $oldTambahanSnapshots = [];
            foreach ($existingTambahan as $tt) {
                $oldTambahanSnapshots[] = [
                    'id'                 => $tt['id'],
                    'deskripsi_kegiatan' => $tt['deskripsi_kegiatan'],
                    'nilai_capaian'      => $tt['nilai_capaian'],
                    'status_penilaian'   => $tt['status_penilaian']
                ];
            }

            // Revert status_approval ke 'menunggu_persetujuan', status ke 'draft', dan reset nilai penilaian (RPK-07 & K-04)
            $laporanModel->where('user_id', $stafId)
                         ->where('bulan', $bulan)
                         ->where('tahun', $tahun)
                         ->set([
                              'status_approval'  => 'menunggu_persetujuan',
                              'status'           => 'draft',
                              'nilai_capaian'    => null,
                              'status_penilaian' => null,
                              'approved_by'      => null,
                              'approved_at'      => null,
                              'penilai_id'       => null,
                              'penilaian_at'     => null,
                         ])
                         ->update();

            // Reset nilai penilaian tugas tambahan pada periode bulan yang sama
            $logTambahanModel->where('user_id', $stafId)
                             ->where('tanggal_kegiatan >=', $startDate)
                             ->where('tanggal_kegiatan <=', $endDate)
                             ->set([
                                 'nilai_capaian'    => null,
                                 'status_penilaian' => null,
                                 'penilai_id'       => null,
                                 'penilaian_at'     => null,
                             ])
                             ->update();

            // Cascade: Revert semua Log Kegiatan Harian terkait yang berstatus 'terkirim' kembali ke 'draft'
            $targetIds = array_column($existingTargets, 'id');
            $affectedLogs = 0;
            if (!empty($targetIds)) {
                $logModel = new LogKegiatanHarian();
                $affectedLogs = $logModel->where('user_id', $stafId)
                                         ->whereIn('target_id', $targetIds)
                                         ->where('status', 'terkirim')
                                         ->countAllResults();
                if ($affectedLogs > 0) {
                    $logModel->where('user_id', $stafId)
                             ->whereIn('target_id', $targetIds)
                             ->where('status', 'terkirim')
                             ->set(['status' => 'draft'])
                             ->update();
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', "cancelApprove transaction failed for stafId: {$stafId}, {$bulan}/{$tahun}");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membatalkan persetujuan target. Silakan coba lagi.',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // Audit log dengan old values dan new values lengkap
            log_audit('CANCEL_APPROVE_TARGET', 'target_kinerja_bulanan', $stafId, [
                'targets'        => $oldTargetSnapshots,
                'tugas_tambahan' => $oldTambahanSnapshots
            ], [
                'bulan'                  => $bulan,
                'tahun'                  => $tahun,
                'staf'                   => $targetUser['nama_lengkap'],
                'target_count'           => count($existingTargets),
                'affected_logs_to_draft' => $affectedLogs,
                'reset_nilai_count'      => count($oldTargetSnapshots) + count($oldTambahanSnapshots),
                'dibatal_oleh'           => session()->get('nama_lengkap')
            ]);

            // Kirim notifikasi ke staf
            helper(['notification']);
            if (function_exists('send_notification')) {
                try {
                    $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $namaBulan = $bulanIndo[(int)$bulan - 1] ?? $bulan;
                    $logInfo = ($affectedLogs > 0) ? " Sebanyak {$affectedLogs} catatan log kegiatan harian juga telah dikembalikan ke status draf untuk disesuaikan." : "";

                    send_notification(
                        $stafId,
                        'Persetujuan Target Dibatalkan',
                        "Persetujuan Target Bulanan ({$namaBulan} {$tahun}) Anda telah dibatalkan oleh Superadmin.{$logInfo} Silakan lakukan revisi dan ajukan kembali ke atasan langsung.",
                        site_url('laporan-harian?bulan=' . $bulan . '&tahun=' . $tahun)
                    );
                } catch (\Throwable $e) {
                    log_message('error', '[LaporanHarianController::cancelApprove:notification] ' . $e->getMessage() . ' | Staf: ' . $stafId);
                }
            }

            $msg = "Persetujuan Target Bulanan {$targetUser['nama_lengkap']} berhasil dibatalkan. Target kini berstatus draf.";
            if ($affectedLogs > 0) {
                $msg .= " Sebanyak {$affectedLogs} log kegiatan terkait dikembalikan ke status draf.";
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $msg,
                'affected_logs' => $affectedLogs,
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            try { @$db->transRollback(); } catch (\Throwable $t) {}
            log_message('error', "cancelApprove exception: " . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal membatalkan persetujuan target. Silakan coba lagi.',
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
        $currentUserId = (int)(session()->get('id') ?? session()->get('user_id'));
        $userModel = new User();
        $currentUser = $userModel->select('id, nama_lengkap, nip, username, role, unit, atasan_id')->find($currentUserId);

        $bulanSumber = (int)$this->request->getVar('bulan');
        $tahunSumber = (int)$this->request->getVar('tahun');
        $stafId = $this->request->getVar('staf_id');

        $targetUserId = (!empty($stafId) && is_numeric($stafId)) ? (int)$stafId : $currentUserId;

        if (!$currentUser) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Sesi tidak valid.',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(401);
        }

        $targetUser = ($targetUserId === $currentUserId) ? $currentUser : $userModel->select('id, nama_lengkap, nip, username, role, unit, atasan_id')->find($targetUserId);
        if (!$targetUser) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pengguna target tidak ditemukan.',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(404);
        }

        if (!PenilaianKinerjaPolicy::canCopyTargetSource($currentUser, $targetUser)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak: Anda tidak memiliki wewenang untuk mengambil data target staf ini.',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(403);
        }

        if (!$bulanSumber || !$tahunSumber || $bulanSumber < 1 || $bulanSumber > 12 || $tahunSumber < 2020 || $tahunSumber > 2099) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pilih bulan dan tahun sumber yang valid.',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(400);
        }

        $laporanModel = new TargetKinerja();
        $targets = $laporanModel->select('sasaran_program, indikator_kinerja, target_bulanan, satuan')
                                ->where('user_id', $targetUserId)
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


