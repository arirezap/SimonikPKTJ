<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEcc2Tables extends Migration
{
    public function up()
    {
        // 1. ref_program_studi
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kode_prodi' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'nama_prodi' => ['type' => 'VARCHAR', 'constraint' => 150],
            'jenjang' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'D-IV'],
            'is_aktif' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ref_program_studi', true);

        // 2. ref_angkatan
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tahun_masuk' => ['type' => 'INT', 'constraint' => 11, 'unique' => true],
            'perkiraan_lulus' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'is_aktif' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ref_angkatan', true);

        // 3. ref_tahun_akademik
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kode_tahun' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'tahun' => ['type' => 'INT', 'constraint' => 11],
            'semester' => ['type' => 'ENUM', 'constraint' => ['ganjil', 'genap'], 'default' => 'ganjil'],
            'tanggal_mulai' => ['type' => 'DATE', 'null' => true],
            'tanggal_selesai' => ['type' => 'DATE', 'null' => true],
            'is_aktif' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ref_tahun_akademik', true);

        // 4. ref_kelas
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'program_studi_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'angkatan_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tingkat' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'nama_kelas' => ['type' => 'VARCHAR', 'constraint' => 50],
            'is_aktif' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['program_studi_id', 'angkatan_id', 'nama_kelas']);
        $this->forge->createTable('ref_kelas', true);

        // 5. ref_status_taruna
        $this->forge->addField([
            'kode_status' => ['type' => 'VARCHAR', 'constraint' => 30],
            'nama_status' => ['type' => 'VARCHAR', 'constraint' => 50],
            'kelompok_status' => ['type' => 'VARCHAR', 'constraint' => 50],
            'badge_color' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'primary'],
        ]);
        $this->forge->addKey('kode_status', true);
        $this->forge->createTable('ref_status_taruna', true);

        // 6. master_taruna
        $this->forge->addField([
            'nomor_taruna' => ['type' => 'VARCHAR', 'constraint' => 30],
            'nama_lengkap' => ['type' => 'VARCHAR', 'constraint' => 150],
            'nik' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_panggilan' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'jenis_kelamin' => ['type' => 'ENUM', 'constraint' => ['L', 'P'], 'default' => 'L'],
            'tempat_lahir' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tanggal_lahir' => ['type' => 'DATE', 'null' => true],
            'agama' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'nomor_hp' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'alamat_domisili' => ['type' => 'TEXT', 'null' => true],
            'alamat_asal' => ['type' => 'TEXT', 'null' => true],
            'nama_wali' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'hubungan_wali' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'nomor_hp_wali' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'status_terkini' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'aktif'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('nomor_taruna', true);
        $this->forge->addKey('status_terkini');
        $this->forge->createTable('master_taruna', true);

        // 7. pendidikan_taruna
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nomor_taruna' => ['type' => 'VARCHAR', 'constraint' => 30, 'unique' => true],
            'program_studi_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'angkatan_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tingkat' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'semester_aktif' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'jalur_penerimaan' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Pola Pembibitan'],
            'asal_formasi' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tahun_akademik_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('program_studi_id');
        $this->forge->addKey('angkatan_id');
        $this->forge->createTable('pendidikan_taruna', true);

        // 8. riwayat_status_taruna
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nomor_taruna' => ['type' => 'VARCHAR', 'constraint' => 30],
            'kode_status' => ['type' => 'VARCHAR', 'constraint' => 30],
            'mulai_berlaku' => ['type' => 'DATE'],
            'akhir_berlaku' => ['type' => 'DATE', 'null' => true],
            'rencana_kembali' => ['type' => 'DATE', 'null' => true],
            'alasan_perubahan' => ['type' => 'TEXT', 'null' => true],
            'nomor_keputusan' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tanggal_keputusan' => ['type' => 'DATE', 'null' => true],
            'bukti_dokumen' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status_validasi' => ['type' => 'ENUM', 'constraint' => ['draf', 'diajukan', 'perlu_perbaikan', 'tervalidasi'], 'default' => 'tervalidasi'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['nomor_taruna', 'mulai_berlaku']);
        $this->forge->addKey('kode_status');
        $this->forge->createTable('riwayat_status_taruna', true);

        // 9. riwayat_pendidikan_taruna
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nomor_taruna' => ['type' => 'VARCHAR', 'constraint' => 30],
            'program_studi_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tingkat' => ['type' => 'INT', 'constraint' => 11],
            'semester' => ['type' => 'INT', 'constraint' => 11],
            'tahun_akademik_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'mulai_berlaku' => ['type' => 'DATE'],
            'akhir_berlaku' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['nomor_taruna', 'mulai_berlaku']);
        $this->forge->createTable('riwayat_pendidikan_taruna', true);

        // 10. taruna_prestasi
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_prestasi' => ['type' => 'VARCHAR', 'constraint' => 200],
            'jenis_prestasi' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'akademik'],
            'bidang_prestasi' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tingkat_prestasi' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'nasional'],
            'peringkat' => ['type' => 'VARCHAR', 'constraint' => 50],
            'penyelenggara' => ['type' => 'VARCHAR', 'constraint' => 150],
            'tanggal_mulai' => ['type' => 'DATE', 'null' => true],
            'tanggal_selesai' => ['type' => 'DATE', 'null' => true],
            'jenis_keikutsertaan' => ['type' => 'ENUM', 'constraint' => ['individu', 'tim'], 'default' => 'individu'],
            'nama_tim' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'nomor_sertifikat' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'bukti_berkas' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status_validasi' => ['type' => 'ENUM', 'constraint' => ['draf', 'diajukan', 'perlu_perbaikan', 'tervalidasi'], 'default' => 'tervalidasi'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('taruna_prestasi', true);

        // 11. taruna_peserta_prestasi
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'prestasi_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nomor_taruna' => ['type' => 'VARCHAR', 'constraint' => 30],
            'peran' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Anggota'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['prestasi_id', 'nomor_taruna']);
        $this->forge->createTable('taruna_peserta_prestasi', true);

        // 12. taruna_pelanggaran
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nomor_perkara' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'nomor_taruna' => ['type' => 'VARCHAR', 'constraint' => 30],
            'tanggal_kejadian' => ['type' => 'DATETIME'],
            'lokasi' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'uraian' => ['type' => 'TEXT', 'null' => true],
            'jenis_pelanggaran' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tingkat_pelanggaran' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'sedang'],
            'pelapor' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status_penetapan' => ['type' => 'ENUM', 'constraint' => ['dugaan', 'terbukti', 'tidak_terbukti', 'dibatalkan'], 'default' => 'dugaan'],
            'status_penyelesaian' => ['type' => 'ENUM', 'constraint' => ['proses', 'selesai', 'banding'], 'default' => 'proses'],
            'sanksi' => ['type' => 'TEXT', 'null' => true],
            'bukti_berkas' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status_validasi' => ['type' => 'ENUM', 'constraint' => ['draf', 'diajukan', 'perlu_perbaikan', 'tervalidasi'], 'default' => 'tervalidasi'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('nomor_taruna');
        $this->forge->addKey('status_penetapan');
        $this->forge->createTable('taruna_pelanggaran', true);

        // 13. taruna_program_beasiswa
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_program' => ['type' => 'VARCHAR', 'constraint' => 150],
            'jenis_beasiswa' => ['type' => 'ENUM', 'constraint' => ['apresiasi', 'prestasi', 'daerah_3tp', 'tidak_mampu']],
            'pemberi' => ['type' => 'VARCHAR', 'constraint' => 150],
            'tahun' => ['type' => 'INT', 'constraint' => 11],
            'periode_mulai' => ['type' => 'DATE', 'null' => true],
            'periode_selesai' => ['type' => 'DATE', 'null' => true],
            'kuota' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'status_program' => ['type' => 'ENUM', 'constraint' => ['dibuka', 'seleksi', 'berjalan', 'selesai'], 'default' => 'berjalan'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('taruna_program_beasiswa', true);

        // 14. taruna_penerima_beasiswa
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'program_beasiswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nomor_taruna' => ['type' => 'VARCHAR', 'constraint' => 30],
            'status_pengajuan' => ['type' => 'ENUM', 'constraint' => ['diajukan', 'diverifikasi', 'diterima', 'ditolak'], 'default' => 'diterima'],
            'status_penetapan' => ['type' => 'ENUM', 'constraint' => ['kandidat', 'ditetapkan', 'dibatalkan'], 'default' => 'ditetapkan'],
            'tanggal_penetapan' => ['type' => 'DATE', 'null' => true],
            'nomor_keputusan' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status_validasi' => ['type' => 'ENUM', 'constraint' => ['draf', 'diajukan', 'perlu_perbaikan', 'tervalidasi'], 'default' => 'tervalidasi'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['program_beasiswa_id', 'nomor_taruna']);
        $this->forge->createTable('taruna_penerima_beasiswa', true);

        // 15. perusahaan_mitra
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_perusahaan' => ['type' => 'VARCHAR', 'constraint' => 150],
            'sektor' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'alamat' => ['type' => 'TEXT', 'null' => true],
            'website' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'kontak_person' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'is_verified' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('perusahaan_mitra', true);

        // 16. lowongan_pekerjaan
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'perusahaan_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'judul_lowongan' => ['type' => 'VARCHAR', 'constraint' => 150],
            'jenis_pekerjaan' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Tetap'],
            'lokasi' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'deskripsi' => ['type' => 'TEXT', 'null' => true],
            'persyaratan' => ['type' => 'TEXT', 'null' => true],
            'batas_pendaftaran' => ['type' => 'DATETIME'],
            'tautan_pendaftaran' => ['type' => 'VARCHAR', 'constraint' => 255],
            'status_publikasi' => ['type' => 'ENUM', 'constraint' => ['draf', 'terbit', 'ditutup'], 'default' => 'terbit'],
            'status_validasi' => ['type' => 'ENUM', 'constraint' => ['draf', 'diajukan', 'perlu_perbaikan', 'tervalidasi'], 'default' => 'tervalidasi'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('lowongan_pekerjaan', true);

        // 17. lowongan_sasaran_prodi
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'lowongan_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'program_studi_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['lowongan_id', 'program_studi_id']);
        $this->forge->createTable('lowongan_sasaran_prodi', true);

        // 18. alumni_profil
        $this->forge->addField([
            'nomor_taruna' => ['type' => 'VARCHAR', 'constraint' => 30],
            'tanggal_lulus' => ['type' => 'DATE'],
            'nomor_keputusan_lulus' => ['type' => 'VARCHAR', 'constraint' => 100],
            'tahun_lulus' => ['type' => 'INT', 'constraint' => 11],
            'pekerjaan_saat_ini' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'instansi_saat_ini' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'kontak_terakhir' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('nomor_taruna', true);
        $this->forge->createTable('alumni_profil', true);
    }

    public function down()
    {
        $this->forge->dropTable('alumni_profil', true);
        $this->forge->dropTable('lowongan_sasaran_prodi', true);
        $this->forge->dropTable('lowongan_pekerjaan', true);
        $this->forge->dropTable('perusahaan_mitra', true);
        $this->forge->dropTable('taruna_penerima_beasiswa', true);
        $this->forge->dropTable('taruna_program_beasiswa', true);
        $this->forge->dropTable('taruna_pelanggaran', true);
        $this->forge->dropTable('taruna_peserta_prestasi', true);
        $this->forge->dropTable('taruna_prestasi', true);
        $this->forge->dropTable('riwayat_pendidikan_taruna', true);
        $this->forge->dropTable('riwayat_status_taruna', true);
        $this->forge->dropTable('pendidikan_taruna', true);
        $this->forge->dropTable('master_taruna', true);
        $this->forge->dropTable('ref_status_taruna', true);
        $this->forge->dropTable('ref_kelas', true);
        $this->forge->dropTable('ref_tahun_akademik', true);
        $this->forge->dropTable('ref_angkatan', true);
        $this->forge->dropTable('ref_program_studi', true);
    }
}

