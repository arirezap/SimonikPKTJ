<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceGovernanceTables extends Migration
{
    public function up()
    {
        $fieldsToAdd = [];

        if (!$this->db->fieldExists('divalidasi_oleh', 'riwayat_status_taruna')) {
            $fieldsToAdd['divalidasi_oleh'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'status_validasi',
            ];
        }

        if (!$this->db->fieldExists('catatan_validasi', 'riwayat_status_taruna')) {
            $fieldsToAdd['catatan_validasi'] = [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'divalidasi_oleh',
            ];
        }

        if (!$this->db->fieldExists('tanggal_validasi', 'riwayat_status_taruna')) {
            $fieldsToAdd['tanggal_validasi'] = [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'catatan_validasi',
            ];
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('riwayat_status_taruna', $fieldsToAdd);
        }

        $idxExists = $this->db->query("SHOW INDEX FROM audit_logs WHERE Key_name = 'idx_audit_entity_id'")->getNumRows();
        if ($idxExists === 0) {
            $this->db->query('CREATE INDEX idx_audit_entity_id ON audit_logs (entity, id)');
        }

        $now = date('Y-m-d H:i:s');
        $defaultSettings = [
            [
                'setting_key'   => 'ecc2_data_mode',
                'setting_name'  => 'Penanda Jenis Data ECC 2.0',
                'setting_value' => 'simulasi',
                'description'   => 'Menentukan apakah laporan dan ringkasan ECC 2.0 bertanda Data Simulasi atau Data Operasional.',
            ],
            [
                'setting_key'   => 'ecc2_skala_ip_maks',
                'setting_name'  => 'Batas Maksimal Skala Indeks Prestasi',
                'setting_value' => '4.00',
                'description'   => 'Batas atas skala Indeks Prestasi pada modul pemeringkatan dan validasi nilai taruna.',
            ],
            [
                'setting_key'   => 'ecc2_batas_jam_lowongan',
                'setting_name'  => 'Batas Jam Tayang Lowongan Setelah Validasi',
                'setting_value' => '24',
                'description'   => 'Target jam penyelesaian pemeriksaan lowongan kerja dari diterima hingga terbit.',
            ],
            [
                'setting_key'   => 'ecc2_catatan_operasional',
                'setting_name'  => 'Catatan Operasional Modul ECC 2.0',
                'setting_value' => 'Lingkungan uji terstruktur dengan data simulasi terkendali.',
                'description'   => 'Catatan ringkas yang ditampilkan pada kepala laporan ketarunaan dan alumni.',
            ],
        ];

        foreach ($defaultSettings as $item) {
            $exists = $this->db->table('settings')
                ->where('setting_key', $item['setting_key'])
                ->countAllResults();

            if ($exists === 0) {
                $item['created_at'] = $now;
                $item['updated_at'] = $now;
                $this->db->table('settings')->insert($item);
            }
        }
    }

    public function down()
    {
        foreach (['tanggal_validasi', 'catatan_validasi', 'divalidasi_oleh'] as $col) {
            if ($this->db->fieldExists($col, 'riwayat_status_taruna')) {
                $this->forge->dropColumn('riwayat_status_taruna', $col);
            }
        }

        $idxExists = $this->db->query("SHOW INDEX FROM audit_logs WHERE Key_name = 'idx_audit_entity_id'")->getNumRows();
        if ($idxExists > 0) {
            $this->db->query('DROP INDEX idx_audit_entity_id ON audit_logs');
        }

        $this->db->table('settings')->whereIn('setting_key', [
            'ecc2_data_mode',
            'ecc2_skala_ip_maks',
            'ecc2_batas_jam_lowongan',
            'ecc2_catatan_operasional',
        ])->delete();
    }
}
