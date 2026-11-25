<?php

if (!function_exists('log_audit')) {
    /**
     * Re-usable helper to insert audit trail logs
     *
     * @param string $action      Tipe aksi (contoh: LOGIN, LOGOUT, CREATE, UPDATE, DELETE, APPROVE, SIMULASI)
     * @param string $entity      Tabel atau entitas yang terdampak (contoh: 'users', 'laporan_harian', 'ecc_scores')
     * @param string $entityId    Primary key dari entitas yang terdampak
     * @param array  $oldValues   Data sebelum dirubah (associative array)
     * @param array  $newValues   Data sesudah dirubah (associative array)
     * @return void
     */
    /**
     * Sanitasi rekursif data audit untuk menyamarkan kata sandi, token, dan rahasia
     */
    function sanitize_audit_payload($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $sensitiveKeys = [
            'password', 'password_hash', 'pass', 'token', 'secret',
            'api_key', 'auth_key', 'csrf_hash', 'remember_token'
        ];

        $sanitized = [];
        foreach ($data as $key => $value) {
            $lowerKey = strtolower((string)$key);
            if (in_array($lowerKey, $sensitiveKeys, true)) {
                $sanitized[$key] = '******** (disamarkan)';
            } elseif (is_array($value)) {
                $sanitized[$key] = sanitize_audit_payload($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Daftar aksi draf sementara berfrekuensi tinggi yang tidak perlu dicatat ke tabel audit_logs
     * agar database tetap ramping dan hanya menyimpan log aktivitas penting.
     * (Catatan: Data draf pegawai di tabel kinerja tetap 100% tersimpan normal).
     *
     * @return list<string>
     */
    function skipped_transient_audit_actions(): array
    {
        return [
            'DRAFT_LOG_HARIAN',
            'DRAFT_TARGET',
            'SAVE_DRAFT_PENILAIAN_KINERJA',
        ];
    }

    /**
     * Pembersihan terkendali tabel audit_logs dan notifications (sama sekali tidak menyentuh data kinerja/draf pegawai).
     *
     * @param int  $keepDays          Simpan log & notifikasi dalam N hari terakhir (default 30 hari)
     * @param bool $cleanDraftNoise   Sekaligus bersihkan log klik draf lama di audit_logs
     * @param bool $cleanNotifications Sekaligus bersihkan notifikasi lonceng lama
     * @return array{deleted_audit:int, deleted_notif:int}
     */
    function prune_old_audit_and_notifications(int $keepDays = 30, bool $cleanDraftNoise = true, bool $cleanNotifications = true): array
    {
        $deletedAudit = 0;
        $deletedNotif = 0;

        try {
            $db     = \Config\Database::connect();
            $keep   = max(1, $keepDays);
            $cutoff = date('Y-m-d H:i:s', strtotime("-{$keep} days"));

            if ($db->tableExists('audit_logs')) {
                $db->table('audit_logs')->where('created_at <', $cutoff)->delete();
                $deletedAudit += (int) $db->affectedRows();

                if ($cleanDraftNoise) {
                    $db->table('audit_logs')->whereIn('action', skipped_transient_audit_actions())->delete();
                    $deletedAudit += (int) $db->affectedRows();
                }
            }

            if ($cleanNotifications && $db->tableExists('notifications')) {
                $readCutoff = date('Y-m-d H:i:s', strtotime('-7 days'));
                $db->table('notifications')
                    ->groupStart()
                        ->where('created_at <', $cutoff)
                        ->orGroupStart()
                            ->where('is_read', 1)
                            ->where('created_at <', $readCutoff)
                        ->groupEnd()
                    ->groupEnd()
                    ->delete();
                $deletedNotif += (int) $db->affectedRows();
            }
        } catch (\Throwable $e) {
            log_message('warning', 'Gagal menjalankan pembersihan retensi log: ' . $e->getMessage());
        }

        return [
            'deleted_audit' => $deletedAudit,
            'deleted_notif' => $deletedNotif,
        ];
    }

    function log_audit(string $action, string $entity = '-', $entityId = null, array $oldValues = null, array $newValues = null)
    {
        try {
            $actionUpper = strtoupper(trim($action));

            // Lewati pencatatan log klik simpan draf sementara agar tabel audit_logs hanya menyimpan aksi penting
            if (in_array($actionUpper, skipped_transient_audit_actions(), true)) {
                return;
            }

            $request = \Config\Services::request();
            $ipAddress = $request->getIPAddress();
            
            // Dapatkan user agent
            $userAgentString = '';
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $userAgentString = $_SERVER['HTTP_USER_AGENT'];
            }

            // Dapatkan user ID yang sedang aktif (bisa admin atau user biasa)
            $userId = session()->get('id') ?? null;

            // Sanitasi data sebelum diubah menjadi JSON
            $cleanOldValues = $oldValues !== null ? sanitize_audit_payload($oldValues) : null;
            $cleanNewValues = $newValues !== null ? sanitize_audit_payload($newValues) : null;

            $data = [
                'user_id'    => $userId,
                'action'     => $actionUpper,
                'entity'     => $entity,
                'entity_id'  => $entityId,
                'old_values' => $cleanOldValues !== null ? json_encode($cleanOldValues, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
                'new_values' => $cleanNewValues !== null ? json_encode($cleanNewValues, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
                'ip_address' => $ipAddress,
                'user_agent' => substr($userAgentString, 0, 250), // Batasi panjang
                'created_at' => date('Y-m-d H:i:s')
            ];

            $model = new \App\Models\AuditLog();
            $model->insert($data);

            // Auto-housekeeping ringan maksimal 1 kali per hari saat ada aktivitas LOGIN
            if ($actionUpper === 'LOGIN') {
                $stampFile = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . '.last_audit_notif_prune';
                $lastRun   = is_file($stampFile) ? (int) @filemtime($stampFile) : 0;
                if ((time() - $lastRun) >= 86400) {
                    @file_put_contents($stampFile, (string) time());
                    prune_old_audit_and_notifications(30, true, true);
                }
            }
        } catch (\Exception $e) {
            // Log secara internal (file log CI) jika gagal menyimpan audit ke database 
            // agar aplikasi tidak crash
            log_message('error', 'Gagal mencatat audit log: ' . $e->getMessage());
        }
    }
}
