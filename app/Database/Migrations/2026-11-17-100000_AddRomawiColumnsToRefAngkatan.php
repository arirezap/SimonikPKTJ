<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRomawiColumnsToRefAngkatan extends Migration
{
    public function up()
    {
        $fields = [];
        if (!$this->db->fieldExists('angkatan_ke', 'ref_angkatan')) {
            $fields['angkatan_ke'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'tahun_masuk',
            ];
        }
        if (!$this->db->fieldExists('kode_romawi', 'ref_angkatan')) {
            $fields['kode_romawi'] = [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => true,
                'after'      => isset($fields['angkatan_ke']) || $this->db->fieldExists('angkatan_ke', 'ref_angkatan') ? 'angkatan_ke' : 'tahun_masuk',
            ];
        }
        if (!empty($fields)) {
            $this->forge->addColumn('ref_angkatan', $fields);
        }

        helper('ketarunaan');
        $now = date('Y-m-d H:i:s');

        // Pastikan tahun masuk 2026 hingga 5 tahun ke belakang (2021..2026) tersedia
        for ($tahun = 2021; $tahun <= 2026; $tahun++) {
            $meta   = ketarunaan_angkatan_meta($tahun);
            $exists = $this->db->table('ref_angkatan')
                ->where('tahun_masuk', $tahun)
                ->countAllResults();

            if ($exists === 0) {
                $this->db->table('ref_angkatan')->insert([
                    'tahun_masuk'     => $tahun,
                    'angkatan_ke'     => $meta['angkatan_ke'],
                    'kode_romawi'     => $meta['kode_romawi'],
                    'perkiraan_lulus' => $tahun + 4,
                    'is_aktif'        => 1,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);
            }
        }

        // Lengkapi kolom angkatan_ke & kode_romawi untuk seluruh baris ref_angkatan yang sudah ada
        $rows = $this->db->table('ref_angkatan')->get()->getResultArray();
        foreach ($rows as $row) {
            $tahun = (int) ($row['tahun_masuk'] ?? 0);
            $meta  = ketarunaan_angkatan_meta($tahun);
            $this->db->table('ref_angkatan')
                ->where('id', (int) $row['id'])
                ->update([
                    'angkatan_ke' => $meta['angkatan_ke'],
                    'kode_romawi' => $meta['kode_romawi'] !== '' ? $meta['kode_romawi'] : null,
                    'updated_at'  => $now,
                ]);
        }
    }

    public function down()
    {
        $cols = [];
        if ($this->db->fieldExists('kode_romawi', 'ref_angkatan')) {
            $cols[] = 'kode_romawi';
        }
        if ($this->db->fieldExists('angkatan_ke', 'ref_angkatan')) {
            $cols[] = 'angkatan_ke';
        }
        if (!empty($cols)) {
            $this->forge->dropColumn('ref_angkatan', $cols);
        }
    }
}
