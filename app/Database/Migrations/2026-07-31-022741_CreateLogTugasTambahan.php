<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogTugasTambahan extends Migration
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
            'tanggal_kegiatan' => [
                'type' => 'DATE',
            ],
            'deskripsi_kegiatan' => [
                'type' => 'TEXT',
            ],
            'link_bukti' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'terkirim'],
                'default'    => 'draft',
            ],
            'status_approval' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu_persetujuan', 'disetujui', 'ditolak'],
                'default'    => 'menunggu_persetujuan',
            ],
            'nilai_capaian' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
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
        $this->forge->addKey('user_id');
        $this->forge->createTable('log_tugas_tambahan');
    }

    public function down()
    {
        $this->forge->dropTable('log_tugas_tambahan');
    }
}
