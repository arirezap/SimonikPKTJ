<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder non-destruktif & idempoten untuk menambahkan data taruna RSTJ Tingkat 2
 * dari direktori ECC 2.0/data taruna/tingkat 2 (Kelas 2A–2E) tanpa mengganggu Tingkat 3 & 4.
 */
class Ecc2Tingkat2TarunaSeeder extends Seeder
{
    use RealTarunaLevelImportTrait;

    public function run(): void
    {
        $this->seedRealLevelDirectory(
            'tingkat 2',
            2,
            2025,
            ['A', 'B', 'C', 'D', 'E'],
            'IMPOR/SIM-PKTJ/2026-T2',
            'REAL_SIM_PKTJ_T2_IMPORT',
            'Data taruna aktif RSTJ Tingkat 2, 3, & 4 bersumber dari sim.pktj.ac.id.'
        );
    }
}
