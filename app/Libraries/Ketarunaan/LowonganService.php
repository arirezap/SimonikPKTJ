<?php

namespace App\Libraries\Ketarunaan;

use App\Models\Ketarunaan\LowonganDokumenModel;
use App\Models\Ketarunaan\LowonganPekerjaanModel;
use App\Models\Ketarunaan\LowonganSasaranProdiModel;
use App\Models\Ketarunaan\PerusahaanMitraModel;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

/**
 * Layanan bisnis terpusat Modul 6: Lowongan Pekerjaan (ECC 2.0).
 * Menjamin pemisahan verifikasi kontak mitra vs lowongan (LOW-04),
 * pemisahan status validasi vs publikasi (LOW-05 / UAT-15),
 * kendali tenggat otomatis & penutupan manual (LOW-06 / UAT-16),
 * serta re-verifikasi otomatis pada perubahan substantif (LOW-07).
 */
class LowonganService
{
    protected BaseConnection $db;
    protected PerusahaanMitraModel $mitraModel;
    protected LowonganPekerjaanModel $lowonganModel;
    protected LowonganSasaranProdiModel $sasaranProdiModel;
    protected LowonganDokumenModel $dokumenModel;

    public function __construct(
        ?BaseConnection $db = null,
        ?PerusahaanMitraModel $mitraModel = null,
        ?LowonganPekerjaanModel $lowonganModel = null,
        ?LowonganSasaranProdiModel $sasaranProdiModel = null,
        ?LowonganDokumenModel $dokumenModel = null
    ) {
        $this->db                = $db ?? Database::connect();
        $this->mitraModel        = $mitraModel ?? new PerusahaanMitraModel();
        $this->lowonganModel     = $lowonganModel ?? new LowonganPekerjaanModel();
        $this->sasaranProdiModel = $sasaranProdiModel ?? new LowonganSasaranProdiModel();
        $this->dokumenModel      = $dokumenModel ?? new LowonganDokumenModel();
        helper(['ketarunaan', 'audit']);
    }

    /**
     * Mengambil data referensi untuk filter, form, dan katalog Modul Lowongan.
     *
     * @return array<string, mixed>
     */
    public function getReferenceOptions(): array
    {
        $prodi = $this->db->table('ref_program_studi')
            ->select('id, kode_prodi, nama_prodi, jenjang')
            ->where('is_aktif', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $perusahaan = $this->db->table('perusahaan_mitra')
            ->select('id, nama_perusahaan, sektor, kota, website, kontak_person, email_rekrutmen, is_verified')
            ->orderBy('nama_perusahaan', 'ASC')
            ->get()
            ->getResultArray();

        return [
            'prodi'             => $prodi,
            'perusahaan'        => $perusahaan,
            'jenis_catalog'     => ketarunaan_lowongan_jenis_catalog(),
            'metode_catalog'    => ketarunaan_lowongan_metode_catalog(),
            'dokumen_kategori'  => ketarunaan_lowongan_dokumen_kategori_catalog(),
            'checklist_catalog' => ketarunaan_lowongan_checklist_catalog(),
            'alasan_penutupan'  => ketarunaan_lowongan_alasan_penutupan_catalog(),
        ];
    }

    /**
     * Menghitung 4 kartu KPI dan menyusun daftar lowongan terfilter sesuai kewenangan pengguna.
     *
     * @param array<string, mixed> $filters
     * @param bool                 $canViewStaffBoard
     * @param string|null          $waktuAcuan Format Y-m-d H:i:s
     * @param bool                 $forExport
     * @return array<string, mixed>
     */
    public function getDirectoryData(array $filters, bool $canViewStaffBoard, ?string $waktuAcuan = null, bool $forExport = false): array
    {
        $acuan = $waktuAcuan ?? date('Y-m-d H:i:s');

        // Hitung 4 KPI tanpa terpengaruh filter pencarian tabel
        $allMetaRows = $this->db->table('lowongan_pekerjaan')
            ->select('id, status_validasi, status_publikasi, batas_pendaftaran')
            ->get()
            ->getResultArray();

        $kpi = [
            'terbuka'          => 0,
            'menunggu'         => 0,
            'ditutup'          => 0,
            'segera_berakhir'  => 0,
            'perusahaan'       => (int) $this->db->table('perusahaan_mitra')->countAllResults(),
        ];

        $batasSegera = date('Y-m-d H:i:s', strtotime($acuan . ' +7 days'));
        foreach ($allMetaRows as $mRow) {
            $efektif = ketarunaan_lowongan_status_efektif($mRow, $acuan);
            if ($efektif === 'terbuka') {
                $kpi['terbuka']++;
                if ((string) $mRow['batas_pendaftaran'] <= $batasSegera) {
                    $kpi['segera_berakhir']++;
                }
            } elseif (in_array($efektif, ['ditutup', 'berakhir'], true)) {
                $kpi['ditutup']++;
            }

            if (in_array((string) $mRow['status_validasi'], ['diajukan', 'perlu_perbaikan'], true)) {
                $kpi['menunggu']++;
            }
        }

        $builder = $this->db->table('lowongan_pekerjaan l')
            ->select('l.id, l.perusahaan_id, l.judul_lowongan, l.jenis_pekerjaan, l.jenjang_pendidikan, l.jumlah_kebutuhan, l.lokasi, l.batas_pendaftaran, l.metode_lamaran, l.tautan_pendaftaran, l.email_lamaran, l.status_publikasi, l.status_validasi, l.alasan_penutupan, l.updated_at, pm.nama_perusahaan, pm.sektor, pm.kota AS kota_perusahaan, pm.is_verified AS perusahaan_verified')
            ->join('perusahaan_mitra pm', 'pm.id = l.perusahaan_id', 'left');

        if (!$canViewStaffBoard) {
            // Alumni & Taruna hanya boleh melihat lowongan tervalidasi, terbit, dan belum melewati tenggat (UAT-15, UAT-16)
            $builder->where('l.status_validasi', 'tervalidasi')
                ->where('l.status_publikasi', 'terbit')
                ->where('l.batas_pendaftaran >=', $acuan);
        }

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $builder->groupStart()
                ->like('l.judul_lowongan', $q)
                ->orLike('pm.nama_perusahaan', $q)
                ->orLike('l.lokasi', $q)
                ->groupEnd();
        }

        $perusahaanId = (string) ($filters['perusahaan_id'] ?? 'all');
        if ($perusahaanId !== '' && $perusahaanId !== 'all' && ctype_digit($perusahaanId)) {
            $builder->where('l.perusahaan_id', (int) $perusahaanId);
        }

        $jenis = trim((string) ($filters['jenis_pekerjaan'] ?? 'all'));
        if ($jenis !== '' && $jenis !== 'all') {
            $builder->where('l.jenis_pekerjaan', $jenis);
        }

        $prodiId = (string) ($filters['prodi_id'] ?? 'all');
        if ($prodiId !== '' && $prodiId !== 'all' && ctype_digit($prodiId)) {
            $builder->join('lowongan_sasaran_prodi lsp_f', 'lsp_f.lowongan_id = l.id')
                ->where('lsp_f.program_studi_id', (int) $prodiId);
        }

        $statusEfektif = trim((string) ($filters['status_efektif'] ?? 'all'));
        if ($canViewStaffBoard) {
            $statusValidasi = trim((string) ($filters['status_validasi'] ?? 'all'));
            if ($statusValidasi !== '' && $statusValidasi !== 'all') {
                if ($statusValidasi === 'antrean') {
                    $builder->whereIn('l.status_validasi', ['diajukan', 'perlu_perbaikan']);
                } else {
                    $builder->where('l.status_validasi', $statusValidasi);
                }
            }

            if ($statusEfektif === 'terbuka') {
                $builder->where('l.status_validasi', 'tervalidasi')
                    ->where('l.status_publikasi', 'terbit')
                    ->where('l.batas_pendaftaran >=', $acuan);
            } elseif ($statusEfektif === 'segera_berakhir') {
                $builder->where('l.status_validasi', 'tervalidasi')
                    ->where('l.status_publikasi', 'terbit')
                    ->where('l.batas_pendaftaran >=', $acuan)
                    ->where('l.batas_pendaftaran <=', $batasSegera);
            } elseif ($statusEfektif === 'draf_tervalidasi') {
                $builder->where('l.status_validasi', 'tervalidasi')
                    ->where('l.status_publikasi', 'draf');
            } elseif ($statusEfektif === 'draf') {
                $builder->where('l.status_publikasi', 'draf');
            } elseif ($statusEfektif === 'berakhir') {
                $builder->where('l.status_publikasi', 'terbit')
                    ->where('l.batas_pendaftaran <', $acuan);
            } elseif ($statusEfektif === 'ditutup') {
                $builder->where('l.status_publikasi', 'ditutup');
            } elseif ($statusEfektif === 'arsip') {
                $builder->groupStart()
                    ->where('l.status_publikasi', 'ditutup')
                    ->orGroupStart()
                        ->where('l.status_publikasi', 'terbit')
                        ->where('l.batas_pendaftaran <', $acuan)
                    ->groupEnd()
                    ->groupEnd();
            }
        } elseif ($statusEfektif === 'segera_berakhir') {
            $builder->where('l.batas_pendaftaran <=', $batasSegera);
        }

        $sortMap = [
            'judul_lowongan'    => 'l.judul_lowongan',
            'nama_perusahaan'   => 'pm.nama_perusahaan',
            'lokasi'            => 'l.lokasi',
            'batas_pendaftaran' => 'l.batas_pendaftaran',
            'status_validasi'   => 'l.status_validasi',
            'status_publikasi'  => 'l.status_publikasi',
        ];
        $sortKey = trim((string) ($filters['sort'] ?? ''));
        $sortDir = strtoupper(trim((string) ($filters['dir'] ?? 'ASC'))) === 'DESC' ? 'DESC' : 'ASC';

        $builder->groupBy('l.id');
        if ($sortKey !== '' && isset($sortMap[$sortKey])) {
            $builder->orderBy($sortMap[$sortKey], $sortDir);
            $builder->orderBy('l.id', 'DESC');
        } else {
            $builder->orderBy("CASE WHEN l.status_publikasi = 'terbit' AND l.status_validasi = 'tervalidasi' AND l.batas_pendaftaran >= " . $this->db->escape($acuan) . ' THEN 0 ELSE 1 END', 'ASC', false)
                ->orderBy('l.batas_pendaftaran', 'ASC')
                ->orderBy('l.id', 'DESC');
        }

        $allRows   = $builder->get()->getResultArray();
        $totalRows = count($allRows);
        $perPage   = 15;
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $page      = max(1, min($totalPages, (int) ($filters['page'] ?? 1)));

        $rows = $forExport
            ? $allRows
            : array_slice($allRows, ($page - 1) * $perPage, $perPage);

        $prodiMap = $this->loadProdiMap(array_column($rows, 'id'));
        $docMap   = $this->loadDokumenSummaryMap(array_column($rows, 'id'));

        foreach ($rows as &$row) {
            $lid = (int) $row['id'];
            $row['sasaran_prodi']  = $prodiMap[$lid] ?? [];
            $row['status_efektif'] = ketarunaan_lowongan_status_efektif($row, $acuan);
            $row['doc_summary']    = $docMap[$lid] ?? ['total' => 0, 'publik' => 0, 'internal' => 0];
        }
        unset($row);

        return [
            'kpi'        => $kpi,
            'rows'       => $rows,
            'pagination' => [
                'page'        => $page,
                'per_page'    => $perPage,
                'total_rows'  => $totalRows,
                'total_pages' => $totalPages,
            ],
        ];
    }

