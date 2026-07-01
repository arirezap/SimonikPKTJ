<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LogKegiatanHarian extends Migration
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
            'target_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal_kegiatan' => [
                'type' => 'DATE',
            ],
            'deskripsi_kegiatan' => [
                'type' => 'TEXT',
            ],
            'jumlah_capaian' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
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
        // Bisa tambah foreign key jika didukung db
        $this->forge->createTable('log_kegiatan_harian');
    }

    public function down()
    {
        $this->forge->dropTable('log_kegiatan_harian');
    }
}
