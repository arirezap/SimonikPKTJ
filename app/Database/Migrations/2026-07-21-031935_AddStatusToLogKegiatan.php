<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToLogKegiatan extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'terkirim'],
                'default'    => 'draft',
                'null'       => false,
            ],
        ];
        $this->forge->addColumn('log_kegiatan_harian', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('log_kegiatan_harian', 'status');
    }
}
