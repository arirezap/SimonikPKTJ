<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentUnitToUnitKerja extends Migration
{
    public function up()
    {
        $this->forge->addColumn('unit_kerja', [
            'parent_unit' => [
                'type'       => "ENUM('aak', 'kuk')",
                'null'       => true,
                'after'      => 'nama_unit',
                'comment'    => 'Penanggung jawab utama (AAK atau KUK)',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('unit_kerja', 'parent_unit');
    }
}