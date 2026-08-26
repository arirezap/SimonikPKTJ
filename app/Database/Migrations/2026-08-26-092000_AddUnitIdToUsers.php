<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUnitIdToUsers extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Cek apakah kolom unit_id sudah ada
        if (!$db->fieldExists('unit_id', 'users')) {
            $this->forge->addColumn('users', [
                'unit_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'unit'
                ],
            ]);
            
            // Tambahkan index
            $db->query('ALTER TABLE `users` ADD INDEX `idx_u_unit_id` (`unit_id`)');
        }

        // Sinkronkan unit_id berdasarkan kecocokan nama_unit yang ada
        if ($db->tableExists('unit_kerja')) {
            $db->query("
                UPDATE `users` u
                INNER JOIN `unit_kerja` uk ON TRIM(LOWER(u.unit)) = TRIM(LOWER(uk.nama_unit))
                SET u.unit_id = uk.id
                WHERE u.unit_id IS NULL OR u.unit_id = 0
            ");
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if ($db->fieldExists('unit_id', 'users')) {
            $this->forge->dropColumn('users', 'unit_id');
        }
    }
}
