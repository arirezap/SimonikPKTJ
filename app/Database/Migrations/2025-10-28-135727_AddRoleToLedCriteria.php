<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleToLedCriteria extends Migration
{
    public function up()
    {
        $this->forge->addColumn('led_criteria', [
            'role_assignment' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // Akan diisi dengan 'aak', 'kuk', atau 'all'
                'null'       => true,
                'after'      => 'kategori', // Meletakkan kolom ini setelah kolom 'kategori'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('led_criteria', 'role_assignment');
    }
}
