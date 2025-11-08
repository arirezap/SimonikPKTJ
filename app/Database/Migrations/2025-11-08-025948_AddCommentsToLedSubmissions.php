<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCommentsToLedSubmissions extends Migration
{
    public function up()
    {
        $fields = [
            'catatan_kabag' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'status',
                'comment'    => 'Catatan/revisi dari Kabag untuk staf',
            ],
            'catatan_wadir' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'catatan_kabag',
                'comment'    => 'Catatan/revisi dari Wadir untuk staf/kabag',
            ],
        ];
        $this->forge->addColumn('led_submissions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('led_submissions', ['catatan_kabag', 'catatan_wadir']);
    }
}