<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Wrapper seeder peran pengguna ECC 2.0.
 * Mendelegasikan penuh ke Ecc2RealStaffRolesSeeder agar akun simulasi fiktif tidak dibuat ulang.
 */
class Ecc2RoleUsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(Ecc2RealStaffRolesSeeder::class);
    }
}
