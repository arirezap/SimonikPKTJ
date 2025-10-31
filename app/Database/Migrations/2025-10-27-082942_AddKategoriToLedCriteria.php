<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKategoriToLedCriteria extends Migration
{
    public function up()
    {
        $this->forge->addColumn('led_criteria', [
            'kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'nama_kriteria', // Meletakkan kolom ini setelah nama_kriteria
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('led_criteria', 'kategori');
    }
}
