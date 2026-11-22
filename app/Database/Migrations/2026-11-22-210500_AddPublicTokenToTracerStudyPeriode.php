<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPublicTokenToTracerStudyPeriode extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tracer_study_periode')) {
            return;
        }

        $colsToAdd = [];
        if (!$this->db->fieldExists('public_token', 'tracer_study_periode')) {
            $colsToAdd['public_token'] = [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'kode_periode',
            ];
        }
        if (!$this->db->fieldExists('allow_public_link', 'tracer_study_periode')) {
            $colsToAdd['allow_public_link'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'public_token',
            ];
        }

        if (!empty($colsToAdd)) {
            $this->forge->addColumn('tracer_study_periode', $colsToAdd);
        }

        // Pastikan unique index pada public_token
        $idx = $this->db->query("SHOW INDEX FROM tracer_study_periode WHERE Key_name = 'uq_ts_periode_public_token'")->getResultArray();
        if (empty($idx)) {
            $this->db->query('ALTER TABLE tracer_study_periode ADD UNIQUE KEY uq_ts_periode_public_token (public_token)');
        }

        // Backfill token acak untuk periode yang sudah ada namun public_token masih NULL
        $rows = $this->db->table('tracer_study_periode')
            ->select('id')
            ->where('public_token IS NULL', null, false)
            ->orWhere('public_token', '')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $token = bin2hex(random_bytes(16));
            $this->db->table('tracer_study_periode')
                ->where('id', (int) $row['id'])
                ->update(['public_token' => $token]);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('tracer_study_periode')) {
            return;
        }

        $idx = $this->db->query("SHOW INDEX FROM tracer_study_periode WHERE Key_name = 'uq_ts_periode_public_token'")->getResultArray();
        if (!empty($idx)) {
            $this->db->query('ALTER TABLE tracer_study_periode DROP INDEX uq_ts_periode_public_token');
        }

        foreach (['allow_public_link', 'public_token'] as $col) {
            if ($this->db->fieldExists($col, 'tracer_study_periode')) {
                $this->forge->dropColumn('tracer_study_periode', $col);
            }
        }
    }
}
