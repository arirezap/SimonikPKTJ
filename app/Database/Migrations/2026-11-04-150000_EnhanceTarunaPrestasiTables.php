<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceTarunaPrestasiTables extends Migration
{
    public function up()
    {
        $fieldsToAdd = [];

        if (!$this->db->fieldExists('lokasi_kegiatan', 'taruna_prestasi')) {
            $fieldsToAdd['lokasi_kegiatan'] = [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'penyelenggara',
            ];
        }
        if (!$this->db->fieldExists('pembimbing', 'taruna_prestasi')) {
            $fieldsToAdd['pembimbing'] = [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'nama_tim',
            ];
        }
        if (!$this->db->fieldExists('unit_pemilik', 'taruna_prestasi')) {
            $fieldsToAdd['unit_pemilik'] = [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'default'    => 'Ketarunaan',
                'after'      => 'pembimbing',
            ];
        }
        if (!$this->db->fieldExists('sumber_data', 'taruna_prestasi')) {
            $fieldsToAdd['sumber_data'] = [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'default'    => 'Piagam Penghargaan',
                'after'      => 'unit_pemilik',
            ];
        }
        if (!$this->db->fieldExists('ukuran_berkas_kb', 'taruna_prestasi')) {
            $fieldsToAdd['ukuran_berkas_kb'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1200,
                'after'      => 'bukti_berkas',
            ];
        }
        if (!$this->db->fieldExists('dokumentasi_json', 'taruna_prestasi')) {
            $fieldsToAdd['dokumentasi_json'] = [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'ukuran_berkas_kb',
            ];
        }
        if (!$this->db->fieldExists('catatan_validasi', 'taruna_prestasi')) {
            $fieldsToAdd['catatan_validasi'] = [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'status_validasi',
            ];
        }
        if (!$this->db->fieldExists('diajukan_oleh', 'taruna_prestasi')) {
            $fieldsToAdd['diajukan_oleh'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'PIC Ketarunaan',
                'after'      => 'catatan_validasi',
            ];
        }
        if (!$this->db->fieldExists('divalidasi_oleh', 'taruna_prestasi')) {
            $fieldsToAdd['divalidasi_oleh'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'diajukan_oleh',
            ];
        }
        if (!$this->db->fieldExists('tanggal_validasi', 'taruna_prestasi')) {
            $fieldsToAdd['tanggal_validasi'] = [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'divalidasi_oleh',
            ];
        }
        if (!$this->db->fieldExists('riwayat_validasi_json', 'taruna_prestasi')) {
            $fieldsToAdd['riwayat_validasi_json'] = [
                'type'  => 'LONGTEXT',
                'null'  => true,
                'after' => 'tanggal_validasi',
            ];
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('taruna_prestasi', $fieldsToAdd);
        }

        // Indeks untuk mempercepat kueri filter dan dashboard
        $tpIndexes = [
            'idx_tp_validasi'     => 'status_validasi',
            'idx_tp_keikutsertaan' => 'jenis_keikutsertaan',
            'idx_tp_tanggal'      => 'tanggal_mulai',
        ];
        foreach ($tpIndexes as $idxName => $colName) {
            $exists = $this->db->query("SHOW INDEX FROM taruna_prestasi WHERE Key_name = ?", [$idxName])->getResultArray();
            if (empty($exists)) {
                $this->db->query("ALTER TABLE taruna_prestasi ADD INDEX {$idxName} ({$colName})");
            }
        }

        // Pastikan tidak ada duplikat (prestasi_id, nomor_taruna) sebelum menambah UNIQUE INDEX
        $this->db->query("
            DELETE t1 FROM taruna_peserta_prestasi t1
            INNER JOIN taruna_peserta_prestasi t2
                ON t1.prestasi_id = t2.prestasi_id
                AND t1.nomor_taruna = t2.nomor_taruna
                AND t1.id > t2.id
        ");

        $uniqExists = $this->db->query("SHOW INDEX FROM taruna_peserta_prestasi WHERE Key_name = 'uniq_prestasi_taruna'")->getResultArray();
        if (empty($uniqExists)) {
            $this->db->query("ALTER TABLE taruna_peserta_prestasi ADD UNIQUE INDEX uniq_prestasi_taruna (prestasi_id, nomor_taruna)");
        }
    }

    public function down()
    {
        $uniqExists = $this->db->query("SHOW INDEX FROM taruna_peserta_prestasi WHERE Key_name = 'uniq_prestasi_taruna'")->getResultArray();
        if (!empty($uniqExists)) {
            $this->db->query("ALTER TABLE taruna_peserta_prestasi DROP INDEX uniq_prestasi_taruna");
        }

        foreach (['idx_tp_validasi', 'idx_tp_keikutsertaan', 'idx_tp_tanggal'] as $idxName) {
            $exists = $this->db->query("SHOW INDEX FROM taruna_prestasi WHERE Key_name = ?", [$idxName])->getResultArray();
            if (!empty($exists)) {
                $this->db->query("ALTER TABLE taruna_prestasi DROP INDEX {$idxName}");
            }
        }

        $cols = [
            'lokasi_kegiatan',
            'pembimbing',
            'unit_pemilik',
            'sumber_data',
            'ukuran_berkas_kb',
            'dokumentasi_json',
            'catatan_validasi',
            'diajukan_oleh',
            'divalidasi_oleh',
            'tanggal_validasi',
            'riwayat_validasi_json',
        ];
        foreach ($cols as $col) {
            if ($this->db->fieldExists($col, 'taruna_prestasi')) {
                $this->forge->dropColumn('taruna_prestasi', $col);
            }
        }
    }
}
