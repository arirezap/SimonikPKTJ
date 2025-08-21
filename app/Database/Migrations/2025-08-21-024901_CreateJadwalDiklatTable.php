<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJadwalDiklatTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_diklat' => ['type' => 'VARCHAR', 'constraint' => 255],
            'periode' => ['type' => 'VARCHAR', 'constraint' => 255],
            'jumlah_peserta' => ['type' => 'INT', 'constraint' => 11],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('jadwal_diklat');
    }

    public function down()
    {
        $this->forge->dropTable('jadwal_diklat');
    }
}
