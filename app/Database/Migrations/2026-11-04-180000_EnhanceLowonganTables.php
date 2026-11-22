<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceLowonganTables extends Migration
{
    public function up()
    {
        // 1. Perluas tabel perusahaan_mitra untuk manajemen kontak & verifikasi mitra (LOW-01, LOW-04)
        $mitraCols = [
            'kota' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'sektor',
            ],
            'pic_jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'kontak_person',
            ],
            'email_rekrutmen' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'pic_jabatan',
            ],
            'telepon_rekrutmen' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'email_rekrutmen',
            ],
            'sumber_verifikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'is_verified',
            ],
            'tanggal_pemeriksaan_kontak' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'sumber_verifikasi',
            ],
            'catatan_verifikasi' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'tanggal_pemeriksaan_kontak',
            ],
            'diperiksa_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'catatan_verifikasi',
            ],
        ];

        $mitraAdd = [];
        foreach ($mitraCols as $col => $def) {
            if (!$this->db->fieldExists($col, 'perusahaan_mitra')) {
                $mitraAdd[$col] = $def;
            }
        }
        if (!empty($mitraAdd)) {
            $this->forge->addColumn('perusahaan_mitra', $mitraAdd);
        }

        // 2. Perluas tabel lowongan_pekerjaan untuk kebutuhan rekrutmen, kanal lamaran, checklist, & riwayat (LOW-02 s.d. LOW-08)
        $this->forge->modifyColumn('lowongan_pekerjaan', [
            'tautan_pendaftaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $lowCols = [
            'jenjang_pendidikan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'D-III / D-IV',
                'after'      => 'jenis_pekerjaan',
            ],
            'tahun_lulus_sasaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'jenjang_pendidikan',
            ],
            'jumlah_kebutuhan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'tahun_lulus_sasaran',
            ],
            'pengalaman_minimum' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'persyaratan',
            ],
            'sertifikasi_opsional' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'after'      => 'pengalaman_minimum',
            ],
            'metode_lamaran' => [
                'type'       => 'ENUM',
                'constraint' => ['tautan_eksternal', 'email', 'keduanya'],
                'default'    => 'tautan_eksternal',
                'after'      => 'batas_pendaftaran',
            ],
            'email_lamaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'tautan_pendaftaran',
            ],
            'sumber_informasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'email_lamaran',
            ],
            'tanggal_diterima' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'sumber_informasi',
            ],
            'pic_pengelola' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'tanggal_diterima',
            ],
            'catatan_sumber' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'pic_pengelola',
            ],
            'checklist_verifikasi_json' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'status_validasi',
            ],
            'catatan_pemeriksa' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'checklist_verifikasi_json',
            ],
            'diajukan_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'catatan_pemeriksa',
            ],
            'diperiksa_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'diajukan_oleh',
            ],
            'tanggal_pemeriksaan' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'diperiksa_oleh',
            ],
            'dipublikasi_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'tanggal_pemeriksaan',
            ],
            'tanggal_publikasi' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'dipublikasi_oleh',
            ],
            'alasan_penutupan' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'tanggal_publikasi',
            ],
            'ditutup_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'alasan_penutupan',
            ],
            'tanggal_ditutup' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'ditutup_oleh',
            ],
            'riwayat_proses_json' => [
                'type'  => 'LONGTEXT',
                'null'  => true,
                'after' => 'tanggal_ditutup',
            ],
        ];

        $lowAdd = [];
        foreach ($lowCols as $col => $def) {
            if (!$this->db->fieldExists($col, 'lowongan_pekerjaan')) {
                $lowAdd[$col] = $def;
            }
        }
        if (!empty($lowAdd)) {
            $this->forge->addColumn('lowongan_pekerjaan', $lowAdd);
        }

        $idxLow = $this->db->query("SHOW INDEX FROM lowongan_pekerjaan WHERE Key_name = 'idx_low_valid_pub_deadline'")->getResultArray();
        if (empty($idxLow)) {
            $this->db->query("ALTER TABLE lowongan_pekerjaan ADD INDEX idx_low_valid_pub_deadline (status_validasi, status_publikasi, batas_pendaftaran)");
        }

        // 3. Buat tabel lowongan_dokumen untuk surat mitra, poster, bukti konfirmasi internal, & tautan sumber (LOW-03)
        if (!$this->db->tableExists('lowongan_dokumen')) {
            $this->forge->addField([
                'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'lowongan_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'kategori_dokumen'   => ['type' => 'ENUM', 'constraint' => ['surat_perusahaan', 'poster_rekrutmen', 'bukti_konfirmasi', 'tautan_sumber'], 'default' => 'surat_perusahaan'],
                'judul_dokumen'      => ['type' => 'VARCHAR', 'constraint' => 160],
                'sumber_dokumen'     => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
                'nama_berkas'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'url_sumber'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'tanggal_diterima'   => ['type' => 'DATE', 'null' => true],
                'status_pemeriksaan' => ['type' => 'ENUM', 'constraint' => ['belum_diperiksa', 'sesuai', 'perlu_perbaikan'], 'default' => 'belum_diperiksa'],
                'is_internal'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'catatan'            => ['type' => 'TEXT', 'null' => true],
                'diunggah_oleh'      => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'created_at'         => ['type' => 'DATETIME', 'null' => true],
                'updated_at'         => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['lowongan_id', 'kategori_dokumen', 'is_internal']);
            $this->forge->createTable('lowongan_dokumen', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('lowongan_dokumen')) {
            $this->forge->dropTable('lowongan_dokumen', true);
        }

        $lowCols = [
            'jenjang_pendidikan', 'tahun_lulus_sasaran', 'jumlah_kebutuhan',
            'pengalaman_minimum', 'sertifikasi_opsional', 'metode_lamaran',
            'email_lamaran', 'sumber_informasi', 'tanggal_diterima', 'pic_pengelola',
            'catatan_sumber', 'checklist_verifikasi_json', 'catatan_pemeriksa',
            'diajukan_oleh', 'diperiksa_oleh', 'tanggal_pemeriksaan',
            'dipublikasi_oleh', 'tanggal_publikasi', 'alasan_penutupan',
            'ditutup_oleh', 'tanggal_ditutup', 'riwayat_proses_json',
        ];
        foreach ($lowCols as $col) {
            if ($this->db->fieldExists($col, 'lowongan_pekerjaan')) {
                $this->forge->dropColumn('lowongan_pekerjaan', $col);
            }
        }

        $mitraCols = [
            'kota', 'pic_jabatan', 'email_rekrutmen', 'telepon_rekrutmen',
            'sumber_verifikasi', 'tanggal_pemeriksaan_kontak',
            'catatan_verifikasi', 'diperiksa_oleh',
        ];
        foreach ($mitraCols as $col) {
            if ($this->db->fieldExists($col, 'perusahaan_mitra')) {
                $this->forge->dropColumn('perusahaan_mitra', $col);
            }
        }
    }
}
