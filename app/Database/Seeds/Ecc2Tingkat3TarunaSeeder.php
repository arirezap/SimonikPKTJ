<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder non-destruktif & idempoten untuk menambahkan data taruna RSTJ Tingkat 3
 * dari direktori ECC 2.0/data taruna/tingkat 3 tanpa mengganggu Tingkat 4.
 */
class Ecc2Tingkat3TarunaSeeder extends Seeder
{
    use RealTarunaLevelImportTrait;

    public function run(): void
    {
        $this->seedRealLevelDirectory(
            'tingkat 3',
            3,
            2024,
            ['A', 'B', 'C', 'D'],
            'IMPOR/SIM-PKTJ/2026-T3',
            'REAL_SIM_PKTJ_T3_IMPORT',
            'Data taruna aktif RSTJ Tingkat 3 & 4 bersumber dari sim.pktj.ac.id.'
        );
    }
}
