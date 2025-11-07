<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class NormalizeLedCriteriaCategory extends Migration
{
    public function up()
    {
        // 1. Tambahkan kolom baru untuk ID
        $this->forge->addColumn('led_criteria', [
            'id_kategori' => [ // Nama kolom foreign key tetap id_kategori (atau bisa Anda ganti id_standar)
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'nama_kriteria',
            ],
        ]);

        // 2. Migrasi data: Isi kolom 'id_kategori' baru berdasarkan nama 'kategori' yang lama
        // INI ADALAH BAGIAN YANG DIPERBAIKI
        $this->db->query(
            "UPDATE led_criteria
             INNER JOIN led_standar ON led_standar.nama_standar = led_criteria.kategori
             SET led_criteria.id_kategori = led_standar.id"
        );

        // 3. Tambahkan Foreign Key Constraint
        // INI JUGA DIPERBAIKI
        $this->forge->addForeignKey('id_kategori', 'led_standar', 'id', 'CASCADE', 'SET NULL');
        
        // 4. Hapus kolom 'kategori' yang lama (berbasis teks)
        $this->forge->dropColumn('led_criteria', 'kategori');
    }

    public function down()
    {
        // 1. Tambahkan kembali kolom 'kategori' (varchar) yang lama
        $this->forge->addColumn('led_criteria', [
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'nama_kriteria',
            ],
        ]);

        // 2. Kembalikan data dari 'id_kategori' ke 'kategori' (INI JUGA DIPERBAIKI)
        $this->db->query(
            "UPDATE led_criteria
             INNER JOIN led_standar ON led_standar.id = led_criteria.id_kategori
             SET led_criteria.kategori = led_standar.nama_standar"
        );

        // 3. Hapus Foreign Key dan kolom 'id_kategori'
        $keys = $this->db->getForeignKeyData('led_criteria');
        foreach ($keys as $key) {
            // INI JUGA DIPERBAIKI
            if ($key->column_name === 'id_kategori' && $key->foreign_table_name === 'led_standar') {
                $this->forge->dropForeignKey('led_criteria', $key->constraint_name);
                break;
            }
        }
        
        $this->forge->dropColumn('led_criteria', 'id_kategori');
    }
}