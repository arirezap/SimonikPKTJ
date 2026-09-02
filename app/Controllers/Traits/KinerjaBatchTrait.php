<?php

namespace App\Controllers\Traits;

trait KinerjaBatchTrait
{
    /**
     * Memuat seluruh target kinerja dan tugas tambahan tahun bersangkutan dalam 2 query efisien (Batch Fetching)
     * Mencegah N+1 query dan Memory Exhaustion pada CodeIgniter Debug Toolbar.
     *
     * @param int $tahun
     * @param array|null $userIds Opsional: Batasi hanya ke daftar user ID tertentu (optimasi query staf biasa)
     * @return array [$batchTargets, $batchTambahan]
     */
    protected function loadBatchKinerjaData($tahun, ?array $userIds = null)
    {
        $db = \Config\Database::connect();
        
        $targetBuilder = $db->table('target_kinerja_bulanan')
            ->select('id, user_id, bulan, nilai_capaian, status_penilaian')
            ->where('tahun', (int)$tahun)
            ->where('status', 'terkirim');

        if (!empty($userIds)) {
            $targetBuilder->whereIn('user_id', array_map('intval', $userIds));
        }

        $targets = $targetBuilder->get()->getResultArray();

        $batchTargets = [];
        foreach ($targets as $t) {
            $uid = (int)$t['user_id'];
            $b = (int)$t['bulan'];
            $batchTargets[$uid][$b][] = $t;
        }

        $tambahanBuilder = $db->table('log_tugas_tambahan')
            ->select('id, user_id, MONTH(tanggal_kegiatan) as bulan, nilai_capaian, status_penilaian')
            ->where('YEAR(tanggal_kegiatan)', (int)$tahun)
            ->where('status', 'terkirim');

        if (!empty($userIds)) {
            $tambahanBuilder->whereIn('user_id', array_map('intval', $userIds));
        }

        $tambahan = $tambahanBuilder->get()->getResultArray();

        $batchTambahan = [];
        foreach ($tambahan as $tmb) {
            $uid = (int)$tmb['user_id'];
            $b = (int)$tmb['bulan'];
            $batchTambahan[$uid][$b][] = $tmb;
        }

        return [$batchTargets, $batchTambahan];
    }

