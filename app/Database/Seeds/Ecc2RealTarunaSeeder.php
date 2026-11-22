<?php

namespace App\Database\Seeds;

use App\Libraries\Ketarunaan\TarunaService;
use CodeIgniter\Database\Seeder;

/**
 * Seeder data taruna riil hasil ekspor sim.pktj.ac.id (ECC 2.0/data taruna).
 * Membersihkan seluruh data simulasi ketarunaan/alumni lalu menginput data nyata secara idempoten.
 */
class Ecc2RealTarunaSeeder extends Seeder
{
    public function run(): void
    {
        helper(['ketarunaan', 'audit']);

        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        $sourceDir = ROOTPATH . 'ECC 2.0' . DIRECTORY_SEPARATOR . 'data taruna';
        if (!is_dir($sourceDir)) {
            throw new \RuntimeException('Direktori sumber data taruna tidak ditemukan: ' . $sourceDir);
        }

        $t4Files = glob($sourceDir . DIRECTORY_SEPARATOR . '*.xls') ?: [];
        $t3Files = glob($sourceDir . DIRECTORY_SEPARATOR . 'tingkat 3' . DIRECTORY_SEPARATOR . '*.xls') ?: [];
        $t2Files = glob($sourceDir . DIRECTORY_SEPARATOR . 'tingkat 2' . DIRECTORY_SEPARATOR . '*.xls') ?: [];
        $t1Files = glob($sourceDir . DIRECTORY_SEPARATOR . 'tingkat 1' . DIRECTORY_SEPARATOR . '*.xls') ?: [];
        sort($t4Files);
        sort($t3Files);
        sort($t2Files);
        sort($t1Files);
        $files = array_merge($t4Files, $t3Files, $t2Files, $t1Files);

        $tarunaService = new TarunaService($db);
        $rawRows       = [];
        $fileSummary   = [];

        foreach ($files as $filePath) {
            $parsed = $tarunaService->bacaBerkasImpor($filePath, 'xls');
            $count  = count($parsed['rows']);
            $fileSummary[basename($filePath)] = $count;
            if ($count > 0) {
                $rawRows = array_merge($rawRows, $parsed['rows']);
            }
        }

        if (empty($rawRows)) {
            throw new \RuntimeException('Tidak ada baris data taruna yang valid ditemukan di ' . $sourceDir);
        }

        // 1. Bersihkan tabel simulasi operasional & sesuaikan ref_kelas ke kelas nyata
        $this->clearSimulationTables($db);
        $this->seedRealClasses($db, $now);

        // 2. Periksa & simpan data nyata tervalidasi (mengikuti sumber resmi sim.pktj.ac.id)
        $preview = $tarunaService->periksaBerkasImpor($rawRows, 'sim_pktj_real.xls');
        if (empty($preview['valid_rows']) || (int) $preview['perlu_perbaikan'] > 0) {
            throw new \RuntimeException(
                'Pemeriksaan sumber gagal: valid=' . count($preview['valid_rows'] ?? [])
                . ', perlu_perbaikan=' . (int) ($preview['perlu_perbaikan'] ?? 0)
            );
        }

        $res = $tarunaService->simpanImporValid($preview['valid_rows'], true, 'IMPOR/SIM-PKTJ/2026');
        if (empty($res['success'])) {
            throw new \RuntimeException($res['message'] ?? 'Gagal menginput data taruna nyata.');
        }

        // 3. Perbarui catatan operasional agar mencerminkan data aktual RSTJ Tingkat 1, 2, 3, & 4
        if ($db->tableExists('settings')) {
            $db->table('settings')
                ->where('setting_key', 'ecc2_catatan_operasional')
                ->update([
                    'setting_value' => 'Data taruna aktif RSTJ Tingkat 1, 2, 3, & 4 bersumber dari sim.pktj.ac.id.',
                    'updated_at'    => $now,
                ]);
        }

        log_audit('CREATE', 'master_taruna', 'REAL_SIM_PKTJ_IMPORT', null, [
            'berkas'   => $fileSummary,
            'imported' => (int) ($res['imported'] ?? 0),
        ]);
    }

    private function clearSimulationTables(\CodeIgniter\Database\BaseConnection $db): void
    {
        $tablesToClear = [
            'taruna_capaian',
            'alumni_riwayat_perubahan',
            'alumni_profil',
            'taruna_peserta_prestasi',
            'taruna_prestasi',
            'taruna_pelanggaran_tindak_lanjut',
            'taruna_pelanggaran_pihak',
            'taruna_pelanggaran',
            'taruna_beasiswa_hasil_peringkat',
            'taruna_beasiswa_pemeringkatan',
            'taruna_nilai_ip',
            'taruna_penerima_beasiswa',
            'taruna_program_beasiswa',
            'lowongan_dokumen',
            'lowongan_sasaran_prodi',
            'lowongan_pekerjaan',
            'perusahaan_mitra',
            'riwayat_pendidikan_taruna',
            'riwayat_status_taruna',
            'pendidikan_taruna',
            'master_taruna',
            'ref_kelas',
        ];

        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        try {
            foreach ($tablesToClear as $table) {
                if ($db->tableExists($table)) {
                    $db->table($table)->truncate();
                }
            }
        } finally {
            $db->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        // Hapus akun login taruna/alumni simulasi lama yang merujuk ke NIM fiktif
        $simUsernames = ['2401001', '20230189'];
        $simUsers     = $db->table('users')
            ->select('id')
            ->whereIn('username', $simUsernames)
            ->get()
            ->getResultArray();

        if (!empty($simUsers)) {
            $simIds = array_map(static fn (array $r): int => (int) $r['id'], $simUsers);
            $db->table('user_roles')->whereIn('user_id', $simIds)->delete();
            $db->table('users')->whereIn('id', $simIds)->delete();
        }
    }

    private function seedRealClasses(\CodeIgniter\Database\BaseConnection $db, string $now): void
    {
        $prodiRstj = $db->table('ref_program_studi')
            ->select('id')
            ->where('kode_prodi', 'RSTJ')
            ->get()
            ->getRowArray();

        $prodiId = !empty($prodiRstj['id']) ? (int) $prodiRstj['id'] : 2;

        $angkatanRows = $db->table('ref_angkatan')->select('id, tahun_masuk')->get()->getResultArray();
        $angkatanMap  = [];
        foreach ($angkatanRows as $a) {
            $angkatanMap[(int) $a['tahun_masuk']] = (int) $a['id'];
        }

        $classes   = [];
        $classPlan = [
            2023 => ['tingkat' => 4, 'kelas' => ['A', 'B', 'C', 'D']],
            2024 => ['tingkat' => 3, 'kelas' => ['A', 'B', 'C', 'D']],
            2025 => ['tingkat' => 2, 'kelas' => ['A', 'B', 'C', 'D', 'E']],
            2026 => ['tingkat' => 1, 'kelas' => ['A', 'B', 'C', 'D', 'E']],
        ];
        foreach ($classPlan as $tahun => $cfg) {
            if (!isset($angkatanMap[$tahun])) {
                continue;
            }
            foreach ($cfg['kelas'] as $kls) {
                $classes[] = [
                    'program_studi_id' => $prodiId,
                    'angkatan_id'      => $angkatanMap[$tahun],
                    'tingkat'          => $cfg['tingkat'],
                    'nama_kelas'       => $kls,
                    'is_aktif'         => 1,
                    'created_at'       => $now,
                ];
            }
        }

        if (!empty($classes)) {
            $db->table('ref_kelas')->insertBatch($classes);
        }
    }
}
