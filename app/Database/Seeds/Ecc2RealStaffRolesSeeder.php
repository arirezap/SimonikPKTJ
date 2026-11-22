<?php

namespace App\Database\Seeds;

use App\Models\SettingModel;
use CodeIgniter\Database\Seeder;

/**
 * Seeder idempoten penugasan peran ECC 2.0 ke 21 pegawai PKTJ nyata lintas 3 unit kerja,
 * penghapusan permanen 2 akun simulasi staf tersisa, dan peralihan ke mode Data Operasional.
 */
class Ecc2RealStaffRolesSeeder extends Seeder
{
    /** @var array<int, string> */
    private const SIMULATION_USERNAMES = ['sim_pusbangkar', 'sim_karir_alumni'];

    /**
     * Peta penugasan peran tambahan ECC 2.0 per pegawai berdasarkan NIP/username.
     * Peran ECC 1.0 eksisting tetap dipertahankan utuh.
     *
     * @var array<string, array<int, string>>
     */
    private const STAFF_ROLE_ASSIGNMENTS = [
        // Unit 6: Tim Substansi Bidang Administrasi Ketarunaan dan Alumni (PIC Ketarunaan, Alumni & Tracer Study)
        '198507012008121002' => ['manajemen', 'ketarunaan', 'ketarunaan_validator', 'karir_alumni'], // AJI RONALDO
        '199108122025211022' => ['ketarunaan', 'karir_alumni'],                                        // AGUS SUTRISNO
        '199502132019022003' => ['ketarunaan', 'karir_alumni'],                                        // ASTRI LESTARI
        '197601041998031001' => ['ketarunaan', 'karir_alumni'],                                        // SUPARTONO

        // Unit 10: Pusat Pengembangan Karakter (Pusbangkar)
        '198501072008121003' => ['manajemen', 'pusbangkar'], // SUGIYARTO
        '199506052022031019' => ['pusbangkar'],              // DARU ADE JUNIARTO
        '200011102023102001' => ['pusbangkar'],              // DENISYA HADDAD FIRDAUS
        '199704212022032017' => ['pusbangkar'],              // DIAJENG AYU DEWI PRITO
        '199401122023212029' => ['pusbangkar'],              // EMA PRATAMI ROSYADA
        '197609062009121001' => ['pusbangkar'],              // FATKHUROZAK
        '198401262008121001' => ['pusbangkar'],              // GUNAWAN
        '198608082025211042' => ['pusbangkar'],              // HERI PURNOMO
        '197906162025211021' => ['pusbangkar'],              // LUKMAN HAKIM
        '199811252023211001' => ['pusbangkar'],              // MASRUKHIN
        '200010252023101001' => ['pusbangkar'],              // MUHAMMAD ISRO' RISQI
        '199907122023102001' => ['pusbangkar'],              // NADILLA EKA KUSUMAWATI
        '199403222024212016' => ['pusbangkar'],              // SHELLA ARIES TANTIA YUSUF
        '198909082025211028' => ['pusbangkar'],              // SHOLEHUDIN

        // Unit 4: Tim Substansi Bidang Kerjasama (Mitra Perusahaan & Share ke Mitra Pengguna Lulusan)
        '197006041996031002' => ['manajemen', 'kerjasama'], // MOHAMAD HERMAWAN
        '198503112008121004' => ['kerjasama'],              // FAHRIZAL ADHIKRISNA
        '197311092005022001' => ['kerjasama'],              // NOVITASARI TRI NUGRAH ENI
    ];

