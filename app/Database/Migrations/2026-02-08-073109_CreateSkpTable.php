<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSkpTable extends Migration
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
            'tahun' => [
                'type'       => 'YEAR',
            ],
            'jenis' => [ // Utama / Tambahan
                'type'       => 'ENUM',
                'constraint' => ['Utama', 'Tambahan'],
                'default'    => 'Utama',
            ],
            'rencana_kinerja' => [
                'type' => 'TEXT',
            ],
            'aspek' => [ // Kuantitas / Kualitas / Waktu
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'indikator' => [
                'type' => 'TEXT',
            ],
            'target' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50, // Dokumen, Laporan, Persen, dll
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
        $this->forge->createTable('skp_targets');
    }

    public function down()
    {
        $this->forge->dropTable('skp_targets');
    }
}