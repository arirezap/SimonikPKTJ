<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusPegawaiAndDispensasiToUsers extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $fieldsToAdd = [];

        if (!$db->fieldExists('status_pegawai', 'users')) {
            $fieldsToAdd['status_pegawai'] = [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'mutasi_keluar', 'pensiun', 'nonaktif'],
                'default'    => 'aktif',
                'null'       => false,
                'after'      => 'role',
            ];
        }

        if (!$db->fieldExists('tmt_selesai', 'users')) {
            $fieldsToAdd['tmt_selesai'] = [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
                'after'   => 'status_pegawai',
            ];
        }

        if (!$db->fieldExists('dispensasi_lampau_sampai', 'users')) {
            $fieldsToAdd['dispensasi_lampau_sampai'] = [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
                'after'   => 'tmt_selesai',
            ];
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('users', $fieldsToAdd);
        }

        // Tambahkan indeks untuk status_pegawai jika belum ada
        $indexQuery = $db->query("SHOW INDEX FROM `users` WHERE Key_name = 'idx_u_status_pegawai'");
        if (empty($indexQuery->getResultArray())) {
            $db->query("ALTER TABLE `users` ADD INDEX `idx_u_status_pegawai` (`status_pegawai`)");
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        if ($db->fieldExists('dispensasi_lampau_sampai', 'users')) {
            $this->forge->dropColumn('users', 'dispensasi_lampau_sampai');
        }
        if ($db->fieldExists('tmt_selesai', 'users')) {
            $this->forge->dropColumn('users', 'tmt_selesai');
        }
        if ($db->fieldExists('status_pegawai', 'users')) {
            $this->forge->dropColumn('users', 'status_pegawai');
        }
    }
}
