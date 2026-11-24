<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentUnitIdForSubUnits extends Migration
{
    public function up()
    {
        // 1. Tambah kolom parent_unit_id (idempoten) untuk relasi sub-unit (Pokja -> Tim Substansi)
        if (!$this->db->fieldExists('parent_unit_id', 'unit_kerja')) {
            $this->db->query(
                "ALTER TABLE `unit_kerja`
                 ADD COLUMN `parent_unit_id` INT UNSIGNED NULL DEFAULT NULL AFTER `parent_unit`,
                 ADD INDEX `idx_unit_kerja_parent_unit_id` (`parent_unit_id`)"
            );
        }

        // 2. Petakan 3 Pokja eksisting ke Tim Substansi induknya berdasarkan nama unit
        $mappings = [
            'Pokja Diklat'         => 'Tim Substansi Bidang Administrasi Akademik',
            'Pokja PPID dan Humas' => 'Tim Substansi Bidang Kerjasama',
            'Pokja Sarpras'        => 'Tim Substansi Bidang Umum',
        ];

        foreach ($mappings as $subName => $parentName) {
            $parentUnit = $this->db->table('unit_kerja')
                ->where('nama_unit', $parentName)
                ->get()
                ->getRowArray();

            if (!$parentUnit) {
                continue;
            }

            $parentId = (int) $parentUnit['id'];
            $parentBagian = $parentUnit['parent_unit'] ?? null;

            $this->db->table('unit_kerja')
                ->where('nama_unit', $subName)
                ->update([
                    'parent_unit_id' => $parentId,
                    'parent_unit'    => $parentBagian,
                ]);

            // 3. Selaraskan atasan_id seluruh pegawai aktif di sub-unit Pokja ke Katim unit induknya
            $subUnit = $this->db->table('unit_kerja')
                ->where('nama_unit', $subName)
                ->get()
                ->getRowArray();

            if (!$subUnit) {
                continue;
            }

            $katim = $this->db->table('users')
                ->select('id')
                ->where('unit_id', $parentId)
                ->whereIn('role', ['manajemen', 'katim'])
                ->where('status_pegawai', 'aktif')
                ->orderBy('id', 'ASC')
                ->get()
                ->getRowArray();

            if ($katim) {
                $katimId = (int) $katim['id'];
                $this->db->table('users')
                    ->where('unit_id', (int) $subUnit['id'])
                    ->where('id !=', $katimId)
                    ->where('status_pegawai', 'aktif')
                    ->update(['atasan_id' => $katimId]);
            }
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('parent_unit_id', 'unit_kerja')) {
            $this->db->query(
                "ALTER TABLE `unit_kerja`
                 DROP INDEX `idx_unit_kerja_parent_unit_id`,
                 DROP COLUMN `parent_unit_id`"
            );
        }
    }
}
