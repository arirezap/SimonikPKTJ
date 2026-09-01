<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\LogKegiatanHarian;

class NotificationController extends BaseController
{
    public function fetch()
    {
        try {
            if (!session()->get('isLoggedIn')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Not logged in']);
            }
            
            // Auto-sync hari libur di background saat user pertama kali login di tahun berjalan
            $year = date('Y');
            if (!session()->get("holidays_synced_{$year}")) {
                $holidayModel = new \App\Models\HolidayModel();
                $count = $holidayModel->like('holiday_date', $year . '-', 'after')->countAllResults();
                if ($count == 0) {
                    // Auto-sync dari API
                    $client = \Config\Services::curlrequest();
                    $endpoints = [
                        "https://api-hari-libur.vercel.app/api?year={$year}",
                        "https://raw.githubusercontent.com/kalender-indonesia/hari-libur/main/data/{$year}.json",
                        "https://libur.deno.dev/api?year={$year}"
                    ];

                    foreach ($endpoints as $url) {
                        try {
                            $response = $client->request('GET', $url, [
                                'timeout' => 3,
                                'headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) EvidenceCommandCenter/1.0']
                            ]);
                            if ($response->getStatusCode() === 200) {
                                $holidays = json_decode($response->getBody(), true);
                                if (is_array($holidays) && !empty($holidays)) {
                                    foreach ($holidays as $h) {
                                        $hDate = $h['holiday_date'] ?? $h['date'] ?? null;
                                        $hName = $h['holiday_name'] ?? $h['name'] ?? null;
                                        $isNational = (isset($h['is_national_holiday']) && $h['is_national_holiday']) ? 1 : 0;
                                        if ($hDate && $hName && !$holidayModel->where('holiday_date', $hDate)->first()) {
                                            $holidayModel->insert([
                                                'holiday_date' => $hDate,
                                                'holiday_name' => $hName,
                                                'is_national'  => $isNational
                                            ]);
                                        }
                                    }
                                    break; // Berhasil, keluar dari loop endpoint
                                }
                            }
                        } catch (\Exception $e) {
                            // Silently try next fallback endpoint
                        }
                    }
                }
                session()->set("holidays_synced_{$year}", true);
            }

            helper('notification');
            $userId = session()->get('id') ?? session()->get('user_id');
            
            // Failsafe: Jika sesi legacy menyimpan NIP / Username di session id
            if (!is_numeric($userId) || strlen((string)$userId) > 10) {
                $userModel = new \App\Models\User();
                $userDb = $userModel->where('username', $userId)
                                    ->orWhere('nip', $userId)
                                    ->first();
                if ($userDb) {
                    $userId = $userDb['id'];
                }
            }
            
            $dbNotifications = get_user_notifications($userId, 20);
            if (!is_array($dbNotifications)) {
                $dbNotifications = [];
            }

            $unreadDbCount = count_unread_notifications($userId);
            
            // Cek pengingat harian (Virtual Notification)
            $virtualNotifications = [];
            if (is_working_day()) {
                $logModel = new LogKegiatanHarian();
                $today = date('Y-m-d');
                
                // Cek apakah user sudah isi laporan hari ini
                $hasLog = $logModel->where('user_id', $userId)
                                   ->where('tanggal_kegiatan', $today)
                                   ->first();
                                   
                if (!$hasLog) {
                    $virtualNotifications[] = [
                        'id' => 'virtual_reminder',
                        'title' => 'Pengingat Laporan Harian',
                        'message' => 'Anda belum mengisi laporan kegiatan harian untuk hari ini.',
                        'link' => site_url('log-kegiatan'),
                        'is_read' => 0,
                        'time_ago' => 'Hari ini',
                        'created_at' => date('Y-m-d H:i:s'),
                        'is_virtual' => true
                    ];
                }
            }

            // SMART EARLY MONTH & DEADLINE REMINDERS
            $settingModel = new \App\Models\SettingModel();
            $currentDay = (int) date('j');
            $currentMonth = (int) date('n');
            $currentYear = (int) date('Y');
            
            helper('tanggal');
            $namaBulanIni = function_exists('bulan_indo') ? bulan_indo($currentMonth) : date('F');

            // 1. PENGINGAT AWAL BULAN & BATAS TARGET KINERJA BULANAN
            $userRole = session()->get('role') ?? 'user';
            // Hanya aktif untuk pengguna yang memiliki kewajiban mengisi target
            if (!in_array($userRole, ['spm'])) {
                $laporanModel = new \App\Models\LaporanHarian();
                $targetList = $laporanModel->where('user_id', $userId)
                                           ->where('bulan', $currentMonth)
                                           ->where('tahun', $currentYear)
                                           ->findAll();

                $isTargetDeadlineActive = $settingModel->getValue('enable_target_deadline', '0') === '1';
                $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);

                if (empty($targetList)) {
                    // Kasus 1: Belum ada target yang dibuat sama sekali untuk bulan berjalan
                    if ($isTargetDeadlineActive && $currentDay >= max(1, $batasTarget - 2) && $currentDay <= $batasTarget) {
                        $sisaHari = $batasTarget - $currentDay;
                        $pesanTarget = ($sisaHari == 0) 
                            ? "Hari ini adalah batas akhir pengisian target kinerja bulanan periode {$namaBulanIni} {$currentYear} (Maksimal tanggal {$batasTarget})." 
                            : "Batas pengisian target kinerja bulanan {$namaBulanIni} {$currentYear} tersisa {$sisaHari} hari lagi (Maksimal tanggal {$batasTarget}).";
                        $timeAgoLabel = 'Mendesak';
                    } else {
                        $pesanTarget = "Periode {$namaBulanIni} {$currentYear} telah dimulai. Anda belum menyusun Target Kinerja Bulanan. Silakan susun dan ajukan ke atasan langsung.";
                        $timeAgoLabel = ($currentDay <= 5) ? 'Awal Bulan' : 'Penting';
                    }

                    // Tampilkan pengingat di awal bulan (tanggal 1 s/d 10) atau jika saklar deadline aktif
                    if ($currentDay <= 10 || $isTargetDeadlineActive) {
                        $virtualNotifications[] = [
                            'id' => 'virtual_target_awal_bulan',
                            'title' => "Pengingat Target Kinerja {$namaBulanIni}",
                            'message' => $pesanTarget,
                            'link' => site_url('laporan-harian'),
                            'is_read' => 0,
                            'time_ago' => $timeAgoLabel,
                            'created_at' => date('Y-m-d H:i:s'),
                            'is_virtual' => true
                        ];
                    }
                } else {
                    // Kasus 2: Target ada, tetapi masih berstatus Draf (belum diajukan ke atasan)
                    $hasOnlyDraft = true;
                    foreach ($targetList as $t) {
                        if (($t['status'] ?? '') === 'terkirim' || ($t['status_approval'] ?? '') === 'disetujui') {
                            $hasOnlyDraft = false;
                            break;
                        }
                    }
                    if ($hasOnlyDraft && $currentDay <= 10) {
                        $virtualNotifications[] = [
                            'id' => 'virtual_target_draft_reminder',
                            'title' => "Target Kinerja {$namaBulanIni} Masih Draf",
                            'message' => "Target Kinerja Bulanan Anda untuk periode {$namaBulanIni} {$currentYear} masih berstatus Draf. Jangan lupa untuk mengajukannya ke atasan langsung.",
                            'link' => site_url('laporan-harian'),
                            'is_read' => 0,
                            'time_ago' => 'Perlu Dikirim',
                            'created_at' => date('Y-m-d H:i:s'),
                            'is_virtual' => true
                        ];
                    }
                }
            }

