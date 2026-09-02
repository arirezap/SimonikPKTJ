<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexToLogTugasTambahan extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        if ($db->tableExists('log_tugas_tambahan')) {
            $indexes = $db->query("SHOW INDEX FROM `log_tugas_tambahan` WHERE Key_name = 'idx_user_tgl'")->getResultArray();
            if (empty($indexes)) {
                $db->query("ALTER TABLE `log_tugas_tambahan` ADD INDEX `idx_user_tgl` (`user_id`, `tanggal_kegiatan`)");
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        if ($db->tableExists('log_tugas_tambahan')) {
            $indexes = $db->query("SHOW INDEX FROM `log_tugas_tambahan` WHERE Key_name = 'idx_user_tgl'")->getResultArray();
            if (!empty($indexes)) {
                $db->query("ALTER TABLE `log_tugas_tambahan` DROP INDEX `idx_user_tgl`");
            }
        }
    }
}
