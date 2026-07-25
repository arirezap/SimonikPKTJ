<?php

use App\Models\NotificationModel;
use App\Models\HolidayModel;

if (!function_exists('send_notification')) {
    /**
     * Kirim Notifikasi ke User
     */
    function send_notification($user_id, $title, $message, $link = null)
    {
        $notifModel = new NotificationModel();
        return $notifModel->insert([
            'user_id'    => $user_id,
            'title'      => $title,
            'message'    => $message,
            'link'       => $link,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}

if (!function_exists('get_unread_notifications')) {
    /**
     * Ambil notifikasi belum dibaca untuk User
     */
    function get_unread_notifications($user_id, $limit = 10)
    {
        $notifModel = new NotificationModel();
        return $notifModel->where('user_id', $user_id)
                          ->where('is_read', 0)
                          ->orderBy('created_at', 'DESC')
                          ->findAll($limit);
    }
}

if (!function_exists('is_working_day')) {
    /**
     * Cek apakah tanggal hari ini adalah hari kerja
     * Mengecek akhir pekan dan hari libur nasional di tabel holidays
     */
    function is_working_day($date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        $timestamp = strtotime($date);
        
        // 1. Cek Akhir Pekan (6 = Sabtu, 7 = Minggu)
        $dayOfWeek = date('N', $timestamp);
        if ($dayOfWeek >= 6) {
            return false;
        }

        // 2. Cek Tanggal Merah (Holidays table)
        $holidayModel = new HolidayModel();
        $isHoliday = $holidayModel->where('holiday_date', $date)->first();
        if ($isHoliday) {
            return false;
        }

        return true;
    }
}
