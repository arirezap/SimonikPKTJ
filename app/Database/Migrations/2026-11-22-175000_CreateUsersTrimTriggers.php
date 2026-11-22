<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTrimTriggers extends Migration
{
    private function buildTrimExpr(string $colExpr): string
    {
        return "TRIM(TRIM(BOTH UNHEX('C2A0') FROM TRIM(BOTH '\\r' FROM TRIM(BOTH '\\n' FROM TRIM(BOTH '\\t' FROM TRIM({$colExpr}))))))";
    }

    public function up()
    {
        $this->db->query('DROP TRIGGER IF EXISTS `trg_users_trim_before_insert`');
        $this->db->query('DROP TRIGGER IF EXISTS `trg_users_trim_before_update`');

        $assignments = implode(",\n            ", [
            'NEW.`nama_lengkap` = ' . $this->buildTrimExpr('NEW.`nama_lengkap`'),
            'NEW.`jabatan` = ' . $this->buildTrimExpr('NEW.`jabatan`'),
            'NEW.`unit` = ' . $this->buildTrimExpr('NEW.`unit`'),
            'NEW.`nip` = ' . $this->buildTrimExpr('NEW.`nip`'),
            'NEW.`username` = ' . $this->buildTrimExpr('NEW.`username`'),
        ]);

        $this->db->query("
            CREATE TRIGGER `trg_users_trim_before_insert`
            BEFORE INSERT ON `users`
            FOR EACH ROW
            SET
            {$assignments}
        ");

        $this->db->query("
            CREATE TRIGGER `trg_users_trim_before_update`
            BEFORE UPDATE ON `users`
            FOR EACH ROW
            SET
            {$assignments}
        ");
    }

    public function down()
    {
        $this->db->query('DROP TRIGGER IF EXISTS `trg_users_trim_before_insert`');
        $this->db->query('DROP TRIGGER IF EXISTS `trg_users_trim_before_update`');
    }
}
