<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHolidaysTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'holiday_date' => [
                'type' => 'DATE',
            ],
            'holiday_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'is_national' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('holiday_date');
        $this->forge->createTable('holidays');
    }

    public function down()
    {
        $this->forge->dropTable('holidays');
    }
}
