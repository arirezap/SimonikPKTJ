<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterIndikator extends Migration
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
            'sasaran_id' => [ // Opsional: Jika ingin dikaitkan dengan Sasaran Strategis
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nama_indikator' => [
                'type' => 'TEXT',
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('master_indikator');
        
        // SEEDER DATA AWAL (Agar dropdown tidak kosong)
        $data = [
            ['nama_indikator' => 'Persentase Lulusan yang Terserap Dunia Kerja', 'satuan' => 'Persen'],
            ['nama_indikator' => 'Jumlah Publikasi Ilmiah Internasional', 'satuan' => 'Dokumen'],
            ['nama_indikator' => 'Indeks Kepuasan Masyarakat', 'satuan' => 'Indeks'],
            ['nama_indikator' => 'Nilai SAKIP Instansi', 'satuan' => 'Nilai'],
        ];
        $this->db->table('master_indikator')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('master_indikator');
    }
}