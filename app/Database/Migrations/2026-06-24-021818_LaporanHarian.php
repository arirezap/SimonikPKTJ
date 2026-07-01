<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LaporanHarian extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'bulan' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'tahun' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'sasaran_program' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'indikator_kinerja' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'target_bulanan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->createTable('laporan_harian');
    }

    public function down()
    {
        $this->forge->dropTable('laporan_harian');
    }
}
