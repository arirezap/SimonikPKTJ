<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropIdKategoriFromLedCriteria extends Migration
{
    public function up()
    {
        // Pengecekan keamanan: Hanya jalankan jika kolom 'id_kategori' ada
        if ($this->db->fieldExists('id_kategori', 'led_criteria')) {
            
            // 1. Hapus Foreign Key yang mungkin masih terkait dengan 'id_kategori'
            // Kita cari constraint-nya secara dinamis
            $keys = $this->db->getForeignKeyData('led_criteria');
            foreach ($keys as $key) {
                if ($key->column_name === 'id_kategori' && $key->foreign_table_name === 'led_standar') {
                    $this->forge->dropForeignKey('led_criteria', $key->constraint_name);
                    break;
                }
            }

            // 2. Hapus kolom 'id_kategori'
            $this->forge->dropColumn('led_criteria', 'id_kategori');
        }
    }

    public function down()
    {
        // Fungsi ini untuk mengembalikan jika terjadi rollback
        
        // 1. Tambahkan kembali kolomnya
        $this->forge->addColumn('led_criteria', [
            'id_kategori' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'nama_kriteria', // Sesuaikan posisi jika perlu
            ],
        ]);

        // 2. Tambahkan kembali foreign key-nya
        $this->forge->addForeignKey('id_kategori', 'led_standar', 'id', 'CASCADE', 'SET NULL');
    }
}