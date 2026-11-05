<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTarunaCapaianTable extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('taruna_capaian')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'nomor_taruna' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                ],
                'kategori' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 40,
                    'default'    => 'kompetensi_sertifikasi',
                ],
                'nama_capaian' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 180,
                ],
                'tanggal_kegiatan' => [
                    'type' => 'DATE',
                ],
                'tahun_akademik_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'semester' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'unsigned'   => true,
                    'default'    => 1,
                ],
                'penyelenggara' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 160,
                ],
                'tingkat_kegiatan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 40,
                    'default'    => 'kampus',
                ],
                'uraian_capaian' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'hasil_capaian' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 60,
                    'default'    => 'Kompeten',
                ],
                'nilai_angka' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '8,2',
                    'null'       => true,
                ],
                'satuan_nilai' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'null'       => true,
                ],
                'nomor_dokumen' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'unit_pemilik' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 60,
                    'default'    => 'Ketarunaan',
                ],
                'sumber_dokumen' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 60,
                    'default'    => 'Sertifikat',
                ],
                'bukti_berkas' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'ukuran_berkas_kb' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 250,
                ],
                'jumlah_berkas' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'unsigned'   => true,
                    'default'    => 1,
                ],
                'status_validasi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'default'    => 'draf',
                ],
                'catatan_validasi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'diajukan_oleh' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'default'    => 'PIC Ketarunaan',
                ],
                'divalidasi_oleh' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
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
            $this->forge->addKey('nomor_taruna');
            $this->forge->addKey('kategori');
            $this->forge->addKey('tanggal_kegiatan');
            $this->forge->addKey('status_validasi');
            $this->forge->createTable('taruna_capaian', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('taruna_capaian', true);
    }
}

