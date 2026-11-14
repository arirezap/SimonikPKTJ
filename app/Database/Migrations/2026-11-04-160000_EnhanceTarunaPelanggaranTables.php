<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceTarunaPelanggaranTables extends Migration
{
    public function up()
    {
        $fieldsToAdd = [];

        $columns = [
            'unit_pemilik' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Ketarunaan',
                'after'      => 'pelapor',
            ],
            'sumber_data' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Laporan petugas',
                'after'      => 'unit_pemilik',
            ],
            'tingkat_kerahasiaan' => [
                'type'       => 'ENUM',
                'constraint' => ['terbatas', 'sangat_terbatas'],
                'default'    => 'terbatas',
                'after'      => 'sumber_data',
            ],
            'tanggal_pemeriksaan' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'tingkat_kerahasiaan',
            ],
            'pemeriksa' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'tanggal_pemeriksaan',
            ],
            'dasar_peraturan' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'after'      => 'pemeriksa',
            ],
            'ringkasan_klarifikasi' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'dasar_peraturan',
            ],
            'hasil_pemeriksaan' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'ringkasan_klarifikasi',
            ],
            'berkas_bap' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'hasil_pemeriksaan',
            ],
            'jenis_sanksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'sanksi',
            ],
            'nomor_keputusan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'jenis_sanksi',
            ],
            'tanggal_keputusan' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'nomor_keputusan',
            ],
            'tindak_lanjut' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'tanggal_keputusan',
            ],
            'tanggal_selesai' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'tindak_lanjut',
            ],
            'pic_tindak_lanjut' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'tanggal_selesai',
            ],
            'status_pemantauan' => [
                'type'       => 'ENUM',
                'constraint' => ['proses', 'selesai', 'banding'],
                'null'       => true,
                'after'      => 'pic_tindak_lanjut',
            ],
            'catatan_pemantauan' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'status_pemantauan',
            ],
            'dokumen_bukti_json' => [
                'type'  => 'LONGTEXT',
                'null'  => true,
                'after' => 'bukti_berkas',
            ],
            'diajukan_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'PIC Ketarunaan',
                'after'      => 'status_validasi',
            ],
            'divalidasi_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'diajukan_oleh',
            ],
            'tanggal_validasi' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'divalidasi_oleh',
            ],
            'catatan_validasi' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'tanggal_validasi',
            ],
            'riwayat_validasi_json' => [
                'type'  => 'LONGTEXT',
                'null'  => true,
                'after' => 'catatan_validasi',
            ],
        ];

        foreach ($columns as $colName => $definition) {
            if (!$this->db->fieldExists($colName, 'taruna_pelanggaran')) {
                $fieldsToAdd[$colName] = $definition;
            }
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('taruna_pelanggaran', $fieldsToAdd);
        }

        $indexes = [
            'idx_pel_validasi'     => 'status_validasi',
            'idx_pel_penyelesaian' => 'status_penyelesaian',
            'idx_pel_tanggal'      => 'tanggal_kejadian',
        ];
        foreach ($indexes as $idxName => $colName) {
            $exists = $this->db->query("SHOW INDEX FROM taruna_pelanggaran WHERE Key_name = ?", [$idxName])->getResultArray();
            if (empty($exists)) {
                $this->db->query("ALTER TABLE taruna_pelanggaran ADD INDEX {$idxName} ({$colName})");
            }
        }

        if (!$this->db->tableExists('taruna_pelanggaran_pihak')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'pelanggaran_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'nomor_taruna' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'null'       => true,
                ],
                'nama_pihak' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'jenis_pihak' => [
                    'type'       => 'ENUM',
                    'constraint' => ['taruna', 'eksternal'],
                    'default'    => 'taruna',
                ],
                'peran' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 80,
                    'default'    => 'Terlapor',
                ],
                'keterangan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('pelanggaran_id');
            $this->forge->addKey('nomor_taruna');
            $this->forge->createTable('taruna_pelanggaran_pihak', true);
        }

        if (!$this->db->tableExists('taruna_pelanggaran_tindak_lanjut')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'pelanggaran_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'tanggal_tindakan' => [
                    'type' => 'DATE',
                ],
                'jenis_tindakan' => [
                    'type'       => 'ENUM',
                    'constraint' => ['penetapan', 'pemantauan', 'banding', 'selesai'],
                    'default'    => 'pemantauan',
                ],
                'status_penetapan_saat_itu' => [
                    'type'       => 'ENUM',
                    'constraint' => ['dugaan', 'terbukti', 'tidak_terbukti', 'dibatalkan'],
                    'default'    => 'dugaan',
                ],
                'status_penyelesaian_saat_itu' => [
                    'type'       => 'ENUM',
                    'constraint' => ['proses', 'selesai', 'banding'],
                    'default'    => 'proses',
                ],
                'nomor_dasar' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'sanksi_tindakan' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'pic' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 120,
                    'null'       => true,
                ],
                'target_selesai' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'berkas_pendukung' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'dicatat_oleh' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('pelanggaran_id');
            $this->forge->createTable('taruna_pelanggaran_tindak_lanjut', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('taruna_pelanggaran_tindak_lanjut')) {
            $this->forge->dropTable('taruna_pelanggaran_tindak_lanjut', true);
        }
        if ($this->db->tableExists('taruna_pelanggaran_pihak')) {
            $this->forge->dropTable('taruna_pelanggaran_pihak', true);
        }

        foreach (['idx_pel_validasi', 'idx_pel_penyelesaian', 'idx_pel_tanggal'] as $idxName) {
            $exists = $this->db->query("SHOW INDEX FROM taruna_pelanggaran WHERE Key_name = ?", [$idxName])->getResultArray();
            if (!empty($exists)) {
                $this->db->query("ALTER TABLE taruna_pelanggaran DROP INDEX {$idxName}");
            }
        }

        $cols = [
            'unit_pemilik', 'sumber_data', 'tingkat_kerahasiaan',
            'tanggal_pemeriksaan', 'pemeriksa', 'dasar_peraturan',
            'ringkasan_klarifikasi', 'hasil_pemeriksaan', 'berkas_bap',
            'jenis_sanksi', 'nomor_keputusan', 'tanggal_keputusan',
            'tindak_lanjut', 'tanggal_selesai', 'pic_tindak_lanjut',
            'status_pemantauan', 'catatan_pemantauan', 'dokumen_bukti_json',
            'diajukan_oleh', 'divalidasi_oleh', 'tanggal_validasi',
            'catatan_validasi', 'riwayat_validasi_json',
        ];
        foreach ($cols as $col) {
            if ($this->db->fieldExists($col, 'taruna_pelanggaran')) {
                $this->forge->dropColumn('taruna_pelanggaran', $col);
            }
        }
    }
}
