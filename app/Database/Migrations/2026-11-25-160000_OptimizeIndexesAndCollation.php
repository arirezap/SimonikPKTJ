<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Optimasi indeks komposit pada tabel pertumbuhan tinggi (log_kegiatan_harian,
 * log_tugas_tambahan, audit_logs, notifications) serta penyeragaman charset & collation
 * seluruh tabel ke utf8mb4_unicode_ci untuk mencegah galat Illegal mix of collations.
 */
class OptimizeIndexesAndCollation extends Migration
{
    public function up()
    {
        $this->addIndexIfMissing('log_kegiatan_harian', 'idx_lkh_user_status_tgl', '`user_id`, `status`, `tanggal_kegiatan`');
        $this->addIndexIfMissing('log_kegiatan_harian', 'idx_lkh_target_status', '`target_id`, `status`');
        $this->addIndexIfMissing('log_tugas_tambahan', 'idx_ltt_user_status_tgl', '`user_id`, `status`, `tanggal_kegiatan`');
        $this->addIndexIfMissing('audit_logs', 'idx_audit_action_created', '`action`, `created_at`');
        $this->addIndexIfMissing('notifications', 'idx_notif_read_created', '`is_read`, `created_at`');

        $tablesToUnify = [
            'holidays',
            'notifications',
            'audit_logs',
            'user_roles',
            'riwayat_atasan',
            'ref_angkatan',
            'ref_kelas',
            'ref_program_studi',
            'ref_status_taruna',
            'ref_tahun_akademik',
            'ref_wilayah_3tp',
            'master_taruna',
            'pendidikan_taruna',
            'riwayat_pendidikan_taruna',
            'riwayat_status_taruna',
            'alumni_profil',
            'alumni_riwayat_perubahan',
            'dosen_profil',
            'dosen_riwayat_kualifikasi',
            'perusahaan_mitra',
            'lowongan_pekerjaan',
            'lowongan_dokumen',
            'lowongan_sasaran_prodi',
            'taruna_capaian',
            'taruna_nilai_ip',
            'taruna_prestasi',
            'taruna_peserta_prestasi',
            'taruna_pelanggaran',
            'taruna_pelanggaran_pihak',
            'taruna_pelanggaran_tindak_lanjut',
            'taruna_program_beasiswa',
            'taruna_penerima_beasiswa',
            'taruna_beasiswa_pemeringkatan',
            'taruna_beasiswa_hasil_peringkat',
            'tracer_study_bundle',
            'tracer_study_bundle_pertanyaan',
            'tracer_study_periode',
            'tracer_study_periode_pertanyaan',
            'tracer_study_respons',
            'tracer_study_jawaban',
        ];

        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tablesToUnify as $table) {
            if ($this->db->tableExists($table)) {
                $this->db->query("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down()
    {
        // Non-destructive optimization; indexes and utf8mb4_unicode_ci collation remain safe.
    }

    private function addIndexIfMissing(string $table, string $indexName, string $columnsSql): void
    {
        if (!$this->db->tableExists($table)) {
            return;
        }

        $exists = $this->db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName])->getResultArray();
        if (empty($exists)) {
            $this->db->query("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` ({$columnsSql})");
        }
    }
}
