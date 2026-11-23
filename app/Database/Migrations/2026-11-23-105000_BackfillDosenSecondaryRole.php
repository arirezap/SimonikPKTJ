<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Backfill peran tambahan (sekunder) 'dosen' pada tabel pivot user_roles
 * untuk seluruh pegawai yang telah terdaftar pada tabel dosen_profil.
 */
class BackfillDosenSecondaryRole extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('user_roles') || !$this->db->tableExists('dosen_profil')) {
            return;
        }

        $now  = date('Y-m-d H:i:s');
        $rows = $this->db->table('dosen_profil dp')
            ->select('dp.user_id')
            ->join('users u', 'u.id = dp.user_id', 'inner')
            ->whereNotIn('u.role', ['taruna', 'alumni'])
            ->where("NOT EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = dp.user_id AND ur.role_name = 'dosen')", null, false)
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $uid = (int) ($row['user_id'] ?? 0);
            if ($uid <= 0) {
                continue;
            }
            $this->db->table('user_roles')->insert([
                'user_id'    => $uid,
                'role_name'  => 'dosen',
                'created_at' => $now,
            ]);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('user_roles') || !$this->db->tableExists('dosen_profil')) {
            return;
        }

        $this->db->table('user_roles')
            ->where('role_name', 'dosen')
            ->where('user_id IN (SELECT user_id FROM dosen_profil)', null, false)
            ->delete();
    }
}
