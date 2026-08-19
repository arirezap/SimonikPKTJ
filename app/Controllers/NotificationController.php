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
            
            $notifications = get_unread_notifications($userId);
            if (!is_array($notifications)) {
                $notifications = [];
            }
            
            // Cek pengingat harian (Virtual Notification)
            $reminder = null;
            if (is_working_day()) {
                $logModel = new LogKegiatanHarian();
                $today = date('Y-m-d');
                
                // Cek apakah user sudah isi laporan hari ini
                $hasLog = $logModel->where('user_id', $userId)
                                   ->where('tanggal_kegiatan', $today)
                                   ->first();
                                   
                if (!$hasLog) {
                    $reminder = [
                        'id' => 'virtual_reminder',
                        'title' => 'Pengingat Laporan Harian',
                        'message' => 'Anda belum mengisi laporan kegiatan harian untuk hari ini.',
                        'link' => site_url('log-kegiatan'),
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'is_virtual' => true
                    ];
                    
                    array_unshift($notifications, $reminder);
                }
            }

            // SMART DEADLINE REMINDERS (Hanya aktif jika saklar batas waktu di /settings = ON)
            $settingModel = new \App\Models\SettingModel();
            $currentDay = (int) date('j');
            $currentMonth = (int) date('n');
            $currentYear = (int) date('Y');

            // 1. Pengingat Batas Target Bulanan (H-2 hingga Hari H)
            $isTargetDeadlineActive = $settingModel->getValue('enable_target_deadline', '0') === '1';
            if ($isTargetDeadlineActive) {
                $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);
                if ($currentDay >= max(1, $batasTarget - 2) && $currentDay <= $batasTarget) {
                    $laporanModel = new \App\Models\LaporanHarian();
                    $hasTarget = $laporanModel->where('user_id', $userId)
                                              ->where('bulan', $currentMonth)
                                              ->where('tahun', $currentYear)
                                              ->first();
                    if (!$hasTarget) {
                        $sisaHari = $batasTarget - $currentDay;
                        $pesanTarget = ($sisaHari == 0) 
                            ? "Hari ini adalah batas akhir pengisian target kinerja bulanan (Tanggal {$batasTarget})." 
                            : "Batas pengisian target kinerja bulanan tersisa {$sisaHari} hari lagi (Maksimal tanggal {$batasTarget}).";

                        $notifications[] = [
                            'id' => 'virtual_target_deadline',
                            'title' => 'Pengingat Batas Target Bulanan',
                            'message' => $pesanTarget,
                            'link' => site_url('laporan-harian'),
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                            'is_virtual' => true
                        ];
                    }
                }
            }

            // 2. Pengingat Batas Penilaian Kinerja untuk Atasan (H-2 hingga Hari H)
            $isPenilaianDeadlineActive = $settingModel->getValue('enable_penilaian_deadline', '0') === '1';
            if ($isPenilaianDeadlineActive) {
                $userModel = new \App\Models\User();
                $daftarStaf = $userModel->getStaf($userId);
                if (!empty($daftarStaf)) {
                    $batasPenilaian = (int) $settingModel->getValue('batas_penilaian_kinerja', 10);
                    if ($currentDay >= max(1, $batasPenilaian - 2) && $currentDay <= $batasPenilaian) {
                        $sisaHariPenilaian = $batasPenilaian - $currentDay;
                        $pesanPenilaian = ($sisaHariPenilaian == 0)
                            ? "Hari ini adalah batas akhir penilaian kinerja staf untuk periode bulan lalu (Maksimal tanggal {$batasPenilaian})."
                            : "Batas penilaian kinerja staf bulan lalu tersisa {$sisaHariPenilaian} hari lagi (Maksimal tanggal {$batasPenilaian}).";

                        $notifications[] = [
                            'id' => 'virtual_penilaian_deadline',
                            'title' => 'Pengingat Penilaian Kinerja Staf',
                            'message' => $pesanPenilaian,
                            'link' => site_url('penilaian-kinerja'),
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                            'is_virtual' => true
                        ];
                    }
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'count' => count($notifications),
                'data' => $notifications
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
