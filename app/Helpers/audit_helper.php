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

    function log_audit(string $action, string $entity = '-', $entityId = null, array $oldValues = null, array $newValues = null)
    {
        try {
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
                'action'     => strtoupper($action),
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
        } catch (\Exception $e) {
            // Log secara internal (file log CI) jika gagal menyimpan audit ke database 
            // agar aplikasi tidak crash
            log_message('error', 'Gagal mencatat audit log: ' . $e->getMessage());
        }
    }
}