            // 2. PENGINGAT ATASAN LANGSUNG: Persetujuan Target Staf Menunggu
            $userModel = new \App\Models\User();
            $daftarStaf = $userModel->getStaf($userId);
            if (!empty($daftarStaf)) {
                $stafIds = array_column($daftarStaf, 'id');
                $laporanModel = new \App\Models\LaporanHarian();
                $pendingTargets = $laporanModel->whereIn('user_id', $stafIds)
                                               ->where('bulan', $currentMonth)
                                               ->where('tahun', $currentYear)
                                               ->where('status', 'terkirim')
                                               ->where('status_approval', 'menunggu_persetujuan')
                                               ->findAll();

                if (!empty($pendingTargets)) {
                    $stafPendingIds = array_unique(array_column($pendingTargets, 'user_id'));
                    $totalStafPending = count($stafPendingIds);

                    $virtualNotifications[] = [
                        'id' => 'virtual_target_approval_needed',
                        'title' => 'Persetujuan Target Staf Menunggu',
                        'message' => "Terdapat {$totalStafPending} staf yang telah mengajukan Target Kinerja Bulanan ({$namaBulanIni} {$currentYear}) dan menunggu persetujuan Anda.",
                        'link' => site_url('laporan-harian?source_tab=staf'),
                        'is_read' => 0,
                        'time_ago' => 'Perlu Tindakan',
                        'created_at' => date('Y-m-d H:i:s'),
                        'is_virtual' => true
                    ];
                }
            }

