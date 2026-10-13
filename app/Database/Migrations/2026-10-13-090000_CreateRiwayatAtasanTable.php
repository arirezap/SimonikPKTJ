<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRiwayatAtasanTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if (!$db->tableExists('riwayat_atasan')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => false,
                ],
                'atasan_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => false,
                ],
                'jabatan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'unit_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                ],
                'unit' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'default'    => 'user',
                ],
                'tahun' => [
                    'type'       => 'INT',
                    'constraint' => 4,
                    'null'       => false,
                ],
                'bulan_mulai' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'null'       => false,
                ],
                'bulan_selesai' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'null'       => false,
                ],
                'is_aktif' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'keterangan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'created_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                ],
                'updated_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->createTable('riwayat_atasan', true);

            // Tambahkan smart index untuk pencarian cepat per periode & atasan
            $idx1 = $db->query("SHOW INDEX FROM `riwayat_atasan` WHERE Key_name = 'idx_ra_user_tahun_bulan'");
            if (empty($idx1->getResultArray())) {
                $db->query("ALTER TABLE `riwayat_atasan` ADD INDEX `idx_ra_user_tahun_bulan` (`user_id`, `tahun`, `bulan_mulai`, `bulan_selesai`)");
            }

            $idx2 = $db->query("SHOW INDEX FROM `riwayat_atasan` WHERE Key_name = 'idx_ra_atasan_tahun_bulan'");
            if (empty($idx2->getResultArray())) {
                $db->query("ALTER TABLE `riwayat_atasan` ADD INDEX `idx_ra_atasan_tahun_bulan` (`atasan_id`, `tahun`, `bulan_mulai`, `bulan_selesai`)");
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('riwayat_atasan', true);
    }
}
