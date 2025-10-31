<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKabagApprovalToLedSubmissions extends Migration
{
    public function up()
    {
        $fields = [
            'kabag_approved' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0, // 0 = Pending, 1 = Approved
                'null'       => false,
                'after'      => 'status',
                'comment'    => 'Persetujuan oleh Kabag',
            ],
        ];
        $this->forge->addColumn('led_submissions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('led_submissions', 'kabag_approved');
    }
}