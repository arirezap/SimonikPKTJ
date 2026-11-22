<?php

namespace App\Database\Seeds;

use App\Libraries\Ketarunaan\TarunaService;
use CodeIgniter\Database\BaseConnection;

/**
 * Helper bersama untuk seeder non-destruktif & idempoten per direktori tingkat taruna riil sim.pktj.ac.id.
 */
trait RealTarunaLevelImportTrait
{
    /**
     * @param list<string> $classNames
     */
    protected function seedRealLevelDirectory(
        string $subDir,
        int $tingkat,
        int $tahunAngkatanNominal,
        array $classNames,
        string $nomorKeputusan,
        string $auditEntityId,
        string $catatanOperasional
    ): void {
        helper(['ketarunaan', 'audit']);

        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        $sourceDir = ROOTPATH . 'ECC 2.0' . DIRECTORY_SEPARATOR . 'data taruna' . DIRECTORY_SEPARATOR . $subDir;
        if (!is_dir($sourceDir)) {
            throw new \RuntimeException('Direktori sumber data taruna tidak ditemukan: ' . $sourceDir);
        }

        $files = glob($sourceDir . DIRECTORY_SEPARATOR . '*.xls') ?: [];
        sort($files);

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

        $this->ensureClassesForTingkat($db, $tingkat, $tahunAngkatanNominal, $classNames, $now);

        $preview = $tarunaService->periksaBerkasImpor($rawRows, 'sim_pktj_t' . $tingkat . '.xls');
        if ((int) ($preview['perlu_perbaikan'] ?? 0) > 0) {
            throw new \RuntimeException(
                "Pemeriksaan sumber tingkat {$tingkat} gagal: perlu_perbaikan=" . (int) $preview['perlu_perbaikan']
            );
        }

        $imported = 0;
        if (!empty($preview['valid_rows'])) {
            $res = $tarunaService->simpanImporValid($preview['valid_rows'], true, $nomorKeputusan);
            if (empty($res['success'])) {
                throw new \RuntimeException($res['message'] ?? "Gagal menginput data taruna tingkat {$tingkat}.");
            }
            $imported = (int) ($res['imported'] ?? 0);
        }

        if ($db->tableExists('settings')) {
            $tingkatList = array_column(
                $db->table('pendidikan_taruna')
                    ->select('tingkat')
                    ->distinct()
                    ->orderBy('tingkat', 'ASC')
                    ->get()
                    ->getResultArray(),
                'tingkat'
            );
            if (!empty($tingkatList)) {
                $catatanDinamis = count($tingkatList) > 1
                    ? 'Data taruna aktif RSTJ Tingkat ' . implode(', ', array_slice($tingkatList, 0, -1)) . ', & ' . end($tingkatList) . ' bersumber dari sim.pktj.ac.id.'
                    : 'Data taruna aktif RSTJ Tingkat ' . $tingkatList[0] . ' bersumber dari sim.pktj.ac.id.';
            } else {
                $catatanDinamis = $catatanOperasional;
            }

            $db->table('settings')
                ->where('setting_key', 'ecc2_catatan_operasional')
                ->update([
                    'setting_value' => $catatanDinamis,
                    'updated_at'    => $now,
                ]);
        }

        // Buat akun login taruna baru secara idempoten tanpa menimpa akun yang sudah ada
        $this->call(Ecc2TarunaLoginSeeder::class);

        log_audit('CREATE', 'master_taruna', $auditEntityId, null, [
            'berkas'   => $fileSummary,
            'imported' => $imported,
            'duplikat' => (int) ($preview['duplikat'] ?? 0),
        ]);
    }

    /**
     * @param list<string> $classNames
     */
    private function ensureClassesForTingkat(
        BaseConnection $db,
        int $tingkat,
        int $tahunAngkatanNominal,
        array $classNames,
        string $now
    ): void {
        $prodiRstj = $db->table('ref_program_studi')
            ->select('id')
            ->where('kode_prodi', 'RSTJ')
            ->get()
            ->getRowArray();
        $prodiId = !empty($prodiRstj['id']) ? (int) $prodiRstj['id'] : 1;

        $angkatanRow = $db->table('ref_angkatan')
            ->select('id')
            ->where('tahun_masuk', $tahunAngkatanNominal)
            ->get()
            ->getRowArray();
        if (!$angkatanRow) {
            return;
        }
        $angkatanId = (int) $angkatanRow['id'];

        foreach ($classNames as $kls) {
            $exists = $db->table('ref_kelas')
                ->where('program_studi_id', $prodiId)
                ->where('angkatan_id', $angkatanId)
                ->where('tingkat', $tingkat)
                ->where('nama_kelas', $kls)
                ->countAllResults();

            if ($exists === 0) {
                $db->table('ref_kelas')->insert([
                    'program_studi_id' => $prodiId,
                    'angkatan_id'      => $angkatanId,
                    'tingkat'          => $tingkat,
                    'nama_kelas'       => $kls,
                    'is_aktif'         => 1,
                    'created_at'       => $now,
                ]);
            }
        }
    }
}
