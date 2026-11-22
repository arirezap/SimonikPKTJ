<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTracerStudyTables extends Migration
{
    public function up()
    {
        // 1. Master Bundel Pertanyaan (dapat dipakai ulang & dimodifikasi antar-periode)
        if (!$this->db->tableExists('tracer_study_bundle')) {
            $this->forge->addField([
                'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'kode_bundle'      => ['type' => 'VARCHAR', 'constraint' => 40],
                'nama_bundle'      => ['type' => 'VARCHAR', 'constraint' => 150],
                'target_responden' => ['type' => 'ENUM', 'constraint' => ['alumni', 'mitra'], 'default' => 'alumni'],
                'deskripsi'        => ['type' => 'TEXT', 'null' => true],
                'is_aktif'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'dibuat_oleh'      => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'diperbarui_oleh'  => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'created_at'       => ['type' => 'DATETIME', 'null' => true],
                'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('kode_bundle', 'uq_ts_bundle_kode');
            $this->forge->addKey(['target_responden', 'is_aktif'], false, false, 'idx_ts_bundle_target');
            $this->forge->createTable('tracer_study_bundle', true);
        }

        // 2. Item Pertanyaan dalam Bundel Master
        if (!$this->db->tableExists('tracer_study_bundle_pertanyaan')) {
            $this->forge->addField([
                'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'bundle_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'kode_pertanyaan'    => ['type' => 'VARCHAR', 'constraint' => 50],
                'bagian'             => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Umum'],
                'urutan'             => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
                'teks_pertanyaan'    => ['type' => 'TEXT'],
                'keterangan_bantuan' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'tipe_jawaban'       => [
                    'type'       => 'ENUM',
                    'constraint' => ['pilihan_tunggal', 'pilihan_ganda', 'skala_likert', 'teks_singkat', 'teks_panjang', 'angka', 'tanggal'],
                    'default'    => 'pilihan_tunggal',
                ],
                'opsi_jawaban_json'  => ['type' => 'LONGTEXT', 'null' => true],
                'is_wajib'           => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'is_aktif'           => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at'         => ['type' => 'DATETIME', 'null' => true],
                'updated_at'         => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['bundle_id', 'urutan'], false, false, 'idx_ts_bp_bundle_urutan');
            $this->forge->addKey('kode_pertanyaan', false, false, 'idx_ts_bp_kode');
            $this->forge->createTable('tracer_study_bundle_pertanyaan', true);
        }

        // 3. Periode Pembukaan Tracer Study (kontrol buka/tutup & pemantauan progres)
        if (!$this->db->tableExists('tracer_study_periode')) {
            $this->forge->addField([
                'id'                        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'kode_periode'              => ['type' => 'VARCHAR', 'constraint' => 40],
                'nama_periode'              => ['type' => 'VARCHAR', 'constraint' => 150],
                'sumber_bundle_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'sumber_periode_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'target_responden'          => ['type' => 'ENUM', 'constraint' => ['alumni', 'mitra'], 'default' => 'alumni'],
                'tahun_lulus_sasaran_json'  => ['type' => 'TEXT', 'null' => true],
                'prodi_sasaran_json'        => ['type' => 'TEXT', 'null' => true],
                'tanggal_mulai'             => ['type' => 'DATE', 'null' => true],
                'tanggal_selesai'           => ['type' => 'DATE', 'null' => true],
                'target_partisipasi_persen' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 80.00],
                'status_periode'            => ['type' => 'ENUM', 'constraint' => ['draf', 'dibuka', 'ditutup'], 'default' => 'draf'],
                'dibuka_oleh'               => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'dibuka_pada'               => ['type' => 'DATETIME', 'null' => true],
                'ditutup_oleh'              => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'ditutup_pada'              => ['type' => 'DATETIME', 'null' => true],
                'deskripsi'                 => ['type' => 'TEXT', 'null' => true],
                'dibuat_oleh'               => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'created_at'                => ['type' => 'DATETIME', 'null' => true],
                'updated_at'                => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('kode_periode', 'uq_ts_periode_kode');
            $this->forge->addKey(['status_periode', 'tanggal_mulai'], false, false, 'idx_ts_periode_status');
            $this->forge->createTable('tracer_study_periode', true);
        }

        // 4. Snapshot Pertanyaan Khusus Periode (dapat ditambah/diubah selama status = draf; terkunci saat dibuka)
        if (!$this->db->tableExists('tracer_study_periode_pertanyaan')) {
            $this->forge->addField([
                'id'                          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'periode_id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'sumber_bundle_pertanyaan_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'kode_pertanyaan'             => ['type' => 'VARCHAR', 'constraint' => 50],
                'bagian'                      => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Umum'],
                'urutan'                      => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
                'teks_pertanyaan'             => ['type' => 'TEXT'],
                'keterangan_bantuan'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'tipe_jawaban'                => [
                    'type'       => 'ENUM',
                    'constraint' => ['pilihan_tunggal', 'pilihan_ganda', 'skala_likert', 'teks_singkat', 'teks_panjang', 'angka', 'tanggal'],
                    'default'    => 'pilihan_tunggal',
                ],
                'opsi_jawaban_json'           => ['type' => 'LONGTEXT', 'null' => true],
                'is_wajib'                    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'is_aktif'                    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at'                  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'                  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['periode_id', 'urutan'], false, false, 'idx_ts_pp_periode_urutan');
            $this->forge->addKey(['periode_id', 'kode_pertanyaan'], false, false, 'idx_ts_pp_periode_kode');
            $this->forge->createTable('tracer_study_periode_pertanyaan', true);
        }

        // 5. Respons Alumni per Periode (indikator inti terstruktur + header jawaban dinamis)
        if (!$this->db->tableExists('tracer_study_respons')) {
            $this->forge->addField([
                'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'periode_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'nomor_taruna'        => ['type' => 'VARCHAR', 'constraint' => 30],
                'status_transisi'     => [
                    'type'       => 'ENUM',
                    'constraint' => ['bekerja', 'wirausaha', 'studi_lanjut', 'mencari_kerja', 'belum_memungkinkan'],
                    'default'    => 'bekerja',
                ],
                'masa_tunggu_bulan'   => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'tanggal_mulai_kerja' => ['type' => 'DATE', 'null' => true],
                'nama_instansi'       => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
                'sektor_industri'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'jabatan_posisi'      => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'kota_kerja'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'kesesuaian_bidang'   => [
                    'type'       => 'ENUM',
                    'constraint' => ['sangat_sesuai', 'sesuai', 'kurang_sesuai', 'tidak_sesuai'],
                    'null'       => true,
                ],
                'kesesuaian_jenjang'  => [
                    'type'       => 'ENUM',
                    'constraint' => ['lebih_tinggi', 'setingkat', 'lebih_rendah'],
                    'null'       => true,
                ],
                'sumber_pengisian'    => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'mandiri_alumni'],
                'status_validasi'     => ['type' => 'ENUM', 'constraint' => ['diajukan', 'tervalidasi', 'perlu_perbaikan'], 'default' => 'tervalidasi'],
                'catatan_validasi'    => ['type' => 'TEXT', 'null' => true],
                'dicatat_oleh'        => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'divalidasi_oleh'     => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'tanggal_pengisian'   => ['type' => 'DATETIME', 'null' => true],
                'created_at'          => ['type' => 'DATETIME', 'null' => true],
                'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['periode_id', 'nomor_taruna'], 'uq_ts_periode_taruna');
            $this->forge->addKey(['periode_id', 'status_transisi'], false, false, 'idx_ts_resp_transisi');
            $this->forge->addKey('nomor_taruna', false, false, 'idx_ts_resp_taruna');
            $this->forge->createTable('tracer_study_respons', true);
        }

        // 6. Jawaban Dinamis per Pertanyaan Periode
        if (!$this->db->tableExists('tracer_study_jawaban')) {
            $this->forge->addField([
                'id'                    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'respons_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'periode_pertanyaan_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'kode_pertanyaan'       => ['type' => 'VARCHAR', 'constraint' => 50],
                'nilai_teks'            => ['type' => 'TEXT', 'null' => true],
                'nilai_angka'           => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
                'nilai_opsi_json'       => ['type' => 'TEXT', 'null' => true],
                'created_at'            => ['type' => 'DATETIME', 'null' => true],
                'updated_at'            => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['respons_id', 'periode_pertanyaan_id'], 'uq_ts_respons_pertanyaan');
            $this->forge->addKey('periode_pertanyaan_id', false, false, 'idx_ts_jwb_pp');
            $this->forge->addKey('kode_pertanyaan', false, false, 'idx_ts_jwb_kode');
            $this->forge->createTable('tracer_study_jawaban', true);
        }

        // Seed 1 Bundel Pertanyaan Standar awal (idempotent) agar siap dipakai & dimodifikasi
        $existingBundle = $this->db->table('tracer_study_bundle')
            ->where('kode_bundle', 'BND-ALUMNI-STD-V1')
            ->get()
            ->getRowArray();

        if (!$existingBundle) {
            $now = date('Y-m-d H:i:s');
            $this->db->table('tracer_study_bundle')->insert([
                'kode_bundle'      => 'BND-ALUMNI-STD-V1',
                'nama_bundle'      => 'Bundel Standar Tracer Study Lulusan PKTJ',
                'target_responden' => 'alumni',
                'deskripsi'        => 'Paket pertanyaan standar evaluasi transisi kerja, relevansi kurikulum, praktik bengkel/lab, dan pembinaan ketarunaan. Dapat dipakai ulang atau dimodifikasi pada periode pembukaan berikutnya.',
                'is_aktif'         => 1,
                'dibuat_oleh'      => 'Sistem ECC',
                'diperbarui_oleh'  => 'Sistem ECC',
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $bundleId = (int) $this->db->insertID();

            if ($bundleId > 0) {
                $defaultQuestions = [
                    [
                        'kode_pertanyaan'    => 'TS_SUMBER_KERJA',
                        'bagian'             => 'Pencarian & Transisi Kerja',
                        'urutan'             => 1,
                        'teks_pertanyaan'    => 'Dari mana Anda memperoleh informasi pekerjaan atau peluang usaha pertama setelah lulus?',
                        'keterangan_bantuan' => 'Pilih satu sumber utama yang paling berperan.',
                        'tipe_jawaban'       => 'pilihan_tunggal',
                        'opsi_jawaban_json'  => json_encode([
                            'Portal Karier / Info Lowongan PKTJ',
                            'Rekrutmen Kampus / Ikatan Dinas',
                            'Relasi Alumni / Dosen',
                            'Portal Lowongan Daring / Media Sosial',
                            'Melamar Langsung ke Perusahaan / Instansi',
                            'Membangun Usaha Mandiri / Belum Bekerja',
                        ], JSON_UNESCAPED_UNICODE),
                        'is_wajib'           => 1,
                    ],
                    [
                        'kode_pertanyaan'    => 'TS_RENTANG_GAJI',
                        'bagian'             => 'Pencarian & Transisi Kerja',
                        'urutan'             => 2,
                        'teks_pertanyaan'    => 'Berapa kisaran penghasilan bulanan Anda saat ini?',
                        'keterangan_bantuan' => 'Data hanya digunakan dalam bentuk agregat statistik.',
                        'tipe_jawaban'       => 'pilihan_tunggal',
                        'opsi_jawaban_json'  => json_encode([
                            'Belum Berpenghasilan',
                            'Kurang dari Rp 3.000.000',
                            'Rp 3.000.000 - Rp 5.000.000',
                            'Rp 5.000.001 - Rp 8.000.000',
                            'Rp 8.000.001 - Rp 12.000.000',
                            'Lebih dari Rp 12.000.000',
                        ], JSON_UNESCAPED_UNICODE),
                        'is_wajib'           => 1,
                    ],
                    [
                        'kode_pertanyaan'    => 'TS_RELEVANSI_KURIKULUM',
                        'bagian'             => 'Evaluasi Pembelajaran & Kompetensi',
                        'urutan'             => 3,
                        'teks_pertanyaan'    => 'Seberapa relevan mata kuliah dan kurikulum prodi dengan kebutuhan pekerjaan Anda saat ini?',
                        'keterangan_bantuan' => 'Skala 1 (Sangat Kurang) sampai 5 (Sangat Baik).',
                        'tipe_jawaban'       => 'skala_likert',
                        'opsi_jawaban_json'  => json_encode(['min_label' => 'Sangat Kurang', 'max_label' => 'Sangat Baik'], JSON_UNESCAPED_UNICODE),
                        'is_wajib'           => 1,
                    ],
                    [
                        'kode_pertanyaan'    => 'TS_PRAKTIK_LAB',
                        'bagian'             => 'Evaluasi Pembelajaran & Kompetensi',
                        'urutan'             => 4,
                        'teks_pertanyaan'    => 'Seberapa besar manfaat kegiatan praktikum bengkel, laboratorium, dan magang dalam menunjang kesiapan kerja?',
                        'keterangan_bantuan' => 'Skala 1 (Sangat Kurang) sampai 5 (Sangat Bermanfaat).',
                        'tipe_jawaban'       => 'skala_likert',
                        'opsi_jawaban_json'  => json_encode(['min_label' => 'Sangat Kurang', 'max_label' => 'Sangat Bermanfaat'], JSON_UNESCAPED_UNICODE),
                        'is_wajib'           => 1,
                    ],
                    [
                        'kode_pertanyaan'    => 'TS_PEMBINAAN_KARAKTER',
                        'bagian'             => 'Evaluasi Pembelajaran & Kompetensi',
                        'urutan'             => 5,
                        'teks_pertanyaan'    => 'Seberapa besar kontribusi pembinaan ketarunaan dan kedisiplinan terhadap etos kerja Anda?',
                        'keterangan_bantuan' => 'Skala 1 (Sangat Kurang) sampai 5 (Sangat Besar).',
                        'tipe_jawaban'       => 'skala_likert',
                        'opsi_jawaban_json'  => json_encode(['min_label' => 'Sangat Kurang', 'max_label' => 'Sangat Besar'], JSON_UNESCAPED_UNICODE),
                        'is_wajib'           => 1,
                    ],
                    [
                        'kode_pertanyaan'    => 'TS_KOMPETENSI_DIBUTUHKAN',
                        'bagian'             => 'Evaluasi Pembelajaran & Kompetensi',
                        'urutan'             => 6,
                        'teks_pertanyaan'    => 'Kompetensi tambahan apa yang paling dibutuhkan di dunia kerja saat ini?',
                        'keterangan_bantuan' => 'Anda dapat memilih lebih dari satu jawaban.',
                        'tipe_jawaban'       => 'pilihan_ganda',
                        'opsi_jawaban_json'  => json_encode([
                            'Analisis Data & Teknologi Digital',
                            'Keselamatan Transportasi & Audit Jalan',
                            'Teknologi Kendaraan Listrik / Otomotif Modern',
                            'Komunikasi & Presentasi Profesional',
                            'Bahasa Inggris Teknis',
                            'Kepemimpinan & Manajemen Proyek',
                        ], JSON_UNESCAPED_UNICODE),
                        'is_wajib'           => 1,
                    ],
                    [
                        'kode_pertanyaan'    => 'TS_SARAN_PKTJ',
                        'bagian'             => 'Masukan & Saran',
                        'urutan'             => 7,
                        'teks_pertanyaan'    => 'Tuliskan saran pengembangan kurikulum, fasilitas praktik, atau layanan karier bagi PKTJ Tegal.',
                        'keterangan_bantuan' => 'Opsional, tuliskan masukan singkat dan konkret.',
                        'tipe_jawaban'       => 'teks_panjang',
                        'opsi_jawaban_json'  => null,
                        'is_wajib'           => 0,
                    ],
                ];

                foreach ($defaultQuestions as $q) {
                    $this->db->table('tracer_study_bundle_pertanyaan')->insert(array_merge($q, [
                        'bundle_id'  => $bundleId,
                        'is_aktif'   => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]));
                }
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('tracer_study_jawaban', true);
        $this->forge->dropTable('tracer_study_respons', true);
        $this->forge->dropTable('tracer_study_periode_pertanyaan', true);
        $this->forge->dropTable('tracer_study_periode', true);
        $this->forge->dropTable('tracer_study_bundle_pertanyaan', true);
        $this->forge->dropTable('tracer_study_bundle', true);
    }
}