            // 3. PENGINGAT AWAL BULAN: Penilaian Kinerja Staf Periode Bulan Kemarin
            if (!empty($daftarStaf)) {
                $bulanLalu = ($currentMonth == 1) ? 12 : ($currentMonth - 1);
                $tahunBulanLalu = ($currentMonth == 1) ? ($currentYear - 1) : $currentYear;
                $namaBulanLalu = function_exists('bulan_indo') ? bulan_indo($bulanLalu) : date('F', mktime(0, 0, 0, $bulanLalu, 10));

                // Periksa staf bawahan yang belum selesai dinilai / diterbitkan nilainya untuk periode bulan kemarin
                $stafIds = array_column($daftarStaf, 'id');
                $laporanModel = new \App\Models\LaporanHarian();
                $targetBulanLalu = $laporanModel->whereIn('user_id', $stafIds)
                                               ->where('bulan', $bulanLalu)
                                               ->where('tahun', $tahunBulanLalu)
                                               ->findAll();

                $stafPerluDinilai = [];
                foreach ($targetBulanLalu as $t) {
                    if (($t['status_penilaian'] ?? '') !== 'terbit') {
                        $stafPerluDinilai[$t['user_id']] = true;
                    }
                }
                $totalStafPerluDinilai = count($stafPerluDinilai);

                if ($totalStafPerluDinilai > 0) {
                    $isPenilaianDeadlineActive = $settingModel->getValue('enable_penilaian_deadline', '0') === '1';
                    $batasPenilaian = (int) $settingModel->getValue('batas_penilaian_kinerja', 10);

                    if ($isPenilaianDeadlineActive && $currentDay >= max(1, $batasPenilaian - 2) && $currentDay <= $batasPenilaian) {
                        $sisaHariPenilaian = $batasPenilaian - $currentDay;
                        $pesanPenilaian = ($sisaHariPenilaian == 0)
                            ? "Hari ini adalah batas akhir penilaian kinerja staf periode {$namaBulanLalu} {$tahunBulanLalu} ({$totalStafPerluDinilai} staf belum dinilai, maksimal tanggal {$batasPenilaian})."
                            : "Batas penilaian kinerja staf periode {$namaBulanLalu} {$tahunBulanLalu} tersisa {$sisaHariPenilaian} hari lagi ({$totalStafPerluDinilai} staf belum dinilai).";
                        $timeAgoPenilaian = 'Mendesak';
                    } else {
                        $pesanPenilaian = "Bulan baru telah dimulai. Silakan lakukan Penilaian Kinerja staf untuk periode {$namaBulanLalu} {$tahunBulanLalu} ({$totalStafPerluDinilai} staf menunggu penilaian).";
                        $timeAgoPenilaian = ($currentDay <= 5) ? 'Awal Bulan' : 'Penting';
                    }

                    // Tampilkan notifikasi di awal bulan (tanggal 1 s/d 15) atau jika saklar batas waktu aktif
                    if ($currentDay <= 15 || $isPenilaianDeadlineActive) {
                        $virtualNotifications[] = [
                            'id' => 'virtual_penilaian_bulan_lalu',
                            'title' => "Pengingat Penilaian Kinerja {$namaBulanLalu}",
                            'message' => $pesanPenilaian,
                            'link' => site_url("penilaian-kinerja?bulan={$bulanLalu}&tahun={$tahunBulanLalu}&active_tab=staf"),
                            'is_read' => 0,
                            'time_ago' => $timeAgoPenilaian,
                            'created_at' => date('Y-m-d H:i:s'),
                            'is_virtual' => true
                        ];
                    }
                }
            }

            // Format waktu relatif untuk setiap notifikasi database
            $formattedDbNotifs = [];
            foreach ($dbNotifications as $n) {
                $n['is_virtual'] = false;
                $n['time_ago'] = format_notif_time($n['created_at']);
                $formattedDbNotifs[] = $n;
            }

            $allNotifications = array_merge($virtualNotifications, $formattedDbNotifs);
            $totalUnreadCount = $unreadDbCount + count($virtualNotifications);

            return $this->response->setJSON([
                'status'       => 'success',
                'unread_count' => $totalUnreadCount,
                'count'        => $totalUnreadCount,
                'total_count'  => count($allNotifications),
                'data'         => $allNotifications
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[Notification Fetch Error] ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server saat memuat notifikasi.'
            ]);
        }
    }
    
    public function markAsRead($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error']);
        }
        
        if (!str_starts_with((string)$id, 'virtual_') && is_numeric($id)) {
            $notifModel = new NotificationModel();
            $notifModel->update($id, ['is_read' => 1]);
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'csrf_hash' => csrf_hash()
        ]);
    }

    public function markAllAsRead()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Not logged in']);
        }
        
        $userId = session()->get('id') ?? session()->get('user_id');
        if (!is_numeric($userId) || strlen((string)$userId) > 10) {
            $userModel = new \App\Models\User();
            $userDb = $userModel->where('username', $userId)->orWhere('nip', $userId)->first();
            if ($userDb) {
                $userId = $userDb['id'];
            }
        }

        if ($userId) {
            $notifModel = new NotificationModel();
            $notifModel->where('user_id', $userId)
                       ->where('is_read', 0)
                       ->set(['is_read' => 1])
                       ->update();
        }

        return $this->response->setJSON([
            'status' => 'success',
            'csrf_hash' => csrf_hash()
        ]);
    }
}
