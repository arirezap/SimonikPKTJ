<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class SettingsController extends BaseController
{
    private function ensureDefaultSettings(SettingModel $settingModel)
    {
        $defaultSettings = [
            'enable_target_deadline' => [
                'setting_name'  => 'Status Batas Waktu Target Bulanan',
                'setting_value' => '0',
                'description'   => 'Aktifkan (ON) untuk membatasi pengisian target bulanan hingga tanggal tertentu, atau Non-aktifkan (OFF) agar bebas diisi kapanpun.'
            ],
            'batas_input_target' => [
                'setting_name'  => 'Batas Tanggal Pengisian Target Bulanan',
                'setting_value' => '5',
                'description'   => 'Tanggal maksimal di bulan berjalan untuk menyusun dan mengirimkan target kinerja bulanan (Contoh: Tanggal 5).'
            ],
            'enable_monthly_log_deadline' => [
                'setting_name'  => 'Status Kunci Laporan Bulan Lalu',
                'setting_value' => '1',
                'description'   => 'Aktifkan (ON) untuk mengunci pengisian seluruh tanggal di bulan-bulan sebelumnya setelah melewati akhir bulan (plus toleransi hari), atau Non-aktifkan (OFF) agar bulan lalu tetap bebas diisi.'
            ],
            'toleransi_hari_bulan_lalu' => [
                'setting_name'  => 'Toleransi Hari Pengisian Bulan Lalu',
                'setting_value' => '0',
                'description'   => 'Jumlah hari toleransi tambahan setelah tanggal terakhir di bulan tersebut sebelum pengisian dikunci secara permanen (Contoh: 0 hari = terkunci tepat tanggal 1 bulan berikutnya).'
            ],
            'enable_log_deadline' => [
                'setting_name'  => 'Status Batas Waktu Harian Laporan',
                'setting_value' => '0',
                'description'   => 'Aktifkan (ON) untuk membatasi input laporan kegiatan harian dengan toleransi hari per tanggal kegiatan, atau Non-aktifkan (OFF) agar bebas diisi kapanpun.'
            ],
            'batas_input_log' => [
                'setting_name'  => 'Toleransi Hari Pelaporan Kegiatan Harian',
                'setting_value' => '3',
                'description'   => 'Jumlah toleransi hari maksimal setelah tanggal kegiatan untuk menginput laporan kegiatan harian (Contoh: 3 hari).'
            ],
            'enable_penilaian_deadline' => [
                'setting_name'  => 'Status Batas Waktu Penilaian Kinerja',
                'setting_value' => '0',
                'description'   => 'Aktifkan (ON) untuk membatasi penilaian kinerja oleh atasan hingga tanggal tertentu di bulan berikutnya, atau Non-aktifkan (OFF) agar bebas dinilai kapanpun.'
            ],
            'batas_penilaian_kinerja' => [
                'setting_name'  => 'Batas Tanggal Penilaian Kinerja oleh Atasan',
                'setting_value' => '10',
                'description'   => 'Tanggal maksimal di bulan berikutnya bagi atasan langsung untuk memberikan nilai capaian kinerja staf (Contoh: Tanggal 10).'
            ],
            'enable_maintenance_mode' => [
                'setting_name'  => 'Status Mode Pemeliharaan (Maintenance Mode)',
                'setting_value' => '0',
                'description'   => 'Aktifkan (ON) untuk mengalihkan seluruh pengguna selain Administrator ke halaman pemeliharaan sementara saat pembaruan berlangsung.'
            ],
            'maintenance_message' => [
                'setting_name'  => 'Pesan Pemeliharaan Sistem',
                'setting_value' => 'Sistem sedang melakukan sinkronisasi pembaruan performa dan peningkatan fitur terbaru. Layanan akan kembali normal dalam beberapa saat.',
                'description'   => 'Pesan kustom yang ditampilkan kepada pengguna saat mode pemeliharaan aktif.'
            ]
        ];

        foreach ($defaultSettings as $key => $data) {
            if (!$settingModel->find($key)) {
                $settingModel->insert([
                    'setting_key'   => $key,
                    'setting_name'  => $data['setting_name'],
                    'setting_value' => $data['setting_value'],
                    'description'   => $data['description'],
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function index()
    {
        if (!hasRole('admin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak. Hanya administrator yang dapat mengubah pengaturan sistem.');
        }

        $settingModel = new SettingModel();
        $this->ensureDefaultSettings($settingModel);
        
        $settingsMap = $settingModel->getAllSettings();

        $db = \Config\Database::connect();
        $dbName = $db->getDatabase();
        $tables = $db->listTables();

        $totalAuditLogs = $db->table('audit_logs')->countAllResults();
        $totalNotifications = $db->table('notifications')->countAllResults();

        $data = [
            'title'                      => 'Pengaturan Sistem',
            'settingsMap'                => $settingsMap,
            'isTargetDeadlineActive'     => ($settingsMap['enable_target_deadline']['setting_value'] ?? '0') === '1',
            'isMonthlyLogDeadlineActive' => ($settingsMap['enable_monthly_log_deadline']['setting_value'] ?? '1') === '1',
            'isLogDeadlineActive'        => ($settingsMap['enable_log_deadline']['setting_value'] ?? '0') === '1',
            'isPenilaianDeadlineActive'  => ($settingsMap['enable_penilaian_deadline']['setting_value'] ?? '0') === '1',
            'isMaintenanceActive'        => ($settingsMap['enable_maintenance_mode']['setting_value'] ?? '0') === '1',
            'maintenanceMessage'         => $settingsMap['maintenance_message']['setting_value'] ?? 'Sistem sedang melakukan sinkronisasi pembaruan performa dan peningkatan fitur terbaru. Layanan akan kembali normal dalam beberapa saat.',
            'databaseName'               => $dbName,
            'totalTables'                => count($tables),
            'totalAuditLogs'             => $totalAuditLogs,
            'totalNotifications'         => $totalNotifications,
        ];
        
        return view('admin/settings/index', $data);
    }

    public function store()
    {
        if (!hasRole('admin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $settingModel = new SettingModel();
        $this->ensureDefaultSettings($settingModel);
        
        // 1. Simpan status masing-masing saklar toggle (1 per 1)
        $toggles = [
            'enable_target_deadline'      => $this->request->getPost('enable_target_deadline') ? '1' : '0',
            'enable_monthly_log_deadline' => $this->request->getPost('enable_monthly_log_deadline') ? '1' : '0',
            'enable_log_deadline'         => $this->request->getPost('enable_log_deadline') ? '1' : '0',
            'enable_penilaian_deadline'   => $this->request->getPost('enable_penilaian_deadline') ? '1' : '0',
            'enable_maintenance_mode'     => $this->request->getPost('enable_maintenance_mode') ? '1' : '0',
        ];

        foreach ($toggles as $tKey => $tVal) {
            $settingModel->update($tKey, [
                'setting_value' => $tVal,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
        }

        // 2. Simpan nilai parameter angka dan teks dengan batasan (clamping) valid
        $settings = $this->request->getPost('settings');
        if ($settings && is_array($settings)) {
            foreach ($settings as $key => $value) {
                if ($key === 'maintenance_message') {
                    $valClean = trim((string)$value);
                } else {
                    $rawVal = (int)trim((string)$value);
                    if (in_array($key, ['batas_input_target', 'batas_penilaian_kinerja'])) {
                        $valClean = (string)min(31, max(1, $rawVal));
                    } elseif ($key === 'toleransi_hari_bulan_lalu') {
                        $valClean = (string)min(30, max(0, $rawVal)); // Min 0 hari, Max 30 hari
                    } elseif ($key === 'batas_input_log') {
                        $valClean = (string)min(60, max(1, $rawVal));
                    } else {
                        $valClean = (string)max(0, $rawVal);
                    }
                }

                $settingModel->update($key, [
                    'setting_value' => (string)$valClean,
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);
            }
        }

        // Invalidate in-memory cache seketika
        SettingModel::clearCache();

        log_audit('UPDATE', 'settings', 'system_and_deadlines', null, [
            'toggles' => $toggles,
            'values'  => $settings
        ]);
        
        $msg = $toggles['enable_maintenance_mode'] === '1'
            ? 'Pengaturan berhasil diperbarui. MODE PEMELIHARAAN AKTIF untuk seluruh pengguna non-admin.'
            : 'Pengaturan sistem berhasil diperbarui.';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Pencadangan Basis Data 1-Klik untuk Superadmin (Pure PHP Stream Writer)
     * Menghasilkan berkas .sql standar yang 100% kompatibel dengan phpMyAdmin / MySQL
     */
    public function backupDatabase()
    {
        if (!hasRole('admin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak. Hanya administrator yang dapat mencadangkan basis data.');
        }

        // Batasi frekuensi pencadangan (maksimal 3 kali per menit)
        if (!$this->checkExportRateLimit('BACKUP_DATABASE', 3, 60)) {
            $this->session->setFlashdata('error', 'Pencadangan basis data dibatasi. Silakan tunggu beberapa saat sebelum mencoba kembali.');
            $referer = $this->request->getServer('HTTP_REFERER');
            return !empty($referer) ? redirect()->back() : redirect()->to(site_url('settings'));
        }

        $db = \Config\Database::connect();
        $dbName = $db->getDatabase();
        $tables = $db->listTables();

        // Audit Trail Pencatatan Backup
        helper('audit');
        if (function_exists('log_audit')) {
            $currentUserId = session()->get('id') ?? session()->get('user_id');
            log_audit(
                'BACKUP_DATABASE',
                'database',
                $currentUserId,
                null,
                [
                    'database'     => $dbName,
                    'total_tables' => count($tables),
                    'ip'           => $this->request->getIPAddress()
                ]
            );
        }

        $timestamp = date('Ymd_His');
        $fileName = 'backup_' . $dbName . '_' . $timestamp . '.sql';

        // Bersihkan output buffer sebelum streaming berkas SQL
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Access-Control-Expose-Headers: Content-Disposition');
        header('Cache-Control: max-age=0, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');

        // Header SQL Kompatibel phpMyAdmin / MySQL
        echo "-- ==========================================================\n";
        echo "-- Evidence Command Center (ECC) Database Backup\n";
        echo "-- Versi Basis Data: MySQL / MariaDB\n";
        echo "-- Database: `" . $dbName . "`\n";
        echo "-- Waktu Pencadangan: " . date('Y-m-d H:i:s') . "\n";
        echo "-- ==========================================================\n\n";
        echo "SET FOREIGN_KEY_CHECKS=0;\n";
        echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        echo "SET AUTOCOMMIT = 0;\n";
        echo "START TRANSACTION;\n";
        echo "SET time_zone = \"+00:00\";\n\n";

        foreach ($tables as $table) {
            echo "-- --------------------------------------------------------\n";
            echo "-- Struktur Tabel: `" . $table . "`\n";
            echo "-- --------------------------------------------------------\n";
            echo "DROP TABLE IF EXISTS `" . $table . "`;\n";

            $createRow = $db->query("SHOW CREATE TABLE `" . $table . "`")->getRowArray();
            if ($createRow && isset($createRow['Create Table'])) {
                echo $createRow['Create Table'] . ";\n\n";
            }

            // Data Baris
            $count = $db->table($table)->countAllResults();
            if ($count > 0) {
                echo "-- Data untuk Tabel: `" . $table . "` (Total: " . $count . " baris)\n";
                $query = $db->table($table)->get();
                $batch = [];
                $batchSize = 100;
                $fields = null;

                foreach ($query->getResultArray() as $row) {
                    if ($fields === null) {
                        $fields = '`' . implode('`, `', array_keys($row)) . '`';
                    }

                    $escapedValues = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $escapedValues[] = 'NULL';
                        } elseif (is_numeric($val) && !is_string($val)) {
                            $escapedValues[] = $val;
                        } else {
                            $escapedValues[] = $db->escape($val);
                        }
                    }
                    $batch[] = '(' . implode(', ', $escapedValues) . ')';

                    if (count($batch) >= $batchSize) {
                        echo "INSERT INTO `" . $table . "` (" . $fields . ") VALUES\n" . implode(",\n", $batch) . ";\n";
                        $batch = [];
                        flush();
                    }
                }

                if (!empty($batch)) {
                    echo "INSERT INTO `" . $table . "` (" . $fields . ") VALUES\n" . implode(",\n", $batch) . ";\n";
                }
                echo "\n";
            }
        }

        echo "COMMIT;\n";
        echo "SET FOREIGN_KEY_CHECKS=1;\n";
        echo "-- Akhir Cadangan Basis Data ECC --\n";
        exit;
    }

    /**
     * Pembersihan & Manajemen Retensi Data Log (Housekeeping)
     * Menghapus catatan log lama (audit_logs & notifications) berdasarkan ambang batas bulan
     */
    public function purgeLogs()
    {
        if (!hasRole('admin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak. Hanya administrator yang dapat membersihkan log sistem.');
        }

        $retentionMonths = (int)$this->request->getPost('retention_months');
        if (!in_array($retentionMonths, [3, 6, 12], true)) {
            $retentionMonths = 12; // default 12 bulan
        }

        $target = (string)($this->request->getPost('target_table') ?? 'all');
        if (!in_array($target, ['all', 'audit_logs', 'notifications'], true)) {
            $target = 'all';
        }

        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$retentionMonths} months"));

        $db = \Config\Database::connect();
        $deletedAudit = 0;
        $deletedNotif = 0;

        $db->transStart();

        if ($target === 'all' || $target === 'audit_logs') {
            $deletedAudit = $db->table('audit_logs')
                               ->where('created_at <', $cutoffDate)
                               ->countAllResults(false);
            if ($deletedAudit > 0) {
                $db->table('audit_logs')
                   ->where('created_at <', $cutoffDate)
                   ->delete();
            }
        }

        if ($target === 'all' || $target === 'notifications') {
            $deletedNotif = $db->table('notifications')
                               ->where('created_at <', $cutoffDate)
                               ->countAllResults(false);
            if ($deletedNotif > 0) {
                $db->table('notifications')
                   ->where('created_at <', $cutoffDate)
                   ->delete();
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal membersihkan log. Silakan coba lagi.');
        }

        // Catat jejak audit atas pembersihan log
        helper('audit');
        if (function_exists('log_audit')) {
            $currentUserId = session()->get('id') ?? session()->get('user_id');
            log_audit(
                'PURGE_OLD_LOGS',
                'system',
                $currentUserId,
                null,
                [
                    'retention_months' => $retentionMonths,
                    'cutoff_date'      => $cutoffDate,
                    'deleted_audit'    => $deletedAudit,
                    'deleted_notif'    => $deletedNotif,
                    'target'           => $target
                ]
            );
        }

        $totalDeleted = $deletedAudit + $deletedNotif;
        $msg = $totalDeleted > 0
            ? "Pembersihan berhasil. Sebanyak {$totalDeleted} catatan log yang berusia lebih dari {$retentionMonths} bulan telah dibersihkan."
            : "Tidak ada catatan log yang berusia lebih dari {$retentionMonths} bulan. Seluruh data masih dalam rentang waktu aktif.";

        return redirect()->back()->with('success', $msg);
    }
}
