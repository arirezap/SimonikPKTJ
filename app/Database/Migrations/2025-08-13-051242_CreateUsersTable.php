<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAtasanToUsers extends Migration
{
    public function up()
    {
        // Menambahkan kolom atasan_id
        $fields = [
            'atasan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'role' // Letakkan setelah kolom role
            ],
        ];

        // Cek agar tidak error jika dijalankan berulang
        if (!$this->db->fieldExists('atasan_id', 'users')) {
            $this->forge->addColumn('users', $fields);
            
            // Opsional: Menambahkan Foreign Key agar data konsisten
            // $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_atasan FOREIGN KEY (atasan_id) REFERENCES users(id) ON DELETE SET NULL');
        }
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'atasan_id');
    }
}