<?php

namespace App\Libraries;

use App\Models\RiwayatAtasan;
use App\Models\User;
use CodeIgniter\Database\BaseConnection;

/**
 * RiwayatPenugasanService
 *
 * Layanan bisnis terpusat untuk mengelola siklus hidup penugasan unit kerja
 * dan atasan langsung berbasis periode (tabel riwayat_atasan), meliputi:
 * - Auto-snapshot periode lama & anti-overlap saat mutasi/rotasi
 * - Rotasi atasan langsung secara massal per pejabat/unit
 * - Penyelarasan & penyaringan unit kerja berbasis periode untuk Monitoring/Penilaian
 */
class RiwayatPenugasanService
{
    protected BaseConnection $db;
    protected User $userModel;
    protected RiwayatAtasan $riwayatModel;

    public function __construct(
        ?BaseConnection $db = null,
        ?User $userModel = null,
        ?RiwayatAtasan $riwayatModel = null
    ) {
        $this->db           = $db ?? \Config\Database::connect();
        $this->userModel    = $userModel ?? new User();
        $this->riwayatModel = $riwayatModel ?? new RiwayatAtasan();
    }

    /**
     * Menghitung rencana mutasi/pemotongan riwayat secara murni (tanpa efek samping DB)
     * agar dijamin bebas tumpang tindih (anti-overlap) dan bebas celah bulan (gap-free).
     *
     * @param array<int, array<string, mixed>> $existingRows Baris riwayat_atasan aktif pada tahun efektif (urut bulan_mulai ASC)
     * @param array<string, mixed>             $oldUser      Snapshot penugasan sebelum perubahan
     * @param array<string, mixed>             $newAssignment Penugasan baru
     * @param int                              $bulanEfektif Bulan mulai berlaku (1..12)
     * @param int                              $tahunEfektif Tahun mulai berlaku
     * @param string|null                      $keterangan   Catatan mutasi/SK opsional
     * @param int|null                         $nowYear      Tahun acuan sekarang (opsional untuk pengujian)
     * @param int|null                         $nowMonth     Bulan acuan sekarang (opsional untuk pengujian)
     * @return array{
     *   updates: list<array{id: int, bulan_selesai: int}>,
     *   deletes: list<int>,
     *   inserts: list<array<string, mixed>>,
     *   should_sync_profile: bool
     * }
     */
    public function computeMutationPlan(
        array $existingRows,
        array $oldUser,
        array $newAssignment,
        int $bulanEfektif,
        int $tahunEfektif,
        ?string $keterangan = null,
        ?int $nowYear = null,
        ?int $nowMonth = null
    ): array {
        $bulanEfektif = max(1, min(12, $bulanEfektif));
        $curYear      = $nowYear ?? (int) date('Y');
        $curMonth     = $nowMonth ?? (int) date('n');

        // Profil aktif di tabel users hanya disinkronkan bila mutasi sudah berlaku saat ini atau lampau
        $shouldSyncProfile = ($tahunEfektif < $curYear)
            || ($tahunEfektif === $curYear && $bulanEfektif <= $curMonth);

        $updates               = [];
        $deletes               = [];
        $inserts               = [];
        $hasPriorRow           = false;
        $hasCoveringPriorMonth = false;
        $maxPriorEnd           = 0;

        foreach ($existingRows as $row) {
            $rId    = (int) ($row['id'] ?? 0);
            $rStart = (int) ($row['bulan_mulai'] ?? 1);
            $rEnd   = (int) ($row['bulan_selesai'] ?? 12);

            if ($rEnd < $bulanEfektif) {
                $hasPriorRow = true;
                if ($rEnd > $maxPriorEnd) {
                    $maxPriorEnd = $rEnd;
                }
                if ($rEnd === $bulanEfektif - 1) {
                    $hasCoveringPriorMonth = true;
                }
            } elseif ($rStart < $bulanEfektif && $rEnd >= $bulanEfektif) {
                $updates[] = [
                    'id'            => $rId,
                    'bulan_selesai' => $bulanEfektif - 1,
                ];
                $hasPriorRow           = true;
                $hasCoveringPriorMonth = true;
                $maxPriorEnd           = max($maxPriorEnd, $bulanEfektif - 1);
            } else {
                // Baris yang mulai pada atau sesudah bulan efektif diganti penugasan baru
                if ($rId > 0) {
                    $deletes[] = $rId;
                }
            }
        }

        $oldUnit    = trim((string) ($oldUser['unit'] ?? ''));
        $oldUnitId  = !empty($oldUser['unit_id']) ? (int) $oldUser['unit_id'] : null;
        $oldAtasan  = (int) ($oldUser['atasan_id'] ?? 0);
        $oldJabatan = trim((string) ($oldUser['jabatan'] ?? ''));
        $oldRole    = trim((string) ($oldUser['role'] ?? 'user')) ?: 'user';

        $hasOldAssignmentData = ($oldUnit !== '' || $oldAtasan > 0 || $oldJabatan !== '');

        // Kunci periode sebelum bulan efektif agar penilaian lama tidak fallback ke unit/atasan baru
        if ($bulanEfektif > 1 && !$hasCoveringPriorMonth && $hasOldAssignmentData) {
            $snapshotStart = $hasPriorRow ? ($maxPriorEnd + 1) : 1;
            if ($snapshotStart <= $bulanEfektif - 1) {
                $inserts[] = [
                    'atasan_id'     => $oldAtasan,
                    'unit_id'       => $oldUnitId,
                    'unit'          => $oldUnit !== '' ? $oldUnit : null,
                    'jabatan'       => $oldJabatan !== '' ? $oldJabatan : null,
                    'role'          => $oldRole,
                    'tahun'         => $tahunEfektif,
                    'bulan_mulai'   => $snapshotStart,
                    'bulan_selesai' => $bulanEfektif - 1,
                    'is_aktif'      => 1,
                    'keterangan'    => 'Penugasan sebelum rotasi/mutasi',
                ];
            }
        }

        $newUnit    = trim((string) ($newAssignment['unit'] ?? ''));
        $newUnitId  = !empty($newAssignment['unit_id']) ? (int) $newAssignment['unit_id'] : null;
        $newAtasan  = (int) ($newAssignment['atasan_id'] ?? 0);
        $newJabatan = trim((string) ($newAssignment['jabatan'] ?? $oldJabatan));
        $newRole    = trim((string) ($newAssignment['role'] ?? $oldRole)) ?: 'user';
        $note       = trim((string) ($keterangan ?? ''));

        $inserts[] = [
            'atasan_id'     => $newAtasan,
            'unit_id'       => $newUnitId,
            'unit'          => $newUnit !== '' ? $newUnit : null,
            'jabatan'       => $newJabatan !== '' ? $newJabatan : null,
            'role'          => $newRole,
            'tahun'         => $tahunEfektif,
            'bulan_mulai'   => $bulanEfektif,
            'bulan_selesai' => 12,
            'is_aktif'      => 1,
            'keterangan'    => $note !== '' ? $note : 'Mutasi / rotasi penugasan',
        ];

        return [
            'updates'             => $updates,
            'deletes'             => $deletes,
            'inserts'             => $inserts,
            'should_sync_profile' => $shouldSyncProfile,
        ];
    }

