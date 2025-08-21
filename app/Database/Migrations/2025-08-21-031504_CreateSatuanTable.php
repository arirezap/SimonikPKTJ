<?php
// File: app/Database/Migrations/YYYY-MM-DD-HHIISS_CreateSatuanTable.php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateSatuanTable extends Migration
{
    public function up()
    {
        $this->forge->addField(['id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true], 'nama_satuan' => ['type' => 'VARCHAR', 'constraint' => 100]]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('satuan');
    }
    public function down() { $this->forge->dropTable('satuan'); }
}