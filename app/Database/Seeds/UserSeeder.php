<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Data untuk akun admin
        $adminData = [
            'username'     => 'admin',
            'password'     => password_hash('admin123', PASSWORD_BCRYPT), // Password: admin123
            'nama_lengkap' => 'Administrator Utama',
            'email'        => 'admin@simonik.com',
            'role'         => 'admin',
            'foto'         => 'default.png',
        ];

        // Data untuk akun user
        $userData = [
            'username'     => 'user',
            'password'     => password_hash('user123', PASSWORD_BCRYPT), // Password: user123
            'nama_lengkap' => 'Pengguna Biasa',
            'email'        => 'user@simonik.com',
            'role'         => 'user',
            'foto'         => 'default.png',
        ];

        // Memasukkan data ke dalam tabel 'users'
        $this->db->table('users')->insert($adminData);
        $this->db->table('users')->insert($userData);
    }
}