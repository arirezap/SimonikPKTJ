<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RevampPenilaianKinerja extends Migration
{
    public function up()
    {
        // Add nilai_capaian to laporan_harian
        $this->forge->addColumn('laporan_harian', [
            'nilai_capaian' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ]
        ]);

        // Drop old evaluation columns from log_kegiatan_harian
        $this->forge->dropColumn('log_kegiatan_harian', ['waktu_penyelesaian', 'kualitas_hasil', 'disiplin', 'kerjasama', 'nilai_harian']);
    }

    public function down()
    {
        // Revert dropping
        $fields = [
            'waktu_penyelesaian' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'kualitas_hasil' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'disiplin' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'kerjasama' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_harian' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
        ];
        $this->forge->addColumn('log_kegiatan_harian', $fields);

        // Revert adding
        $this->forge->dropColumn('laporan_harian', 'nilai_capaian');
    }
}
