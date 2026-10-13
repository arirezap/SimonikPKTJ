<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApprovalAndPenilaianAuditColumns extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Kolom audit trail pada tabel target_kinerja_bulanan
        if ($db->tableExists('target_kinerja_bulanan')) {
            $fieldsTarget = [];
            if (!$db->fieldExists('approved_by', 'target_kinerja_bulanan')) {
                $fieldsTarget['approved_by'] = [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'status_approval',
                ];
            }
            if (!$db->fieldExists('approved_at', 'target_kinerja_bulanan')) {
                $fieldsTarget['approved_at'] = [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'approved_by',
                ];
            }
            if (!$db->fieldExists('penilai_id', 'target_kinerja_bulanan')) {
                $fieldsTarget['penilai_id'] = [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'status_penilaian',
                ];
            }
            if (!$db->fieldExists('penilaian_at', 'target_kinerja_bulanan')) {
                $fieldsTarget['penilaian_at'] = [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'penilai_id',
                ];
            }
            if (!empty($fieldsTarget)) {
                $this->forge->addColumn('target_kinerja_bulanan', $fieldsTarget);
            }
        }

        // 2. Kolom audit trail pada tabel log_tugas_tambahan
        if ($db->tableExists('log_tugas_tambahan')) {
            $fieldsTambahan = [];
            if (!$db->fieldExists('penilai_id', 'log_tugas_tambahan')) {
                $fieldsTambahan['penilai_id'] = [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'status_penilaian',
                ];
            }
            if (!$db->fieldExists('penilaian_at', 'log_tugas_tambahan')) {
                $fieldsTambahan['penilaian_at'] = [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'penilai_id',
                ];
            }
            if (!empty($fieldsTambahan)) {
                $this->forge->addColumn('log_tugas_tambahan', $fieldsTambahan);
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('target_kinerja_bulanan')) {
            $dropTarget = [];
            foreach (['penilaian_at', 'penilai_id', 'approved_at', 'approved_by'] as $col) {
                if ($db->fieldExists($col, 'target_kinerja_bulanan')) {
                    $dropTarget[] = $col;
                }
            }
            if (!empty($dropTarget)) {
                $this->forge->dropColumn('target_kinerja_bulanan', $dropTarget);
            }
        }

        if ($db->tableExists('log_tugas_tambahan')) {
            $dropTambahan = [];
            foreach (['penilaian_at', 'penilai_id'] as $col) {
                if ($db->fieldExists($col, 'log_tugas_tambahan')) {
                    $dropTambahan[] = $col;
                }
            }
            if (!empty($dropTambahan)) {
                $this->forge->dropColumn('log_tugas_tambahan', $dropTambahan);
            }
        }
    }
}
