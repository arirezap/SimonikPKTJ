<?php

namespace App\Database\Migrations;

use App\Libraries\Ketarunaan\TracerStudyOfficialTemplates;
use CodeIgniter\Database\Migration;

/**
 * Penyesuaian skema & seeding resmi 2 bundel soal Tracer Study Tahun 2025 PKTJ:
 * 1. BND-ALUMNI-2025 (Tracer Study Tahun 2025 untuk Lulusan PKTJ) + periode draf siap publish
 * 2. BND-MITRA-2025  (Tracer Study Tahun 2025 untuk Pengguna Lulusan PKTJ) + periode draf siap publish
 */
class EnhanceTracerStudy2025AndBundles extends Migration
{
    public function up()
    {
        // 1. Tambah kolom nama_alumni_dinilai untuk respons Pengguna Lulusan (mitra)
        if ($this->db->tableExists('tracer_study_respons') && !$this->db->fieldExists('nama_alumni_dinilai', 'tracer_study_respons')) {
            $this->forge->addColumn('tracer_study_respons', [
                'nama_alumni_dinilai' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                    'after'      => 'nama_responden',
                ],
            ]);
        }

        if (!$this->db->tableExists('tracer_study_bundle') || !$this->db->tableExists('tracer_study_bundle_pertanyaan')) {
            return;
        }

        // 2. Seed idempotent kedua bundel resmi PKTJ 2025
        $alumniBundleId = $this->seedBundleIfMissing(TracerStudyOfficialTemplates::alumni2025Bundle());
        $mitraBundleId  = $this->seedBundleIfMissing(TracerStudyOfficialTemplates::mitra2025Bundle());

        // 3. Seed idempotent 2 periode Draf siap ditinjau & dibuka (publish)
        if ($this->db->tableExists('tracer_study_periode') && $this->db->tableExists('tracer_study_periode_pertanyaan')) {
            if ($alumniBundleId > 0) {
                $this->seedDraftPeriodeIfMissing([
                    'kode_periode'              => 'TS-ALUMNI-2025',
                    'public_token'              => 'tracerlulusan2025',
                    'nama_periode'              => 'Tracer Study Tahun 2025 untuk Lulusan PKTJ Tahun 2024 dan 2023',
                    'sumber_bundle_id'          => $alumniBundleId,
                    'target_responden'          => 'alumni',
                    'tahun_lulus_sasaran_json'  => json_encode([2024, 2023]),
                    'prodi_sasaran_json'        => null,
                    'target_partisipasi_persen' => 80.00,
                    'allow_unregistered_alumni' => 1,
                    'deskripsi'                 => TracerStudyOfficialTemplates::alumni2025Bundle()['bundle']['deskripsi'],
                ], $alumniBundleId);
            }

            if ($mitraBundleId > 0) {
                $this->seedDraftPeriodeIfMissing([
                    'kode_periode'              => 'TS-MITRA-2025',
                    'public_token'              => 'tracerpengguna2025',
                    'nama_periode'              => 'Tracer Study Tahun 2025 untuk Pengguna Lulusan PKTJ',
                    'sumber_bundle_id'          => $mitraBundleId,
                    'target_responden'          => 'mitra',
                    'tahun_lulus_sasaran_json'  => null,
                    'prodi_sasaran_json'        => null,
                    'target_partisipasi_persen' => 80.00,
                    'allow_unregistered_alumni' => 0,
                    'deskripsi'                 => TracerStudyOfficialTemplates::mitra2025Bundle()['bundle']['deskripsi'],
                ], $mitraBundleId);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('tracer_study_respons') && $this->db->fieldExists('nama_alumni_dinilai', 'tracer_study_respons')) {
            $this->forge->dropColumn('tracer_study_respons', 'nama_alumni_dinilai');
        }
    }

