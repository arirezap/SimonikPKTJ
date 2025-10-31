<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLedCriteriaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nomor_kriteria' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'comment'    => 'Nomor urut kriteria, cth: 1.1, 2.a',
            ],
            'nama_kriteria' => [
                'type' => 'TEXT',
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
        $this->forge->createTable('led_criteria');
    }

    public function down()
    {
        $this->forge->dropTable('led_criteria');
    }
}

