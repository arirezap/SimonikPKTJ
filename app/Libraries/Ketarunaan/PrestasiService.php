<?php

namespace App\Libraries\Ketarunaan;

use App\Models\Ketarunaan\TarunaPesertaPrestasiModel;
use App\Models\Ketarunaan\TarunaPrestasiModel;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

/**
 * Service terpusat untuk logika bisnis Modul 3: Prestasi Taruna (ECC 2.0).
 * Mematuhi prinsip Single Responsibility (SRP), Dual-Count, dan Anti-Duplikasi Peserta.
 */
class PrestasiService
{
    protected BaseConnection $db;
    protected TarunaPrestasiModel $prestasiModel;
    protected TarunaPesertaPrestasiModel $pesertaModel;

    public function __construct(
        ?BaseConnection $db = null,
        ?TarunaPrestasiModel $prestasiModel = null,
        ?TarunaPesertaPrestasiModel $pesertaModel = null
    ) {
        $this->db            = $db ?? Database::connect();
        $this->prestasiModel = $prestasiModel ?? new TarunaPrestasiModel();
        $this->pesertaModel  = $pesertaModel ?? new TarunaPesertaPrestasiModel();
        helper('ketarunaan');
    }

    /**
     * Mengambil opsi referensi untuk filter dan formulir prestasi.
     *
     * @return array<string, mixed>
     */
    public function getReferenceOptions(): array
    {
        ketarunaan_ensure_kalender_terkini($this->db);

        $prodiList = $this->db->table('ref_program_studi')
            ->select('id, kode_prodi, nama_prodi, jenjang')
            ->where('is_aktif', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $tahunAkademikList = $this->db->table('ref_tahun_akademik')
            ->select('id, kode_tahun, tahun, semester, tanggal_mulai, tanggal_selesai, is_aktif')
            ->orderBy('tahun', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return [
            'prodi_list'          => $prodiList,
            'tahun_akademik_list' => $tahunAkademikList,
            'jenis_catalog'       => ketarunaan_jenis_prestasi_catalog(),
            'tingkat_catalog'     => ketarunaan_tingkat_prestasi_catalog(),
            'bidang_options'      => [
                'Inovasi Keselamatan Jalan',
                'Keselamatan & Transportasi',
                'Teknologi Otomotif',
                'Karya Tulis Ilmiah',
                'Olahraga & Kedinasan',
                'Seni & Budaya',
                'Lainnya',
            ],
            'peringkat_options'   => [
                'Juara I',
                'Juara II',
                'Juara III',
                'Juara Harapan',
                'Medali Emas',
                'Medali Perak',
                'Medali Perunggu',
                'Finalis',
                'Peserta Terbaik',
            ],
            'pembimbing_options'  => [
                'Pembina Ketarunaan',
                'Dosen Pembimbing Prodi',
                'Pelatih Unit / UKM',
                'Tim Pusbangkar',
            ],
            'unit_options'        => ['Ketarunaan', 'Pusbangkar', 'Program Studi'],
            'sumber_options'      => ['Piagam Penghargaan', 'Sertifikat', 'Surat Keputusan (SK)', 'Laporan Kegiatan'],
            'peran_options'       => ['Ketua Tim', 'Anggota', 'Peserta Utama'],
        ];
    }

    /**
     * Mengambil daftar taruna ringkas untuk pilihan penambahan peserta prestasi.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTarunaSelectList(bool $onlyAktif = false): array
    {
        $builder = $this->db->table('master_taruna mt')
            ->select('mt.nomor_taruna, mt.nama_lengkap, mt.status_terkini, ps.kode_prodi, ang.tahun_masuk AS angkatan, ang.kode_romawi AS angkatan_romawi')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = mt.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan ang', 'ang.id = pt.angkatan_id', 'left');

        if ($onlyAktif) {
            $builder->where('mt.status_terkini', 'aktif');
        }

        return $builder->orderBy('mt.nomor_taruna', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Mengambil data direktori prestasi beserta 4 kartu metrik dual-count.
     *
     * @param array<string, mixed> $filters
     * @param int                  $page
     * @param int                  $perPage
     * @param bool                 $isSelfTarunaOnly
     * @param string|null          $ownNomorTaruna
     * @return array<string, mixed>
     */
    public function getDirectoryData(
        array $filters = [],
        int $page = 1,
        int $perPage = 15,
        bool $isSelfTarunaOnly = false,
        ?string $ownNomorTaruna = null
    ): array {
        $q             = trim((string) ($filters['q'] ?? ''));
        $jenis         = trim((string) ($filters['jenis'] ?? 'all'));
        $tingkat       = trim((string) ($filters['tingkat'] ?? 'all'));
        $prodi         = trim((string) ($filters['prodi'] ?? 'all'));
        $periode       = trim((string) ($filters['periode'] ?? 'all'));
        $status        = trim((string) ($filters['status'] ?? 'all'));
        $keikutsertaan = trim((string) ($filters['keikutsertaan'] ?? 'all'));
        $milikSaya     = ((string) ($filters['milik_saya'] ?? '') === '1');
        $sortBy        = trim((string) ($filters['sort_by'] ?? ''));
        $sortOrder     = strtolower(trim((string) ($filters['sort_order'] ?? 'desc'))) === 'asc' ? 'ASC' : 'DESC';

        // 1. Empat kartu metrik dual-count dari sumber basis data
        $prestasiTervalidasi = (int) $this->db->table('taruna_prestasi')
            ->where('status_validasi', 'tervalidasi')
            ->countAllResults();

        $tarunaBerprestasi = (int) $this->db->table('taruna_peserta_prestasi tpp')
            ->select('tpp.nomor_taruna')
            ->distinct()
            ->join('taruna_prestasi tp', 'tp.id = tpp.prestasi_id', 'inner')
            ->where('tp.status_validasi', 'tervalidasi')
            ->countAllResults();

        $prestasiTim = (int) $this->db->table('taruna_prestasi')
            ->where('status_validasi', 'tervalidasi')
            ->where('jenis_keikutsertaan', 'tim')
            ->countAllResults();

        if ($isSelfTarunaOnly) {
            $menungguValidasi = ($ownNomorTaruna !== null && trim($ownNomorTaruna) !== '')
                ? (int) $this->db->table('taruna_prestasi tp')
                    ->join('taruna_peserta_prestasi tpp_w', 'tpp_w.prestasi_id = tp.id', 'inner')
                    ->where('tp.status_validasi', 'diajukan')
                    ->where('tpp_w.nomor_taruna', trim($ownNomorTaruna))
                    ->countAllResults()
                : 0;
        } else {
            $menungguValidasi = (int) $this->db->table('taruna_prestasi')
                ->where('status_validasi', 'diajukan')
                ->countAllResults();
        }

        // 2. Kueri daftar prestasi dengan jumlah peserta unik per prestasi
        $pesertaAggSql = "(SELECT tpp.prestasi_id, COUNT(DISTINCT tpp.nomor_taruna) AS jml_peserta
                           FROM taruna_peserta_prestasi tpp
                           GROUP BY tpp.prestasi_id) pagg";

        $ownNomorClean = ($ownNomorTaruna !== null) ? trim($ownNomorTaruna) : '';
        $isPesertaSelect = ($ownNomorClean !== '')
            ? "EXISTS (SELECT 1 FROM taruna_peserta_prestasi tpp_me WHERE tpp_me.prestasi_id = tp.id AND tpp_me.nomor_taruna = " . $this->db->escape($ownNomorClean) . ") AS is_peserta_saya"
            : "0 AS is_peserta_saya";

        $builder = $this->db->table('taruna_prestasi tp')
            ->select("tp.id, tp.nama_prestasi, tp.jenis_prestasi, tp.bidang_prestasi, tp.tingkat_prestasi,
                      tp.peringkat, tp.penyelenggara, tp.tanggal_mulai, tp.tanggal_selesai,
                      tp.jenis_keikutsertaan, tp.nama_tim, tp.status_validasi,
                      COALESCE(pagg.jml_peserta, 0) AS jumlah_peserta,
                      {$isPesertaSelect}", false)
            ->join($pesertaAggSql, 'pagg.prestasi_id = tp.id', 'left', false);

        if ($isSelfTarunaOnly) {
            if ($ownNomorClean !== '') {
                $escapedOwn = $this->db->escape($ownNomorClean);
                if ($milikSaya) {
                    $builder->where(
                        "EXISTS (SELECT 1 FROM taruna_peserta_prestasi tpp_own
                                 WHERE tpp_own.prestasi_id = tp.id AND tpp_own.nomor_taruna = {$escapedOwn})",
                        null,
                        false
                    );
                } else {
                    $builder->groupStart()
                        ->where('tp.status_validasi', 'tervalidasi')
                        ->orWhere(
                            "EXISTS (SELECT 1 FROM taruna_peserta_prestasi tpp_own
                                     WHERE tpp_own.prestasi_id = tp.id AND tpp_own.nomor_taruna = {$escapedOwn})",
                            null,
                            false
                        )
                        ->groupEnd();
                }
            } else {
                if ($milikSaya) {
                    $builder->where('1 = 0', null, false);
                } else {
                    $builder->where('tp.status_validasi', 'tervalidasi');
                }
            }
        }

        if ($q !== '') {
            $escapedLike = '%' . $this->db->escapeLikeString($q) . '%';
            $builder->groupStart()
                ->like('tp.nama_prestasi', $q)
                ->orLike('tp.nama_tim', $q)
                ->orWhere(
                    "EXISTS (SELECT 1 FROM taruna_peserta_prestasi tppq
                             INNER JOIN master_taruna mtq ON mtq.nomor_taruna = tppq.nomor_taruna
                             WHERE tppq.prestasi_id = tp.id
                             AND (tppq.nomor_taruna LIKE '{$escapedLike}' ESCAPE '!' OR mtq.nama_lengkap LIKE '{$escapedLike}' ESCAPE '!'))",
                    null,
                    false
                )
                ->groupEnd();
        }

