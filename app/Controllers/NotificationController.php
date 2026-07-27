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
                    try {
                        $response = $client->request('GET', "https://libur.deno.dev/api?year={$year}", ['timeout' => 3]);
                        if ($response->getStatusCode() === 200) {
                            $holidays = json_decode($response->getBody(), true);
                            if (is_array($holidays)) {
                                foreach ($holidays as $h) {
                                    if (!$holidayModel->where('holiday_date', $h['date'])->first()) {
                                        $holidayModel->insert([
                                            'holiday_date' => $h['date'],
                                            'holiday_name' => $h['name'],
                                            'is_national'  => (isset($h['is_national_holiday']) && $h['is_national_holiday']) ? 1 : 0
                                        ]);
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // Silently fail so we don't break the notification fetch
                    }
                }
                session()->set("holidays_synced_{$year}", true);
            }

            helper('notification');
            $userId = session()->get('id');
            
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
                    
                    // Tambahkan di urutan paling atas
                    array_unshift($notifications, $reminder);
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
        
        if ($id !== 'virtual_reminder') {
            $notifModel = new NotificationModel();
            $notifModel->update($id, ['is_read' => 1]);
        }
        
        return $this->response->setJSON(['status' => 'success']);
    }
}
