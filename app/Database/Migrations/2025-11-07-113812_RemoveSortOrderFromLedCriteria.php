<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveSortOrderFromLedCriteria extends Migration
{
    public function up()
    {
        // Hapus kolom sort_order
        $this->forge->dropColumn('led_criteria', 'sort_order');
    }

    public function down()
    {
        // Tambahkan kembali kolom sort_order jika di-rollback
        $this->forge->addColumn('led_criteria', [
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'nama_kriteria',
            ],
        ]);
    }
}