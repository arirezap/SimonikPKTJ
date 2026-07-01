<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPenilaianToLogKegiatan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('log_kegiatan_harian', [
            'waktu_penyelesaian' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'kualitas_hasil' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'disiplin' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'kerjasama' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'nilai_harian' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('log_kegiatan_harian', ['waktu_penyelesaian', 'kualitas_hasil', 'disiplin', 'kerjasama', 'nilai_harian']);
    }
}
