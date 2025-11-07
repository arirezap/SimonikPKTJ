<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UbahLedCriteriaPerProdi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('led_criteria', [
            'prodi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => 'RSTJ', // Set default untuk data yang sudah ada
                'after'      => 'id',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'nama_kriteria',
            ],
        ]);
        
        // Hapus kolom nomor_kriteria
        $this->forge->dropColumn('led_criteria', 'nomor_kriteria');
    }

    public function down()
    {
        // Tambahkan kembali kolom nomor_kriteria
        $this->forge->addColumn('led_criteria', [
            'nomor_kriteria' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'after'      => 'nama_kriteria',
            ],
        ]);

        // Hapus kolom prodi dan sort_order
        $this->forge->dropColumn('led_criteria', 'prodi');
        $this->forge->dropColumn('led_criteria', 'sort_order');
    }
}