    /**
     * Menghitung capaian kinerja resmi pegawai (Target RHK + Tugas Tambahan)
     * Hanya menghitung nilai yang sudah resmi diterbitkan (status_penilaian = 'terbit').
     * Nilai yang masih draf / simpan sementara tidak dimunculkan di dashboard.
     *
     * @param int $userId
     * @param string|int $bulan 'all' atau 1..12
     * @param int $tahun
     * @param array|null $batchTargets
     * @param array|null $batchTambahan
     * @return array ['rata_rata' => float, 'total_laporan' => int, 'dinilai' => int, 'monthly_averages' => array|null]
     */
    protected function hitungKinerjaPegawai($userId, $bulan, $tahun, $batchTargets = null, $batchTambahan = null)
    {
        $userId = (int)$userId;
        if ($batchTargets === null || $batchTambahan === null) {
            [$batchTargets, $batchTambahan] = $this->loadBatchKinerjaData($tahun, [$userId]);
        }

        if ($bulan === 'all' || $bulan === '' || $bulan === null) {
            $rataRataPerBulan = array_fill(1, 12, null);
            $targetsPerBulan = array_fill(1, 12, 0);
            $pokokPerBulan = array_fill(1, 12, 0);
            $tambahanPerBulan = array_fill(1, 12, 0);
            $dinilaiPerBulan = array_fill(1, 12, 0);
            $nilaiPerBulan = array_fill(1, 12, 0);

            for ($m = 1; $m <= 12; $m++) {
                $userRhkMonth = $batchTargets[$userId][$m] ?? [];
                foreach ($userRhkMonth as $rd) {
                    $pokokPerBulan[$m]++;
                    $targetsPerBulan[$m]++;
                    if (($rd['status_penilaian'] ?? '') === 'terbit' && $rd['nilai_capaian'] !== null && trim((string)$rd['nilai_capaian']) !== '') {
                        $dinilaiPerBulan[$m]++;
                        $nilaiPerBulan[$m] += (float)$rd['nilai_capaian'];
                    }
                }

                $userTmbMonth = $batchTambahan[$userId][$m] ?? [];
                if (!empty($userTmbMonth)) {
                    $tambahanPerBulan[$m] += count($userTmbMonth);
                    $targetsPerBulan[$m]++;
                    $scoreM = null;
                    foreach ($userTmbMonth as $tmb) {
                        if (($tmb['status_penilaian'] ?? '') === 'terbit' && $tmb['nilai_capaian'] !== null && trim((string)$tmb['nilai_capaian']) !== '') {
                            $scoreM = (float)$tmb['nilai_capaian'];
                            break;
                        }
                    }
                    if ($scoreM !== null) {
                        $dinilaiPerBulan[$m]++;
                        $nilaiPerBulan[$m] += $scoreM;
                    }
                }

                if ($targetsPerBulan[$m] > 0) {
                    $rataRataPerBulan[$m] = $dinilaiPerBulan[$m] > 0 ? round($nilaiPerBulan[$m] / $dinilaiPerBulan[$m], 2) : 0;
                }
            }

            $validMonths = array_filter($rataRataPerBulan, fn($v) => $v !== null && $v > 0);
            $rataRata = count($validMonths) > 0 ? round(array_sum($validMonths) / count($validMonths), 2) : 0;
            $totalLaporan = array_sum($targetsPerBulan);
            $totalPokok = array_sum($pokokPerBulan);
            $totalTambahan = array_sum($tambahanPerBulan);
            $totalDinilai = array_sum($dinilaiPerBulan);

            return [
                'rata_rata' => $rataRata,
                'total_pokok' => $totalPokok,
                'total_tambahan' => $totalTambahan,
                'total_laporan' => $totalLaporan,
                'dinilai' => $totalDinilai,
                'monthly_averages' => $rataRataPerBulan
            ];
        } else {
            $m = (int)$bulan;
            $userRhkMonth = $batchTargets[$userId][$m] ?? [];
            $totalRhk = count($userRhkMonth);
            $dinilai = 0;
            $totalNilai = 0;

            foreach ($userRhkMonth as $rd) {
                // Hanya nilai yang sudah terbit / dipublish
                if (($rd['status_penilaian'] ?? '') === 'terbit' && $rd['nilai_capaian'] !== null && trim((string)$rd['nilai_capaian']) !== '') {
                    $dinilai++;
                    $totalNilai += (float)$rd['nilai_capaian'];
                }
            }

            $userTmbMonth = $batchTambahan[$userId][$m] ?? [];
            $hasTambahan = !empty($userTmbMonth);
            $totalTambahan = count($userTmbMonth);
            $scoreTambahan = null;
            if ($hasTambahan) {
                foreach ($userTmbMonth as $tmb) {
                    // Hanya nilai tugas tambahan yang sudah terbit / dipublish
                    if (($tmb['status_penilaian'] ?? '') === 'terbit' && $tmb['nilai_capaian'] !== null && trim((string)$tmb['nilai_capaian']) !== '') {
                        $scoreTambahan = (float)$tmb['nilai_capaian'];
                        break;
                    }
                }
                if ($scoreTambahan !== null) {
                    $dinilai++;
                    $totalNilai += $scoreTambahan;
                }
            }

            $totalKomponen = $totalRhk + ($hasTambahan ? 1 : 0);
            $rataRata = $dinilai > 0 ? round($totalNilai / $dinilai, 2) : 0;

            return [
                'rata_rata' => $rataRata,
                'total_pokok' => $totalRhk,
                'total_tambahan' => $totalTambahan,
                'total_laporan' => $totalKomponen,
                'dinilai' => $dinilai
            ];
        }
    }
}
