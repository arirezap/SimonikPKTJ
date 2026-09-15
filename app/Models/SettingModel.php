<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'setting_key';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['setting_key', 'setting_name', 'setting_value', 'description'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // Model Lifecycle Callbacks untuk Cache Invalidation
    protected $afterInsert = ['invalidateCacheCallback'];
    protected $afterUpdate = ['invalidateCacheCallback'];
    protected $afterDelete = ['invalidateCacheCallback'];

    /**
     * Cache in-memory statis pada level siklus request (request-scoped)
     * Mengeliminasi 5-8 query berulang ke tabel settings dalam satu request.
     */
    protected static ?array $cachedSettings = null;
    protected static ?array $cachedFullRows = null;
    protected static int $loadCount = 0;

    public static function getLoadCount(): int
    {
        return self::$loadCount;
    }

    public static function resetLoadCount(): void
    {
        self::$loadCount = 0;
    }

    /**
     * Ambil nilai pengaturan berdasarkan setting_key
     * Dijawab instan O(1) dari cache in-memory jika sudah pernah dimuat dalam request yang sama.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getValue($key, $default = null)
    {
        if (self::$cachedSettings === null) {
            $this->loadSettings();
        }
        return self::$cachedSettings[$key] ?? $default;
    }

    /**
     * Ambil seluruh data baris pengaturan sebagai map terindeks setting_key
     *
     * @return array
     */
    public function getAllSettings(): array
    {
        if (self::$cachedFullRows === null) {
            $this->loadSettings();
        }
        return self::$cachedFullRows ?? [];
    }

    /**
     * Ambil seluruh pasangan [setting_key => setting_value]
     *
     * @return array
     */
    public function getAllValues(): array
    {
        if (self::$cachedSettings === null) {
            $this->loadSettings();
        }
        return self::$cachedSettings ?? [];
    }

    /**
     * Muat seluruh pengaturan dari database ke memori statis (1 query per request)
     *
     * @return array
     */
    public function loadSettings(): array
    {
        try {
            self::$loadCount++;
            $rows = $this->findAll();
            self::$cachedSettings = [];
            self::$cachedFullRows = [];

            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $k = (string)($row['setting_key'] ?? '');
                    if ($k !== '') {
                        self::$cachedSettings[$k] = $row['setting_value'] ?? null;
                        self::$cachedFullRows[$k] = $row;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Failsafe tenang jika koneksi atau tabel belum siap
            if (self::$cachedSettings === null) {
                self::$cachedSettings = [];
            }
            if (self::$cachedFullRows === null) {
                self::$cachedFullRows = [];
            }
        }

        return self::$cachedSettings;
    }

    /**
     * Reset memori cache statis (dipanggil saat mutasi data atau pembaruan pengaturan)
     */
    public static function clearCache(): void
    {
        self::$cachedSettings = null;
        self::$cachedFullRows = null;
    }

    /**
     * Callback CI4 Model saat insert/update/delete
     */
    protected function invalidateCacheCallback(array $data)
    {
        self::clearCache();
        return $data;
    }
}
