<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRencanaKinerjaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'sasaran_program' => ['type' => 'TEXT'],
            'indikator_kinerja' => ['type' => 'TEXT'],
            'satuan' => ['type' => 'VARCHAR', 'constraint' => 100],
            'target_utama' => ['type' => 'VARCHAR', 'constraint' => 255],
            'kegiatan' => ['type' => 'TEXT'],
            'target_bulanan' => ['type' => 'JSON'],
            'realisasi_bulanan' => ['type' => 'JSON', 'null' => true],
            'tahun_anggaran' => ['type' => 'INT', 'constraint' => 4],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('rencana_kinerja');
    }

    public function down()
    {
        $this->forge->dropTable('rencana_kinerja');
    }
}