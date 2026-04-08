<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToSkpTargets extends Migration
{
    public function up()
    {
        $fields = [
            'skp_header_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'after'      => 'user_id' // Menambahkan relasi ke Header
            ],
            'rhk_pimpinan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'jenis' // Kolom RHK Pimpinan yg Diintervensi
            ]
        ];
        $this->forge->addColumn('skp_targets', $fields);
        $this->forge->addForeignKey('skp_header_id', 'skp_headers', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('skp_targets', 'skp_targets_skp_header_id_foreign');
        $this->forge->dropColumn('skp_targets', ['skp_header_id', 'rhk_pimpinan']);
    }
}