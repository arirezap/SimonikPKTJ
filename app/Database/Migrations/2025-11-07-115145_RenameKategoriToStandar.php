<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameKategoriToStandar extends Migration
{
    public function up()
    {
        // 1. Rename tabel led_categories -> led_standar
        $this->forge->renameTable('led_categories', 'led_standar');

        // 2. Rename kolom nama_kategori -> nama_standar
        $this->forge->modifyColumn('led_standar', [
            'nama_kategori' => [
                'name'       => 'nama_standar',
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);

        // 3. Hapus Foreign Key lama di led_criteria
        // Dapatkan nama constraint foreign key secara dinamis
        $keys = $this->db->getForeignKeyData('led_criteria');
        foreach ($keys as $key) {
            if ($key->column_name === 'id_kategori' && $key->foreign_table_name === 'led_standar') {
                $this->forge->dropForeignKey('led_criteria', $key->constraint_name);
                break;
            }
        }

        // 4. Rename kolom id_kategori -> id_standar di led_criteria
        $this->forge->modifyColumn('led_criteria', [
            'id_kategori' => [
                'name'       => 'id_standar',
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        // 5. Buat ulang Foreign Key dengan nama baru
        $this->forge->addForeignKey('id_standar', 'led_standar', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        // 1. Hapus Foreign Key
        $keys = $this->db->getForeignKeyData('led_criteria');
        foreach ($keys as $key) {
            if ($key->column_name === 'id_standar' && $key->foreign_table_name === 'led_standar') {
                $this->forge->dropForeignKey('led_criteria', $key->constraint_name);
                break;
            }
        }
        
        // 2. Rename kolom id_standar -> id_kategori
        $this->forge->modifyColumn('led_criteria', [
            'id_standar' => [
                'name'       => 'id_kategori',
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        
        // 3. Buat ulang Foreign Key lama
        $this->forge->addForeignKey('id_kategori', 'led_standar', 'id', 'CASCADE', 'SET NULL');

        // 4. Rename kolom nama_standar -> nama_kategori
        $this->forge->modifyColumn('led_standar', [
            'nama_standar' => [
                'name'       => 'nama_kategori',
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);

        // 5. Rename tabel led_standar -> led_categories
        $this->forge->renameTable('led_standar', 'led_categories');
    }
}