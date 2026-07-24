<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserRolesTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'role_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'role_name'], false, true); // Unique composite
        $this->forge->addKey('role_name');
        $this->forge->createTable('user_roles');

        // Populate dari kolom `role` yang sudah ada di tabel `users`
        $db = \Config\Database::connect();
        $users = $db->table('users')->select('id, role')->get()->getResultArray();
        
        $now = date('Y-m-d H:i:s');
        foreach ($users as $user) {
            if (!empty($user['role'])) {
                $db->table('user_roles')->insert([
                    'user_id'    => $user['id'],
                    'role_name'  => strtolower(trim($user['role'])),
                    'created_at' => $now,
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('user_roles');
    }
}
