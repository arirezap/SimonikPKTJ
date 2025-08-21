<?php
// File: app/Database/Migrations/YYYY-MM-DD-HHIISS_CreateSasaranTable.php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateSasaranTable extends Migration
{
    public function up()
    {
        $this->forge->addField(['id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true], 'nama_sasaran' => ['type' => 'VARCHAR', 'constraint' => 255]]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('sasaran');
    }
    public function down() { $this->forge->dropTable('sasaran'); }
}