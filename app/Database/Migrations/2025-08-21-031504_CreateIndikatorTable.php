<?php
// File: app/Database/Migrations/YYYY-MM-DD-HHIISS_CreateIndikatorTable.php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateIndikatorTable extends Migration
{
    public function up()
    {
        $this->forge->addField(['id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true], 'nama_indikator' => ['type' => 'VARCHAR', 'constraint' => 255]]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('indikator');
    }
    public function down() { $this->forge->dropTable('indikator'); }
}