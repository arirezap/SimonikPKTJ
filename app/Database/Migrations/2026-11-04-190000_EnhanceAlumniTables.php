<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceAlumniTables extends Migration
{
    public function up()
    {
        // 1. Perluas tabel alumni_profil untuk profil dasar alumni, status aktif/nonaktif koreksi, & sumber konfirmasi (ALM-01..03)
        $alumniCols = [
            'email_alumni' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'kontak_terakhir',
            ],
            'telepon_alumni' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'email_alumni',
            ],
            'kota_domisili' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'telepon_alumni',
            ],
            'alamat_domisili' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'kota_domisili',
            ],
            'status_profil' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif_koreksi'],
                'default'    => 'aktif',
                'after'      => 'alamat_domisili',
            ],
            'sumber_konfirmasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
                'after'      => 'status_profil',
            ],
            'tanggal_pembaruan_kontak' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'sumber_konfirmasi',
            ],
            'dicatat_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'tanggal_pembaruan_kontak',
            ],
            'divalidasi_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'dicatat_oleh',
            ],
            'tanggal_validasi' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'divalidasi_oleh',
            ],
            'catatan_profil' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'tanggal_validasi',
            ],
        ];

        $toAdd = [];
        foreach ($alumniCols as $col => $def) {
            if (!$this->db->fieldExists($col, 'alumni_profil')) {
                $toAdd[$col] = $def;
            }
        }
        if (!empty($toAdd)) {
            $this->forge->addColumn('alumni_profil', $toAdd);
        }

        $idxAlumni = $this->db->query("SHOW INDEX FROM alumni_profil WHERE Key_name = 'idx_alumni_status_tahun'")->getResultArray();
        if (empty($idxAlumni)) {
            $this->db->query('ALTER TABLE alumni_profil ADD INDEX idx_alumni_status_tahun (status_profil, tahun_lulus)');
        }

        // 2. Buat tabel alumni_riwayat_perubahan untuk jejak pembaruan kontak & koreksi kelulusan terkendali (ALM-02, ALM-03, UAT-17)
        if (!$this->db->tableExists('alumni_riwayat_perubahan')) {
            $this->forge->addField([
                'id'                      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'nomor_taruna'            => ['type' => 'VARCHAR', 'constraint' => 30],
                'jenis_kejadian'          => ['type' => 'ENUM', 'constraint' => ['kelulusan_awal', 'pembaruan_kontak', 'ralat_sk', 'batal_lulus'], 'default' => 'pembaruan_kontak'],
                'judul_ringkas'           => ['type' => 'VARCHAR', 'constraint' => 180],
                'data_sebelum_json'       => ['type' => 'LONGTEXT', 'null' => true],
                'data_sesudah_json'       => ['type' => 'LONGTEXT', 'null' => true],
                'tanggal_lulus_baru'      => ['type' => 'DATE', 'null' => true],
                'nomor_keputusan_baru'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'status_taruna_pengganti' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'sumber_konfirmasi'       => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
                'tanggal_kejadian'        => ['type' => 'DATE'],
                'alasan_perubahan'        => ['type' => 'TEXT', 'null' => true],
                'dampak_ringkasan'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'status_validasi'         => ['type' => 'ENUM', 'constraint' => ['diajukan', 'tervalidasi', 'ditolak'], 'default' => 'tervalidasi'],
                'catatan_validator'       => ['type' => 'TEXT', 'null' => true],
                'diajukan_oleh'           => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'peran_pencatat'          => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
                'divalidasi_oleh'         => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'tanggal_validasi'        => ['type' => 'DATE', 'null' => true],
                'created_at'              => ['type' => 'DATETIME', 'null' => true],
                'updated_at'              => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['nomor_taruna', 'status_validasi']);
            $this->forge->addKey(['jenis_kejadian', 'tanggal_kejadian']);
            $this->forge->createTable('alumni_riwayat_perubahan', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('alumni_riwayat_perubahan')) {
            $this->forge->dropTable('alumni_riwayat_perubahan', true);
        }

        $alumniCols = [
            'email_alumni',
            'telepon_alumni',
            'kota_domisili',
            'alamat_domisili',
            'status_profil',
            'sumber_konfirmasi',
            'tanggal_pembaruan_kontak',
            'dicatat_oleh',
            'divalidasi_oleh',
            'tanggal_validasi',
            'catatan_profil',
        ];
        foreach ($alumniCols as $col) {
            if ($this->db->fieldExists($col, 'alumni_profil')) {
                $this->forge->dropColumn('alumni_profil', $col);
            }
        }
    }
}
