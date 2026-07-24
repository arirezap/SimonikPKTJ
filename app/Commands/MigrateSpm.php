<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MigrateSpm extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'custom:migrate_spm';
    protected $description = 'Migrate SPM users from primary role to secondary role';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // Find users with primary role 'spm'
        $users = $db->table('users')->where('role', 'spm')->get()->getResultArray();

        $count = 0;
        foreach ($users as $u) {
            // Determine fallback primary role.
            $newPrimaryRole = 'user';
            if (stripos($u['jabatan'] ?? '', 'ketua') !== false || stripos($u['jabatan'] ?? '', 'kepala') !== false) {
                $newPrimaryRole = 'manajemen';
            }

            // Update primary role in users table
            $db->table('users')->where('id', $u['id'])->update(['role' => $newPrimaryRole]);

            // Ensure the new primary role is also in the user_roles pivot table
            $exists = $db->table('user_roles')
                        ->where('user_id', $u['id'])
                        ->where('role_name', $newPrimaryRole)
                        ->countAllResults();
                        
            if ($exists == 0) {
                $db->table('user_roles')->insert([
                    'user_id' => $u['id'],
                    'role_name' => $newPrimaryRole,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            CLI::write("User {$u['nama_lengkap']} changed from 'spm' to '{$newPrimaryRole}' (Primary). Secondary 'spm' retained.");
            $count++;
        }

        CLI::write("Done. Migrated {$count} users.", 'green');
    }
}
