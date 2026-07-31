<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusPenilaianToLaporanHarian extends Migration
{
    public function up()
    {
        $fields = [
            'status_penilaian' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'terbit'],
                'default'    => 'draft',
                'after'      => 'nilai_capaian'
            ]
        ];
        $this->forge->addColumn('laporan_harian', $fields);

        $fieldsTambahan = [
            'status_penilaian' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'terbit'],
                'default'    => 'draft',
                'after'      => 'nilai_capaian'
            ]
        ];
        $this->forge->addColumn('log_tugas_tambahan', $fieldsTambahan);
    }

    public function down()
    {
        $this->forge->dropColumn('laporan_harian', 'status_penilaian');
        $this->forge->dropColumn('log_tugas_tambahan', 'status_penilaian');
    }
}
