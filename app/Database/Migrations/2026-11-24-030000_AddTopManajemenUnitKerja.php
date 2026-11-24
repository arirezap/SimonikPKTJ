<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTopManajemenUnitKerja extends Migration
{
    public function up()
    {
        // 1. Perluas ENUM parent_unit agar mendukung kategori 'pimpinan' (Top Manajemen)
        $this->db->query("ALTER TABLE `unit_kerja` MODIFY COLUMN `parent_unit` ENUM('aak','kuk','pimpinan') NULL DEFAULT NULL");

        // 2. Pastikan unit kerja 'Top Manajemen' tersedia (idempoten)
        $existing = $this->db->table('unit_kerja')
            ->where('nama_unit', 'Top Manajemen')
            ->get()
            ->getRowArray();

        if (!$existing) {
            $now = date('Y-m-d H:i:s');
            $this->db->table('unit_kerja')->insert([
                'nama_unit'   => 'Top Manajemen',
                'parent_unit' => 'pimpinan',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $topManajemenId = (int) $this->db->insertID();
        } else {
            $topManajemenId = (int) $existing['id'];
            if (($existing['parent_unit'] ?? null) !== 'pimpinan') {
                $this->db->table('unit_kerja')
                    ->where('id', $topManajemenId)
                    ->update(['parent_unit' => 'pimpinan']);
            }
        }

        // 3. Petakan akun Direktur & Wakil Direktur yang unitnya masih kosong ke 'Top Manajemen'
        if ($topManajemenId > 0) {
            $this->db->query(
                "UPDATE `users` SET `unit` = ?, `unit_id` = ? WHERE `role` IN ('direktur', 'wadir') AND (`unit` IS NULL OR TRIM(`unit`) = '')",
                ['Top Manajemen', $topManajemenId]
            );
        }
    }

    public function down()
    {
        $topUnit = $this->db->table('unit_kerja')
            ->where('nama_unit', 'Top Manajemen')
            ->get()
            ->getRowArray();

        if ($topUnit) {
            $topId = (int) $topUnit['id'];
            $this->db->query(
                "UPDATE `users` SET `unit` = '', `unit_id` = NULL WHERE `unit_id` = ? AND `role` IN ('direktur', 'wadir')",
                [$topId]
            );

            $remaining = $this->db->table('users')->where('unit_id', $topId)->countAllResults();
            if ($remaining === 0) {
                $this->db->table('unit_kerja')->where('id', $topId)->delete();
            }
        }

        $this->db->query("UPDATE `unit_kerja` SET `parent_unit` = NULL WHERE `parent_unit` = 'pimpinan'");
        $this->db->query("ALTER TABLE `unit_kerja` MODIFY COLUMN `parent_unit` ENUM('aak','kuk') NULL DEFAULT NULL");
    }
}
