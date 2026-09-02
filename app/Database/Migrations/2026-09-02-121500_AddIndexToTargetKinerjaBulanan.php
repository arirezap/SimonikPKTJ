<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexToTargetKinerjaBulanan extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Cek apakah tabel fisik target_kinerja_bulanan ada
        if ($db->tableExists('target_kinerja_bulanan')) {
            // Cek apakah index sudah ada
            $indexes = $db->query("SHOW INDEX FROM `target_kinerja_bulanan` WHERE Key_name = 'idx_user_bulan_tahun'")->getResultArray();
            if (empty($indexes)) {
                $db->query("ALTER TABLE `target_kinerja_bulanan` ADD INDEX `idx_user_bulan_tahun` (`user_id`, `bulan`, `tahun`)");
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        if ($db->tableExists('target_kinerja_bulanan')) {
            $indexes = $db->query("SHOW INDEX FROM `target_kinerja_bulanan` WHERE Key_name = 'idx_user_bulan_tahun'")->getResultArray();
            if (!empty($indexes)) {
                $db->query("ALTER TABLE `target_kinerja_bulanan` DROP INDEX `idx_user_bulan_tahun`");
            }
        }
    }
}
