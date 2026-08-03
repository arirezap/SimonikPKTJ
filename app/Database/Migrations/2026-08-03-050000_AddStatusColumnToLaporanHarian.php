<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusColumnToLaporanHarian extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'terkirim'],
                'default'    => 'draft',
                'null'       => false,
                'after'      => 'status_approval'
            ],
        ];
        $this->forge->addColumn('laporan_harian', $fields);

        // Update existing records to 'terkirim' so current production data stays submitted
        $this->db->query("UPDATE laporan_harian SET status = 'terkirim'");
    }

    public function down()
    {
        $this->forge->dropColumn('laporan_harian', 'status');
    }
}
