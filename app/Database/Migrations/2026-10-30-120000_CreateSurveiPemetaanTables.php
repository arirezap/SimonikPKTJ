<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSurveiPemetaanTables extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Tabel survei_pemetaan_respon
        if (!$db->tableExists('survei_pemetaan_respon')) {
            $db->query("
                CREATE TABLE `survei_pemetaan_respon` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                  `user_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Terisi users.id untuk pemilik akun ECC; NULL untuk Non-ASN tanpa akun',
                  `nomor_identitas` VARCHAR(32) NOT NULL COMMENT 'NIP 18 digit (ASN) atau NIK KTP 16 digit (Non-ASN) tanpa spasi',
                  `tipe_responden` ENUM('akun_ecc', 'non_akun') NOT NULL DEFAULT 'akun_ecc',
                  `tahun_survei` YEAR NOT NULL DEFAULT 2026 COMMENT 'Mendukung evaluasi pemetaan tahunan',
                  `status` ENUM('draft', 'submitted') NOT NULL DEFAULT 'draft',
                  `last_step` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Bagian terakhir yang dibuka (1-10)',
                  `total_answered` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                  `total_applicable` SMALLINT UNSIGNED NOT NULL DEFAULT 83 COMMENT 'Jumlah pertanyaan efektif sesuai cabang kondisional',
                  `progress_percent` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                  `resume_token_hash` CHAR(64) NULL DEFAULT NULL COMMENT 'SHA-256 hash token kuki perangkat untuk melanjutkan pengisian',
                  `filled_via_auth` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 jika ASN telah memverifikasi Kata Sandi akun ECC',
                  `pending_profile_sync` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 jika ASN mengisi profil kosong via cookie & menunggu sinkron ke users',
                  `is_valid` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = Valid; 0 = Diarsipkan Kabag KUK',
                  `snapshot_nama` VARCHAR(255) NULL DEFAULT NULL,
                  `snapshot_nip` VARCHAR(50) NULL DEFAULT NULL,
                  `snapshot_status_kepegawaian` VARCHAR(50) NULL DEFAULT NULL COMMENT 'PNS / PPPK / Non-ASN / Lainnya',
                  `snapshot_pangkat` VARCHAR(100) NULL DEFAULT NULL,
                  `snapshot_jabatan` VARCHAR(100) NULL DEFAULT NULL,
                  `snapshot_unit` VARCHAR(100) NULL DEFAULT NULL,
                  `snapshot_unit_id` INT UNSIGNED NULL DEFAULT NULL,
                  `submitted_at` DATETIME NULL DEFAULT NULL,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `idx_spr_identitas_tahun` (`nomor_identitas`, `tahun_survei`),
                  UNIQUE KEY `idx_spr_user_tahun` (`user_id`, `tahun_survei`),
                  KEY `idx_spr_status_valid` (`tahun_survei`, `is_valid`, `status`, `tipe_responden`),
                  KEY `idx_spr_unit` (`snapshot_unit_id`),
                  KEY `idx_spr_pending_sync` (`pending_profile_sync`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        }

        // 2. Tabel survei_pemetaan_jawaban
        if (!$db->tableExists('survei_pemetaan_jawaban')) {
            $db->query("
                CREATE TABLE `survei_pemetaan_jawaban` (
                  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                  `respon_id` INT UNSIGNED NOT NULL,
                  `user_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'NULL untuk responden Non-ASN tanpa akun ECC',
                  `nomor_pertanyaan` SMALLINT UNSIGNED NOT NULL COMMENT 'Nomor 1 s.d. 83',
                  `kode_bagian` VARCHAR(15) NOT NULL COMMENT 'BAGIAN_1 s.d. BAGIAN_10',
                  `jawaban_teks` LONGTEXT NULL DEFAULT NULL COMMENT 'Teks jawaban tunggal atau esai terbuka',
                  `jawaban_angka` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Usia, masa kerja tahun, skor Likert/skala 1-5',
                  `jawaban_pilihan` LONGTEXT NULL DEFAULT NULL COMMENT 'JSON array opsi standar untuk checkbox multi-select',
                  `jawaban_lainnya` TEXT NULL DEFAULT NULL COMMENT 'Teks tambahan saat memilih opsi Lainnya atau penjelasan opsional No. 19',
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `idx_spj_respon_no` (`respon_id`, `nomor_pertanyaan`),
                  KEY `idx_spj_user` (`user_id`),
                  KEY `idx_spj_nomor` (`nomor_pertanyaan`),
                  CONSTRAINT `fk_spj_respon` FOREIGN KEY (`respon_id`) REFERENCES `survei_pemetaan_respon` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        }

        // 3. Tabel survei_pemetaan_delegasi
        if (!$db->tableExists('survei_pemetaan_delegasi')) {
            $db->query("
                CREATE TABLE `survei_pemetaan_delegasi` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                  `user_id` INT UNSIGNED NOT NULL COMMENT 'ID staf pemilik akun ECC yang diberi akses hasil survei',
                  `delegated_by` INT UNSIGNED NOT NULL COMMENT 'ID pejabat yang memberi delegasi (Kabag KUK / Direktur / Admin)',
                  `can_export` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = Diizinkan mengunduh Excel rekapitulasi',
                  `catatan` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Keterangan penugasan singkat (opsional)',
                  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `idx_spd_user` (`user_id`),
                  KEY `idx_spd_active` (`is_active`, `user_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        }

        // 4. Pengaturan default status buka/tutup periode survei di tabel settings
        if ($db->tableExists('settings')) {
            $exists = $db->table('settings')
                ->where('setting_key', 'enable_survei_pemetaan')
                ->countAllResults();

            if ($exists === 0) {
                $db->table('settings')->insert([
                    'setting_key'   => 'enable_survei_pemetaan',
                    'setting_name'  => 'Status Penerimaan Survei Pemetaan SDM',
                    'setting_value' => '1',
                    'description'   => '1 = Tautan survei pemetaan SDM dibuka; 0 = Tautan survei ditutup.',
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }

            $existsYear = $db->table('settings')
                ->where('setting_key', 'tahun_aktif_survei_pemetaan')
                ->countAllResults();

            if ($existsYear === 0) {
                $db->table('settings')->insert([
                    'setting_key'   => 'tahun_aktif_survei_pemetaan',
                    'setting_name'  => 'Tahun Aktif Survei Pemetaan SDM',
                    'setting_value' => date('Y'),
                    'description'   => 'Tahun pelaksanaan aktif untuk tautan Survei Pemetaan SDM.',
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('survei_pemetaan_jawaban', true);
        $this->forge->dropTable('survei_pemetaan_respon', true);
        $this->forge->dropTable('survei_pemetaan_delegasi', true);
    }
}

