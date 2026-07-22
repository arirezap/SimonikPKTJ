<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToLaporanHarian extends Migration
{
    public function up()
    {
        $fields = [
            'status_approval' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu_persetujuan', 'disetujui'],
                'default'    => 'menunggu_persetujuan',
                'null'       => false,
            ],
        ];
        $this->forge->addColumn('laporan_harian', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('laporan_harian', 'status_approval');
    }
}
