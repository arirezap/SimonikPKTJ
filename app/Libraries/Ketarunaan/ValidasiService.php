<?php

namespace App\Libraries\Ketarunaan;

use CodeIgniter\Database\BaseConnection;

/**
 * Service terpusat antrean validasi lintas modul, tinjauan rencana kembali,
 * dan jejak aktivitas ECC 2.0 (GOV-01..04, UAT-03).
 */
class ValidasiService
{
    protected BaseConnection $db;

    /**
     * Daftar entitas ECC 2.0 pada tabel audit_logs untuk tab Jejak Aktivitas.
     * Memanfaatkan indeks (entity, id) agar pencarian tetap cepat.
     */
    private const ECC2_AUDIT_ENTITIES = [
        'master_taruna',
        'pendidikan_taruna',
        'riwayat_status_taruna',
        'riwayat_pendidikan_taruna',
        'taruna_capaian',
        'taruna_prestasi',
        'taruna_pelanggaran',
        'taruna_program_beasiswa',
        'taruna_penerima_beasiswa',
        'perusahaan_mitra',
        'lowongan_pekerjaan',
        'lowongan_dokumen',
        'alumni_profil',
        'alumni_riwayat_perubahan',
        'ref_program_studi',
        'ref_angkatan',
        'ref_tahun_akademik',
        'ref_kelas',
        'ref_wilayah_3tp',
        'settings',
    ];

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
        helper('ketarunaan');
    }

    /**
     * Menghimpun antrean validasi lintas modul, ringkasan KPI, daftar rencana kembali cuti/skorsing,
     * dan jejak aktivitas ECC 2.0 sesuai hak akses pengguna.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getWorkspaceData(array $filters = []): array
    {
        $defaultTanggal = date('Y-m-d');
        $rawTanggal     = trim((string) ($filters['tanggal_acuan'] ?? $defaultTanggal));
        $dtCheck        = \DateTimeImmutable::createFromFormat('Y-m-d', $rawTanggal);
        $tanggalAcuan   = ($dtCheck !== false && $dtCheck->format('Y-m-d') === $rawTanggal) ? $rawTanggal : $defaultTanggal;
        $sumberFilter = strtolower(trim((string) ($filters['sumber'] ?? 'semua')));
        $statusFilter = strtolower(trim((string) ($filters['status'] ?? 'antrean')));
        $keyword      = trim((string) ($filters['q'] ?? ''));

        $allowedStatuses = $this->resolveStatusFilterValues($statusFilter);
        $items           = [];
        $countsBySource  = [];

        foreach (array_keys(ketarunaan_validasi_sumber_catalog()) as $kode) {
            $countsBySource[$kode] = [
                'kode'            => $kode,
                'label'           => ketarunaan_validasi_sumber_catalog()[$kode]['label'],
                'diajukan'        => 0,
                'perlu_perbaikan' => 0,
                'draf'            => 0,
                'total_antrean'   => 0,
            ];
        }

        // 1. Status Pendidikan Taruna
        $statusSummary = $this->countStatusGroup('riwayat_status_taruna', 'status_validasi');
        $countsBySource['status_pendidikan'] = array_merge($countsBySource['status_pendidikan'], $statusSummary);
        if ($sumberFilter === 'semua' || $sumberFilter === 'status_pendidikan') {
            $items = array_merge($items, $this->fetchStatusQueue($allowedStatuses, $keyword));
        }

        // 2. Capaian Taruna
        $capaianSummary = $this->countStatusGroup('taruna_capaian', 'status_validasi');
        $countsBySource['capaian'] = array_merge($countsBySource['capaian'], $capaianSummary);
        if ($sumberFilter === 'semua' || $sumberFilter === 'capaian') {
            $items = array_merge($items, $this->fetchCapaianQueue($allowedStatuses, $keyword));
        }

        // 3. Prestasi Taruna
        $prestasiSummary = $this->countStatusGroup('taruna_prestasi', 'status_validasi');
        $countsBySource['prestasi'] = array_merge($countsBySource['prestasi'], $prestasiSummary);
        if ($sumberFilter === 'semua' || $sumberFilter === 'prestasi') {
            $items = array_merge($items, $this->fetchPrestasiQueue($allowedStatuses, $keyword));
        }

        // 4. Pelanggaran / Disiplin (strict RBAC)
        if (KetarunaanPolicy::canAccessPelanggaranModule()) {
            $pelanggaranSummary = $this->countStatusGroup('taruna_pelanggaran', 'status_validasi');
            $countsBySource['pelanggaran'] = array_merge($countsBySource['pelanggaran'], $pelanggaranSummary);
            if ($sumberFilter === 'semua' || $sumberFilter === 'pelanggaran') {
                $items = array_merge($items, $this->fetchPelanggaranQueue($allowedStatuses, $keyword));
            }
        } else {
            unset($countsBySource['pelanggaran']);
        }

        // 5. Beasiswa Taruna (strict RBAC)
        if (KetarunaanPolicy::canAccessBeasiswaModule()) {
            $beasiswaSummary = $this->countStatusGroup('taruna_penerima_beasiswa', 'status_validasi');
            $countsBySource['beasiswa'] = array_merge($countsBySource['beasiswa'], $beasiswaSummary);
            if ($sumberFilter === 'semua' || $sumberFilter === 'beasiswa') {
                $items = array_merge($items, $this->fetchBeasiswaQueue($allowedStatuses, $keyword));
            }
        } else {
            unset($countsBySource['beasiswa']);
        }

        // 6. Lowongan Pekerjaan
        $lowonganSummary = $this->countStatusGroup('lowongan_pekerjaan', 'status_validasi');
        $countsBySource['lowongan'] = array_merge($countsBySource['lowongan'], $lowonganSummary);
        if ($sumberFilter === 'semua' || $sumberFilter === 'lowongan') {
            $items = array_merge($items, $this->fetchLowonganQueue($allowedStatuses, $keyword));
        }

        // 7. Koreksi / Pembatalan Alumni (strict RBAC)
        if (KetarunaanPolicy::canAccessAlumniModule()) {
            $alumniSummary = $this->countAlumniKoreksiGroup();
            $countsBySource['alumni'] = array_merge($countsBySource['alumni'], $alumniSummary);
            if ($sumberFilter === 'semua' || $sumberFilter === 'alumni') {
                $items = array_merge($items, $this->fetchAlumniKoreksiQueue($statusFilter, $keyword));
            }
        } else {
            unset($countsBySource['alumni']);
        }

        // 8. Pengajuan Foto Taruna (unggah mandiri taruna menunggu validasi)
        $fotoSummary = $this->countFotoTarunaGroup();
        $countsBySource['foto_taruna'] = array_merge($countsBySource['foto_taruna'], $fotoSummary);
        if ($sumberFilter === 'semua' || $sumberFilter === 'foto_taruna') {
            $items = array_merge($items, $this->fetchFotoTarunaQueue($allowedStatuses, $keyword));
        }

        usort($items, static function (array $a, array $b): int {
            $prio = ['diajukan' => 1, 'perlu_perbaikan' => 2, 'dikembalikan' => 2, 'draf' => 3, 'tervalidasi' => 4, 'ditolak' => 5];
            $pa   = $prio[$a['status_validasi']] ?? 9;
            $pb   = $prio[$b['status_validasi']] ?? 9;
            if ($pa !== $pb) {
                return $pa <=> $pb;
            }
            return strcmp((string) ($b['sort_date'] ?? ''), (string) ($a['sort_date'] ?? ''));
        });

        $rencanaKembaliList = $this->getRencanaKembaliList($tanggalAcuan);
        $auditTrail         = $this->getAuditTrail(40, trim((string) ($filters['entitas'] ?? '')));

        $totalDiajukan       = 0;
        $totalPerluPerbaikan = 0;
        foreach ($countsBySource as $grp) {
            $totalDiajukan += (int) ($grp['diajukan'] ?? 0);
            $totalPerluPerbaikan += (int) ($grp['perlu_perbaikan'] ?? 0);
        }

        return [
            'kpi' => [
                'menunggu'        => $totalDiajukan,
                'perlu_perbaikan' => $totalPerluPerbaikan,
                'total_antrean'   => $totalDiajukan + $totalPerluPerbaikan,
                'rencana_kembali' => count($rencanaKembaliList),
            ],
            'counts_by_source'     => $countsBySource,
            'items'                => $items,
            'rencana_kembali_list' => $rencanaKembaliList,
            'audit_trail'          => $auditTrail,
            'entity_options'       => $this->getAllowedEntityLabels(),
            'tanggal_acuan'        => $tanggalAcuan,
            'data_mode'            => ketarunaan_data_mode_info(),
        ];
    }

    /**
     * Daftar taruna berstatus Cuti/Skorsing tervalidasi yang sudah melewati tanggal rencana kembali (UAT-03).
     * Status pendidikan taruna tetap Cuti/Skorsing dan tidak pernah otomatis menjadi Aktif.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRencanaKembaliList(string $tanggalAcuan): array
    {
        $rows = $this->db->table('riwayat_status_taruna r')
            ->select('r.id, r.nomor_taruna, r.kode_status, r.mulai_berlaku, r.rencana_kembali, r.nomor_keputusan, r.alasan_perubahan, m.nama_lengkap, p.kode_prodi, a.tahun_masuk')
            ->join('master_taruna m', 'm.nomor_taruna = r.nomor_taruna')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = r.nomor_taruna', 'left')
            ->join('ref_program_studi p', 'p.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan a', 'a.id = pt.angkatan_id', 'left')
            ->where('r.status_validasi', 'tervalidasi')
            ->whereIn('r.kode_status', ['cuti', 'skorsing'])
            ->where('r.akhir_berlaku IS NULL')
            ->where('r.rencana_kembali IS NOT NULL')
            ->where('r.rencana_kembali <=', $tanggalAcuan)
            ->orderBy('r.rencana_kembali', 'ASC')
            ->get()
            ->getResultArray();

        $acuanTs = strtotime($tanggalAcuan) ?: time();

        foreach ($rows as &$row) {
            $rkTs               = strtotime((string) $row['rencana_kembali']) ?: $acuanTs;
            $row['hari_lewat']  = max(0, (int) floor(($acuanTs - $rkTs) / 86400));
            $row['detail_url']  = site_url('ketarunaan/taruna/profil/' . urlencode((string) $row['nomor_taruna']));
        }
        unset($row);

        return $rows;
    }

    /**
     * Mengambil jejak aktivitas terbaru pada tabel ECC 2.0 (GOV-04) tanpa mengekspos jam/menit ke UI.
     *
     * @return array<int, array<string, string>>
     */
    public function getAuditTrail(int $limit = 40, string $filterEntitas = ''): array
    {
        $allowedEntities = self::ECC2_AUDIT_ENTITIES;

        if (!KetarunaanPolicy::canAccessPelanggaranModule()) {
            $allowedEntities = array_values(array_diff($allowedEntities, ['taruna_pelanggaran']));
        }
        if (!KetarunaanPolicy::canAccessBeasiswaModule()) {
            $allowedEntities = array_values(array_diff($allowedEntities, ['taruna_program_beasiswa', 'taruna_penerima_beasiswa']));
        }
        if (!KetarunaanPolicy::canAccessAlumniModule()) {
            $allowedEntities = array_values(array_diff($allowedEntities, ['alumni_profil', 'alumni_riwayat_perubahan']));
        }

        if ($filterEntitas !== '' && in_array($filterEntitas, $allowedEntities, true)) {
            $allowedEntities = [$filterEntitas];
        }

        if (empty($allowedEntities)) {
            return [];
        }

        $rows = $this->db->table('audit_logs al')
            ->select('al.id, al.user_id, al.action, al.entity, al.entity_id, al.created_at, u.nama_lengkap, u.username')
            ->join('users u', 'u.id = al.user_id', 'left')
            ->whereIn('al.entity', $allowedEntities)
            ->orderBy('al.id', 'DESC')
            ->limit(max(1, min(100, $limit)))
            ->get()
            ->getResultArray();

        $actionLabels = [
            'CREATE'  => 'Tambah Data',
            'UPDATE'  => 'Pembaruan',
            'APPROVE' => 'Disetujui',
            'REJECT'  => 'Ditolak',
            'DELETE'  => 'Penghapusan',
            'EXPORT'  => 'Unduh Data',
            'IMPORT'  => 'Impor Data',
        ];

        $entityLabels = [
            'master_taruna'             => 'Biodata Taruna',
            'pendidikan_taruna'         => 'Pendidikan Taruna',
            'riwayat_status_taruna'     => 'Status Pendidikan',
            'riwayat_pendidikan_taruna' => 'Riwayat Tingkat / Kelas',
            'taruna_capaian'            => 'Capaian Taruna',
            'taruna_prestasi'           => 'Prestasi Taruna',
            'taruna_pelanggaran'        => 'Pembinaan & Pelanggaran',
            'taruna_program_beasiswa'   => 'Program Beasiswa',
            'taruna_penerima_beasiswa'  => 'Penerima Beasiswa',
            'perusahaan_mitra'          => 'Perusahaan Mitra',
            'lowongan_pekerjaan'        => 'Lowongan Pekerjaan',
            'lowongan_dokumen'          => 'Dokumen Lowongan',
            'alumni_profil'             => 'Profil Alumni',
            'alumni_riwayat_perubahan'  => 'Koreksi Alumni',
            'ref_program_studi'         => 'Rujukan Program Studi',
            'ref_angkatan'              => 'Rujukan Angkatan',
            'ref_tahun_akademik'        => 'Rujukan Tahun Akademik',
            'ref_kelas'                 => 'Rujukan Kelas',
            'ref_wilayah_3tp'           => 'Rujukan Wilayah 3TP',
            'settings'                  => 'Parameter Operasional Ketarunaan',
        ];

        $result = [];
        foreach ($rows as $row) {
            $aktor = trim((string) ($row['nama_lengkap'] ?? ''));
            if ($aktor === '') {
                $aktor = trim((string) ($row['username'] ?? '')) ?: 'Petugas Sistem';
            }

            $rawAction = strtoupper(trim((string) ($row['action'] ?? 'UPDATE')));
            $rawEntity = trim((string) ($row['entity'] ?? ''));
            $dateOnly  = substr((string) ($row['created_at'] ?? ''), 0, 10);

            $result[] = [
                'id'           => (string) $row['id'],
                'tanggal'      => ketarunaan_format_tanggal($dateOnly, true),
                'aktor'        => $aktor,
                'aksi'         => $actionLabels[$rawAction] ?? ucwords(strtolower(str_replace('_', ' ', $rawAction))),
                'entitas_kode' => $rawEntity,
                'modul'        => $entityLabels[$rawEntity] ?? ucwords(str_replace('_', ' ', $rawEntity)),
                'referensi'    => (string) ($row['entity_id'] ?? '-'),
            ];
        }

        return $result;
    }

    /**
     * Daftar opsi label entitas yang boleh diakses peran pengguna untuk filter Jejak Aktivitas.
     *
     * @return array<string, string>
     */
    private function getAllowedEntityLabels(): array
    {
        $allowedEntities = self::ECC2_AUDIT_ENTITIES;
        if (!KetarunaanPolicy::canAccessPelanggaranModule()) {
            $allowedEntities = array_values(array_diff($allowedEntities, ['taruna_pelanggaran']));
        }
        if (!KetarunaanPolicy::canAccessBeasiswaModule()) {
            $allowedEntities = array_values(array_diff($allowedEntities, ['taruna_program_beasiswa', 'taruna_penerima_beasiswa']));
        }
        if (!KetarunaanPolicy::canAccessAlumniModule()) {
            $allowedEntities = array_values(array_diff($allowedEntities, ['alumni_profil', 'alumni_riwayat_perubahan']));
        }

        $labels = [
            'master_taruna'             => 'Biodata Taruna',
            'pendidikan_taruna'         => 'Pendidikan Taruna',
            'riwayat_status_taruna'     => 'Status Pendidikan',
            'riwayat_pendidikan_taruna' => 'Riwayat Tingkat / Kelas',
            'taruna_capaian'            => 'Capaian Taruna',
            'taruna_prestasi'           => 'Prestasi Taruna',
            'taruna_pelanggaran'        => 'Pembinaan & Pelanggaran',
            'taruna_program_beasiswa'   => 'Program Beasiswa',
            'taruna_penerima_beasiswa'  => 'Penerima Beasiswa',
            'perusahaan_mitra'          => 'Perusahaan Mitra',
            'lowongan_pekerjaan'        => 'Lowongan Pekerjaan',
            'lowongan_dokumen'          => 'Dokumen Lowongan',
            'alumni_profil'             => 'Profil Alumni',
            'alumni_riwayat_perubahan'  => 'Koreksi Alumni',
            'ref_program_studi'         => 'Rujukan Program Studi',
            'ref_angkatan'              => 'Rujukan Angkatan',
            'ref_tahun_akademik'        => 'Rujukan Tahun Akademik',
            'ref_kelas'                 => 'Rujukan Kelas',
            'ref_wilayah_3tp'           => 'Rujukan Wilayah 3TP',
            'settings'                  => 'Parameter Operasional Ketarunaan',
        ];

        $out = [];
        foreach ($allowedEntities as $ent) {
            $out[$ent] = $labels[$ent] ?? ucwords(str_replace('_', ' ', $ent));
        }
        return $out;
    }

    /**
     * @return array<int, string>
     */
    private function resolveStatusFilterValues(string $statusFilter): array
    {
        return match ($statusFilter) {
            'diajukan'        => ['diajukan'],
            'perlu_perbaikan' => ['perlu_perbaikan'],
            'draf'            => ['draf'],
            'tervalidasi'     => ['tervalidasi'],
            'semua'           => ['diajukan', 'perlu_perbaikan', 'draf', 'tervalidasi'],
            default           => ['diajukan', 'perlu_perbaikan'],
        };
    }

    /**
     * @return array{diajukan: int, perlu_perbaikan: int, draf: int, total_antrean: int}
     */
    private function countStatusGroup(string $table, string $column): array
    {
        $rows = $this->db->table($table)
            ->select($column . ' AS st, COUNT(*) AS cnt')
            ->whereIn($column, ['diajukan', 'perlu_perbaikan', 'draf'])
            ->groupBy($column)
            ->get()
            ->getResultArray();

        $out = ['diajukan' => 0, 'perlu_perbaikan' => 0, 'draf' => 0, 'total_antrean' => 0];
        foreach ($rows as $r) {
            $st = (string) ($r['st'] ?? '');
            if (array_key_exists($st, $out)) {
                $out[$st] = (int) $r['cnt'];
            }
        }
        $out['total_antrean'] = $out['diajukan'] + $out['perlu_perbaikan'];

        return $out;
    }

    /**
     * @return array{diajukan: int, perlu_perbaikan: int, draf: int, total_antrean: int}
     */
    private function countAlumniKoreksiGroup(): array
    {
        $rows = $this->db->table('alumni_riwayat_perubahan')
            ->select('status_validasi AS st, COUNT(*) AS cnt')
            ->whereIn('status_validasi', ['diajukan', 'ditolak'])
            ->groupBy('status_validasi')
            ->get()
            ->getResultArray();

        $out = ['diajukan' => 0, 'perlu_perbaikan' => 0, 'draf' => 0, 'total_antrean' => 0];
        foreach ($rows as $r) {
            if ($r['st'] === 'diajukan') {
                $out['diajukan'] = (int) $r['cnt'];
            } elseif ($r['st'] === 'ditolak') {
                $out['perlu_perbaikan'] = (int) $r['cnt'];
            }
        }
        $out['total_antrean'] = $out['diajukan'] + $out['perlu_perbaikan'];

        return $out;
    }

    /**
     * @return array{diajukan: int, perlu_perbaikan: int, draf: int, total_antrean: int}
     */
    private function countFotoTarunaGroup(): array
    {
        $rows = $this->db->table('master_taruna')
            ->select('foto_pending_status AS st, COUNT(*) AS cnt')
            ->whereIn('foto_pending_status', ['diajukan', 'perlu_perbaikan'])
            ->groupBy('foto_pending_status')
            ->get()
            ->getResultArray();

        $out = ['diajukan' => 0, 'perlu_perbaikan' => 0, 'draf' => 0, 'total_antrean' => 0];
        foreach ($rows as $r) {
            $st = (string) ($r['st'] ?? '');
            if (array_key_exists($st, $out)) {
                $out[$st] = (int) $r['cnt'];
            }
        }
        $out['total_antrean'] = $out['diajukan'] + $out['perlu_perbaikan'];

        return $out;
    }

    /**
     * @param array<int, string> $allowedStatuses
     * @return array<int, array<string, mixed>>
     */
    private function fetchFotoTarunaQueue(array $allowedStatuses, string $keyword): array
    {
        $statuses = array_values(array_intersect($allowedStatuses, ['diajukan', 'perlu_perbaikan']));
        if (empty($statuses)) {
            return [];
        }

        $builder = $this->db->table('master_taruna m')
            ->select('m.nomor_taruna, m.nama_lengkap, m.foto_pending, m.foto_pending_status, m.foto_pending_catatan, m.foto_pending_at, p.kode_prodi')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = m.nomor_taruna', 'left')
            ->join('ref_program_studi p', 'p.id = pt.program_studi_id', 'left')
            ->whereIn('m.foto_pending_status', $statuses)
            ->orderBy('m.foto_pending_at', 'DESC')
            ->limit(60);

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('m.nomor_taruna', $keyword)
                ->orLike('m.nama_lengkap', $keyword)
                ->groupEnd();
        }

        $rows      = $builder->get()->getResultArray();
        $canDecide = KetarunaanPolicy::canValidate() || KetarunaanPolicy::canManageTaruna();
        $out       = [];

        foreach ($rows as $r) {
            $pendingFile = basename((string) ($r['foto_pending'] ?? ''));
            $fotoUrl     = ($pendingFile !== '' && is_file(FCPATH . 'assets/uploads/taruna/' . $pendingFile))
                ? base_url('assets/uploads/taruna/' . $pendingFile)
                : null;
            $tglRaw      = substr((string) ($r['foto_pending_at'] ?? ''), 0, 10);

            $out[] = [
                'id'                 => (string) $r['nomor_taruna'],
                'sumber'             => 'foto_taruna',
                'subjek_utama'       => (string) $r['nama_lengkap'],
                'subjek_kode'        => (string) $r['nomor_taruna'] . (!empty($r['kode_prodi']) ? ' (' . $r['kode_prodi'] . ')' : ''),
                'judul'              => 'Pengajuan Foto Profil Baru',
                'ringkasan'          => ($r['foto_pending_status'] === 'diajukan')
                    ? 'Taruna mengunggah foto profil baru menunggu persetujuan.'
                    : 'Pengajuan foto sebelumnya dikembalikan untuk diperbaiki.',
                'tanggal_tampil'     => $tglRaw !== '' ? ketarunaan_format_tanggal($tglRaw, true) : '-',
                'sort_date'          => $tglRaw,
                'status_validasi'    => (string) $r['foto_pending_status'],
                'catatan'            => (string) ($r['foto_pending_catatan'] ?? ''),
                'dokumen_url'        => $fotoUrl,
                'dokumen_label'      => 'Lihat Foto',
                'detail_url'         => site_url('ketarunaan/taruna/profil/' . urlencode((string) $r['nomor_taruna']) . '?tab=identitas'),
                'can_decide_inline'  => $canDecide && ($r['foto_pending_status'] === 'diajukan'),
                'inline_action_url'  => site_url('ketarunaan/validasi/foto/' . urlencode((string) $r['nomor_taruna'])),
                'is_kelulusan'       => false,
            ];
        }

        return $out;
    }

    /**
     * @param array<int, string> $statuses
     * @return array<int, array<string, mixed>>
     */
    private function fetchStatusQueue(array $statuses, string $keyword): array
    {
        $builder = $this->db->table('riwayat_status_taruna r')
            ->select('r.id, r.nomor_taruna, r.kode_status, r.mulai_berlaku, r.rencana_kembali, r.alasan_perubahan, r.nomor_keputusan, r.bukti_dokumen, r.status_validasi, r.catatan_validasi, m.nama_lengkap, p.kode_prodi')
            ->join('master_taruna m', 'm.nomor_taruna = r.nomor_taruna')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = r.nomor_taruna', 'left')
            ->join('ref_program_studi p', 'p.id = pt.program_studi_id', 'left')
            ->whereIn('r.status_validasi', $statuses)
            ->orderBy('r.id', 'DESC')
            ->limit(60);

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('r.nomor_taruna', $keyword)
                ->orLike('m.nama_lengkap', $keyword)
                ->orLike('r.alasan_perubahan', $keyword)
                ->groupEnd();
        }

        $rows = $builder->get()->getResultArray();
        $out  = [];

        foreach ($rows as $r) {
            $isLulus   = strtolower((string) $r['kode_status']) === 'lulus';
            $canDecide = in_array($r['status_validasi'], ['diajukan', 'perlu_perbaikan', 'draf'], true)
                && ($isLulus ? KetarunaanPolicy::canValidateAlumni() : KetarunaanPolicy::canValidate());

            $statusMeta = ketarunaan_status_meta((string) $r['kode_status']);
            $ringkasan  = 'Usulan status: ' . $statusMeta['label'];
            if (!empty($r['nomor_keputusan'])) {
                $ringkasan .= ' | SK: ' . $r['nomor_keputusan'];
            }
            if (!empty($r['alasan_perubahan'])) {
                $ringkasan .= ' — ' . $r['alasan_perubahan'];
            }

            $out[] = [
                'id'                 => (int) $r['id'],
                'sumber'             => 'status_pendidikan',
                'subjek_utama'       => (string) $r['nama_lengkap'],
                'subjek_kode'        => (string) $r['nomor_taruna'] . (!empty($r['kode_prodi']) ? ' (' . $r['kode_prodi'] . ')' : ''),
                'judul'              => 'Perubahan Status -> ' . $statusMeta['label'],
                'ringkasan'          => $ringkasan,
                'tanggal_tampil'     => ketarunaan_format_tanggal((string) $r['mulai_berlaku'], true),
                'sort_date'          => (string) ($r['mulai_berlaku'] ?? ''),
                'status_validasi'    => (string) $r['status_validasi'],
                'catatan'            => (string) ($r['catatan_validasi'] ?? ''),
                'dokumen_url'        => ketarunaan_resolve_sk_url($r['bukti_dokumen'] ?? null, (int) $r['id']),
                'detail_url'         => site_url('ketarunaan/taruna/profil/' . urlencode((string) $r['nomor_taruna']) . '?tab=riwayat-status'),
                'can_decide_inline'  => $canDecide,
                'inline_action_url'  => site_url('ketarunaan/validasi/status/' . (int) $r['id']),
                'is_kelulusan'       => $isLulus,
            ];
        }

        return $out;
    }

    /**
     * @param array<int, string> $statuses
     * @return array<int, array<string, mixed>>
     */
    private function fetchCapaianQueue(array $statuses, string $keyword): array
    {
        $builder = $this->db->table('taruna_capaian c')
            ->select('c.id, c.nomor_taruna, c.kategori, c.nama_capaian, c.tanggal_kegiatan, c.penyelenggara, c.status_validasi, c.catatan_validasi, m.nama_lengkap, p.kode_prodi')
            ->join('master_taruna m', 'm.nomor_taruna = c.nomor_taruna')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = c.nomor_taruna', 'left')
            ->join('ref_program_studi p', 'p.id = pt.program_studi_id', 'left')
            ->whereIn('c.status_validasi', $statuses)
            ->orderBy('c.id', 'DESC')
            ->limit(60);

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('c.nomor_taruna', $keyword)
                ->orLike('m.nama_lengkap', $keyword)
                ->orLike('c.nama_capaian', $keyword)
                ->groupEnd();
        }

        $rows = $builder->get()->getResultArray();
        $out  = [];

        foreach ($rows as $r) {
            $canDecide = in_array($r['status_validasi'], ['diajukan', 'perlu_perbaikan', 'draf'], true)
                && KetarunaanPolicy::canValidate();
            $katMeta = ketarunaan_capaian_kategori_meta((string) ($r['kategori'] ?? ''));

            $out[] = [
                'id'                 => (int) $r['id'],
                'sumber'             => 'capaian',
                'subjek_utama'       => (string) $r['nama_lengkap'],
                'subjek_kode'        => (string) $r['nomor_taruna'] . (!empty($r['kode_prodi']) ? ' (' . $r['kode_prodi'] . ')' : ''),
                'judul'              => (string) $r['nama_capaian'],
                'ringkasan'          => ($katMeta['label'] ?? 'Capaian') . (!empty($r['penyelenggara']) ? ' • ' . $r['penyelenggara'] : ''),
                'tanggal_tampil'     => ketarunaan_format_tanggal((string) ($r['tanggal_kegiatan'] ?? ''), true),
                'sort_date'          => (string) ($r['tanggal_kegiatan'] ?? ''),
                'status_validasi'    => (string) $r['status_validasi'],
                'catatan'            => (string) ($r['catatan_validasi'] ?? ''),
                'detail_url'         => site_url('ketarunaan/rekam-jejak/capaian/' . (int) $r['id']),
                'can_decide_inline'  => $canDecide,
                'inline_action_url'  => site_url('ketarunaan/validasi/capaian/' . (int) $r['id']),
                'is_kelulusan'       => false,
            ];
        }

        return $out;
    }

    /**
     * @param array<int, string> $statuses
     * @return array<int, array<string, mixed>>
     */
    private function fetchPrestasiQueue(array $statuses, string $keyword): array
    {
        $builder = $this->db->table('taruna_prestasi p')
            ->select("p.id, p.nama_prestasi, p.tingkat_prestasi, p.peringkat, p.tanggal_mulai, p.status_validasi, p.catatan_validasi,
                (SELECT GROUP_CONCAT(CONCAT(pp.nomor_taruna, ' ', COALESCE(m2.nama_lengkap, '')) SEPARATOR ', ')
                 FROM taruna_peserta_prestasi pp
                 LEFT JOIN master_taruna m2 ON m2.nomor_taruna = pp.nomor_taruna
                 WHERE pp.prestasi_id = p.id) AS peserta_label")
            ->whereIn('p.status_validasi', $statuses)
            ->orderBy('p.id', 'DESC')
            ->limit(60);

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('p.nama_prestasi', $keyword)
                ->orLike('p.peringkat', $keyword)
                ->groupEnd();
        }

        $rows = $builder->get()->getResultArray();
        $out  = [];

        foreach ($rows as $r) {
            $out[] = [
                'id'                 => (int) $r['id'],
                'sumber'             => 'prestasi',
                'subjek_utama'       => !empty($r['peserta_label']) ? (string) $r['peserta_label'] : 'Peserta Taruna',
                'subjek_kode'        => ucfirst((string) ($r['tingkat_prestasi'] ?? '-')),
                'judul'              => (string) $r['nama_prestasi'],
                'ringkasan'          => 'Peringkat: ' . ($r['peringkat'] ?: '-'),
                'tanggal_tampil'     => ketarunaan_format_tanggal((string) ($r['tanggal_mulai'] ?? ''), true),
                'sort_date'          => (string) ($r['tanggal_mulai'] ?? ''),
                'status_validasi'    => (string) $r['status_validasi'],
                'catatan'            => (string) ($r['catatan_validasi'] ?? ''),
                'detail_url'         => site_url('ketarunaan/prestasi/detail/' . (int) $r['id']),
                'can_decide_inline'  => false,
                'inline_action_url'  => '',
                'is_kelulusan'       => false,
            ];
        }

        return $out;
    }

    /**
     * @param array<int, string> $statuses
     * @return array<int, array<string, mixed>>
     */
    private function fetchPelanggaranQueue(array $statuses, string $keyword): array
    {
        $builder = $this->db->table('taruna_pelanggaran pl')
            ->select('pl.id, pl.nomor_perkara, pl.nomor_taruna, pl.jenis_pelanggaran, pl.tingkat_pelanggaran, pl.tanggal_kejadian, pl.status_validasi, pl.catatan_validasi, m.nama_lengkap')
            ->join('master_taruna m', 'm.nomor_taruna = pl.nomor_taruna')
            ->whereIn('pl.status_validasi', $statuses)
            ->orderBy('pl.id', 'DESC')
            ->limit(60);

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('pl.nomor_perkara', $keyword)
                ->orLike('pl.nomor_taruna', $keyword)
                ->orLike('m.nama_lengkap', $keyword)
                ->orLike('pl.jenis_pelanggaran', $keyword)
                ->groupEnd();
        }

        $rows = $builder->get()->getResultArray();
        $out  = [];

        foreach ($rows as $r) {
            $out[] = [
                'id'                 => (int) $r['id'],
                'sumber'             => 'pelanggaran',
                'subjek_utama'       => (string) $r['nama_lengkap'],
                'subjek_kode'        => (string) $r['nomor_taruna'] . ' • ' . ($r['nomor_perkara'] ?: '-'),
                'judul'              => (string) $r['jenis_pelanggaran'],
                'ringkasan'          => 'Tingkat: ' . ucwords(str_replace('_', ' ', (string) ($r['tingkat_pelanggaran'] ?? '-'))),
                'tanggal_tampil'     => ketarunaan_format_tanggal((string) ($r['tanggal_kejadian'] ?? ''), true),
                'sort_date'          => (string) ($r['tanggal_kejadian'] ?? ''),
                'status_validasi'    => (string) $r['status_validasi'],
                'catatan'            => (string) ($r['catatan_validasi'] ?? ''),
                'detail_url'         => site_url('ketarunaan/pelanggaran/detail/' . (int) $r['id']),
                'can_decide_inline'  => false,
                'inline_action_url'  => '',
                'is_kelulusan'       => false,
            ];
        }

        return $out;
    }

    /**
     * @param array<int, string> $statuses
     * @return array<int, array<string, mixed>>
     */
    private function fetchBeasiswaQueue(array $statuses, string $keyword): array
    {
        $builder = $this->db->table('taruna_penerima_beasiswa pb')
            ->select('pb.id, pb.nomor_taruna, pb.tanggal_pengajuan, pb.status_validasi, pb.catatan_validasi, m.nama_lengkap, prog.nama_program, prog.jenis_beasiswa')
            ->join('master_taruna m', 'm.nomor_taruna = pb.nomor_taruna')
            ->join('taruna_program_beasiswa prog', 'prog.id = pb.program_beasiswa_id', 'left')
            ->whereIn('pb.status_validasi', $statuses)
            ->orderBy('pb.id', 'DESC')
            ->limit(60);

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('pb.nomor_taruna', $keyword)
                ->orLike('m.nama_lengkap', $keyword)
                ->orLike('prog.nama_program', $keyword)
                ->groupEnd();
        }

        $rows = $builder->get()->getResultArray();
        $out  = [];

        foreach ($rows as $r) {
            $beasiswaRoute = in_array((string) $r['status_validasi'], ['diajukan', 'perlu_perbaikan'], true)
                ? 'ketarunaan/beasiswa/verifikasi/'
                : 'ketarunaan/beasiswa/detail/';
            $out[] = [
                'id'                 => (int) $r['id'],
                'sumber'             => 'beasiswa',
                'subjek_utama'       => (string) $r['nama_lengkap'],
                'subjek_kode'        => (string) $r['nomor_taruna'],
                'judul'              => (string) ($r['nama_program'] ?? 'Klaim Beasiswa'),
                'ringkasan'          => 'Kategori: ' . ucwords(str_replace('_', ' ', (string) ($r['jenis_beasiswa'] ?? '-'))),
                'tanggal_tampil'     => ketarunaan_format_tanggal((string) ($r['tanggal_pengajuan'] ?? ''), true),
                'sort_date'          => (string) ($r['tanggal_pengajuan'] ?? ''),
                'status_validasi'    => (string) $r['status_validasi'],
                'catatan'            => (string) ($r['catatan_validasi'] ?? ''),
                'detail_url'         => site_url($beasiswaRoute . (int) $r['id']),
                'can_decide_inline'  => false,
                'inline_action_url'  => '',
                'is_kelulusan'       => false,
            ];
        }

        return $out;
    }

    /**
     * @param array<int, string> $statuses
     * @return array<int, array<string, mixed>>
     */
    private function fetchLowonganQueue(array $statuses, string $keyword): array
    {
        $builder = $this->db->table('lowongan_pekerjaan l')
            ->select('l.id, l.judul_lowongan, l.jenis_pekerjaan, l.tanggal_diterima, l.batas_pendaftaran, l.status_validasi, l.catatan_pemeriksa, pm.nama_perusahaan')
            ->join('perusahaan_mitra pm', 'pm.id = l.perusahaan_id', 'left')
            ->whereIn('l.status_validasi', $statuses)
            ->orderBy('l.id', 'DESC')
            ->limit(60);

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('l.judul_lowongan', $keyword)
                ->orLike('pm.nama_perusahaan', $keyword)
                ->groupEnd();
        }

        $rows = $builder->get()->getResultArray();
        $out  = [];

        foreach ($rows as $r) {
            $out[] = [
                'id'                 => (int) $r['id'],
                'sumber'             => 'lowongan',
                'subjek_utama'       => (string) ($r['nama_perusahaan'] ?? 'Perusahaan Mitra'),
                'subjek_kode'        => ucwords(str_replace('_', ' ', (string) ($r['jenis_pekerjaan'] ?? '-'))),
                'judul'              => (string) $r['judul_lowongan'],
                'ringkasan'          => 'Batas pendaftaran: ' . ketarunaan_format_tanggal((string) ($r['batas_pendaftaran'] ?? ''), true),
                'tanggal_tampil'     => ketarunaan_format_tanggal((string) ($r['tanggal_diterima'] ?? ''), true),
                'sort_date'          => (string) ($r['tanggal_diterima'] ?? ''),
                'status_validasi'    => (string) $r['status_validasi'],
                'catatan'            => (string) ($r['catatan_pemeriksa'] ?? ''),
                'detail_url'         => site_url('ketarunaan/lowongan/detail/' . (int) $r['id']),
                'can_decide_inline'  => false,
                'inline_action_url'  => '',
                'is_kelulusan'       => false,
            ];
        }

        return $out;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchAlumniKoreksiQueue(string $statusFilter, string $keyword): array
    {
        $mapped = match ($statusFilter) {
            'diajukan'        => ['diajukan'],
            'perlu_perbaikan' => ['ditolak'],
            'tervalidasi'     => ['tervalidasi'],
            'draf'            => [],
            'semua'           => ['diajukan', 'ditolak', 'tervalidasi'],
            default           => ['diajukan', 'ditolak'],
        };

        if (empty($mapped)) {
            return [];
        }

        $builder = $this->db->table('alumni_riwayat_perubahan ar')
            ->select('ar.id, ar.nomor_taruna, ar.jenis_kejadian, ar.judul_ringkas, ar.tanggal_kejadian, ar.status_validasi, ar.catatan_validator, m.nama_lengkap')
            ->join('master_taruna m', 'm.nomor_taruna = ar.nomor_taruna')
            ->whereIn('ar.status_validasi', $mapped)
            ->orderBy('ar.id', 'DESC')
            ->limit(60);

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('ar.nomor_taruna', $keyword)
                ->orLike('m.nama_lengkap', $keyword)
                ->orLike('ar.judul_ringkas', $keyword)
                ->groupEnd();
        }

        $rows = $builder->get()->getResultArray();
        $out  = [];

        foreach ($rows as $r) {
            $jMeta = ketarunaan_alumni_jenis_kejadian_meta((string) ($r['jenis_kejadian'] ?? ''));

            $out[] = [
                'id'                 => (int) $r['id'],
                'sumber'             => 'alumni',
                'subjek_utama'       => (string) $r['nama_lengkap'],
                'subjek_kode'        => (string) $r['nomor_taruna'],
                'judul'              => (string) $r['judul_ringkas'],
                'ringkasan'          => (string) ($jMeta['label'] ?? 'Koreksi Alumni'),
                'tanggal_tampil'     => ketarunaan_format_tanggal((string) ($r['tanggal_kejadian'] ?? ''), true),
                'sort_date'          => (string) ($r['tanggal_kejadian'] ?? ''),
                'status_validasi'    => $r['status_validasi'] === 'ditolak' ? 'perlu_perbaikan' : (string) $r['status_validasi'],
                'catatan'            => (string) ($r['catatan_validator'] ?? ''),
                'detail_url'         => site_url('ketarunaan/alumni/koreksi?q=' . urlencode((string) $r['nomor_taruna'])),
                'can_decide_inline'  => false,
                'inline_action_url'  => '',
                'is_kelulusan'       => false,
            ];
        }

        return $out;
    }
}
