<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoPendingToMasterTaruna extends Migration
{
    public function up()
    {
        $fields = [];

        if (!$this->db->fieldExists('foto_pending', 'master_taruna')) {
            $fields['foto_pending'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'foto',
            ];
        }

        if (!$this->db->fieldExists('foto_pending_status', 'master_taruna')) {
            $fields['foto_pending_status'] = [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => isset($fields['foto_pending']) || $this->db->fieldExists('foto_pending', 'master_taruna') ? 'foto_pending' : 'foto',
            ];
        }

        if (!$this->db->fieldExists('foto_pending_catatan', 'master_taruna')) {
            $fields['foto_pending_catatan'] = [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => isset($fields['foto_pending_status']) || $this->db->fieldExists('foto_pending_status', 'master_taruna') ? 'foto_pending_status' : 'foto',
            ];
        }

        if (!$this->db->fieldExists('foto_pending_at', 'master_taruna')) {
            $fields['foto_pending_at'] = [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => isset($fields['foto_pending_catatan']) || $this->db->fieldExists('foto_pending_catatan', 'master_taruna') ? 'foto_pending_catatan' : 'foto',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('master_taruna', $fields);
        }
    }

    public function down()
    {
        $cols = [];
        foreach (['foto_pending_at', 'foto_pending_catatan', 'foto_pending_status', 'foto_pending'] as $col) {
            if ($this->db->fieldExists($col, 'master_taruna')) {
                $cols[] = $col;
            }
        }
        if (!empty($cols)) {
            $this->forge->dropColumn('master_taruna', $cols);
        }
    }
}
