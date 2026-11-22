<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder non-destruktif & idempoten untuk menambahkan data taruna RSTJ Tingkat 1
 * dari direktori ECC 2.0/data taruna/tingkat 1 (Kelas 1A–1E) tanpa mengganggu Tingkat 2–4.
 */
class Ecc2Tingkat1TarunaSeeder extends Seeder
{
    use RealTarunaLevelImportTrait;

    public function run(): void
    {
        $this->seedRealLevelDirectory(
            'tingkat 1',
            1,
            2026,
            ['A', 'B', 'C', 'D', 'E'],
            'IMPOR/SIM-PKTJ/2026-T1',
            'REAL_SIM_PKTJ_T1_IMPORT',
            'Data taruna aktif RSTJ Tingkat 1, 2, 3, & 4 bersumber dari sim.pktj.ac.id.'
        );
    }
}