        if ($jenis !== '' && $jenis !== 'all') {
            $builder->where('tp.jenis_prestasi', $jenis);
        }
        if ($tingkat !== '' && $tingkat !== 'all') {
            $builder->where('tp.tingkat_prestasi', $tingkat);
        }
        if ($status !== '' && $status !== 'all') {
            if ($status === 'dikembalikan') {
                $builder->where('tp.status_validasi', 'perlu_perbaikan');
            } else {
                $builder->where('tp.status_validasi', $status);
            }
        }
        if (in_array($keikutsertaan, ['tim', 'individu'], true)) {
            $builder->where('tp.jenis_keikutsertaan', $keikutsertaan);
        }
        if ($prodi !== '' && $prodi !== 'all') {
            $escapedProdi = $this->db->escape($prodi);
            $builder->where(
                "EXISTS (SELECT 1 FROM taruna_peserta_prestasi tppp
                         INNER JOIN pendidikan_taruna ptp ON ptp.nomor_taruna = tppp.nomor_taruna
                         INNER JOIN ref_program_studi psp ON psp.id = ptp.program_studi_id
                         WHERE tppp.prestasi_id = tp.id AND psp.kode_prodi = {$escapedProdi})",
                null,
                false
            );
        }
        if ($periode !== '' && $periode !== 'all' && ctype_digit($periode)) {
            $taRow = $this->db->table('ref_tahun_akademik')
                ->where('id', (int) $periode)
                ->get()
                ->getRowArray();

            if ($taRow && !empty($taRow['tanggal_mulai']) && !empty($taRow['tanggal_selesai'])) {
                $builder->where('tp.tanggal_mulai >=', $taRow['tanggal_mulai'])
                    ->where('tp.tanggal_mulai <=', $taRow['tanggal_selesai']);
            }
        }

        $totalFiltered = $builder->countAllResults(false);
        $isUnlimited   = ($perPage <= 0);
        $totalPages    = $isUnlimited ? 1 : max(1, (int) ceil($totalFiltered / $perPage));
        $page          = $isUnlimited ? 1 : max(1, min($page, $totalPages));
        $offset        = $isUnlimited ? 0 : ($page - 1) * $perPage;

        switch ($sortBy) {
            case 'nama_prestasi':
                $builder->orderBy('tp.nama_prestasi', $sortOrder)->orderBy('tp.id', 'DESC');
                break;
            case 'tingkat_prestasi':
                $builder->orderBy("FIELD(tp.tingkat_prestasi, 'internal', 'daerah', 'nasional', 'internasional')", $sortOrder, false)
                    ->orderBy('tp.tanggal_mulai', 'DESC');
                break;
            case 'peringkat':
                $builder->orderBy('tp.peringkat', $sortOrder)->orderBy('tp.tanggal_mulai', 'DESC');
                break;
            case 'jenis_keikutsertaan':
                $builder->orderBy('tp.jenis_keikutsertaan', $sortOrder)->orderBy('tp.tanggal_mulai', 'DESC');
                break;
            case 'jumlah_peserta':
                $builder->orderBy('COALESCE(pagg.jml_peserta, 0)', $sortOrder, false)->orderBy('tp.id', 'DESC');
                break;
            case 'tanggal_mulai':
                $builder->orderBy('tp.tanggal_mulai', $sortOrder)->orderBy('tp.id', 'DESC');
                break;
            case 'status_validasi':
                $builder->orderBy("FIELD(tp.status_validasi, 'diajukan', 'perlu_perbaikan', 'draf', 'tervalidasi')", $sortOrder, false)
                    ->orderBy('tp.tanggal_mulai', 'DESC');
                break;
            default:
                $builder->orderBy("FIELD(tp.status_validasi, 'diajukan', 'perlu_perbaikan', 'draf', 'tervalidasi')", '', false)
                    ->orderBy('tp.tanggal_mulai', 'DESC')
                    ->orderBy('tp.id', 'DESC');
                break;
        }

        if (!$isUnlimited) {
            $builder->limit($perPage, $offset);
        }

        $rows = $builder
            ->get()
            ->getResultArray();

        return [
            'metrics' => [
                'prestasi_tervalidasi' => $prestasiTervalidasi,
                'taruna_berprestasi'   => $tarunaBerprestasi,
                'prestasi_tim'         => $prestasiTim,
                'menunggu_validasi'    => $menungguValidasi,
            ],
            'rows'       => $rows,
            'pagination' => [
                'page'           => $page,
                'per_page'       => $isUnlimited ? $totalFiltered : $perPage,
                'total_filtered' => $totalFiltered,
                'total_pages'    => $totalPages,
                'from'           => $totalFiltered > 0 ? $offset + 1 : 0,
                'to'             => $isUnlimited ? $totalFiltered : min($offset + $perPage, $totalFiltered),
            ],
        ];
    }

    /**
     * Mengambil detail lengkap satu prestasi beserta peserta, dokumen, riwayat validasi, dan kesiapan pengajuan.
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function getDetail(int $id): ?array
    {
        $prestasi = $this->prestasiModel->find($id);
        if (!$prestasi) {
            return null;
        }

        $peserta = $this->db->table('taruna_peserta_prestasi tpp')
            ->select('tpp.id AS peserta_id, tpp.prestasi_id, tpp.nomor_taruna, tpp.peran,
                      mt.nama_lengkap, mt.status_terkini, ps.kode_prodi, ang.tahun_masuk AS angkatan, ang.kode_romawi AS angkatan_romawi')
            ->join('master_taruna mt', 'mt.nomor_taruna = tpp.nomor_taruna', 'inner')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = mt.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan ang', 'ang.id = pt.angkatan_id', 'left')
            ->where('tpp.prestasi_id', $id)
            ->orderBy("FIELD(tpp.peran, 'Ketua Tim', 'Peserta Utama', 'Anggota')", '', false)
            ->orderBy('tpp.id', 'ASC')
            ->get()
            ->getResultArray();

        $dokumentasi = [];
        if (!empty($prestasi['dokumentasi_json'])) {
            $decodedDok = json_decode((string) $prestasi['dokumentasi_json'], true);
            if (is_array($decodedDok)) {
                $dokumentasi = $decodedDok;
            }
        }

        $riwayat = [];
        if (!empty($prestasi['riwayat_validasi_json'])) {
            $decodedRiw = json_decode((string) $prestasi['riwayat_validasi_json'], true);
            if (is_array($decodedRiw)) {
                $riwayat = $decodedRiw;
            }
        }

        $infoLengkap = !empty($prestasi['nama_prestasi'])
            && !empty($prestasi['jenis_prestasi'])
            && !empty($prestasi['tingkat_prestasi'])
            && !empty($prestasi['peringkat'])
            && !empty($prestasi['penyelenggara'])
            && !empty($prestasi['tanggal_mulai']);

        $pesertaAda = count($peserta) >= 1;
        $buktiAda   = !empty($prestasi['bukti_berkas']) && !empty($prestasi['nomor_sertifikat']);

        return [
            'prestasi'    => $prestasi,
            'peserta'     => $peserta,
            'dokumentasi' => $dokumentasi,
            'riwayat'     => $riwayat,
            'checklist'   => [
                'info_lengkap' => $infoLengkap,
                'peserta_ada'  => $pesertaAda,
                'jumlah_peserta' => count($peserta),
                'bukti_ada'    => $buktiAda,
            ],
        ];
    }

    /**
     * Menyimpan Tahap 1 (Informasi Prestasi) dalam transaksi atomik.
     *
     * @param array<string, mixed> $input
     * @param int|null             $id
     * @return array{status: bool, message: string, id?: int}
     */
    public function saveInformasi(array $input, ?int $id = null): array
    {
        $namaPrestasi  = trim((string) ($input['nama_prestasi'] ?? ''));
        $penyelenggara = trim((string) ($input['penyelenggara'] ?? ''));
        $lokasi        = trim((string) ($input['lokasi_kegiatan'] ?? ''));
        if (mb_strlen($namaPrestasi) < 3) {
            return [
                'status'  => false,
                'message' => 'Nama prestasi minimal 3 karakter.',
            ];
        }
        if ($penyelenggara === '' || $lokasi === '') {
            return [
                'status'  => false,
                'message' => 'Penyelenggara dan lokasi kegiatan wajib diisi.',
            ];
        }

        $tglMulai   = trim((string) ($input['tanggal_mulai'] ?? ''));
        $tglSelesai = trim((string) ($input['tanggal_selesai'] ?? ''));
        if ($tglMulai !== '' && $tglSelesai !== '' && $tglSelesai < $tglMulai) {
            return [
                'status'  => false,
                'message' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            ];
        }

        $keikutsertaan = trim((string) ($input['jenis_keikutsertaan'] ?? 'individu'));
        $namaTim       = ($keikutsertaan === 'tim') ? trim((string) ($input['nama_tim'] ?? '')) : null;

        if ($keikutsertaan === 'tim' && ($namaTim === null || $namaTim === '')) {
            return [
                'status'  => false,
                'message' => 'Nama tim wajib diisi untuk keikutsertaan tim.',
            ];
        }

        $pelakuLabel = trim((string) ($input['diajukan_oleh'] ?? 'PIC Ketarunaan'));

        $payload = [
            'nama_prestasi'       => trim((string) ($input['nama_prestasi'] ?? '')),
            'jenis_prestasi'      => trim((string) ($input['jenis_prestasi'] ?? 'akademik')),
            'bidang_prestasi'     => trim((string) ($input['bidang_prestasi'] ?? 'Inovasi Keselamatan Jalan')),
            'tingkat_prestasi'    => trim((string) ($input['tingkat_prestasi'] ?? 'nasional')),
            'peringkat'           => trim((string) ($input['peringkat'] ?? '')),
            'penyelenggara'       => trim((string) ($input['penyelenggara'] ?? '')),
            'lokasi_kegiatan'     => trim((string) ($input['lokasi_kegiatan'] ?? '')),
            'tanggal_mulai'       => $tglMulai,
            'tanggal_selesai'     => $tglSelesai !== '' ? $tglSelesai : $tglMulai,
            'jenis_keikutsertaan' => $keikutsertaan,
            'nama_tim'            => $namaTim,
            'pembimbing'          => trim((string) ($input['pembimbing'] ?? 'Pembina Ketarunaan')),
            'unit_pemilik'        => trim((string) ($input['unit_pemilik'] ?? 'Ketarunaan')),
            'sumber_data'         => trim((string) ($input['sumber_data'] ?? 'Piagam Penghargaan')),
            'diajukan_oleh'       => $pelakuLabel,
        ];

        try {
            $this->db->transStart();

            if ($id !== null && $id > 0) {
                $existing = $this->prestasiModel->find($id);
                if (!$existing) {
                    $this->db->transRollback();
                    return ['status' => false, 'message' => 'Data prestasi tidak ditemukan.'];
                }

                if ($keikutsertaan === 'individu') {
                    $jumlahPeserta = (int) $this->pesertaModel->where('prestasi_id', $id)->countAllResults();
                    if ($jumlahPeserta > 1) {
                        $this->db->transRollback();
                        return [
                            'status'  => false,
                            'message' => 'Hapus peserta tambahan terlebih dahulu sebelum mengubah keikutsertaan menjadi Individu.',
                        ];
                    }
                }

                // Jika sebelumnya tervalidasi dan dilakukan koreksi, kembalikan ke draf untuk diajukan ulang
                $statusBaru = ($existing['status_validasi'] === 'tervalidasi') ? 'draf' : $existing['status_validasi'];
                $payload['status_validasi'] = $statusBaru;
                $payload['riwayat_validasi_json'] = $this->prependRiwayat(
                    $existing['riwayat_validasi_json'] ?? null,
                    $pelakuLabel,
                    ($existing['status_validasi'] === 'tervalidasi') ? 'Mengajukan koreksi' : 'Memperbarui draf',
                    $statusBaru,
                    'Pembaruan informasi prestasi.'
                );

                $this->prestasiModel->update($id, $payload);
                $savedId = $id;
            } else {
                $initialNomor = trim((string) ($input['initial_peserta_nomor'] ?? ''));
                if ($initialNomor !== '') {
                    $tarunaAda = $this->db->table('master_taruna')
                        ->select('nomor_taruna')
                        ->where('nomor_taruna', $initialNomor)
                        ->get()
                        ->getRowArray();
                    if (!$tarunaAda) {
                        $this->db->transRollback();
                        return ['status' => false, 'message' => 'Data taruna pengaju tidak ditemukan.'];
                    }
                }

                $payload['status_validasi']       = 'draf';
                $payload['riwayat_validasi_json'] = $this->prependRiwayat(
                    null,
                    $pelakuLabel,
                    'Menyimpan Draf',
                    'draf',
                    'Pencatatan awal prestasi.'
                );
                $this->prestasiModel->insert($payload);
                $savedId = (int) $this->prestasiModel->getInsertID();

                if ($initialNomor !== '') {
                    $initialPeran = ($keikutsertaan === 'individu') ? 'Peserta Utama' : 'Ketua Tim';
                    $this->pesertaModel->insert([
                        'prestasi_id'  => $savedId,
                        'nomor_taruna' => $initialNomor,
                        'peran'        => $initialPeran,
                        'created_at'   => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan informasi prestasi. Silakan coba lagi.'];
            }

            return [
                'status'  => true,
                'message' => 'Informasi prestasi berhasil disimpan.',
                'id'      => $savedId,
            ];
        } catch (\Throwable $e) {
            log_message('error', '[PrestasiService::saveInformasi] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menyimpan informasi prestasi. Silakan coba lagi.'];
        }
    }

    /**
     * Menambahkan peserta ke dalam prestasi dengan proteksi anti-duplikasi dan batas individu.
     *
     * @param int    $prestasiId
     * @param string $nomorTaruna
     * @param string $peran
     * @return array{status: bool, message: string}
     */
    public function addPeserta(int $prestasiId, string $nomorTaruna, string $peran = 'Anggota', ?string $pelakuLabel = null): array
    {
        $prestasi = $this->prestasiModel->find($prestasiId);
        if (!$prestasi) {
            return ['status' => false, 'message' => 'Data prestasi tidak ditemukan.'];
        }

        $nomorClean = trim($nomorTaruna);
        $tarunaExists = $this->db->table('master_taruna')
            ->select('nomor_taruna')
            ->where('nomor_taruna', $nomorClean)
            ->get()
            ->getRowArray();

        if (!$tarunaExists) {
            return ['status' => false, 'message' => 'Data taruna tidak ditemukan.'];
        }

        $existingSame = $this->pesertaModel
            ->where('prestasi_id', $prestasiId)
            ->where('nomor_taruna', $nomorClean)
            ->first();

        if ($existingSame) {
            return ['status' => false, 'message' => 'Nomor Taruna yang sama tidak dapat ditambahkan dua kali.'];
        }

        $currentCount = (int) $this->pesertaModel->where('prestasi_id', $prestasiId)->countAllResults();
        if ($prestasi['jenis_keikutsertaan'] === 'individu' && $currentCount >= 1) {
            return ['status' => false, 'message' => 'Prestasi individu hanya dapat memiliki 1 peserta. Ubah keikutsertaan ke Tim jika ingin menambah anggota.'];
        }

        $peranClean = in_array($peran, ['Ketua Tim', 'Anggota', 'Peserta Utama'], true)
            ? $peran
            : (($prestasi['jenis_keikutsertaan'] === 'individu') ? 'Peserta Utama' : 'Anggota');

        try {
            $this->db->transStart();
            $this->pesertaModel->insert([
                'prestasi_id'  => $prestasiId,
                'nomor_taruna' => $nomorClean,
                'peran'        => $peranClean,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
            if (($prestasi['status_validasi'] ?? '') === 'tervalidasi') {
                $actor = trim((string) ($pelakuLabel ?: 'PIC Ketarunaan'));
                $this->prestasiModel->update($prestasiId, [
                    'status_validasi'       => 'draf',
                    'riwayat_validasi_json' => $this->prependRiwayat(
                        $prestasi['riwayat_validasi_json'] ?? null,
                        $actor,
                        'Mengajukan koreksi',
                        'draf',
                        'Penambahan peserta prestasi.'
                    ),
                ]);
            }
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menambahkan peserta prestasi. Silakan coba lagi.'];
            }

            $msg = (($prestasi['status_validasi'] ?? '') === 'tervalidasi')
                ? 'Peserta ditambahkan. Status prestasi dikembalikan ke Draf untuk validasi ulang.'
                : 'Peserta berhasil ditambahkan ke prestasi.';

            return ['status' => true, 'message' => $msg];
        } catch (\Throwable $e) {
            log_message('error', '[PrestasiService::addPeserta] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menambahkan peserta prestasi. Silakan coba lagi.'];
        }
    }

    /**
     * Menghapus peserta dari prestasi dengan validasi kepemilikan prestasi_id.
     *
     * @param int         $prestasiId
     * @param int         $pesertaId
     * @param string|null $pelakuLabel
     * @return array{status: bool, message: string}
     */
    public function removePeserta(int $prestasiId, int $pesertaId, ?string $pelakuLabel = null): array
    {
        $row = $this->pesertaModel->find($pesertaId);
        if (!$row || (int) $row['prestasi_id'] !== $prestasiId) {
            return ['status' => false, 'message' => 'Data peserta tidak ditemukan pada prestasi ini.'];
        }

        $prestasi = $this->prestasiModel->find($prestasiId);

        try {
            $this->db->transStart();
            $this->pesertaModel->delete($pesertaId);
            if ($prestasi && ($prestasi['status_validasi'] ?? '') === 'tervalidasi') {
                $actor = trim((string) ($pelakuLabel ?: 'PIC Ketarunaan'));
                $this->prestasiModel->update($prestasiId, [
                    'status_validasi'       => 'draf',
                    'riwayat_validasi_json' => $this->prependRiwayat(
                        $prestasi['riwayat_validasi_json'] ?? null,
                        $actor,
                        'Mengajukan koreksi',
                        'draf',
                        'Penghapusan peserta prestasi.'
                    ),
                ]);
            }
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menghapus peserta. Silakan coba lagi.'];
            }

            $msg = ($prestasi && ($prestasi['status_validasi'] ?? '') === 'tervalidasi')
                ? 'Peserta dihapus. Status prestasi dikembalikan ke Draf untuk validasi ulang.'
                : 'Peserta berhasil dihapus dari daftar prestasi.';

            return ['status' => true, 'message' => $msg];
        } catch (\Throwable $e) {
            log_message('error', '[PrestasiService::removePeserta] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menghapus peserta. Silakan coba lagi.'];
        }
    }

    /**
     * Menyimpan Tahap 3 (Dokumen Bukti) dan mengajukan validasi bila diminta.
     *
     * @param int                  $prestasiId
     * @param array<string, mixed> $data
     * @return array{status: bool, message: string}
     */
    public function saveDokumenAndSubmit(int $prestasiId, array $data): array
    {
        $detail = $this->getDetail($prestasiId);
        if (!$detail) {
            return ['status' => false, 'message' => 'Data prestasi tidak ditemukan.'];
        }

        $prestasi    = $detail['prestasi'];
        $existingDok = $detail['dokumentasi'];

        $nomorSertifikat = trim((string) ($data['nomor_sertifikat'] ?? $prestasi['nomor_sertifikat'] ?? ''));

        if (array_key_exists('bukti_berkas', $data)) {
            $buktiVal    = trim((string) ($data['bukti_berkas'] ?? ''));
            $buktiBerkas = $buktiVal;
            $ukuranKb    = ($buktiVal !== '' && !ketarunaan_is_http_url($buktiVal, 2000))
                ? max(1, (int) ($data['ukuran_berkas_kb'] ?? $prestasi['ukuran_berkas_kb'] ?? 1))
                : 0;
        } else {
            $buktiBerkas = trim((string) ($prestasi['bukti_berkas'] ?? ''));
            $ukuranKb    = (int) ($prestasi['ukuran_berkas_kb'] ?? 0);
        }

        $newItems  = is_array($data['dokumentasi_baru'] ?? null) ? $data['dokumentasi_baru'] : [];
        $mergedDok = array_values(array_slice(array_unique(array_merge($existingDok, $newItems)), 0, 5));

        $aksiSubmit = trim((string) ($data['aksi_submit'] ?? 'draf'));

        if ($aksiSubmit === 'diajukan') {
            if (!$detail['checklist']['info_lengkap']) {
                return ['status' => false, 'message' => 'Lengkapi informasi prestasi sebelum mengajukan validasi.'];
            }
            if (count($detail['peserta']) < 1) {
                return ['status' => false, 'message' => 'Tambahkan minimal 1 peserta prestasi sebelum mengajukan validasi.'];
            }
            if ($nomorSertifikat === '' || $buktiBerkas === '') {
                return ['status' => false, 'message' => 'Nomor sertifikat dan tautan bukti utama wajib diisi sebelum mengajukan validasi.'];
            }
        }

        $statusBaru = ($aksiSubmit === 'diajukan') ? 'diajukan' : 'draf';
        $pelaku     = trim((string) ($data['pelaku'] ?? 'PIC Ketarunaan'));
        $aksiLabel  = ($aksiSubmit === 'diajukan')
            ? ($prestasi['status_validasi'] === 'perlu_perbaikan' ? 'Mengajukan ulang' : 'Mengajukan')
            : 'Menyimpan Draf';
        $catatan    = ($aksiSubmit === 'diajukan')
            ? ($prestasi['status_validasi'] === 'perlu_perbaikan' ? 'Bukti dan data telah diperbarui.' : 'Pengajuan validasi prestasi.')
            : 'Pembaruan dokumen bukti draf.';

        try {
            $this->db->transStart();
            $this->prestasiModel->update($prestasiId, [
                'nomor_sertifikat'      => $nomorSertifikat ?: null,
                'bukti_berkas'          => $buktiBerkas ?: null,
                'ukuran_berkas_kb'      => $ukuranKb,
                'dokumentasi_json'      => !empty($mergedDok) ? json_encode($mergedDok) : null,
                'status_validasi'       => $statusBaru,
                'catatan_validasi'      => ($statusBaru === 'diajukan') ? null : $prestasi['catatan_validasi'],
                'riwayat_validasi_json' => $this->prependRiwayat(
                    $prestasi['riwayat_validasi_json'] ?? null,
                    $pelaku,
                    $aksiLabel,
                    $statusBaru,
                    $catatan
                ),
            ]);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan dokumen prestasi. Silakan coba lagi.'];
            }

            return [
                'status'  => true,
                'message' => ($statusBaru === 'diajukan')
                    ? 'Data prestasi berhasil diajukan untuk validasi.'
                    : 'Draf dokumen prestasi berhasil disimpan.',
            ];
        } catch (\Throwable $e) {
            log_message('error', '[PrestasiService::saveDokumenAndSubmit] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menyimpan dokumen prestasi. Silakan coba lagi.'];
        }
    }

    /**
     * Menghapus satu file foto dokumentasi dari daftar dokumentasi_json.
     *
     * @param int    $prestasiId
     * @param string $filename
     * @return array{status: bool, message: string}
     */
    public function removeDokumentasi(int $prestasiId, string $filename, ?string $pelakuLabel = null): array
    {
        $prestasi = $this->prestasiModel->find($prestasiId);
        if (!$prestasi) {
            return ['status' => false, 'message' => 'Data prestasi tidak ditemukan.'];
        }

        $rawTarget = trim($filename);
        $isUrl     = ketarunaan_is_http_url($rawTarget, 2000);
        $target    = $isUrl ? $rawTarget : basename($rawTarget);
        $list      = json_decode((string) ($prestasi['dokumentasi_json'] ?? '[]'), true);

        if (!is_array($list) || !in_array($target, $list, true)) {
            return ['status' => false, 'message' => 'Dokumentasi tidak ditemukan.'];
        }

        $filtered = array_values(array_filter($list, static fn ($f) => (string) $f !== $target));

        $updatePayload = [
            'dokumentasi_json' => !empty($filtered) ? json_encode($filtered) : null,
        ];
        if (($prestasi['status_validasi'] ?? '') === 'tervalidasi') {
            $actor = trim((string) ($pelakuLabel ?: 'PIC Ketarunaan'));
            $updatePayload['status_validasi']       = 'draf';
            $updatePayload['riwayat_validasi_json'] = $this->prependRiwayat(
                $prestasi['riwayat_validasi_json'] ?? null,
                $actor,
                'Mengajukan koreksi',
                'draf',
                'Penghapusan dokumentasi prestasi.'
            );
        }

        try {
            $this->db->transStart();
            $this->prestasiModel->update($prestasiId, $updatePayload);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menghapus dokumentasi. Silakan coba lagi.'];
            }

            if (!$isUrl) {
                $filePath = FCPATH . 'assets/uploads/ketarunaan/prestasi/dokumentasi/' . $target;
                if (is_file($filePath)) {
                    @unlink($filePath);
                }
            }

            $msg = (($prestasi['status_validasi'] ?? '') === 'tervalidasi')
                ? 'Dokumentasi dihapus. Status prestasi dikembalikan ke Draf untuk validasi ulang.'
                : 'Dokumentasi berhasil dihapus.';

            return ['status' => true, 'message' => $msg];
        } catch (\Throwable $e) {
            log_message('error', '[PrestasiService::removeDokumentasi] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menghapus dokumentasi. Silakan coba lagi.'];
        }
    }

    /**
     * Memproses pengesahan atau pengembalian prestasi oleh validator.
     *
     * @param int         $prestasiId
     * @param string      $keputusan
     * @param string|null $catatan
     * @param string      $validatorLabel
     * @return array{status: bool, message: string}
     */
    public function processValidation(int $prestasiId, string $keputusan, ?string $catatan, string $validatorLabel): array
    {
        if (!in_array($keputusan, ['tervalidasi', 'perlu_perbaikan'], true)) {
            return ['status' => false, 'message' => 'Keputusan validasi tidak valid.'];
        }

        $prestasi = $this->prestasiModel->find($prestasiId);
        if (!$prestasi) {
            return ['status' => false, 'message' => 'Data prestasi tidak ditemukan.'];
        }

        $currentStatus = (string) ($prestasi['status_validasi'] ?? '');
        if ($currentStatus === 'tervalidasi') {
            return ['status' => false, 'message' => 'Prestasi ini sudah tervalidasi.'];
        }
        if ($currentStatus === 'draf') {
            return ['status' => false, 'message' => 'Prestasi berstatus draf belum diajukan untuk validasi.'];
        }

        $isValid      = ($keputusan === 'tervalidasi');
        $statusBaru   = $isValid ? 'tervalidasi' : 'perlu_perbaikan';
        $aksiLabel    = $isValid ? 'Menyetujui' : 'Mengembalikan';
        $catatanClean = $isValid
            ? (trim((string) $catatan) ?: 'Data dan bukti sesuai.')
            : trim((string) $catatan);

        try {
            $this->db->transStart();
            $this->prestasiModel->update($prestasiId, [
                'status_validasi'       => $statusBaru,
                'catatan_validasi'      => $isValid ? null : $catatanClean,
                'divalidasi_oleh'       => $validatorLabel,
                'tanggal_validasi'      => date('Y-m-d'),
                'riwayat_validasi_json' => $this->prependRiwayat(
                    $prestasi['riwayat_validasi_json'] ?? null,
                    $validatorLabel,
                    $aksiLabel,
                    $statusBaru,
                    $catatanClean
                ),
            ]);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal memperbarui status validasi. Silakan coba lagi.'];
            }

            return [
                'status'  => true,
                'message' => $isValid
                    ? 'Prestasi berhasil divalidasi.'
                    : 'Prestasi dikembalikan untuk perbaikan.',
            ];
        } catch (\Throwable $e) {
            log_message('error', '[PrestasiService::processValidation] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal memperbarui status validasi. Silakan coba lagi.'];
        }
    }

    /**
     * Menghapus prestasi yang masih berstatus draf atau perlu perbaikan.
     *
     * @param int $prestasiId
     * @return array{status: bool, message: string}
     */
    public function deleteDraftPrestasi(int $prestasiId): array
    {
        $prestasi = $this->prestasiModel->find($prestasiId);
        if (!$prestasi) {
            return ['status' => false, 'message' => 'Data prestasi tidak ditemukan.'];
        }

        if (!in_array((string) ($prestasi['status_validasi'] ?? ''), ['draf', 'perlu_perbaikan'], true)) {
            return ['status' => false, 'message' => 'Hanya draf atau pengajuan yang perlu perbaikan yang dapat dihapus.'];
        }

        try {
            $this->db->transStart();
            $this->pesertaModel->where('prestasi_id', $prestasiId)->delete();
            $this->prestasiModel->delete($prestasiId);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menghapus data prestasi. Silakan coba lagi.'];
            }

            return ['status' => true, 'message' => 'Data prestasi berhasil dihapus.'];
        } catch (\Throwable $e) {
            log_message('error', '[PrestasiService::deleteDraftPrestasi] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menghapus data prestasi. Silakan coba lagi.'];
        }
    }

    /**
     * Menambahkan entri baru ke awal log riwayat validasi JSON (disimpan terurut terbaru di atas).
     */
    private function prependRiwayat(?string $existingJson, string $pelaku, string $aksi, string $status, string $catatan): string
    {
        $list = $existingJson ? json_decode($existingJson, true) : [];
        if (!is_array($list)) {
            $list = [];
        }

        array_unshift($list, [
            'tanggal' => date('Y-m-d'),
            'waktu'   => date('Y-m-d H:i'),
            'pelaku'  => $pelaku,
            'aksi'    => $aksi,
            'status'  => $status,
            'catatan' => $catatan,
        ]);

        return json_encode($list, JSON_UNESCAPED_UNICODE) ?: '[]';
    }
}
