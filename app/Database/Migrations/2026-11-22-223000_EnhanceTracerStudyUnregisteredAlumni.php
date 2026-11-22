<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceTracerStudyUnregisteredAlumni extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('tracer_study_periode')) {
            if (!$this->db->fieldExists('allow_unregistered_alumni', 'tracer_study_periode')) {
                $this->forge->addColumn('tracer_study_periode', [
                    'allow_unregistered_alumni' => [
                        'type'       => 'TINYINT',
                        'constraint' => 1,
                        'default'    => 1,
                        'after'      => 'allow_public_link',
                    ],
                ]);
            }
        }

        if ($this->db->tableExists('tracer_study_respons')) {
            // Perluas ENUM status_validasi agar mendukung 'ditolak'
            $this->db->query("ALTER TABLE tracer_study_respons MODIFY COLUMN status_validasi ENUM('diajukan','tervalidasi','perlu_perbaikan','ditolak') NOT NULL DEFAULT 'tervalidasi'");

            $newCols = [
                'is_alumni_terdaftar' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'after'      => 'nomor_taruna',
                ],
                'nama_responden' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                    'after'      => 'is_alumni_terdaftar',
                ],
                'prodi_id_responden' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'nama_responden',
                ],
                'tahun_lulus_responden' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 4,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'prodi_id_responden',
                ],
                'tanggal_lahir_responden' => [
                    'type'  => 'DATE',
                    'null'  => true,
                    'after' => 'tahun_lulus_responden',
                ],
                'no_hp_responden' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'null'       => true,
                    'after'      => 'tanggal_lahir_responden',
                ],
                'email_responden' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                    'after'      => 'no_hp_responden',
                ],
            ];

            $toAdd = [];
            foreach ($newCols as $name => $def) {
                if (!$this->db->fieldExists($name, 'tracer_study_respons')) {
                    $toAdd[$name] = $def;
                }
            }
            if (!empty($toAdd)) {
                $this->forge->addColumn('tracer_study_respons', $toAdd);
            }

            $idx = $this->db->query("SHOW INDEX FROM tracer_study_respons WHERE Key_name = 'idx_ts_resp_validasi'")->getResultArray();
            if (empty($idx)) {
                $this->db->query('ALTER TABLE tracer_study_respons ADD INDEX idx_ts_resp_validasi (periode_id, status_validasi, is_alumni_terdaftar)');
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('tracer_study_respons')) {
            $idx = $this->db->query("SHOW INDEX FROM tracer_study_respons WHERE Key_name = 'idx_ts_resp_validasi'")->getResultArray();
            if (!empty($idx)) {
                $this->db->query('ALTER TABLE tracer_study_respons DROP INDEX idx_ts_resp_validasi');
            }
            foreach (['email_responden', 'no_hp_responden', 'tanggal_lahir_responden', 'tahun_lulus_responden', 'prodi_id_responden', 'nama_responden', 'is_alumni_terdaftar'] as $col) {
                if ($this->db->fieldExists($col, 'tracer_study_respons')) {
                    $this->forge->dropColumn('tracer_study_respons', $col);
                }
            }
        }
        if ($this->db->tableExists('tracer_study_periode') && $this->db->fieldExists('allow_unregistered_alumni', 'tracer_study_periode')) {
            $this->forge->dropColumn('tracer_study_periode', 'allow_unregistered_alumni');
        }
    }
}
