<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropDuplicateIndexes extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Hapus duplicate index pada target_kinerja_bulanan (idx_lh_user_bulan_tahun tetap aktif)
        if ($db->tableExists('target_kinerja_bulanan')) {
            $indexes = $db->query("SHOW INDEX FROM `target_kinerja_bulanan` WHERE Key_name = 'idx_user_bulan_tahun'")->getResultArray();
            if (!empty($indexes)) {
                $db->query("ALTER TABLE `target_kinerja_bulanan` DROP INDEX `idx_user_bulan_tahun`");
            }
        }

        // 2. Hapus duplicate index pada log_tugas_tambahan (idx_ltt_user_tgl tetap aktif)
        if ($db->tableExists('log_tugas_tambahan')) {
            $indexes = $db->query("SHOW INDEX FROM `log_tugas_tambahan` WHERE Key_name = 'idx_user_tgl'")->getResultArray();
            if (!empty($indexes)) {
                $db->query("ALTER TABLE `log_tugas_tambahan` DROP INDEX `idx_user_tgl`");
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('target_kinerja_bulanan')) {
            $indexes = $db->query("SHOW INDEX FROM `target_kinerja_bulanan` WHERE Key_name = 'idx_user_bulan_tahun'")->getResultArray();
            if (empty($indexes)) {
                $db->query("ALTER TABLE `target_kinerja_bulanan` ADD INDEX `idx_user_bulan_tahun` (`user_id`, `bulan`, `tahun`)");
            }
        }

        if ($db->tableExists('log_tugas_tambahan')) {
            $indexes = $db->query("SHOW INDEX FROM `log_tugas_tambahan` WHERE Key_name = 'idx_user_tgl'")->getResultArray();
            if (empty($indexes)) {
                $db->query("ALTER TABLE `log_tugas_tambahan` ADD INDEX `idx_user_tgl` (`user_id`, `tanggal_kegiatan`)");
            }
        }
    }
}
