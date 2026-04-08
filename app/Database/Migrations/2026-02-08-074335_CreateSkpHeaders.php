<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSkpHeaders extends Migration
{
    public function up()
    {
        // Tabel Header SKP (Untuk menyimpan Tahun & Periode)
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
                'type' => 'YEAR',
            ],
            'periode_awal' => [
                'type' => 'DATE',
            ],
            'periode_akhir' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Draft', 'Diajukan', 'Disetujui'],
                'default'    => 'Draft',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('skp_headers', true);
    }

    public function down()
    {
        $this->forge->dropTable('skp_headers');
    }
}