    public function run(): void
    {
        helper('audit');

        $this->db->transBegin();

        try {
            $this->ensureAdminPivotRole();
            $this->cleanupSimulationAccounts();
            $this->assignRealStaffRoles();
            $this->switchDataModeToProduction();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaksi penetapan peran pegawai PKTJ gagal disimpan.');
            }

            $this->db->transCommit();
            SettingModel::clearCache();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    private function ensureAdminPivotRole(): void
    {
        $admin = $this->db->table('users')
            ->select('id')
            ->where('username', 'admin')
            ->get()
            ->getRowArray();

        if ($admin) {
            $this->mergeUserRoles((int) $admin['id'], ['admin']);
        }
    }

    private function cleanupSimulationAccounts(): void
    {
        $simUsers = $this->db->table('users')
            ->select('id, username, nama_lengkap, role, unit')
            ->whereIn('username', self::SIMULATION_USERNAMES)
            ->get()
            ->getResultArray();

        if (empty($simUsers)) {
            return;
        }

        $simIds = array_map(static fn (array $row): int => (int) $row['id'], $simUsers);

        $this->db->table('user_roles')->whereIn('user_id', $simIds)->delete();
        $this->db->table('users')->whereIn('id', $simIds)->delete();

        foreach ($simUsers as $simUser) {
            log_audit(
                'DELETE',
                'users',
                (int) $simUser['id'],
                [
                    'username'     => $simUser['username'],
                    'nama_lengkap' => $simUser['nama_lengkap'],
                    'role'         => $simUser['role'],
                ],
                ['keterangan' => 'Pembersihan akun simulasi ECC 2.0 saat peralihan ke peran pegawai nyata.']
            );
        }
    }

    private function assignRealStaffRoles(): void
    {
        $usernames = array_keys(self::STAFF_ROLE_ASSIGNMENTS);
        $userRows  = $this->db->table('users')
            ->select('id, username, nama_lengkap, role')
            ->whereIn('username', $usernames)
            ->get()
            ->getResultArray();

        $byUsername = [];
        foreach ($userRows as $row) {
            $byUsername[(string) $row['username']] = $row;
        }

        foreach (self::STAFF_ROLE_ASSIGNMENTS as $username => $ecc2Roles) {
            if (!isset($byUsername[$username])) {
                throw new \RuntimeException('Akun pegawai dengan NIP/username "' . $username . '" tidak ditemukan.');
            }

            $user         = $byUsername[$username];
            $userId       = (int) $user['id'];
            $primaryRole  = strtolower(trim((string) ($user['role'] ?? 'user')));
            $oldRoles     = $this->getUserRoles($userId);
            $targetRoles  = array_values(array_unique(array_merge([$primaryRole], $ecc2Roles)));

            $addedRoles = $this->mergeUserRoles($userId, $targetRoles);
            if (!empty($addedRoles)) {
                log_audit(
                    'UPDATE',
                    'user_roles',
                    $userId,
                    ['roles' => $oldRoles],
                    [
                        'roles'      => $this->getUserRoles($userId),
                        'nama'       => $user['nama_lengkap'],
                        'keterangan' => 'Penetapan peran ECC 2.0 pada pegawai aktif PKTJ.',
                    ]
                );
            }
        }
    }

    private function switchDataModeToProduction(): void
    {
        $row = $this->db->table('settings')
            ->select('setting_key, setting_value')
            ->where('setting_key', 'ecc2_data_mode')
            ->get()
            ->getRowArray();

        $now      = date('Y-m-d H:i:s');
        $oldValue = $row['setting_value'] ?? null;

        if ($row) {
            if ($oldValue !== 'produksi') {
                $this->db->table('settings')
                    ->where('setting_key', 'ecc2_data_mode')
                    ->update([
                        'setting_value' => 'produksi',
                        'updated_at'    => $now,
                    ]);

                log_audit(
                    'UPDATE',
                    'settings',
                    'ecc2_data_mode',
                    ['ecc2_data_mode' => $oldValue],
                    ['ecc2_data_mode' => 'produksi']
                );
            }
            return;
        }

        $this->db->table('settings')->insert([
            'setting_key'   => 'ecc2_data_mode',
            'setting_name'  => 'Penanda Jenis Data ECC 2.0',
            'setting_value' => 'produksi',
            'description'   => 'Menentukan apakah laporan dan ringkasan ECC 2.0 bertanda Data Simulasi atau Data Operasional.',
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        log_audit(
            'CREATE',
            'settings',
            'ecc2_data_mode',
            null,
            ['ecc2_data_mode' => 'produksi']
        );
    }

    /**
     * Menambahkan peran yang belum ada tanpa menghapus peran eksisting.
     *
     * @param array<int, string> $roles
     * @return array<int, string> Peran yang baru ditambahkan
     */
    private function mergeUserRoles(int $userId, array $roles): array
    {
        $existing = $this->getUserRoles($userId);
        $now      = date('Y-m-d H:i:s');
        $added    = [];

        foreach ($roles as $rawRole) {
            $roleName = strtolower(trim($rawRole));
            if ($roleName === '' || in_array($roleName, $existing, true)) {
                continue;
            }

            $this->db->table('user_roles')->insert([
                'user_id'    => $userId,
                'role_name'  => $roleName,
                'created_at' => $now,
            ]);
            $existing[] = $roleName;
            $added[]    = $roleName;
        }

        return $added;
    }

    /**
     * @return array<int, string>
     */
    private function getUserRoles(int $userId): array
    {
        $rows = $this->db->table('user_roles')
            ->select('role_name')
            ->where('user_id', $userId)
            ->get()
            ->getResultArray();

        return array_values(array_unique(array_filter(array_map(
            static fn (array $r): string => strtolower(trim((string) ($r['role_name'] ?? ''))),
            $rows
        ))));
    }
}
