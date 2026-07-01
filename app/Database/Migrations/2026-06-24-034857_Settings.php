<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Settings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'setting_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'setting_value' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
        
        $this->forge->addKey('setting_key', true);
        $this->forge->createTable('settings', true);

        // Insert default values
        $data = [
            [
                'setting_key'   => 'batas_input_target',
                'setting_name'  => 'Batas Pengisian Target Bulanan',
                'setting_value' => '5',
                'description'   => 'Angka (tanggal) maksimal di bulan tersebut untuk mengisi target bulan berjalan.',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'batas_input_log',
                'setting_name'  => 'Batas Pelaporan Harian',
                'setting_value' => '3',
                'description'   => 'Jumlah hari maksimal (setelah tanggal kegiatan) untuk boleh mengisi laporan harian.',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];
        
        $this->db->table('settings')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('settings', true);
    }
}
