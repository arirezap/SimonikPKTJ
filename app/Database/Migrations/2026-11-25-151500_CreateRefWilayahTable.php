<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel referensi wilayah administrasi pemerintahan Indonesia (Provinsi & Kabupaten/Kota)
 * mengacu pada dokumentasi dan dataset resmi https://github.com/cahyadsn/wilayah
 * (Kepmendagri Tahun 2025, MIT License).
 *
 * Struktur kolom (kode VARCHAR(13), nama VARCHAR(100)) dipertahankan identik dengan
 * skema cahyadsn/wilayah agar kompatibel bila suatu saat Level 3/4 (Kecamatan/Desa) diimpor.
 */
class CreateRefWilayahTable extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('ref_wilayah')) {
            $this->forge->addField([
                'kode' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 13,
                    'null'       => false,
                ],
                'nama' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => false,
                ],
            ]);
            $this->forge->addKey('kode', true);
            $this->forge->addKey('nama', false, false, 'idx_ref_wilayah_nama');
            $this->forge->createTable('ref_wilayah', true, [
                'ENGINE'  => 'InnoDB',
                'CHARSET' => 'utf8mb4',
                'COLLATE' => 'utf8mb4_unicode_ci',
            ]);
        }

        // Seed idempotent 38 Provinsi + 514 Kabupaten/Kota (552 baris resmi Kepmendagri 2025)
        $dataFile = APPPATH . 'Database/Data/wilayah_level_1_2.php';
        $wilayah  = is_file($dataFile) ? (array) require $dataFile : [];
        if (!empty($wilayah)) {
            $batch = [];
            foreach ($wilayah as $kode => $nama) {
                $batch[] = [
                    'kode' => (string) $kode,
                    'nama' => (string) $nama,
                ];
            }
            foreach (array_chunk($batch, 200) as $chunk) {
                $this->db->table('ref_wilayah')->ignore(true)->insertBatch($chunk);
            }
        }

        // Selaraskan metadata wilayah pada pertanyaan Tracer Study Lulusan 2025 (bundel & periode)
        $provList = [];
        foreach ($wilayah as $kode => $nama) {
            if (strlen((string) $kode) === 2) {
                $provList[] = (string) $nama;
            }
        }
        $this->syncTracerStudyWilayahQuestions($provList);
    }

    public function down()
    {
        if ($this->db->tableExists('ref_wilayah')) {
            $this->forge->dropTable('ref_wilayah', true);
        }
    }

    /**
     * Memperbarui opsi_jawaban_json pertanyaan wilayah pada BND-ALUMNI-2025 & periode turunannya
     * agar memakai dropdown Provinsi -> Kabupaten/Kota terpadu dari ref_wilayah.
     *
     * @param list<string> $provList
     */
    private function syncTracerStudyWilayahQuestions(array $provList): void
    {
        $tables = ['tracer_study_bundle_pertanyaan', 'tracer_study_periode_pertanyaan'];
        $now    = date('Y-m-d H:i:s');

        $provJson = !empty($provList)
            ? json_encode([
                'options'      => $provList,
                'render_as'    => 'dropdown',
                'wilayah_role' => 'provinsi',
            ], JSON_UNESCAPED_UNICODE)
            : null;

        $kotaJson = json_encode([
            'prefill_key'         => 'kota_domisili',
            'wilayah_role'        => 'kabupaten_kota',
            'wilayah_parent_kode' => 'TS25_PROVINSI_DOMISILI',
        ], JSON_UNESCAPED_UNICODE);

        $lahirJson = json_encode([
            'prefill_key'  => 'tempat_lahir',
            'wilayah_role' => 'kabupaten_kota_tags',
        ], JSON_UNESCAPED_UNICODE);

        foreach ($tables as $tbl) {
            if (!$this->db->tableExists($tbl)) {
                continue;
            }
            if ($provJson !== null) {
                $this->db->table($tbl)
                    ->where('kode_pertanyaan', 'TS25_PROVINSI_DOMISILI')
                    ->update(['opsi_jawaban_json' => $provJson, 'updated_at' => $now]);
            }
            $this->db->table($tbl)
                ->where('kode_pertanyaan', 'TS25_KOTA_DOMISILI')
                ->update(['opsi_jawaban_json' => $kotaJson, 'updated_at' => $now]);
            $this->db->table($tbl)
                ->where('kode_pertanyaan', 'TS25_TEMPAT_LAHIR')
                ->update(['opsi_jawaban_json' => $lahirJson, 'updated_at' => $now]);
        }
    }
}
