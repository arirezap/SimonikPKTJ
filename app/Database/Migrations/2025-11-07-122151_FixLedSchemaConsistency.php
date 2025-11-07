<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixLedSchemaConsistency extends Migration
{
    public function up()
    {
        $this->db->disableForeignKeyChecks();

        // 1. Cek & Tambah kolom 'id_kategori' jika belum ada
        if (! $this->db->fieldExists('id_kategori', 'led_criteria')) {
            $this->forge->addColumn('led_criteria', [
                'id_kategori' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'nama_kriteria',
                ],
            ]);
        }

        // 2. Cek apakah kolom 'kategori' (teks) yang lama masih ada
        if ($this->db->fieldExists('kategori', 'led_criteria')) {
            
            // 3. Pindahkan data dari 'kategori' lama ke 'id_kategori' baru
            $this->db->query(
                "UPDATE led_criteria
                 INNER JOIN led_standar ON led_standar.nama_standar = led_criteria.kategori
                 SET led_criteria.id_kategori = led_standar.id
                 WHERE led_criteria.kategori IS NOT NULL"
            );

            // 4. Tambahkan Foreign Key
            $this->forge->addForeignKey('id_kategori', 'led_standar', 'id', 'CASCADE', 'SET NULL');
            
            // 5. Hapus kolom 'kategori' (teks) yang lama
            $this->forge->dropColumn('led_criteria', 'kategori');
        }

        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->db->disableForeignKeyChecks();

        // 1. Tambahkan kembali kolom 'kategori' (varchar) yang lama jika belum ada
        if (! $this->db->fieldExists('kategori', 'led_criteria')) {
            $this->forge->addColumn('led_criteria', [
                'kategori' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'nama_kriteria',
                ],
            ]);
        }

        // 2. Kembalikan data dari 'id_kategori' ke 'kategori'
        $this->db->query(
            "UPDATE led_criteria
             INNER JOIN led_standar ON led_standar.id = led_criteria.id_kategori
             SET led_criteria.kategori = led_standar.nama_standar
             WHERE led_criteria.id_kategori IS NOT NULL"
        );

        // 3. Hapus Foreign Key dan kolom 'id_kategori'
        if ($this->db->fieldExists('id_kategori', 'led_criteria')) {
            $keys = $this->db->getForeignKeyData('led_criteria');
            foreach ($keys as $key) {
                if ($key->column_name === 'id_kategori' && $key->foreign_table_name === 'led_standar') {
                    $this->forge->dropForeignKey('led_criteria', $key->constraint_name);
                    break;
                }
            }
            $this->forge->dropColumn('led_criteria', 'id_kategori');
        }

        $this->db->enableForeignKeyChecks();
    }
}