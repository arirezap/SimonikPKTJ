<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLedScoresTable extends Migration
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
            'user_id' => [ // User yang menginput skor
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'prodi' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'tahun' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'led_criteria_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'skor' => [ // Kolom baru untuk nilai/skor
                'type'       => 'DECIMAL',
                'constraint' => '5,2', // Misal: 100.00
                'null'       => true,
                'default'    => 0.00,
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
        // Menambahkan unique key agar 1 kriteria per prodi per tahun hanya punya 1 skor
        $this->forge->addUniqueKey(['prodi', 'tahun', 'led_criteria_id']);
        $this->forge->createTable('led_scores');
    }

    public function down()
    {
        $this->forge->dropTable('led_scores');
    }
}
