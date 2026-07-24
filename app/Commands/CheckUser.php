<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckUser extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'custom:check_user';
    protected $description = 'Check user hierarchy for ECC LED';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $user = $db->table('users')->where('username', '199806112023211004')->orWhere('nip', '199806112023211004')->get()->getRowArray();

        if (!$user) {
            CLI::write("User not found", 'red');
            return;
        }

        CLI::write("User ID: " . $user['id']);
        CLI::write("Name: " . $user['nama_lengkap']);
        CLI::write("Role: " . $user['role']);
        CLI::write("Unit: " . $user['unit']);
        CLI::write("Atasan ID: " . $user['atasan_id']);

        if ($user['atasan_id']) {
            $atasan = $db->table('users')->where('id', $user['atasan_id'])->get()->getRowArray();
            if ($atasan) {
                CLI::write("\nAtasan Name: " . $atasan['nama_lengkap']);
                CLI::write("Atasan Role: " . $atasan['role']);
                CLI::write("Atasan Unit: " . $atasan['unit']);
                
                if ($atasan['atasan_id']) {
                    $atasan2 = $db->table('users')->where('id', $atasan['atasan_id'])->get()->getRowArray();
                    if ($atasan2) {
                        CLI::write("\nAtasan Level 2 Name: " . $atasan2['nama_lengkap']);
                        CLI::write("Atasan Level 2 Role: " . $atasan2['role']);
                    }
                }
            }
        }

        if ($user['unit']) {
            $unit = $db->table('unit_kerja')->where('nama_unit', $user['unit'])->get()->getRowArray();
            if ($unit) {
                CLI::write("\nUnit Parent: " . ($unit['parent_unit'] ?? 'NULL'));
            }
        }
    }
}
