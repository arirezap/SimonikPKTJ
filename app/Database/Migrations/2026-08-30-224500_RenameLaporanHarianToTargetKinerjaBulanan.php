<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameLaporanHarianToTargetKinerjaBulanan extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // 1. Cek apakah tabel fisik laporan_harian ada dan bukan view
        $tableExists = $db->tableExists('laporan_harian');
        $targetExists = $db->tableExists('target_kinerja_bulanan');

        if ($tableExists && !$targetExists) {
            // Rename tabel fisik
            $db->query("RENAME TABLE `laporan_harian` TO `target_kinerja_bulanan`");
        }

        // 2. Buat / perbarui view laporan_harian untuk backward compatibility
        $db->query("CREATE OR REPLACE VIEW `laporan_harian` AS SELECT * FROM `target_kinerja_bulanan`");
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        // Drop view
        $db->query("DROP VIEW IF EXISTS `laporan_harian`");

        // Revert nama tabel fisik
        if ($db->tableExists('target_kinerja_bulanan')) {
            $db->query("RENAME TABLE `target_kinerja_bulanan` TO `laporan_harian`");
        }
    }
}
