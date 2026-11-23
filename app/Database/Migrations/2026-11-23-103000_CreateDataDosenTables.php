<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDataDosenTables extends Migration
{
    public function up()
    {
        // 1. Profil Akademik & Kepakaran Dosen (1:1 terhadap users.id — DRY tanpa duplikasi data kepegawaian)
        if (!$this->db->tableExists('dosen_profil')) {
            $this->forge->addField([
                'id'                      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'nidn'                    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'nuptk'                   => ['type' => 'VARCHAR', 'constraint' => 24, 'null' => true],
                'homebase_prodi_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'jabatan_fungsional'      => [
                    'type'       => 'ENUM',
                    'constraint' => ['tenaga_pengajar', 'asisten_ahli', 'lektor', 'lektor_kepala', 'guru_besar'],
                    'default'    => 'tenaga_pengajar',
                ],
                'tmt_jabatan_fungsional'  => ['type' => 'DATE', 'null' => true],
                'pendidikan_terakhir'     => [
                    'type'       => 'ENUM',
                    'constraint' => ['S1', 'S2', 'S3', 'spesialis'],
                    'default'    => 'S2',
                ],
                'bidang_keilmuan'         => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'kepakaran_tags'          => ['type' => 'TEXT', 'null' => true],
                'status_serdos'           => [
                    'type'       => 'ENUM',
                    'constraint' => ['belum', 'proses', 'sudah'],
                    'default'    => 'belum',
                ],
                'tahun_serdos'            => ['type' => 'SMALLINT', 'constraint' => 4, 'unsigned' => true, 'null' => true],
                'nomor_sertifikat_serdos' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
                'status_ikatan'           => [
                    'type'       => 'ENUM',
                    'constraint' => ['dosen_tetap', 'dosen_luar_biasa', 'tugas_belajar', 'izin_belajar'],
                    'default'    => 'dosen_tetap',
                ],
                'is_siap_ditugaskan'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'sinta_id'                => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
                'google_scholar_url'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'scopus_id'               => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
                'catatan_pimpinan'        => ['type' => 'TEXT', 'null' => true],
                'updated_by'              => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'created_at'              => ['type' => 'DATETIME', 'null' => true],
                'updated_at'              => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('user_id', 'uq_dosen_profil_user');
            $this->forge->addKey(['homebase_prodi_id', 'jabatan_fungsional'], false, false, 'idx_dosen_prodi_jafung');
            $this->forge->addKey(['pendidikan_terakhir', 'status_serdos', 'is_siap_ditugaskan'], false, false, 'idx_dosen_profiling');
            $this->forge->createTable('dosen_profil', true);
        }

        // 2. Riwayat Pendidikan, Sertifikasi Kompetensi, Pelatihan, Penugasan Strategis, & Penghargaan Dosen
        if (!$this->db->tableExists('dosen_riwayat_kualifikasi')) {
            $this->forge->addField([
                'id'                      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'dosen_profil_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'kategori'                => [
                    'type'       => 'ENUM',
                    'constraint' => ['pendidikan', 'sertifikasi_kompetensi', 'pelatihan_strategis', 'penugasan_khusus', 'penghargaan'],
                    'default'    => 'pendidikan',
                ],
                'judul'                   => ['type' => 'VARCHAR', 'constraint' => 180],
                'institusi_penyelenggara' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'tahun_mulai'             => ['type' => 'SMALLINT', 'constraint' => 4, 'unsigned' => true, 'null' => true],
                'tahun_selesai'           => ['type' => 'SMALLINT', 'constraint' => 4, 'unsigned' => true, 'null' => true],
                'nomor_dokumen'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'tautan_bukti'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'keterangan'              => ['type' => 'TEXT', 'null' => true],
                'status_verifikasi'       => ['type' => 'ENUM', 'constraint' => ['terverifikasi', 'menunggu'], 'default' => 'terverifikasi'],
                'created_by'              => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'created_at'              => ['type' => 'DATETIME', 'null' => true],
                'updated_at'              => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['dosen_profil_id', 'kategori'], false, false, 'idx_dosen_kual_profil_kat');
            $this->forge->createTable('dosen_riwayat_kualifikasi', true);
        }

        // 3. Sinkronisasi Awal Otomatis: Petakan pegawai berjabatan dosen dari tabel users ke dosen_profil
        $prodiMap = [];
        if ($this->db->tableExists('ref_program_studi')) {
            foreach ($this->db->table('ref_program_studi')->select('id, kode_prodi')->get()->getResultArray() as $pr) {
                $prodiMap[strtoupper(trim((string) $pr['kode_prodi']))] = (int) $pr['id'];
            }
        }

        $dosenUsers = $this->db->table('users')
            ->select('id, nama_lengkap, jabatan, unit, role, status_pegawai')
            ->whereNotIn('role', ['taruna', 'alumni'])
            ->groupStart()
                ->like('jabatan', 'Dosen')
                ->orLike('jabatan', 'Lektor')
                ->orLike('jabatan', 'Asisten Ahli')
                ->orLike('jabatan', 'Guru Besar')
            ->groupEnd()
            ->get()
            ->getResultArray();

        $now = date('Y-m-d H:i:s');
        foreach ($dosenUsers as $du) {
            $uid    = (int) $du['id'];
            $exists = $this->db->table('dosen_profil')->where('user_id', $uid)->countAllResults();
            if ($exists > 0) {
                continue;
            }

            $jabLow  = strtolower((string) ($du['jabatan'] ?? ''));
            $unitUp  = strtoupper((string) ($du['unit'] ?? ''));
            $roleLow = strtolower((string) ($du['role'] ?? ''));

            $jafung = 'tenaga_pengajar';
            if (strpos($jabLow, 'guru besar') !== false) {
                $jafung = 'guru_besar';
            } elseif (strpos($jabLow, 'lektor kepala') !== false) {
                $jafung = 'lektor_kepala';
            } elseif (strpos($jabLow, 'lektor') !== false) {
                $jafung = 'lektor';
            } elseif (strpos($jabLow, 'asisten ahli') !== false) {
                $jafung = 'asisten_ahli';
            }

            $homebaseId = null;
            if (strpos($unitUp, 'RSTJ') !== false && isset($prodiMap['RSTJ'])) {
                $homebaseId = $prodiMap['RSTJ'];
            } elseif (strpos($unitUp, 'TRO') !== false && isset($prodiMap['TRO'])) {
                $homebaseId = $prodiMap['TRO'];
            } elseif ((strpos($unitUp, 'PRODI TO') !== false || strpos($unitUp, 'OTOMOTIF') !== false) && isset($prodiMap['TO'])) {
                $homebaseId = $prodiMap['TO'];
            }

            $isTubel = ($roleLow === 'tugas_belajar' || strpos($jabLow, 'tugas belajar') !== false);
            $isAktif = (strtolower((string) ($du['status_pegawai'] ?? 'aktif')) === 'aktif');

            $this->db->table('dosen_profil')->insert([
                'user_id'            => $uid,
                'homebase_prodi_id'  => $homebaseId,
                'jabatan_fungsional' => $jafung,
                'pendidikan_terakhir'=> 'S2',
                'status_serdos'      => 'belum',
                'status_ikatan'      => $isTubel ? 'tugas_belajar' : 'dosen_tetap',
                'is_siap_ditugaskan' => ($isAktif && !$isTubel) ? 1 : 0,
                'updated_by'         => 'Sinkronisasi Awal ECC',
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('dosen_riwayat_kualifikasi', true);
        $this->forge->dropTable('dosen_profil', true);
    }
}
