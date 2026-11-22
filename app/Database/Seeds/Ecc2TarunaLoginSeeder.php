<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder penyedia akun login untuk seluruh taruna aktif di master_taruna.
 * Username dan kata sandi awal mengikuti Nomor Taruna masing-masing (Opsi B).
 * Idempoten: akun yang sudah ada tidak ditimpa kata sandinya.
 */
class Ecc2TarunaLoginSeeder extends Seeder
{
    public function run(): void
    {
        helper('audit');

        $tarunas = $this->db->table('master_taruna')
            ->select('nomor_taruna, nama_lengkap, email, status_terkini')
            ->orderBy('nomor_taruna', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($tarunas)) {
            return;
        }

        $now          = date('Y-m-d H:i:s');
        $createdCount = 0;
        $skippedCount = 0;

        $this->db->transBegin();

        try {
            $existingUsers = [];
            $userRows      = $this->db->table('users')
                ->select('id, username')
                ->whereIn('username', array_column($tarunas, 'nomor_taruna'))
                ->get()
                ->getResultArray();

            foreach ($userRows as $u) {
                $existingUsers[(string) $u['username']] = (int) $u['id'];
            }

            foreach ($tarunas as $t) {
                $nomor = trim((string) ($t['nomor_taruna'] ?? ''));
                if ($nomor === '') {
                    continue;
                }

                $email = trim((string) ($t['email'] ?? ''));
                if ($email === '') {
                    $email = strtolower($nomor) . '@student.pktj.ac.id';
                }

                if (isset($existingUsers[$nomor])) {
                    $userId = $existingUsers[$nomor];
                    $this->ensureTarunaRole($userId, $now);
                    $skippedCount++;
                    continue;
                }

                $this->db->table('users')->insert([
                    'username'       => $nomor,
                    'password'       => password_hash($nomor, PASSWORD_DEFAULT),
                    'nama_lengkap'   => trim((string) ($t['nama_lengkap'] ?? $nomor)),
                    'email'          => $email,
                    'role'           => 'taruna',
                    'status_pegawai' => 'aktif',
                    'nip'            => '',
                    'jabatan'        => 'Taruna PKTJ',
                    'unit'           => 'Taruna',
                    'foto'           => 'default.png',
                ]);

                $newUserId = (int) $this->db->insertID();
                $this->ensureTarunaRole($newUserId, $now);
                $createdCount++;
            }

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaksi pembuatan akun taruna gagal disimpan.');
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }

        log_audit('CREATE', 'users', 'BATCH_TARUNA_LOGIN', null, [
            'total_taruna' => count($tarunas),
            'created'      => $createdCount,
            'existing'     => $skippedCount,
        ]);
    }

    private function ensureTarunaRole(int $userId, string $now): void
    {
        $exists = $this->db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_name', 'taruna')
            ->countAllResults();

        if ($exists === 0) {
            $this->db->table('user_roles')->insert([
                'user_id'    => $userId,
                'role_name'  => 'taruna',
                'created_at' => $now,
            ]);
        }
    }
}
