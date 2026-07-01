<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTanggalToLaporanHarian extends Migration
{
    public function up()
    {
        $this->forge->addColumn('laporan_harian', [
            'tanggal' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'user_id'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('laporan_harian', 'tanggal');
    }
}
