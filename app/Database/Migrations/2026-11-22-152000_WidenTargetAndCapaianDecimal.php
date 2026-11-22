<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class WidenTargetAndCapaianDecimal extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE `target_kinerja_bulanan` MODIFY COLUMN `target_bulanan` DECIMAL(20,4) DEFAULT NULL');
        $this->db->query('ALTER TABLE `log_kegiatan_harian` MODIFY COLUMN `jumlah_capaian` DECIMAL(20,4) DEFAULT NULL');
        $this->db->query('ALTER TABLE `log_tugas_tambahan` MODIFY COLUMN `jumlah_capaian` DECIMAL(20,4) DEFAULT NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `target_kinerja_bulanan` MODIFY COLUMN `target_bulanan` DECIMAL(10,4) DEFAULT NULL');
        $this->db->query('ALTER TABLE `log_kegiatan_harian` MODIFY COLUMN `jumlah_capaian` DECIMAL(10,4) DEFAULT NULL');
        $this->db->query('ALTER TABLE `log_tugas_tambahan` MODIFY COLUMN `jumlah_capaian` DECIMAL(10,4) DEFAULT NULL');
    }
}