    /**
     * Mengambil detail lengkap satu lowongan beserta dokumen & riwayat tersaring hak akses.
     *
     * @param int         $id
     * @param bool        $canViewStaffBoard
     * @param bool        $canViewInternalProof
     * @param string|null $waktuAcuan
     * @return array<string, mixed>|null
     */
    public function getLowonganDetail(int $id, bool $canViewStaffBoard, bool $canViewInternalProof, ?string $waktuAcuan = null): ?array
    {
        $acuan = $waktuAcuan ?? date('Y-m-d H:i:s');
        $row   = $this->db->table('lowongan_pekerjaan l')
            ->select('l.*, pm.nama_perusahaan, pm.sektor, pm.kota AS kota_perusahaan, pm.alamat AS alamat_perusahaan, pm.website AS website_perusahaan, pm.kontak_person, pm.pic_jabatan, pm.email_rekrutmen, pm.telepon_rekrutmen, pm.is_verified AS perusahaan_verified')
            ->join('perusahaan_mitra pm', 'pm.id = l.perusahaan_id', 'left')
            ->where('l.id', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }

        $efektif = ketarunaan_lowongan_status_efektif($row, $acuan);
        if (!$canViewStaffBoard && $efektif !== 'terbuka') {
            // Draf tervalidasi, pengajuan, atau lowongan ditutup/berakhir tertutup bagi alumni/taruna
            return null;
        }

        $prodiMap = $this->loadProdiMap([$id]);
        $row['sasaran_prodi']  = $prodiMap[$id] ?? [];
        $row['sasaran_prodi_ids'] = array_map(static fn($p) => (int) $p['id'], $row['sasaran_prodi']);
        $row['status_efektif'] = $efektif;
        $row['checklist']      = $this->decodeChecklist($row['checklist_verifikasi_json'] ?? null);
        $row['riwayat_proses'] = $this->decodeRiwayat($row['riwayat_proses_json'] ?? null);

        $docBuilder = $this->db->table('lowongan_dokumen')
            ->where('lowongan_id', $id)
            ->orderBy('id', 'DESC');
        if (!$canViewInternalProof) {
            $docBuilder->where('is_internal', 0);
        }
        $row['dokumen_list'] = $docBuilder->get()->getResultArray();

        if (!$canViewInternalProof) {
            // Bersihkan catatan sumber internal & kontak privat staf agar tidak bocor (NFR-02 & LOW-03)
            $row['sumber_informasi']  = null;
            $row['catatan_sumber']    = null;
            $row['catatan_pemeriksa'] = null;
            $row['pic_pengelola']     = null;
            $row['diperiksa_oleh']    = null;
            $row['riwayat_proses']    = [];
        }

        return $row;
    }

    /**
     * Mengambil daftar perusahaan mitra beserta jumlah lowongan & status verifikasi kontak.
     *
     * @param array<string, mixed> $filters
     * @param string|null          $waktuAcuan
     * @return array<int, array<string, mixed>>
     */
    public function listPerusahaan(array $filters = [], ?string $waktuAcuan = null): array
    {
        $acuan   = $waktuAcuan ?? date('Y-m-d H:i:s');
        $builder = $this->db->table('perusahaan_mitra');

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $builder->groupStart()
                ->like('nama_perusahaan', $q)
                ->orLike('sektor', $q)
                ->orLike('kota', $q)
                ->orLike('kontak_person', $q)
                ->groupEnd();
        }

        $verif = trim((string) ($filters['is_verified'] ?? 'all'));
        if ($verif === '1' || $verif === '0') {
            $builder->where('is_verified', (int) $verif);
        }