    /**
     * @param array{bundle:array<string,mixed>,questions:list<array<string,mixed>>} $tpl
     */
    private function seedBundleIfMissing(array $tpl): int
    {
        $b    = $tpl['bundle'];
        $kode = (string) $b['kode_bundle'];
        $now  = date('Y-m-d H:i:s');

        $existing = $this->db->table('tracer_study_bundle')
            ->where('kode_bundle', $kode)
            ->get()
            ->getRowArray();

        if ($existing) {
            return (int) $existing['id'];
        }

        $this->db->table('tracer_study_bundle')->insert([
            'kode_bundle'      => $kode,
            'nama_bundle'      => $b['nama_bundle'],
            'target_responden' => $b['target_responden'],
            'deskripsi'        => $b['deskripsi'],
            'is_aktif'         => 1,
            'dibuat_oleh'      => 'Tim Tracer Study 2025 PKTJ',
            'diperbarui_oleh'  => 'Tim Tracer Study 2025 PKTJ',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        $bundleId = (int) $this->db->insertID();
        if ($bundleId <= 0) {
            return 0;
        }

        foreach ($tpl['questions'] as $q) {
            $this->db->table('tracer_study_bundle_pertanyaan')->insert([
                'bundle_id'          => $bundleId,
                'kode_pertanyaan'    => $q['kode_pertanyaan'],
                'bagian'             => $q['bagian'],
                'urutan'             => (int) $q['urutan'],
                'teks_pertanyaan'    => $q['teks_pertanyaan'],
                'keterangan_bantuan' => $q['keterangan_bantuan'],
                'tipe_jawaban'       => $q['tipe_jawaban'],
                'opsi_jawaban_json'  => $q['opsi_jawaban_json'],
                'is_wajib'           => (int) $q['is_wajib'],
                'is_aktif'           => 1,
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }

        return $bundleId;
    }

    /**
     * @param array<string, mixed> $periodeData
     */
    private function seedDraftPeriodeIfMissing(array $periodeData, int $bundleId): void
    {
        $exists = $this->db->table('tracer_study_periode')
            ->where('kode_periode', $periodeData['kode_periode'])
            ->get()
            ->getRowArray();

        if ($exists) {
            return;
        }

        // Pastikan public_token unik jika sebelumnya sudah terpakai
        $slug = (string) $periodeData['public_token'];
        if ($this->db->fieldExists('public_token', 'tracer_study_periode')) {
            $slugDup = $this->db->table('tracer_study_periode')->where('public_token', $slug)->get()->getRowArray();
            if ($slugDup) {
                $slug .= 'v2';
            }
        }

        $now = date('Y-m-d H:i:s');
        $row = [
            'kode_periode'              => $periodeData['kode_periode'],
            'nama_periode'              => $periodeData['nama_periode'],
            'sumber_bundle_id'          => $bundleId,
            'sumber_periode_id'         => null,
            'target_responden'          => $periodeData['target_responden'],
            'tahun_lulus_sasaran_json'  => $periodeData['tahun_lulus_sasaran_json'],
            'prodi_sasaran_json'        => $periodeData['prodi_sasaran_json'],
            'tanggal_mulai'             => null,
            'tanggal_selesai'           => null,
            'target_partisipasi_persen' => $periodeData['target_partisipasi_persen'],
            'status_periode'            => 'draf',
            'deskripsi'                 => $periodeData['deskripsi'],
            'dibuat_oleh'               => 'Tim Tracer Study 2025 PKTJ',
            'created_at'                => $now,
            'updated_at'                => $now,
        ];

        if ($this->db->fieldExists('public_token', 'tracer_study_periode')) {
            $row['public_token'] = $slug;
        }
        if ($this->db->fieldExists('allow_public_link', 'tracer_study_periode')) {
            $row['allow_public_link'] = 1;
        }
        if ($this->db->fieldExists('allow_unregistered_alumni', 'tracer_study_periode')) {
            $row['allow_unregistered_alumni'] = (int) $periodeData['allow_unregistered_alumni'];
        }

        $this->db->table('tracer_study_periode')->insert($row);
        $periodeId = (int) $this->db->insertID();
        if ($periodeId <= 0) {
            return;
        }

        $bundleQuestions = $this->db->table('tracer_study_bundle_pertanyaan')
            ->where('bundle_id', $bundleId)
            ->where('is_aktif', 1)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($bundleQuestions as $bq) {
            $this->db->table('tracer_study_periode_pertanyaan')->insert([
                'periode_id'                  => $periodeId,
                'sumber_bundle_pertanyaan_id' => (int) $bq['id'],
                'kode_pertanyaan'             => $bq['kode_pertanyaan'],
                'bagian'                      => $bq['bagian'],
                'urutan'                      => (int) $bq['urutan'],
                'teks_pertanyaan'             => $bq['teks_pertanyaan'],
                'keterangan_bantuan'          => $bq['keterangan_bantuan'],
                'tipe_jawaban'                => $bq['tipe_jawaban'],
                'opsi_jawaban_json'           => $bq['opsi_jawaban_json'],
                'is_wajib'                    => (int) $bq['is_wajib'],
                'is_aktif'                    => 1,
                'created_at'                  => $now,
                'updated_at'                  => $now,
            ]);
        }
    }
}