    /**
     * Menerapkan snapshot & pencatatan mutasi/rotasi ke tabel riwayat_atasan.
     * Pemanggil diharapkan membungkus operasi ini dalam transaksi DB jika digabung dengan update tabel users.
     */
    public function recordMutation(
        int $userId,
        array $oldUser,
        array $newAssignment,
        int $bulanEfektif,
        int $tahunEfektif,
        ?string $keterangan = null,
        ?int $actorId = null
    ): array {
        if ($userId <= 0 || !$this->db->tableExists('riwayat_atasan')) {
            return [
                'recorded'            => false,
                'should_sync_profile' => true,
            ];
        }

        $existingRows = $this->db->table('riwayat_atasan')
            ->where('user_id', $userId)
            ->where('tahun', $tahunEfektif)
            ->where('is_aktif', 1)
            ->orderBy('bulan_mulai', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $plan = $this->computeMutationPlan(
            $existingRows,
            $oldUser,
            $newAssignment,
            $bulanEfektif,
            $tahunEfektif,
            $keterangan
        );

        foreach ($plan['updates'] as $upd) {
            $updPayload = ['bulan_selesai' => $upd['bulan_selesai']];
            if ($actorId !== null && $actorId > 0) {
                $updPayload['updated_by'] = $actorId;
            }
            $this->riwayatModel->update($upd['id'], $updPayload);
        }

        if (!empty($plan['deletes'])) {
            $this->db->table('riwayat_atasan')
                ->whereIn('id', $plan['deletes'])
                ->delete();
        }

        foreach ($plan['inserts'] as $ins) {
            $ins['user_id'] = $userId;
            if ($actorId !== null && $actorId > 0) {
                $ins['created_by'] = $actorId;
                $ins['updated_by'] = $actorId;
            }
            $this->riwayatModel->insert($ins);
        }

        return [
            'recorded'            => true,
            'should_sync_profile' => $plan['should_sync_profile'],
            'updates'             => count($plan['updates']),
            'deletes'             => count($plan['deletes']),
            'inserts'             => count($plan['inserts']),
        ];
    }

    /**
     * Memindahkan seluruh staf aktif dari atasan lama ke atasan baru mulai bulan efektif tertentu
     * dalam satu transaksi atomik tanpa menghapus riwayat penilaian bulan sebelumnya.
     */
    public function rotateSupervisorMass(
        int $oldAtasanId,
        int $newAtasanId,
        int $bulanEfektif,
        int $tahunEfektif,
        ?string $unitFilter = null,
        ?string $keterangan = null,
        ?int $actorId = null
    ): array {
        $bulanEfektif = max(1, min(12, $bulanEfektif));

        if ($oldAtasanId <= 0 || $newAtasanId <= 0) {
            return ['success' => false, 'message' => 'Pilih atasan lama dan atasan baru yang valid.'];
        }
        if ($oldAtasanId === $newAtasanId) {
            return ['success' => false, 'message' => 'Atasan baru tidak boleh sama dengan atasan lama.'];
        }

        $periodDate = sprintf('%04d-%02d-01', $tahunEfektif, $bulanEfektif);
        $stafList   = $this->userModel->getStaf($oldAtasanId, $periodDate);

        $unitFilterNorm = strtolower(trim((string) ($unitFilter ?? '')));
        $eligibleStaf   = [];

        foreach ($stafList as $staf) {
            $stafId = (int) ($staf['id'] ?? 0);
            if ($stafId <= 0 || $stafId === $newAtasanId) {
                continue;
            }
            if ($unitFilterNorm !== '') {
                $stafUnitNorm = strtolower(trim((string) ($staf['unit'] ?? '')));
                if ($stafUnitNorm !== $unitFilterNorm) {
                    continue;
                }
            }
            $eligibleStaf[] = $staf;
        }

        if (empty($eligibleStaf)) {
            return [
                'success' => false,
                'message' => 'Tidak ditemukan staf aktif di bawah atasan lama pada periode dan filter tersebut.',
            ];
        }

        $affectedIds       = [];
        $shouldSyncProfile = false;

        $this->db->transStart();
        try {
            foreach ($eligibleStaf as $staf) {
                $stafId = (int) $staf['id'];

                $oldSnapshot = [
                    'atasan_id' => $oldAtasanId,
                    'unit'      => $staf['unit'] ?? '',
                    'unit_id'   => $staf['unit_id'] ?? null,
                    'jabatan'   => $staf['jabatan'] ?? '',
                    'role'      => $staf['role'] ?? 'user',
                ];

                $newAssignment = [
                    'atasan_id' => $newAtasanId,
                    'unit'      => $staf['unit'] ?? '',
                    'unit_id'   => $staf['unit_id'] ?? null,
                    'jabatan'   => $staf['jabatan'] ?? '',
                    'role'      => $staf['role'] ?? 'user',
                ];

                $res = $this->recordMutation(
                    $stafId,
                    $oldSnapshot,
                    $newAssignment,
                    $bulanEfektif,
                    $tahunEfektif,
                    $keterangan ?: 'Rotasi atasan langsung massal',
                    $actorId
                );

                if (!empty($res['should_sync_profile'])) {
                    $shouldSyncProfile = true;
                    $this->userModel->update($stafId, ['atasan_id' => $newAtasanId]);
                }

                $affectedIds[] = $stafId;
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return ['success' => false, 'message' => 'Gagal memproses rotasi atasan. Silakan coba lagi.'];
            }

            return [
                'success'             => true,
                'affected_count'      => count($affectedIds),
                'affected_ids'        => $affectedIds,
                'should_sync_profile' => $shouldSyncProfile,
            ];
        } catch (\Throwable $e) {
            try {
                @$this->db->transRollback();
            } catch (\Throwable $t) {
            }
            log_message('error', 'Error rotateSupervisorMass: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal memproses rotasi atasan. Silakan coba lagi.'];
        }
    }

    /**
     * Menyelaraskan unit, unit_id, jabatan, dan atasan_id seluruh pegawai berdasarkan
     * riwayat_atasan pada periode terkait (1 batch query, bebas N+1), lalu menyaring
     * pegawai sesuai filter unit efektif periode tersebut.
     *
     * @param array<int, array<string, mixed>> $employees
     * @param string                           $unitFilter
     * @param string                           $bulanTerpilih '1'..'12' atau 'all'
     * @param int                              $tahunTerpilih
     * @return array<int, array<string, mixed>>
     */
    public function applyPeriodAssignmentsAndUnitFilter(
        array $employees,
        string $unitFilter,
        string $bulanTerpilih,
        int $tahunTerpilih
    ): array {
        if (empty($employees)) {
            return [];
        }

        $userIds        = array_values(array_unique(array_filter(array_map('intval', array_column($employees, 'id')))));
        $unitFilterNorm = strtolower(trim($unitFilter));
        $isYearly       = ($bulanTerpilih === 'all' || $bulanTerpilih === '');
        $bulanCek       = $isYearly ? (int) date('n') : max(1, min(12, (int) $bulanTerpilih));

        $penugasanBatch = $this->userModel->getPenugasanPeriodeBatch($userIds, $bulanCek, $tahunTerpilih);

        // Untuk mode Sepanjang Tahun + filter unit aktif, kumpulkan seluruh unit yang pernah ditempati tiap user di tahun itu
        $yearlyUnitsByUser = [];
        $hasFullYearCoverage = [];
        if ($isYearly && $unitFilterNorm !== '' && !empty($userIds) && $this->db->tableExists('riwayat_atasan')) {
            $yearRows = $this->db->table('riwayat_atasan')
                ->select('user_id, unit, bulan_mulai, bulan_selesai')
                ->whereIn('user_id', $userIds)
                ->where('tahun', $tahunTerpilih)
                ->where('is_aktif', 1)
                ->get()
                ->getResultArray();

            $monthsCovered = [];
            foreach ($yearRows as $yr) {
                $uid   = (int) ($yr['user_id'] ?? 0);
                $uName = strtolower(trim((string) ($yr['unit'] ?? '')));
                if ($uid > 0 && $uName !== '') {
                    $yearlyUnitsByUser[$uid][$uName] = true;
                }
                if ($uid > 0) {
                    $s = max(1, (int) ($yr['bulan_mulai'] ?? 1));
                    $e = min(12, (int) ($yr['bulan_selesai'] ?? 12));
                    for ($m = $s; $m <= $e; $m++) {
                        $monthsCovered[$uid][$m] = true;
                    }
                }
            }
            foreach ($monthsCovered as $uid => $mMap) {
                if (count($mMap) >= 12) {
                    $hasFullYearCoverage[$uid] = true;
                }
            }
        }

        $filtered = [];
        foreach ($employees as $emp) {
            $uid       = (int) ($emp['id'] ?? 0);
            $penugasan = $penugasanBatch[$uid] ?? null;

            if ($penugasan) {
                if (!empty($penugasan['unit'])) {
                    $emp['unit'] = $penugasan['unit'];
                }
                if (!empty($penugasan['unit_id'])) {
                    $emp['unit_id'] = (int) $penugasan['unit_id'];
                }
                if (!empty($penugasan['jabatan'])) {
                    $emp['jabatan'] = $penugasan['jabatan'];
                }
                if (array_key_exists('atasan_id', $penugasan) && $penugasan['atasan_id'] !== null) {
                    $emp['atasan_id'] = (int) $penugasan['atasan_id'];
                }
                $emp['_has_riwayat_periode'] = true;
            } else {
                $emp['_has_riwayat_periode'] = false;
            }

            if ($unitFilterNorm !== '') {
                $effectiveUnitNorm = strtolower(trim((string) ($emp['unit'] ?? '')));
                if ($isYearly) {
                    $matchedInHistory = !empty($yearlyUnitsByUser[$uid][$unitFilterNorm]);
                    $matchedFallback  = empty($hasFullYearCoverage[$uid]) && ($effectiveUnitNorm === $unitFilterNorm);
                    if (!$matchedInHistory && !$matchedFallback) {
                        continue;
                    }
                } elseif ($effectiveUnitNorm !== $unitFilterNorm) {
                    continue;
                }
            }

            $filtered[] = $emp;
        }

        return $filtered;
    }

    /**
     * Mengambil daftar unit kerja untuk dropdown filter monitoring/penilaian:
     * gabungan unit aktif pada tabel users dan unit historis pada riwayat_atasan tahun terpilih.
     *
     * @return list<string>
     */
    public function getSelectableUnitsForYear(int $tahun): array
    {
        $unitSet = [];

        $unitQuery = $this->userModel->select('unit')
            ->distinct()
            ->where('unit !=', null)
            ->where('unit !=', '');
        User::excludeSelfServiceFromQuery($unitQuery);
        foreach ($unitQuery->findAll() as $u) {
            $name = trim((string) ($u['unit'] ?? ''));
            if ($name !== '') {
                $unitSet[$name] = true;
            }
        }

        if ($this->db->tableExists('riwayat_atasan')) {
            $histUnits = $this->db->table('riwayat_atasan')
                ->select('unit')
                ->distinct()
                ->where('tahun', $tahun)
                ->where('is_aktif', 1)
                ->where('unit !=', null)
                ->where('unit !=', '')
                ->get()
                ->getResultArray();
            foreach ($histUnits as $hu) {
                $name = trim((string) ($hu['unit'] ?? ''));
                if ($name !== '') {
                    $unitSet[$name] = true;
                }
            }
        }

        $result = array_keys($unitSet);
        sort($result, SORT_NATURAL | SORT_FLAG_CASE);
        return $result;
    }
}