        $companies = $builder->orderBy('is_verified', 'DESC')
            ->orderBy('nama_perusahaan', 'ASC')
            ->get()
            ->getResultArray();

        $vacStats = [];
        $vacRows  = $this->db->table('lowongan_pekerjaan')
            ->select('perusahaan_id, status_validasi, status_publikasi, batas_pendaftaran')
            ->get()
            ->getResultArray();
        foreach ($vacRows as $vr) {
            $pid = (int) $vr['perusahaan_id'];
            if (!isset($vacStats[$pid])) {
                $vacStats[$pid] = ['total' => 0, 'terbuka' => 0];
            }
            $vacStats[$pid]['total']++;
            if (ketarunaan_lowongan_status_efektif($vr, $acuan) === 'terbuka') {
                $vacStats[$pid]['terbuka']++;
            }
        }

        foreach ($companies as &$comp) {
            $cid = (int) $comp['id'];
            $comp['jumlah_lowongan'] = $vacStats[$cid]['total'] ?? 0;
            $comp['jumlah_terbuka']  = $vacStats[$cid]['terbuka'] ?? 0;
        }
        unset($comp);

        return $companies;
    }

    /**
     * Mengambil satu baris perusahaan mitra untuk form sunting.
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function getPerusahaanById(int $id): ?array
    {
        return $this->mitraModel->find($id) ?: null;
    }

    /**
     * Menyimpan atau memperbarui profil perusahaan mitra dan status pemeriksaan kontak (LOW-01, LOW-04).
     * Verifikasi kontak perusahaan TIDAK mengubah status validasi lowongan perusahaan terkait.
     *
     * @param int|null             $id
     * @param array<string, mixed> $input
     * @param bool                 $canValidate
     * @param string               $actorName
     * @return array{status: bool, message: string, id?: int}
     */
    public function savePerusahaan(?int $id, array $input, bool $canValidate, string $actorName): array
    {
        $nama = trim((string) ($input['nama_perusahaan'] ?? ''));
        if ($nama === '') {
            return ['status' => false, 'message' => 'Nama perusahaan wajib diisi.'];
        }

        $websiteRaw = trim((string) ($input['website'] ?? ''));
        $websiteUrl = null;
        if ($websiteRaw !== '') {
            $websiteUrl = ketarunaan_sanitize_external_url($websiteRaw, false);
            if ($websiteUrl === null) {
                return ['status' => false, 'message' => 'Tautan situs perusahaan harus diawali http:// atau https://.'];
            }
        }

        $emailRek = trim((string) ($input['email_rekrutmen'] ?? ''));
        if ($emailRek !== '' && !filter_var($emailRek, FILTER_VALIDATE_EMAIL)) {
            return ['status' => false, 'message' => 'Format Alamat Email rekrutmen tidak sesuai.'];
        }

        $existing = $id ? $this->mitraModel->find($id) : null;
        if ($id !== null && !$existing) {
            return ['status' => false, 'message' => 'Data perusahaan mitra tidak ditemukan.'];
        }

        $isVerified = $existing ? (int) $existing['is_verified'] : 0;
        $diperiksaOleh = $existing['diperiksa_oleh'] ?? null;
        $tglPeriksa    = $existing['tanggal_pemeriksaan_kontak'] ?? null;

        if ($canValidate && array_key_exists('is_verified', $input)) {
            $requestedVer = (int) $input['is_verified'] === 1 ? 1 : 0;
            if ($requestedVer !== $isVerified || $requestedVer === 1) {
                $isVerified    = $requestedVer;
                $diperiksaOleh = $requestedVer === 1 ? $actorName : null;
                $tglInput      = trim((string) ($input['tanggal_pemeriksaan_kontak'] ?? ''));
                if ($tglInput !== '' && !$this->isValidDateYmd($tglInput)) {
                    return ['status' => false, 'message' => 'Format tanggal pemeriksaan kontak tidak sesuai.'];
                }
                $tglPeriksa    = $requestedVer === 1
                    ? ($tglInput !== '' ? $tglInput : ($tglPeriksa ?: date('Y-m-d')))
                    : null;
            }
        }

        $payload = [
            'nama_perusahaan'            => $nama,
            'sektor'                     => trim((string) ($input['sektor'] ?? '')) ?: null,
            'kota'                       => trim((string) ($input['kota'] ?? '')) ?: null,
            'alamat'                     => trim((string) ($input['alamat'] ?? '')) ?: null,
            'website'                    => $websiteUrl,
            'kontak_person'              => trim((string) ($input['kontak_person'] ?? '')) ?: null,
            'pic_jabatan'                => trim((string) ($input['pic_jabatan'] ?? '')) ?: null,
            'email_rekrutmen'            => $emailRek ?: null,
            'telepon_rekrutmen'          => trim((string) ($input['telepon_rekrutmen'] ?? '')) ?: null,
            'is_verified'                => $isVerified,
            'sumber_verifikasi'          => trim((string) ($input['sumber_verifikasi'] ?? '')) ?: null,
            'tanggal_pemeriksaan_kontak' => $tglPeriksa,
            'catatan_verifikasi'         => trim((string) ($input['catatan_verifikasi'] ?? '')) ?: null,
            'diperiksa_oleh'             => $diperiksaOleh,
        ];

        $this->db->transStart();
        if ($existing) {
            $this->mitraModel->update((int) $existing['id'], $payload);
            $savedId = (int) $existing['id'];
            log_audit('UPDATE', 'perusahaan_mitra', (string) $savedId, $existing, $payload);
        } else {
            $this->mitraModel->insert($payload);
            $savedId = (int) $this->mitraModel->getInsertID();
            log_audit('CREATE', 'perusahaan_mitra', (string) $savedId, null, $payload);
        }
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['status' => false, 'message' => 'Gagal menyimpan data perusahaan mitra. Silakan coba lagi.'];
        }

        return [
            'status'  => true,
            'id'      => $savedId,
            'message' => $existing ? 'Data perusahaan mitra berhasil diperbarui.' : 'Perusahaan mitra baru berhasil disimpan.',
        ];
    }

    /**
     * Menyimpan lowongan baru atau perubahan lowongan serta memicu re-verifikasi otomatis jika ada perubahan substantif (LOW-02, LOW-07).
     *
     * @param int|null             $id
     * @param array<string, mixed> $input
     * @param array<int, int>      $prodiIds
     * @param string               $actionMode draf|ajukan
     * @param string               $actorName
     * @return array{status: bool, message: string, id?: int, reverified?: bool}
     */
    public function saveLowongan(?int $id, array $input, array $prodiIds, string $actionMode, string $actorName): array
    {
        $existing = $id ? $this->lowonganModel->find($id) : null;
        if ($id !== null && !$existing) {
            return ['status' => false, 'message' => 'Data lowongan tidak ditemukan.'];
        }

        $judul        = trim((string) ($input['judul_lowongan'] ?? ''));
        $perusahaanId = (int) ($input['perusahaan_id'] ?? 0);
        $lokasi       = trim((string) ($input['lokasi'] ?? ''));
        $jenis        = trim((string) ($input['jenis_pekerjaan'] ?? 'Tetap'));
        $jenjang      = trim((string) ($input['jenjang_pendidikan'] ?? 'D-III / D-IV'));
        $tenggatRaw   = trim((string) ($input['batas_pendaftaran'] ?? ''));
        $metode       = trim((string) ($input['metode_lamaran'] ?? 'tautan_eksternal'));
        $tautanRaw    = trim((string) ($input['tautan_pendaftaran'] ?? ''));
        $emailLamaran = trim((string) ($input['email_lamaran'] ?? ''));
        $deskripsi    = trim((string) ($input['deskripsi'] ?? ''));
        $persyaratan  = trim((string) ($input['persyaratan'] ?? ''));

        if ($judul === '' || $perusahaanId <= 0 || $lokasi === '' || $tenggatRaw === '' || $deskripsi === '' || $persyaratan === '') {
            return ['status' => false, 'message' => 'Lengkapi posisi, perusahaan, lokasi, batas lamaran, ringkasan, dan persyaratan.'];
        }

        if (!$this->mitraModel->find($perusahaanId)) {
            return ['status' => false, 'message' => 'Perusahaan mitra tidak valid.'];
        }

        $prodiClean = $this->filterValidProdiIds($prodiIds);
        if (empty($prodiClean)) {
            return ['status' => false, 'message' => 'Pilih minimal 1 program studi sasaran.'];
        }

        $tenggat = $this->normalizeDeadline($tenggatRaw);
        if ($tenggat === null) {
            return ['status' => false, 'message' => 'Format tanggal batas pendaftaran tidak sesuai.'];
        }

        if (!in_array($metode, ['tautan_eksternal', 'email', 'keduanya'], true)) {
            $metode = 'tautan_eksternal';
        }

        $tautanSafe = '';
        if (in_array($metode, ['tautan_eksternal', 'keduanya'], true)) {
            $sanitized = ketarunaan_sanitize_external_url($tautanRaw, false);
            if ($sanitized === null) {
                return ['status' => false, 'message' => 'Tautan pendaftaran resmi wajib diawali http:// atau https://.'];
            }
            $tautanSafe = $sanitized;
        } elseif ($tautanRaw !== '') {
            $sanitized = ketarunaan_sanitize_external_url($tautanRaw, false);
            if ($sanitized === null) {
                return ['status' => false, 'message' => 'Tautan pendaftaran harus diawali http:// atau https://.'];
            }
            $tautanSafe = $sanitized;
        }

        if (in_array($metode, ['email', 'keduanya'], true)) {
            if (!filter_var($emailLamaran, FILTER_VALIDATE_EMAIL)) {
                return ['status' => false, 'message' => 'Alamat Email lamaran wajib diisi dengan format yang benar.'];
            }
        } elseif ($emailLamaran !== '' && !filter_var($emailLamaran, FILTER_VALIDATE_EMAIL)) {
            return ['status' => false, 'message' => 'Format Alamat Email lamaran tidak valid.'];
        }

        $kebutuhanRaw = trim((string) ($input['jumlah_kebutuhan'] ?? ''));
        $kebutuhan    = ($kebutuhanRaw !== '' && ctype_digit($kebutuhanRaw) && (int) $kebutuhanRaw > 0) ? (int) $kebutuhanRaw : null;

        $tglDiterimaRaw = trim((string) ($input['tanggal_diterima'] ?? ''));
        if ($tglDiterimaRaw !== '' && !$this->isValidDateYmd($tglDiterimaRaw)) {
            return ['status' => false, 'message' => 'Format tanggal diterima tidak sesuai.'];
        }
        $tglDiterima    = $tglDiterimaRaw !== '' ? $tglDiterimaRaw : date('Y-m-d');

        $payload = [
            'perusahaan_id'        => $perusahaanId,
            'judul_lowongan'       => $judul,
            'jenis_pekerjaan'      => $jenis,
            'jenjang_pendidikan'   => $jenjang,
            'tahun_lulus_sasaran'  => trim((string) ($input['tahun_lulus_sasaran'] ?? '')) ?: null,
            'jumlah_kebutuhan'     => $kebutuhan,
            'lokasi'               => $lokasi,
            'deskripsi'            => $deskripsi,
            'persyaratan'          => $persyaratan,
            'pengalaman_minimum'   => trim((string) ($input['pengalaman_minimum'] ?? '')) ?: null,
            'sertifikasi_opsional' => trim((string) ($input['sertifikasi_opsional'] ?? '')) ?: null,
            'batas_pendaftaran'    => $tenggat,
            'metode_lamaran'       => $metode,
            'tautan_pendaftaran'   => $tautanSafe,
            'email_lamaran'        => $emailLamaran ?: null,
            'sumber_informasi'     => trim((string) ($input['sumber_informasi'] ?? '')) ?: null,
            'tanggal_diterima'     => $tglDiterima,
            'pic_pengelola'        => trim((string) ($input['pic_pengelola'] ?? '')) ?: $actorName,
            'catatan_sumber'       => trim((string) ($input['catatan_sumber'] ?? '')) ?: null,
        ];

        $reverified = false;
        $riwayat    = $existing ? $this->decodeRiwayat($existing['riwayat_proses_json'] ?? null) : [];
        $today      = date('Y-m-d');

        if ($existing) {
            $oldProdiIds = $this->getProdiIdsByLowongan((int) $existing['id']);
            $hasSubstantialChange = $this->detectSubstantialChanges($existing, $payload, $oldProdiIds, $prodiClean);
            $wasValidatedOrPublished = ($existing['status_validasi'] === 'tervalidasi' || $existing['status_publikasi'] === 'terbit');

            if ($wasValidatedOrPublished && !empty($hasSubstantialChange)) {
                // LOW-07: Perubahan substantif pasca validasi/terbit otomatis menarik ke antrean verifikasi ulang
                $reverified = true;
                $payload['status_validasi']           = 'diajukan';
                $payload['status_publikasi']          = 'draf';
                $payload['checklist_verifikasi_json'] = null;
                $payload['catatan_pemeriksa']         = null;
                $payload['diperiksa_oleh']            = null;
                $payload['tanggal_pemeriksaan']       = null;
                $payload['dipublikasi_oleh']          = null;
                $payload['tanggal_publikasi']         = null;
                $payload['diajukan_oleh']             = $actorName;
                $riwayat[] = [
                    'tanggal' => $today,
                    'pelaku'  => $actorName,
                    'aksi'    => 'Perubahan Substantif — Diajukan Ulang',
                    'status'  => 'diajukan',
                    'catatan' => 'Pembaruan pada ' . implode(', ', $hasSubstantialChange) . ' memerlukan pemeriksaan ulang sebelum tayang.',
                ];
            } else {
                if ($actionMode === 'ajukan') {
                    $payload['status_validasi']  = 'diajukan';
                    $payload['status_publikasi'] = 'draf';
                    $payload['diajukan_oleh']    = $actorName;
                    $riwayat[] = [
                        'tanggal' => $today,
                        'pelaku'  => $actorName,
                        'aksi'    => 'Diajukan untuk Verifikasi',
                        'status'  => 'diajukan',
                        'catatan' => 'Rincian lowongan diperbarui dan dikirim ke pemeriksa.',
                    ];
                } elseif (!in_array($existing['status_validasi'], ['tervalidasi'], true)) {
                    $payload['status_validasi'] = $existing['status_validasi'] === 'perlu_perbaikan' ? 'perlu_perbaikan' : 'draf';
                    $riwayat[] = [
                        'tanggal' => $today,
                        'pelaku'  => $actorName,
                        'aksi'    => 'Pembaruan Draf',
                        'status'  => $payload['status_validasi'],
                        'catatan' => 'Perubahan disimpan pada draf lowongan.',
                    ];
                }
            }
        } else {
            $statusValid = $actionMode === 'ajukan' ? 'diajukan' : 'draf';
            $payload['status_validasi']  = $statusValid;
            $payload['status_publikasi'] = 'draf';
            $payload['diajukan_oleh']    = $actionMode === 'ajukan' ? $actorName : null;
            $riwayat[] = [
                'tanggal' => $today,
                'pelaku'  => $actorName,
                'aksi'    => $actionMode === 'ajukan' ? 'Dicatat & Diajukan' : 'Disimpan sebagai Draf',
                'status'  => $statusValid,
                'catatan' => $actionMode === 'ajukan'
                    ? 'Lowongan baru dibuat dan dikirim untuk pemeriksaan.'
                    : 'Lowongan baru disimpan sebagai draf sebelum diajukan.',
            ];
        }

        $payload['riwayat_proses_json'] = json_encode($riwayat, JSON_UNESCAPED_UNICODE);

        $this->db->transStart();
        if ($existing) {
            $savedId = (int) $existing['id'];
            $this->lowonganModel->update($savedId, $payload);
            log_audit('UPDATE', 'lowongan_pekerjaan', (string) $savedId, ['judul_lowongan' => $existing['judul_lowongan'], 'status_validasi' => $existing['status_validasi']], $payload);
        } else {
            $this->lowonganModel->insert($payload);
            $savedId = (int) $this->lowonganModel->getInsertID();
            log_audit('CREATE', 'lowongan_pekerjaan', (string) $savedId, null, $payload);
        }

        $this->syncSasaranProdi($savedId, $prodiClean);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['status' => false, 'message' => 'Gagal menyimpan data lowongan. Silakan coba lagi.'];
        }

        $msg = 'Lowongan berhasil disimpan.';
        if ($reverified) {
            $msg = 'Perubahan substantif tersimpan. Status dikembalikan ke antrean verifikasi ulang sebelum ditayangkan kembali.';
        } elseif ($actionMode === 'ajukan') {
            $msg = 'Lowongan berhasil diajukan untuk pemeriksaan.';
        }

        return [
            'status'     => true,
            'id'         => $savedId,
            'reverified' => $reverified,
            'message'    => $msg,
        ];
    }

    /**
     * Mengambil daftar dokumen sumber & ringkasan lowongan untuk halaman Dokumen dan Sumber Lowongan (LOW-03).
     *
     * @param int|null $lowonganId
     * @return array<string, mixed>
     */
    public function getDokumenPageData(?int $lowonganId = null): array
    {
        $vacancies = $this->db->table('lowongan_pekerjaan l')
            ->select('l.id, l.judul_lowongan, l.status_validasi, l.status_publikasi, l.batas_pendaftaran, l.sumber_informasi, l.tanggal_diterima, l.pic_pengelola, l.catatan_sumber, pm.nama_perusahaan, pm.is_verified AS perusahaan_verified')
            ->join('perusahaan_mitra pm', 'pm.id = l.perusahaan_id', 'left')
            ->orderBy('l.id', 'DESC')
            ->get()
            ->getResultArray();

        $selected = null;
        if ($lowonganId !== null && $lowonganId > 0) {
            foreach ($vacancies as $v) {
                if ((int) $v['id'] === $lowonganId) {
                    $selected = $v;
                    break;
                }
            }
        } elseif (!empty($vacancies)) {
            $selected = $vacancies[0];
        }

        $docBuilder = $this->db->table('lowongan_dokumen d')
            ->select('d.*, l.judul_lowongan, pm.nama_perusahaan')
            ->join('lowongan_pekerjaan l', 'l.id = d.lowongan_id', 'left')
            ->join('perusahaan_mitra pm', 'pm.id = l.perusahaan_id', 'left')
            ->orderBy('d.id', 'DESC');

        if ($selected !== null) {
            $docBuilder->where('d.lowongan_id', (int) $selected['id']);
        }

        return [
            'vacancies' => $vacancies,
            'selected'  => $selected,
            'documents' => $docBuilder->get()->getResultArray(),
        ];
    }

    /**
     * Menyimpan dokumen pendukung atau tautan sumber baru untuk satu lowongan (LOW-03).
     *
     * @param int                  $lowonganId
     * @param array<string, mixed> $input
     * @param string|null          $savedFileName
     * @param string               $actorName
     * @return array{status: bool, message: string}
     */
    public function saveDokumen(int $lowonganId, array $input, ?string $savedFileName, string $actorName): array
    {
        $vacancy = $this->lowonganModel->find($lowonganId);
        if (!$vacancy) {
            return ['status' => false, 'message' => 'Data lowongan tidak ditemukan.'];
        }

        $catalog  = ketarunaan_lowongan_dokumen_kategori_catalog();
        $kategori = trim((string) ($input['kategori_dokumen'] ?? 'surat_perusahaan'));
        if (!array_key_exists($kategori, $catalog)) {
            return ['status' => false, 'message' => 'Kategori dokumen tidak sesuai ketentuan.'];
        }

        $judul  = trim((string) ($input['judul_dokumen'] ?? ''));
        $sumber = trim((string) ($input['sumber_dokumen'] ?? ''));
        $urlRaw = trim((string) ($input['url_sumber'] ?? ''));
        $urlOk  = null;

        if ($urlRaw !== '') {
            if (!ketarunaan_is_http_url($urlRaw, 255)) {
                return ['status' => false, 'message' => 'Tautan dokumen atau sumber harus diawali http:// atau https:// (maksimal 255 karakter).'];
            }
            $urlOk = ketarunaan_sanitize_external_url($urlRaw, false);
            if ($urlOk === null) {
                return ['status' => false, 'message' => 'Tautan sumber harus diawali http:// atau https://.'];
            }
        }

        if ($judul === '') {
            return ['status' => false, 'message' => 'Judul dokumen atau sumber wajib diisi.'];
        }

        if ($kategori === 'tautan_sumber' && $urlOk === null) {
            return ['status' => false, 'message' => 'Tautan sumber wajib diisi untuk kategori Tautan Sumber.'];
        }

        if ($kategori !== 'tautan_sumber' && $savedFileName === null && $urlOk === null) {
            return ['status' => false, 'message' => 'Isi tautan Google Drive / Cloud atau portal resmi terlebih dahulu (diawali http:// atau https://).'];
        }

        $defaultInternal = (int) $catalog[$kategori]['is_internal'];
        $isInternal      = array_key_exists('is_internal', $input) ? ((int) $input['is_internal'] === 1 ? 1 : 0) : $defaultInternal;
        // Bukti konfirmasi & surat perusahaan selalu terkunci sebagai bukti internal sesuai LOW-03
        if (in_array($kategori, ['bukti_konfirmasi', 'surat_perusahaan'], true)) {
            $isInternal = 1;
        }

        $tglRaw = trim((string) ($input['tanggal_diterima'] ?? ''));
        if ($tglRaw !== '' && !$this->isValidDateYmd($tglRaw)) {
            return ['status' => false, 'message' => 'Format tanggal diterima tidak sesuai.'];
        }
        $tgl    = $tglRaw !== '' ? $tglRaw : date('Y-m-d');

        $payload = [
            'lowongan_id'        => $lowonganId,
            'kategori_dokumen'   => $kategori,
            'judul_dokumen'      => $judul,
            'sumber_dokumen'     => $sumber ?: null,
            'nama_berkas'        => $savedFileName,
            'url_sumber'         => $urlOk,
            'tanggal_diterima'   => $tgl,
            'status_pemeriksaan' => 'belum_diperiksa',
            'is_internal'        => $isInternal,
            'catatan'            => trim((string) ($input['catatan'] ?? '')) ?: null,
            'diunggah_oleh'      => $actorName,
        ];

        $this->db->transStart();
        $this->dokumenModel->insert($payload);
        $docId = (int) $this->dokumenModel->getInsertID();
        log_audit('CREATE', 'lowongan_dokumen', (string) $docId, null, $payload);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['status' => false, 'message' => 'Gagal menyimpan dokumen lowongan. Silakan coba lagi.'];
        }

        return ['status' => true, 'message' => 'Dokumen pendukung lowongan berhasil ditambahkan.'];
    }

    /**
     * Menghapus satu dokumen lowongan dan file fisiknya secara aman.
     *
     * @param int $docId
     * @return array{status: bool, message: string, lowongan_id?: int}
     */
    public function removeDokumen(int $docId): array
    {
        $doc = $this->dokumenModel->find($docId);
        if (!$doc) {
            return ['status' => false, 'message' => 'Dokumen tidak ditemukan.'];
        }

        $lowonganId = (int) $doc['lowongan_id'];
        $namaBerkas = (string) ($doc['nama_berkas'] ?? '');

        $this->db->transStart();
        $this->dokumenModel->delete($docId);
        log_audit('DELETE', 'lowongan_dokumen', (string) $docId, $doc, null);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['status' => false, 'message' => 'Gagal menghapus dokumen lowongan.'];
        }

        if ($namaBerkas !== '') {
            $safePath = FCPATH . 'assets/uploads/ketarunaan/lowongan/' . basename($namaBerkas);
            if (is_file($safePath)) {
                @unlink($safePath);
            }
        }

        return [
            'status'      => true,
            'lowongan_id' => $lowonganId,
            'message'     => 'Dokumen berhasil dihapus.',
        ];
    }

    /**
     * Mengambil satu baris dokumen beserta lowongan induknya untuk pengecekan otorisasi unduhan (LOW-03, NFR-02).
     *
     * @param int $docId
     * @return array<string, mixed>|null
     */
    public function getDokumenForDownload(int $docId): ?array
    {
        $row = $this->db->table('lowongan_dokumen d')
            ->select('d.*, l.status_validasi, l.status_publikasi, l.batas_pendaftaran')
            ->join('lowongan_pekerjaan l', 'l.id = d.lowongan_id', 'left')
            ->where('d.id', $docId)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Mengambil antrean verifikasi & detail pemeriksaan lowongan terpilih (LOW-04, LOW-05).
     *
     * @param int|null    $lowonganId
     * @param string|null $waktuAcuan
     * @return array<string, mixed>
     */
    public function getVerifikasiPageData(?int $lowonganId = null, ?string $waktuAcuan = null): array
    {
        $acuan = $waktuAcuan ?? date('Y-m-d H:i:s');

        $queue = $this->db->table('lowongan_pekerjaan l')
            ->select('l.*, pm.nama_perusahaan, pm.sektor, pm.kota AS kota_perusahaan, pm.kontak_person, pm.pic_jabatan, pm.email_rekrutmen, pm.telepon_rekrutmen, pm.is_verified AS perusahaan_verified')
            ->join('perusahaan_mitra pm', 'pm.id = l.perusahaan_id', 'left')
            ->orderBy("CASE WHEN l.status_validasi = 'diajukan' THEN 0 WHEN l.status_validasi = 'perlu_perbaikan' THEN 1 WHEN l.status_validasi = 'tervalidasi' AND l.status_publikasi = 'draf' THEN 2 ELSE 3 END", 'ASC', false)
            ->orderBy('l.id', 'DESC')
            ->get()
            ->getResultArray();

        $prodiMap = $this->loadProdiMap(array_column($queue, 'id'));
        $selected = null;

        foreach ($queue as &$item) {
            $lid = (int) $item['id'];
            $item['sasaran_prodi']  = $prodiMap[$lid] ?? [];
            $item['status_efektif'] = ketarunaan_lowongan_status_efektif($item, $acuan);
            $item['checklist']      = $this->decodeChecklist($item['checklist_verifikasi_json'] ?? null);
            if ($lowonganId !== null && $lid === $lowonganId) {
                $selected = $item;
            }
        }
        unset($item);

        if ($selected === null && !empty($queue)) {
            $selected = $queue[0];
        }

        $selectedDocs = [];
        if ($selected !== null) {
            $selectedDocs = $this->db->table('lowongan_dokumen')
                ->where('lowongan_id', (int) $selected['id'])
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();
        }

        return [
            'queue'        => $queue,
            'selected'     => $selected,
            'selectedDocs' => $selectedDocs,
        ];
    }

    /**
     * Memproses pemeriksaan checklist & penetapan hasil validasi (TERPISAH dari publikasi - LOW-05, UAT-15).
     *
     * @param int                  $id
     * @param array<string, mixed> $checklistInput
     * @param string               $keputusan tervalidasi|perlu_perbaikan
     * @param string               $catatan
     * @param string               $actorName
     * @return array{status: bool, message: string}
     */
    public function saveVerifikasi(int $id, array $checklistInput, string $keputusan, string $catatan, string $actorName): array
    {
        $existing = $this->lowonganModel->find($id);
        if (!$existing) {
            return ['status' => false, 'message' => 'Data lowongan tidak ditemukan.'];
        }

        if (!in_array($keputusan, ['tervalidasi', 'perlu_perbaikan'], true)) {
            return ['status' => false, 'message' => 'Keputusan pemeriksaan tidak sesuai ketentuan.'];
        }

        $catalog   = ketarunaan_lowongan_checklist_catalog();
        $checklist = [];
        $allChecked = true;
        foreach (array_keys($catalog) as $key) {
            $checked = !empty($checklistInput[$key]);
            $checklist[$key] = $checked;
            if (!$checked) {
                $allChecked = false;
            }
        }

        $catatanClean = trim($catatan);
        if ($keputusan === 'perlu_perbaikan' && $catatanClean === '') {
            return ['status' => false, 'message' => 'Catatan perbaikan wajib diisi saat mengembalikan lowongan.'];
        }

        if ($keputusan === 'tervalidasi' && !$allChecked) {
            return ['status' => false, 'message' => 'Centang seluruh butir daftar periksa sebelum mengesahkan validasi lowongan.'];
        }

        $riwayat = $this->decodeRiwayat($existing['riwayat_proses_json'] ?? null);
        $today   = date('Y-m-d');
        $riwayat[] = [
            'tanggal' => $today,
            'pelaku'  => $actorName,
            'aksi'    => $keputusan === 'tervalidasi' ? 'Divalidasi (Siap Terbit / Draf)' : 'Dikembalikan untuk Perbaikan',
            'status'  => $keputusan,
            'catatan' => $catatanClean !== '' ? $catatanClean : 'Seluruh kelengkapan pemeriksaan lowongan terpenuhi.',
        ];

        $updateData = [
            'status_validasi'           => $keputusan,
            // Jika dikembalikan untuk perbaikan, publikasi ditahan di draf; jika tervalidasi, publikasi tetap pada nilai sebelumnya (default draf - UAT-15)
            'status_publikasi'          => $keputusan === 'perlu_perbaikan' ? 'draf' : ($existing['status_publikasi'] === 'ditutup' ? 'ditutup' : 'draf'),
            'checklist_verifikasi_json' => json_encode($checklist, JSON_UNESCAPED_UNICODE),
            'catatan_pemeriksa'         => $catatanClean ?: null,
            'diperiksa_oleh'            => $actorName,
            'tanggal_pemeriksaan'       => $today,
            'riwayat_proses_json'       => json_encode($riwayat, JSON_UNESCAPED_UNICODE),
        ];

        $this->db->transStart();
        $this->lowonganModel->update($id, $updateData);
        // Sinkronkan status pemeriksaan dokumen terkait saat validasi selesai
        $docStatus = $keputusan === 'tervalidasi' ? 'sesuai' : 'perlu_perbaikan';
        $this->db->table('lowongan_dokumen')
            ->where('lowongan_id', $id)
            ->update(['status_pemeriksaan' => $docStatus, 'updated_at' => date('Y-m-d H:i:s')]);
        log_audit($keputusan === 'tervalidasi' ? 'APPROVE' : 'UPDATE', 'lowongan_pekerjaan', (string) $id, ['status_validasi' => $existing['status_validasi']], $updateData);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['status' => false, 'message' => 'Gagal menyimpan hasil verifikasi lowongan.'];
        }

        $msg = $keputusan === 'tervalidasi'
            ? 'Lowongan tervalidasi. Publikasi masih berstatus draf sampai diterbitkan.'
            : 'Lowongan dikembalikan ke pengelola untuk perbaikan.';

        return ['status' => true, 'message' => $msg];
    }

    /**
     * Mengubah status publikasi lowongan secara terpisah (Terbit, Draf, atau Penutupan Manual Beralasan - LOW-05, LOW-06, UAT-15, UAT-16).
     *
     * @param int         $id
     * @param string      $aksi terbit|draf|ditutup
     * @param string|null $alasanPenutupan
     * @param string      $actorName
     * @param string|null $waktuAcuan
     * @return array{status: bool, message: string}
     */
    public function setPublikasi(int $id, string $aksi, ?string $alasanPenutupan, string $actorName, ?string $waktuAcuan = null): array
    {
        $existing = $this->lowonganModel->find($id);
        if (!$existing) {
            return ['status' => false, 'message' => 'Data lowongan tidak ditemukan.'];
        }

        $acuan   = $waktuAcuan ?? date('Y-m-d H:i:s');
        $today   = date('Y-m-d');
        $riwayat = $this->decodeRiwayat($existing['riwayat_proses_json'] ?? null);

        if ($aksi === 'terbit') {
            if ($existing['status_validasi'] !== 'tervalidasi') {
                return ['status' => false, 'message' => 'Lowongan belum tervalidasi. Selesaikan verifikasi sebelum menerbitkan.'];
            }
            if ((string) $existing['batas_pendaftaran'] < $acuan) {
                return ['status' => false, 'message' => 'Batas pendaftaran sudah berakhir. Perpanjang tanggal tenggat dan ajukan verifikasi ulang.'];
            }

            $riwayat[] = [
                'tanggal' => $today,
                'pelaku'  => $actorName,
                'aksi'    => 'Diterbitkan ke Direktori',
                'status'  => 'terbit',
                'catatan' => 'Lowongan resmi ditayangkan untuk alumni dan taruna tingkat akhir.',
            ];

            $payload = [
                'status_publikasi'    => 'terbit',
                'dipublikasi_oleh'    => $actorName,
                'tanggal_publikasi'   => $today,
                'alasan_penutupan'    => null,
                'ditutup_oleh'        => null,
                'tanggal_ditutup'     => null,
                'riwayat_proses_json' => json_encode($riwayat, JSON_UNESCAPED_UNICODE),
            ];
            $msg = 'Lowongan berhasil diterbitkan.';
        } elseif ($aksi === 'draf') {
            $riwayat[] = [
                'tanggal' => $today,
                'pelaku'  => $actorName,
                'aksi'    => 'Ditahan sebagai Draf',
                'status'  => 'draf',
                'catatan' => 'Lowongan diturunkan sementara dari daftar tayang.',
            ];
            $payload = [
                'status_publikasi'    => 'draf',
                'riwayat_proses_json' => json_encode($riwayat, JSON_UNESCAPED_UNICODE),
            ];
            $msg = 'Status publikasi diubah menjadi draf.';
        } elseif ($aksi === 'ditutup') {
            $alasanClean = trim((string) $alasanPenutupan);
            if ($alasanClean === '') {
                return ['status' => false, 'message' => 'Alasan penutupan wajib diisi sebelum menutup lowongan.'];
            }

            $riwayat[] = [
                'tanggal' => $today,
                'pelaku'  => $actorName,
                'aksi'    => 'Ditutup Manual',
                'status'  => 'ditutup',
                'catatan' => $alasanClean,
            ];
            $payload = [
                'status_publikasi'    => 'ditutup',
                'alasan_penutupan'    => $alasanClean,
                'ditutup_oleh'        => $actorName,
                'tanggal_ditutup'     => $today,
                'riwayat_proses_json' => json_encode($riwayat, JSON_UNESCAPED_UNICODE),
            ];
            $msg = 'Lowongan ditutup dan dipindahkan ke arsip riwayat.';
        } else {
            return ['status' => false, 'message' => 'Aksi publikasi tidak dikenali.'];
        }

        $this->db->transStart();
        $this->lowonganModel->update($id, $payload);
        log_audit('UPDATE', 'lowongan_pekerjaan', (string) $id, ['status_publikasi' => $existing['status_publikasi']], $payload);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['status' => false, 'message' => 'Gagal memperbarui status publikasi lowongan.'];
        }

        return ['status' => true, 'message' => $msg];
    }

    /**
     * Mengambil data halaman Riwayat Validasi & Pengendalian Masa Tayang (LOW-06, LOW-07).
     *
     * @param int|null    $lowonganId
     * @param string|null $waktuAcuan
     * @return array<string, mixed>
     */
    public function getRiwayatPageData(?int $lowonganId = null, ?string $waktuAcuan = null): array
    {
        $acuan = $waktuAcuan ?? date('Y-m-d H:i:s');
        $rows  = $this->db->table('lowongan_pekerjaan l')
            ->select('l.*, pm.nama_perusahaan, pm.is_verified AS perusahaan_verified')
            ->join('perusahaan_mitra pm', 'pm.id = l.perusahaan_id', 'left')
            ->orderBy('l.updated_at', 'DESC')
            ->orderBy('l.id', 'DESC')
            ->get()
            ->getResultArray();

        $selected = null;
        foreach ($rows as &$r) {
            $r['status_efektif'] = ketarunaan_lowongan_status_efektif($r, $acuan);
            $r['riwayat_proses'] = $this->decodeRiwayat($r['riwayat_proses_json'] ?? null);
            if ($lowonganId !== null && (int) $r['id'] === $lowonganId) {
                $selected = $r;
            }
        }
        unset($r);

        if ($selected === null && !empty($rows)) {
            $selected = $rows[0];
        }

        return [
            'vacancies' => $rows,
            'selected'  => $selected,
        ];
    }

    /**
     * Menyusun baris ekspor CSV lowongan kerja sesuai filter dan kewenangan pengguna.
     *
     * @param array<string, mixed> $filters
     * @param bool                 $canViewStaffBoard
     * @param string|null          $waktuAcuan
     * @return array<int, array<int, string>>
     */
    public function getExportRows(array $filters, bool $canViewStaffBoard, ?string $waktuAcuan = null): array
    {
        $data = $this->getDirectoryData($filters, $canViewStaffBoard, $waktuAcuan, true);
        $out  = [
            ['ID', 'Posisi', 'Perusahaan', 'Kontak Mitra', 'Jenis', 'Jenjang', 'Lokasi', 'Program Studi Sasaran', 'Batas Pendaftaran', 'Status Validasi', 'Status Tayang Efektif', 'Alasan Penutupan'],
        ];

        foreach ($data['rows'] as $row) {
            $prodiStr = implode(', ', array_map(static fn($p) => $p['kode_prodi'], $row['sasaran_prodi'] ?? []));
            $out[] = [
                (string) $row['id'],
                $this->sanitizeCsvCell((string) $row['judul_lowongan']),
                $this->sanitizeCsvCell((string) ($row['nama_perusahaan'] ?? '-')),
                ((int) ($row['perusahaan_verified'] ?? 0) === 1) ? 'Kontak diperiksa' : 'Belum diperiksa',
                $this->sanitizeCsvCell((string) $row['jenis_pekerjaan']),
                $this->sanitizeCsvCell((string) ($row['jenjang_pendidikan'] ?? '-')),
                $this->sanitizeCsvCell((string) ($row['lokasi'] ?? '-')),
                $this->sanitizeCsvCell($prodiStr ?: '-'),
                substr((string) ($row['batas_pendaftaran'] ?? ''), 0, 10),
                (string) $row['status_validasi'],
                (string) $row['status_efektif'],
                $this->sanitizeCsvCell((string) ($row['alasan_penutupan'] ?? '-')),
            ];
        }

        return $out;
    }

    /**
     * Mendeteksi perubahan kolom substantif yang memicu verifikasi ulang (LOW-07).
     *
     * @param array<string, mixed> $existing
     * @param array<string, mixed> $incoming
     * @param array<int, int>      $oldProdiIds
     * @param array<int, int>      $newProdiIds
     * @return array<int, string>
     */
    private function detectSubstantialChanges(array $existing, array $incoming, array $oldProdiIds, array $newProdiIds): array
    {
        $changed = [];
        $fieldLabels = [
            'perusahaan_id'      => 'Perusahaan Mitra',
            'judul_lowongan'     => 'Posisi Lowongan',
            'jenis_pekerjaan'    => 'Jenis Pekerjaan',
            'jenjang_pendidikan' => 'Jenjang Pendidikan',
            'lokasi'             => 'Lokasi',
            'deskripsi'          => 'Ringkasan Pekerjaan',
            'persyaratan'        => 'Persyaratan',
            'batas_pendaftaran'  => 'Batas Pendaftaran',
            'metode_lamaran'     => 'Metode Lamaran',
            'tautan_pendaftaran' => 'Tautan Lamaran',
            'email_lamaran'      => 'Alamat Email Lamaran',
        ];

        foreach ($fieldLabels as $col => $label) {
            $oldVal = trim((string) ($existing[$col] ?? ''));
            $newVal = trim((string) ($incoming[$col] ?? ''));
            if ($col === 'perusahaan_id') {
                if ((int) $oldVal !== (int) $newVal) {
                    $changed[] = $label;
                }
                continue;
            }
            if ($oldVal !== $newVal) {
                $changed[] = $label;
            }
        }

        sort($oldProdiIds);
        sort($newProdiIds);
        if ($oldProdiIds !== $newProdiIds) {
            $changed[] = 'Program Studi Sasaran';
        }

        return $changed;
    }

    /**
     * @param array<int, int> $lowonganIds
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function loadProdiMap(array $lowonganIds): array
    {
        $ids = array_values(array_filter(array_map('intval', $lowonganIds)));
        if (empty($ids)) {
            return [];
        }

        $rows = $this->db->table('lowongan_sasaran_prodi lsp')
            ->select('lsp.lowongan_id, rps.id, rps.kode_prodi, rps.nama_prodi')
            ->join('ref_program_studi rps', 'rps.id = lsp.program_studi_id', 'inner')
            ->whereIn('lsp.lowongan_id', $ids)
            ->orderBy('rps.id', 'ASC')
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $r) {
            $map[(int) $r['lowongan_id']][] = [
                'id'         => (int) $r['id'],
                'kode_prodi' => (string) $r['kode_prodi'],
                'nama_prodi' => (string) $r['nama_prodi'],
            ];
        }

        return $map;
    }

    /**
     * @param array<int, int> $lowonganIds
     * @return array<int, array<string, int>>
     */
    private function loadDokumenSummaryMap(array $lowonganIds): array
    {
        $ids = array_values(array_filter(array_map('intval', $lowonganIds)));
        if (empty($ids)) {
            return [];
        }

        $rows = $this->db->table('lowongan_dokumen')
            ->select('lowongan_id, is_internal')
            ->whereIn('lowongan_id', $ids)
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $r) {
            $lid = (int) $r['lowongan_id'];
            if (!isset($map[$lid])) {
                $map[$lid] = ['total' => 0, 'publik' => 0, 'internal' => 0];
            }
            $map[$lid]['total']++;
            if ((int) $r['is_internal'] === 1) {
                $map[$lid]['internal']++;
            } else {
                $map[$lid]['publik']++;
            }
        }

        return $map;
    }

    /**
     * @param int $lowonganId
     * @return array<int, int>
     */
    private function getProdiIdsByLowongan(int $lowonganId): array
    {
        $rows = $this->db->table('lowongan_sasaran_prodi')
            ->select('program_studi_id')
            ->where('lowongan_id', $lowonganId)
            ->get()
            ->getResultArray();

        return array_map(static fn($r) => (int) $r['program_studi_id'], $rows);
    }

    /**
     * @param int             $lowonganId
     * @param array<int, int> $prodiIds
     * @return void
     */
    private function syncSasaranProdi(int $lowonganId, array $prodiIds): void
    {
        $this->db->table('lowongan_sasaran_prodi')->where('lowongan_id', $lowonganId)->delete();
        $now = date('Y-m-d H:i:s');
        foreach ($prodiIds as $pid) {
            $this->sasaranProdiModel->insert([
                'lowongan_id'      => $lowonganId,
                'program_studi_id' => $pid,
                'created_at'       => $now,
            ]);
        }
    }

    /**
     * @param array<int, mixed> $prodiIds
     * @return array<int, int>
     */
    private function filterValidProdiIds(array $prodiIds): array
    {
        $clean = array_values(array_unique(array_filter(array_map('intval', $prodiIds), static fn($v) => $v > 0)));
        if (empty($clean)) {
            return [];
        }

        $valid = $this->db->table('ref_program_studi')
            ->select('id')
            ->whereIn('id', $clean)
            ->get()
            ->getResultArray();

        return array_map(static fn($r) => (int) $r['id'], $valid);
    }

    /**
     * Normalisasi batas pendaftaran ke akhir hari 23:59:59 WIB bila hanya diisi tanggal (OPN-09).
     */
    private function normalizeDeadline(string $raw): ?string
    {
        $clean = trim($raw);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $clean)) {
            return $this->isValidDateYmd($clean) ? ($clean . ' 23:59:59') : null;
        }
        if (preg_match('/^(\d{4}-\d{2}-\d{2})[ T]\d{2}:\d{2}(:\d{2})?$/', $clean, $m)) {
            if (!$this->isValidDateYmd($m[1])) {
                return null;
            }
            $ts = strtotime($clean);
            return ($ts !== false && date('Y-m-d', $ts) === $m[1]) ? date('Y-m-d H:i:s', $ts) : null;
        }

        return null;
    }

    /**
     * Memvalidasi format tanggal ISO Y-m-d secara ketat (mencegah tanggal kalender tidak sah).
     */
    protected function isValidDateYmd(string $date): bool
    {
        $dt = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        return $dt !== false && $dt->format('Y-m-d') === $date;
    }

    /**
     * @return array<string, bool>
     */
    private function decodeChecklist(?string $json): array
    {
        $keys = array_keys(ketarunaan_lowongan_checklist_catalog());
        $base = array_fill_keys($keys, false);
        if (!$json) {
            return $base;
        }

        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return $base;
        }

        foreach ($keys as $k) {
            $base[$k] = !empty($decoded[$k]);
        }

        return $base;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function decodeRiwayat(?string $json): array
    {
        if (!$json) {
            return [];
        }
        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function sanitizeCsvCell(string $value): string
    {
        $clean = str_replace(["\r", "\n"], ' ', trim($value));
        if ($clean !== '' && in_array($clean[0], ['=', '+', '-', '@', "\t"], true)) {
            return "'" . $clean;
        }

        return $clean;
    }
}
