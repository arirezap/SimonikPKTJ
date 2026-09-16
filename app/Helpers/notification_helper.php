<?php

use App\Models\NotificationModel;
use App\Models\HolidayModel;

if (!function_exists('normalize_notif_link')) {
    /**
     * Menormalisasi tautan notifikasi agar selalu merujuk ke baseURL lingkungan aktif (Localhost / Production)
     */
    function normalize_notif_link(?string $link): string
    {
        if (empty($link) || $link === '#' || str_starts_with(strtolower($link), 'javascript:')) {
            return '#';
        }

        if (preg_match('#^https?://#i', $link)) {
            $parts = parse_url($link);
            $path = ltrim($parts['path'] ?? '', '/');
            $query = isset($parts['query']) ? '?' . $parts['query'] : '';
            $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
            return site_url($path . $query . $fragment);
        }

        return site_url(ltrim($link, '/'));
    }
}

if (!function_exists('send_notification')) {
    /**
     * Kirim Notifikasi ke User
     * Tautan disimpan sebagai relative path agar database portabel antar server lokal dan produksi
     */
    function send_notification($user_id, $title, $message, $link = null)
    {
        if (!empty($link) && $link !== '#' && !str_starts_with(strtolower($link), 'javascript:')) {
            if (preg_match('#^https?://#i', $link)) {
                $parts = parse_url($link);
                $path = ltrim($parts['path'] ?? '', '/');
                $query = isset($parts['query']) ? '?' . $parts['query'] : '';
                $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
                $link = $path . $query . $fragment;
            } else {
                $link = ltrim($link, '/');
            }
        }

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

if (!function_exists('get_user_notifications')) {
    /**
     * Ambil daftar riwayat notifikasi terbaru user (baik belum dibaca maupun sudah dibaca)
     */
    function get_user_notifications($user_id, $limit = 20)
    {
        $notifModel = new NotificationModel();
        return $notifModel->where('user_id', $user_id)
                          ->orderBy('created_at', 'DESC')
                          ->findAll($limit);
    }
}

if (!function_exists('count_unread_notifications')) {
    /**
     * Hitung total notifikasi belum dibaca untuk User di database
     */
    function count_unread_notifications($user_id)
    {
        $notifModel = new NotificationModel();
        return $notifModel->where('user_id', $user_id)
                          ->where('is_read', 0)
                          ->countAllResults();
    }
}

if (!function_exists('format_notif_time')) {
    /**
     * Format waktu relatif notifikasi (Bahasa Indonesia)
     */
    function format_notif_time($datetime)
    {
        if (empty($datetime)) return '';
        $time = strtotime($datetime);
        $diff = time() - $time;
        
        if ($diff < 60) {
            return 'Baru saja';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' mnt lalu';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' jam lalu';
        } elseif ($diff < 172800) {
            return 'Kemarin ' . date('H:i', $time);
        } else {
            return date('d M Y, H:i', $time);
        }
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
