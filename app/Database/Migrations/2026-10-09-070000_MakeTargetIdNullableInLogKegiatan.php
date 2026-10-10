<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeTargetIdNullableInLogKegiatan extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('log_kegiatan_harian', [
            'target_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('log_kegiatan_harian', [
            'target_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
    }
}
