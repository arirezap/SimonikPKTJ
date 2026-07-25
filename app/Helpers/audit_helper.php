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

            $data = [
                'user_id'    => $userId,
                'action'     => strtoupper($action),
                'entity'     => $entity,
                'entity_id'  => $entityId,
                'old_values' => $oldValues !== null ? json_encode($oldValues) : null,
                'new_values' => $newValues !== null ? json_encode($newValues) : null,
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
