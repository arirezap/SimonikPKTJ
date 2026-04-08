<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRemunerasiTable extends Migration
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
            'user_id' => [ // ID Pegawai (aak, kuk, spm)
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tahun' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'bulan' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'jumlah' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2', // Cukup besar untuk nominal uang
                'default'    => 0.00,
            ],
            'created_by_user_id' => [ // ID Manajemen/Admin yang menginput
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        // Unique key agar 1 user hanya punya 1 entri per bulan per tahun
        $this->forge->addUniqueKey(['user_id', 'tahun', 'bulan']);
        // Parameter TRUE artinya "IF NOT EXISTS"
        $this->forge->createTable('remunerasi', true);
    }

    public function down()
    {
        $this->forge->dropTable('remunerasi');
    }
}
