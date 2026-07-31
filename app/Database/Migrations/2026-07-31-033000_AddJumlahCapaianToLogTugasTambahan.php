<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJumlahCapaianToLogTugasTambahan extends Migration
{
    public function up()
    {
        $fields = [
            'jumlah_capaian' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 1.00,
                'after'      => 'deskripsi_kegiatan'
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => 'Kegiatan',
                'after'      => 'jumlah_capaian'
            ]
        ];
        $this->forge->addColumn('log_tugas_tambahan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('log_tugas_tambahan', ['jumlah_capaian', 'satuan']);
    }
}
