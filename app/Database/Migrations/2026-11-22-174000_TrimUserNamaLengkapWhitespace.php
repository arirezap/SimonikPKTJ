<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TrimUserNamaLengkapWhitespace extends Migration
{
    public function up()
    {
        $rows = $this->db->table('users')
            ->select('id, nama_lengkap')
            ->where('nama_lengkap IS NOT NULL')
            ->get()
            ->getResultArray();

        $updates = [];
        foreach ($rows as $row) {
            $raw     = (string) $row['nama_lengkap'];
            $trimmed = preg_replace('/^[\s\p{Z}\x{200B}\x{FEFF}]+|[\s\p{Z}\x{200B}\x{FEFF}]+$/u', '', $raw);
            if ($trimmed !== null && $trimmed !== $raw) {
                $updates[] = [
                    'id'           => (int) $row['id'],
                    'nama_lengkap' => $trimmed,
                ];
            }
        }

        if (!empty($updates)) {
            $this->db->transStart();
            $this->db->table('users')->updateBatch($updates, 'id');
            $this->db->transComplete();
        }
    }

    public function down()
    {
        // Pembersihan spasi awal/akhir tidak dikembalikan
    }
}
