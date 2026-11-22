<?php

namespace App\Libraries\Ketarunaan;

use App\Models\Ketarunaan\BeasiswaHasilPeringkatModel;
use App\Models\Ketarunaan\BeasiswaPemeringkatanModel;
use App\Models\Ketarunaan\PenerimaBeasiswaModel;
use App\Models\Ketarunaan\ProgramBeasiswaModel;
use App\Models\Ketarunaan\RefWilayah3tpModel;
use App\Models\Ketarunaan\TarunaNilaiIpModel;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

/**
 * Service terpusat Modul 5: Beasiswa Taruna (ECC 2.0).
 * Mengelola 4 jenis beasiswa resmi, seleksi Apresiasi berbasis IP semester per kelas,
 * klaim terverifikasi (Prestasi, Daerah 3TP, Tidak Mampu), serta pemisahan status
 * verifikasi, penetapan keputusan, dan validasi.
 */
class BeasiswaService
{
    protected BaseConnection $db;
    protected ProgramBeasiswaModel $programModel;
    protected PenerimaBeasiswaModel $penerimaModel;
    protected TarunaNilaiIpModel $nilaiIpModel;
    protected BeasiswaPemeringkatanModel $pemeringkatanModel;
    protected BeasiswaHasilPeringkatModel $hasilPeringkatModel;
    protected RefWilayah3tpModel $wilayah3tpModel;

    public function __construct(
        ?BaseConnection $db = null,
        ?ProgramBeasiswaModel $programModel = null,
        ?PenerimaBeasiswaModel $penerimaModel = null,
        ?TarunaNilaiIpModel $nilaiIpModel = null,
        ?BeasiswaPemeringkatanModel $pemeringkatanModel = null,
        ?BeasiswaHasilPeringkatModel $hasilPeringkatModel = null,
        ?RefWilayah3tpModel $wilayah3tpModel = null
    ) {
        $this->db                  = $db ?? Database::connect();
        $this->programModel        = $programModel ?? new ProgramBeasiswaModel();
        $this->penerimaModel       = $penerimaModel ?? new PenerimaBeasiswaModel();
        $this->nilaiIpModel        = $nilaiIpModel ?? new TarunaNilaiIpModel();
        $this->pemeringkatanModel  = $pemeringkatanModel ?? new BeasiswaPemeringkatanModel();
        $this->hasilPeringkatModel = $hasilPeringkatModel ?? new BeasiswaHasilPeringkatModel();
        $this->wilayah3tpModel     = $wilayah3tpModel ?? new RefWilayah3tpModel();
        helper('ketarunaan');
    }

    /**
     * Mengambil daftar referensi program, periode, prodi, kelas, taruna, dan katalog.
     *
     * @param bool $includeTaruna
     * @param bool $includeWilayah
     * @return array<string, mixed>
     */
    public function getReferenceOptions(bool $includeTaruna = true, bool $includeWilayah = true): array
    {
        ketarunaan_ensure_kalender_terkini($this->db);

        $tahunAkademik = $this->db->table('ref_tahun_akademik')
            ->select('id, kode_tahun, tahun, semester, is_aktif')
            ->orderBy('tahun', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $prodi = $this->db->table('ref_program_studi')
            ->select('id, kode_prodi, nama_prodi, jenjang')
            ->where('is_aktif', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $kelas = $this->db->table('ref_kelas kls')
            ->select('kls.id, kls.program_studi_id, kls.angkatan_id, kls.tingkat, kls.nama_kelas, ps.kode_prodi, ps.nama_prodi')
            ->join('ref_program_studi ps', 'ps.id = kls.program_studi_id', 'left')
            ->where('kls.is_aktif', 1)
            ->orderBy('ps.id', 'ASC')
            ->orderBy('kls.tingkat', 'ASC')
            ->orderBy('kls.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        $programs = $this->db->table('taruna_program_beasiswa prog')
            ->select('prog.*, ta.kode_tahun AS tahun_akademik_label, pn.kode_tahun AS periode_nilai_label')
            ->join('ref_tahun_akademik ta', 'ta.id = prog.tahun_akademik_id', 'left')
            ->join('ref_tahun_akademik pn', 'pn.id = prog.periode_nilai_id', 'left')
            ->orderBy('prog.id', 'ASC')
            ->get()
            ->getResultArray();

        $taruna = [];
        if ($includeTaruna) {
            $taruna = $this->db->table('master_taruna mt')
                ->select('mt.nomor_taruna, mt.nama_lengkap, mt.status_terkini, mt.alamat_domisili,
                          pt.program_studi_id, pt.kelas_id, pt.tingkat, pt.semester_aktif,
                          ps.kode_prodi, ps.nama_prodi, kls.nama_kelas')
                ->join('pendidikan_taruna pt', 'pt.nomor_taruna = mt.nomor_taruna', 'left')
                ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
                ->join('ref_kelas kls', 'kls.id = pt.kelas_id', 'left')
                ->orderBy('mt.nomor_taruna', 'ASC')
                ->get()
                ->getResultArray();
        }

        $wilayah3tp = [];
        if ($includeWilayah) {
            $wilayah3tp = $this->wilayah3tpModel
                ->where('is_aktif', 1)
                ->orderBy('provinsi', 'ASC')
                ->orderBy('kabupaten_kota', 'ASC')
                ->findAll();
        }

        $aktifTahunId = null;
        foreach ($tahunAkademik as $ta) {
            if ((int) ($ta['is_aktif'] ?? 0) === 1) {
                $aktifTahunId = (int) $ta['id'];
                break;
            }
        }
        if ($aktifTahunId === null && !empty($tahunAkademik)) {
            $aktifTahunId = (int) $tahunAkademik[0]['id'];
        }

        return [
            'tahun_akademik'          => $tahunAkademik,
            'aktif_tahun_akademik_id' => $aktifTahunId,
            'prodi'                   => $prodi,
            'kelas'                   => $kelas,
            'programs'                => $programs,
            'taruna'                  => $taruna,
            'wilayah_3tp'             => $wilayah3tp,
            'jenis_beasiswa'          => ketarunaan_jenis_beasiswa_catalog(),
            'aturan_seri'             => ketarunaan_aturan_seri_catalog(),
            'dokumen_3tp_options'     => [
                'Kartu Keluarga',
                'Surat Keterangan Domisili',
                'KTP Daerah Asal',
                'Surat Keterangan Kepala Desa / Lurah',
            ],
            'dokumen_ekonomi_options' => [
                'SKTM Kelurahan',
                'Kartu Indonesia Pintar (KIP)',
                'Surat Keterangan Penghasilan Orang Tua',
                'Kartu Keluarga',
                'Bukti Kondisi Rumah / Listrik',
            ],
        ];
    }

    /**
     * Mengambil data Ringkasan Beasiswa untuk tab utama (/ketarunaan/beasiswa).
     *
     * @param int|null    $tahunAkademikId
     * @param string|null $onlyNomorTaruna
     * @return array<string, mixed>
     */
    public function getRingkasanData(?int $tahunAkademikId = null, ?string $onlyNomorTaruna = null): array
    {
        $catalog = ketarunaan_jenis_beasiswa_catalog();

        $progBuilder = $this->db->table('taruna_program_beasiswa prog')
            ->select('prog.*, ta.kode_tahun AS tahun_akademik_label, pn.kode_tahun AS periode_nilai_label')
            ->join('ref_tahun_akademik ta', 'ta.id = prog.tahun_akademik_id', 'left')
            ->join('ref_tahun_akademik pn', 'pn.id = prog.periode_nilai_id', 'left')
            ->orderBy('prog.id', 'ASC');

        if ($tahunAkademikId !== null && $tahunAkademikId > 0) {
            $progBuilder->where('prog.tahun_akademik_id', $tahunAkademikId);
        }
        $programRows = $progBuilder->get()->getResultArray();

        $penerimaBuilder = $this->db->table('taruna_penerima_beasiswa tpb')
            ->select('tpb.id, tpb.program_beasiswa_id, tpb.nomor_taruna, tpb.hasil_verifikasi,
                      tpb.status_penetapan, tpb.status_validasi, prog.jenis_beasiswa, prog.nominal, prog.nama_program')
            ->join('taruna_program_beasiswa prog', 'prog.id = tpb.program_beasiswa_id', 'inner');

        if ($tahunAkademikId !== null && $tahunAkademikId > 0) {
            $penerimaBuilder->where('prog.tahun_akademik_id', $tahunAkademikId);
        }
        $allScopeRows = $penerimaBuilder->get()->getResultArray();

        $rows = $allScopeRows;
        $myProgramMap = [];
        if ($onlyNomorTaruna !== null && $onlyNomorTaruna !== '') {
            $rows = array_values(array_filter($allScopeRows, static function (array $r) use ($onlyNomorTaruna): bool {
                return strcasecmp((string) ($r['nomor_taruna'] ?? ''), $onlyNomorTaruna) === 0;
            }));
            foreach ($rows as $mr) {
                $pid = (int) ($mr['program_beasiswa_id'] ?? 0);
                if (!isset($myProgramMap[$pid])) {
                    $myProgramMap[$pid] = $mr;
                }
            }
        }

        // Hitung keterisian kuota agregat program (angka saja, tanpa data pribadi taruna lain)
        $programQuotaStats = [];
        foreach ($allScopeRows as $ar) {
            $pid   = (int) ($ar['program_beasiswa_id'] ?? 0);
            $verif = (string) ($ar['hasil_verifikasi'] ?? 'belum_diperiksa');
            $tetap = (string) ($ar['status_penetapan'] ?? 'kandidat');
            $valid = (string) ($ar['status_validasi'] ?? 'draf');
            if (!isset($programQuotaStats[$pid])) {
                $programQuotaStats[$pid] = ['ditetapkan' => 0, 'kandidat' => 0];
            }
            if ($tetap === 'ditetapkan' && $valid === 'tervalidasi') {
                $programQuotaStats[$pid]['ditetapkan']++;
            } elseif ($tetap === 'kandidat' && $verif === 'memenuhi') {
                $programQuotaStats[$pid]['kandidat']++;
            }
        }

        $breakdown = [];
        foreach ($catalog as $kode => $meta) {
            $breakdown[$kode] = [
                'kode'              => $kode,
                'meta'              => $meta,
                'program_count'     => 0,
                'kuota_total'       => 0,
                'nominal_contoh'    => 0,
                'ditetapkan_count'  => 0,
                'penerima_unik_map' => [],
                'lolos_menunggu_sk' => 0,
                'antrean_count'     => 0,
                'perlu_perbaikan'   => 0,
            ];
        }

        $perProgramStats = [];
        $programAktif    = 0;
        foreach ($programRows as $prog) {
            $pid   = (int) $prog['id'];
            $jenis = (string) ($prog['jenis_beasiswa'] ?? 'apresiasi');
            if (($prog['status_program'] ?? '') === 'berjalan') {
                $programAktif++;
            }
            $perProgramStats[$pid] = [
                'ditetapkan' => 0,
                'menunggu'   => 0,
                'kandidat'   => 0,
            ];
            if (isset($breakdown[$jenis])) {
                $breakdown[$jenis]['program_count']++;
                $breakdown[$jenis]['kuota_total'] += (int) ($prog['kuota'] ?? 0);
                if ($breakdown[$jenis]['nominal_contoh'] === 0 && (int) ($prog['nominal'] ?? 0) > 0) {
                    $breakdown[$jenis]['nominal_contoh'] = (int) $prog['nominal'];
                }
            }
        }

        $ditetapkanTotal      = 0;
        $tarunaUnikMap        = [];
        $menungguVerif        = 0;
        $belumDiperiksa       = 0;
        $perluPerbaikan       = 0;
        $lolosMenungguSk      = 0;
        $nominalTersalurkan   = 0;

        foreach ($rows as $row) {
            $pid   = (int) $row['program_beasiswa_id'];
            $jenis = (string) $row['jenis_beasiswa'];
            $nim   = (string) $row['nomor_taruna'];
            $verif = (string) ($row['hasil_verifikasi'] ?? 'belum_diperiksa');
            $tetap = (string) ($row['status_penetapan'] ?? 'kandidat');
            $valid = (string) ($row['status_validasi'] ?? 'draf');

            $isResmi = ($tetap === 'ditetapkan' && $valid === 'tervalidasi');
            if ($isResmi) {
                $ditetapkanTotal++;
                $tarunaUnikMap[$nim] = true;
                $nominalTersalurkan += (int) ($row['nominal'] ?? 0);
                if (isset($perProgramStats[$pid])) {
                    $perProgramStats[$pid]['ditetapkan']++;
                }
                if (isset($breakdown[$jenis])) {
                    $breakdown[$jenis]['ditetapkan_count']++;
                    $breakdown[$jenis]['penerima_unik_map'][$nim] = true;
                }
                continue;
            }

            if ($tetap === 'kandidat' && $verif === 'memenuhi') {
                $lolosMenungguSk++;
                if (isset($perProgramStats[$pid])) {
                    $perProgramStats[$pid]['kandidat']++;
                }
                if (isset($breakdown[$jenis])) {
                    $breakdown[$jenis]['lolos_menunggu_sk']++;
                }
            }

            if ($tetap !== 'dibatalkan' && $valid !== 'draf' && in_array($verif, ['belum_diperiksa', 'perlu_perbaikan'], true)) {
                $menungguVerif++;
                if ($verif === 'belum_diperiksa') {
                    $belumDiperiksa++;
                } else {
                    $perluPerbaikan++;
                }
                if (isset($perProgramStats[$pid])) {
                    $perProgramStats[$pid]['menunggu']++;
                }
                if (isset($breakdown[$jenis])) {
                    $breakdown[$jenis]['antrean_count']++;
                    if ($verif === 'perlu_perbaikan') {
                        $breakdown[$jenis]['perlu_perbaikan']++;
                    }
                }
            }
        }

        foreach ($breakdown as $k => $item) {
            $breakdown[$k]['penerima_unik'] = count($item['penerima_unik_map']);
            unset($breakdown[$k]['penerima_unik_map']);
        }

        foreach ($programRows as $idx => $prog) {
            $pid = (int) $prog['id'];
            $programRows[$idx]['ditetapkan_count']    = $programQuotaStats[$pid]['ditetapkan'] ?? ($perProgramStats[$pid]['ditetapkan'] ?? 0);
            $programRows[$idx]['menunggu_count']      = $perProgramStats[$pid]['menunggu'] ?? 0;
            $programRows[$idx]['kandidat_siap_count'] = $programQuotaStats[$pid]['kandidat'] ?? ($perProgramStats[$pid]['kandidat'] ?? 0);
            $programRows[$idx]['my_submission']       = $myProgramMap[$pid] ?? null;
        }

        $recentAntrean = $this->db->table('taruna_penerima_beasiswa tpb')
            ->select('tpb.id, tpb.nomor_taruna, tpb.sumber_klaim, tpb.tanggal_pengajuan,
                      tpb.hasil_verifikasi, tpb.status_penetapan, tpb.status_validasi,
                      mt.nama_lengkap, ps.kode_prodi, prog.nama_program, prog.jenis_beasiswa')
            ->join('taruna_program_beasiswa prog', 'prog.id = tpb.program_beasiswa_id', 'inner')
            ->join('master_taruna mt', 'mt.nomor_taruna = tpb.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = tpb.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->where('tpb.status_penetapan', 'kandidat')
            ->orderBy('tpb.id', 'DESC')
            ->limit(6);

        if ($onlyNomorTaruna !== null && $onlyNomorTaruna !== '') {
            $recentAntrean->where('tpb.nomor_taruna', $onlyNomorTaruna);
        }

        return [
            'summary' => [
                'program_aktif'         => $programAktif,
                'total_program'         => count($programRows),
                'penetapan_disahkan'    => $ditetapkanTotal,
                'penerima_unik'         => count($tarunaUnikMap),
                'menunggu_verifikasi'   => $menungguVerif,
                'belum_diperiksa'       => $belumDiperiksa,
                'perlu_perbaikan'       => $perluPerbaikan,
                'lolos_menunggu_sk'     => $lolosMenungguSk,
                'nominal_tersalurkan'   => $nominalTersalurkan,
            ],
            'breakdown_jenis'  => $breakdown,
            'program_rows'     => $programRows,
            'recent_kandidat'  => $recentAntrean->get()->getResultArray(),
        ];
    }

    /**
     * Mengambil daftar program beasiswa dengan filter opsional.
     *
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function listPrograms(array $filters = []): array
    {
        $builder = $this->db->table('taruna_program_beasiswa prog')
            ->select('prog.*, ta.kode_tahun AS tahun_akademik_label, pn.kode_tahun AS periode_nilai_label')
            ->join('ref_tahun_akademik ta', 'ta.id = prog.tahun_akademik_id', 'left')
            ->join('ref_tahun_akademik pn', 'pn.id = prog.periode_nilai_id', 'left');

        $jenis  = trim((string) ($filters['jenis_beasiswa'] ?? 'all'));
        $status = trim((string) ($filters['status_program'] ?? 'all'));
        $tahun  = trim((string) ($filters['tahun_akademik_id'] ?? 'all'));
        $q      = trim((string) ($filters['q'] ?? ''));

        if ($jenis !== '' && $jenis !== 'all') {
            $builder->where('prog.jenis_beasiswa', $jenis);
        }
        if ($status !== '' && $status !== 'all') {
            $builder->where('prog.status_program', $status);
        }
        if ($tahun !== '' && $tahun !== 'all' && (int) $tahun > 0) {
            $builder->where('prog.tahun_akademik_id', (int) $tahun);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('prog.nama_program', $q)
                ->orLike('prog.pemberi', $q)
                ->groupEnd();
        }

        $sortBy    = trim((string) ($filters['sort_by'] ?? ''));
        $sortOrder = strtolower(trim((string) ($filters['sort_order'] ?? 'asc'))) === 'desc' ? 'DESC' : 'ASC';
        $sortMap   = [
            'nama_program'   => 'prog.nama_program',
            'jenis_beasiswa' => 'prog.jenis_beasiswa',
            'pemberi'        => 'prog.pemberi',
            'kuota'          => 'prog.kuota',
            'nominal'        => 'prog.nominal',
            'status_program' => 'prog.status_program',
        ];
        if (isset($sortMap[$sortBy])) {
            $builder->orderBy($sortMap[$sortBy], $sortOrder)->orderBy('prog.id', 'ASC');
        } else {
            $builder->orderBy('prog.id', 'ASC');
        }

        $programs = $builder->get()->getResultArray();
        if (empty($programs)) {
            return [];
        }

        $counts = $this->db->table('taruna_penerima_beasiswa')
            ->select("program_beasiswa_id,
                SUM(CASE WHEN status_penetapan = 'ditetapkan' AND status_validasi = 'tervalidasi' THEN 1 ELSE 0 END) AS ditetapkan_count,
                SUM(CASE WHEN status_penetapan = 'kandidat' AND hasil_verifikasi = 'memenuhi' THEN 1 ELSE 0 END) AS siap_sk_count,
                SUM(CASE WHEN status_penetapan != 'dibatalkan' AND hasil_verifikasi IN ('belum_diperiksa','perlu_perbaikan') AND status_validasi != 'draf' THEN 1 ELSE 0 END) AS verif_count")
            ->groupBy('program_beasiswa_id')
            ->get()
            ->getResultArray();

        $countMap = [];
        foreach ($counts as $c) {
            $countMap[(int) $c['program_beasiswa_id']] = $c;
        }

        foreach ($programs as $idx => $p) {
            $pid = (int) $p['id'];
            $programs[$idx]['ditetapkan_count'] = (int) ($countMap[$pid]['ditetapkan_count'] ?? 0);
            $programs[$idx]['siap_sk_count']    = (int) ($countMap[$pid]['siap_sk_count'] ?? 0);
            $programs[$idx]['verif_count']      = (int) ($countMap[$pid]['verif_count'] ?? 0);
        }

        if (($filters['sort_by'] ?? '') === 'ditetapkan_count') {
            $desc = strtolower(trim((string) ($filters['sort_order'] ?? 'asc'))) === 'desc';
            usort($programs, static function (array $a, array $b) use ($desc): int {
                $cmp = ($a['ditetapkan_count'] ?? 0) <=> ($b['ditetapkan_count'] ?? 0);
                return $desc ? -$cmp : $cmp;
            });
        }

        return $programs;
    }

    /**
     * Mengambil satu data program beasiswa berdasarkan ID.
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function getProgram(int $id): ?array
    {
        $row = $this->db->table('taruna_program_beasiswa prog')
            ->select('prog.*, ta.kode_tahun AS tahun_akademik_label, pn.kode_tahun AS periode_nilai_label')
            ->join('ref_tahun_akademik ta', 'ta.id = prog.tahun_akademik_id', 'left')
            ->join('ref_tahun_akademik pn', 'pn.id = prog.periode_nilai_id', 'left')
            ->where('prog.id', $id)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Menyimpan atau memperbarui program beasiswa serta pengaturan seleksi Apresiasi.
     *
     * @param array<string, mixed> $input
     * @param int|null             $id
     * @return array<string, mixed>
     */
    public function saveProgram(array $input, ?int $id = null): array
    {
        $namaProgram = trim((string) ($input['nama_program'] ?? ''));
        $jenis       = strtolower(trim((string) ($input['jenis_beasiswa'] ?? '')));
        $pemberi     = trim((string) ($input['pemberi'] ?? ''));
        $tahunId     = (int) ($input['tahun_akademik_id'] ?? 0);
        $statusProg  = strtolower(trim((string) ($input['status_program'] ?? 'dibuka')));
        $kuota       = max(0, (int) ($input['kuota'] ?? 0));
        $nominal     = $this->sanitizePositiveInt($input['nominal'] ?? 0);

        if ($namaProgram === '' || $pemberi === '') {
            return ['status' => false, 'message' => 'Nama program dan sumber dana atau pemberi wajib diisi.'];
        }
        if (!in_array($jenis, ['apresiasi', 'prestasi', 'daerah_3tp', 'tidak_mampu'], true)) {
            return ['status' => false, 'message' => 'Jenis beasiswa resmi tidak dikenali.'];
        }
        if (!in_array($statusProg, ['dibuka', 'seleksi', 'berjalan', 'selesai'], true)) {
            $statusProg = 'dibuka';
        }

        $tahunRow = $tahunId > 0
            ? $this->db->table('ref_tahun_akademik')->where('id', $tahunId)->get()->getRowArray()
            : null;
        $tahunAngka = $tahunRow ? (int) $tahunRow['tahun'] : (int) ($input['tahun'] ?? date('Y'));

        $aturanSeri = strtolower(trim((string) ($input['aturan_seri'] ?? 'belum_ditetapkan')));
        if (!array_key_exists($aturanSeri, ketarunaan_aturan_seri_catalog())) {
            $aturanSeri = 'belum_ditetapkan';
        }

        $periodeMulai   = trim((string) ($input['periode_mulai'] ?? ''));
        $periodeSelesai = trim((string) ($input['periode_selesai'] ?? ''));
        if ($periodeMulai !== '' && !$this->isValidDateYmd($periodeMulai)) {
            return ['status' => false, 'message' => 'Format tanggal mulai periode tidak valid.'];
        }
        if ($periodeSelesai !== '' && !$this->isValidDateYmd($periodeSelesai)) {
            return ['status' => false, 'message' => 'Format tanggal selesai periode tidak valid.'];
        }
        if ($periodeMulai !== '' && $periodeSelesai !== '' && $periodeSelesai < $periodeMulai) {
            return ['status' => false, 'message' => 'Tanggal selesai periode tidak boleh lebih awal dari tanggal mulai.'];
        }

        $periodeNilaiId = (int) ($input['periode_nilai_id'] ?? 0);
        $jumlahPeringkat = max(1, min(15, (int) ($input['jumlah_peringkat'] ?? ($jenis === 'apresiasi' ? 5 : 0))));

        $payload = [
            'nama_program'      => $namaProgram,
            'jenis_beasiswa'    => $jenis,
            'pemberi'           => $pemberi,
            'tahun'             => $tahunAngka,
            'tahun_akademik_id' => $tahunId > 0 ? $tahunId : null,
            'periode_mulai'     => $periodeMulai !== '' ? $periodeMulai : null,
            'periode_selesai'   => $periodeSelesai !== '' ? $periodeSelesai : null,
            'kuota'             => $kuota,
            'nominal'           => $nominal,
            'persyaratan'       => trim((string) ($input['persyaratan'] ?? '')),
            'penanggung_jawab'  => trim((string) ($input['penanggung_jawab'] ?? 'Unit Ketarunaan')) ?: 'Unit Ketarunaan',
            'dasar_penilaian'   => $jenis === 'apresiasi' ? 'ip_semester' : ($jenis === 'prestasi' ? 'klaim_prestasi' : ($jenis === 'daerah_3tp' ? 'domisili_3tp' : 'klaim_ekonomi')),
            'periode_nilai_id'  => $jenis === 'apresiasi' && $periodeNilaiId > 0 ? $periodeNilaiId : null,
            'basis_kelas'       => $jenis === 'apresiasi' ? 'kelas_periode_penilaian' : 'individu',
            'jumlah_peringkat'  => $jenis === 'apresiasi' ? $jumlahPeringkat : 0,
            'aturan_seri'       => $jenis === 'apresiasi' ? $aturanSeri : 'belum_ditetapkan',
            'status_program'    => $statusProg,
        ];

        $this->db->transStart();
        try {
            $previousAturan = null;
            if ($id !== null && $id > 0) {
                $existing = $this->programModel->find($id);
                if (!$existing) {
                    $this->db->transRollback();
                    return ['status' => false, 'message' => 'Program beasiswa tidak ditemukan.'];
                }
                $previousAturan = $existing['aturan_seri'] ?? 'belum_ditetapkan';
                $this->programModel->update($id, $payload);
                $savedId = $id;
            } else {
                $this->programModel->insert($payload);
                $savedId = (int) $this->programModel->getInsertID();
            }
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan pengaturan program beasiswa.'];
            }

            $msg = 'Program beasiswa berhasil disimpan.';
            if ($jenis === 'apresiasi' && $previousAturan === 'belum_ditetapkan' && $aturanSeri !== 'belum_ditetapkan') {
                $msg = 'Program beasiswa disimpan dan aturan pemecah seri ditetapkan. Silakan hitung ulang pemeringkatan kelas yang sempat ditahan.';
            }

            return ['status' => true, 'id' => $savedId, 'message' => $msg];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Gagal menyimpan data program beasiswa.'];
        }
    }

    /**
     * Mengambil daftar prestasi tervalidasi milik seorang taruna untuk picker Klaim Beasiswa Prestasi.
     *
     * @param string $nomorTaruna
     * @return array<int, array<string, mixed>>
     */
    public function getTarunaValidatedPrestasi(string $nomorTaruna): array
    {
        $nim = trim($nomorTaruna);
        if ($nim === '') {
            return [];
        }

        return $this->db->table('taruna_prestasi tp')
            ->select('tp.id, tp.nama_prestasi, tp.jenis_prestasi, tp.bidang_prestasi,
                      tp.tingkat_prestasi, tp.peringkat, tp.penyelenggara,
                      tp.tanggal_mulai, tp.bukti_berkas, tp.dokumentasi_json, tpp.peran')
            ->join('taruna_peserta_prestasi tpp', 'tpp.prestasi_id = tp.id', 'inner')
            ->where('tpp.nomor_taruna', $nim)
            ->where('tp.status_validasi', 'tervalidasi')
            ->orderBy('tp.tanggal_mulai', 'DESC')
            ->orderBy('tp.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Mengambil data layar Seleksi Apresiasi (IP Semester per kelas, status versi pemeringkatan & seri peringkat 5).
     *
     * @param int|null $programId
     * @param int|null $tahunAkademikId
     * @param int|null $prodiId
     * @param int|null $kelasId
     * @return array<string, mixed>
     */
    public function getApresiasiData(
        ?int $programId = null,
        ?int $tahunAkademikId = null,
        ?int $prodiId = null,
        ?int $kelasId = null
    ): array {
        $apresiasiPrograms = $this->db->table('taruna_program_beasiswa')
            ->where('jenis_beasiswa', 'apresiasi')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $selectedProgram = null;
        if ($programId !== null && $programId > 0) {
            foreach ($apresiasiPrograms as $ap) {
                if ((int) $ap['id'] === $programId) {
                    $selectedProgram = $ap;
                    break;
                }
            }
        }
        if ($selectedProgram === null && !empty($apresiasiPrograms)) {
            $selectedProgram = $apresiasiPrograms[0];
        }

        $effectiveProgramId = $selectedProgram ? (int) $selectedProgram['id'] : 0;
        $defaultPeriodeId   = (int) ($selectedProgram['periode_nilai_id'] ?? 0);
        if ($defaultPeriodeId <= 0) {
            $aktifRow = $this->db->table('ref_tahun_akademik')
                ->select('id')
                ->orderBy('is_aktif', 'DESC')
                ->orderBy('tahun', 'DESC')
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();
            $defaultPeriodeId = (int) ($aktifRow['id'] ?? 1);
        }
        $effectivePeriodeId = ($tahunAkademikId !== null && $tahunAkademikId > 0)
            ? $tahunAkademikId
            : $defaultPeriodeId;

        $kelasListBuilder = $this->db->table('taruna_nilai_ip nip')
            ->select('kls.id, kls.nama_kelas, kls.program_studi_id, kls.tingkat, ps.kode_prodi, ps.nama_prodi, COUNT(nip.id) AS total_nilai')
            ->join('ref_kelas kls', 'kls.id = nip.kelas_id', 'inner')
            ->join('ref_program_studi ps', 'ps.id = kls.program_studi_id', 'left')
            ->where('nip.tahun_akademik_id', $effectivePeriodeId)
            ->where('nip.status_validasi', 'tervalidasi')
            ->groupBy('kls.id, kls.nama_kelas, kls.program_studi_id, kls.tingkat, ps.kode_prodi, ps.nama_prodi')
            ->orderBy('ps.id', 'ASC')
            ->orderBy('kls.nama_kelas', 'ASC');

        if ($prodiId !== null && $prodiId > 0) {
            $kelasListBuilder->where('kls.program_studi_id', $prodiId);
        }
        $kelasOptions = $kelasListBuilder->get()->getResultArray();

        $selectedKelasId = $kelasId;
        if (($selectedKelasId === null || $selectedKelasId <= 0) && !empty($kelasOptions)) {
            $selectedKelasId = (int) $kelasOptions[0]['id'];
        }

        $pemeringkatan = null;
        $rankingRows   = [];
        $riwayatVersi  = [];

        if ($effectiveProgramId > 0 && $selectedKelasId !== null && $selectedKelasId > 0) {
            $riwayatVersi = $this->db->table('taruna_beasiswa_pemeringkatan')
                ->where('program_beasiswa_id', $effectiveProgramId)
                ->where('tahun_akademik_id', $effectivePeriodeId)
                ->where('kelas_id', $selectedKelasId)
                ->orderBy('versi', 'DESC')
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();

            if (!empty($riwayatVersi)) {
                $pemeringkatan = $riwayatVersi[0];
                $rankingRows   = $this->db->table('taruna_beasiswa_hasil_peringkat')
                    ->where('pemeringkatan_id', (int) $pemeringkatan['id'])
                    ->orderBy('peringkat', 'ASC')
                    ->orderBy('ip_semester', 'DESC')
                    ->orderBy('id', 'ASC')
                    ->get()
                    ->getResultArray();
            }
        }

        $ipRawCount = 0;
        if ($selectedKelasId !== null && $selectedKelasId > 0) {
            $ipRawCount = (int) $this->db->table('taruna_nilai_ip')
                ->where('tahun_akademik_id', $effectivePeriodeId)
                ->where('kelas_id', $selectedKelasId)
                ->where('status_validasi', 'tervalidasi')
                ->countAllResults();
        }

        $periodeRow = $this->db->table('ref_tahun_akademik')
            ->where('id', $effectivePeriodeId)
            ->get()
            ->getRowArray();

        return [
            'programs'         => $apresiasiPrograms,
            'selected_program' => $selectedProgram,
            'periode_nilai_id' => $effectivePeriodeId,
            'periode_label'    => $periodeRow['kode_tahun'] ?? '-',
            'kelas_options'    => $kelasOptions,
            'selected_kelas_id'=> $selectedKelasId,
            'pemeringkatan'    => $pemeringkatan,
            'ranking_rows'     => $rankingRows,
            'riwayat_versi'    => $riwayatVersi,
            'populasi_ip'      => $ipRawCount,
        ];
    }

    /**
     * Memeriksa dan membuat pratinjau unggahan CSV Nilai IP Semester sebelum disimpan ke basis data.
     *
     * @param string $csvPath
     * @param int    $tahunAkademikId
     * @return array<string, mixed>
     */
    public function previewIpCsv(string $csvPath, int $tahunAkademikId): array
    {
        if (!is_file($csvPath)) {
            return ['status' => false, 'message' => 'Berkas CSV tidak ditemukan.'];
        }

        $rawContent = file_get_contents($csvPath);
        if ($rawContent === false) {
            return ['status' => false, 'message' => 'Berkas CSV gagal dibaca.'];
        }
        $rawContent = preg_replace('/^\xEF\xBB\xBF/', '', (string) $rawContent);
        if (trim((string) $rawContent) === '') {
            return ['status' => false, 'message' => 'Berkas CSV kosong.'];
        }

        $lines = preg_split('/\r\n|\r|\n/', trim((string) $rawContent));
        if (!$lines || count($lines) === 0) {
            return ['status' => false, 'message' => 'Tidak ada baris data pada berkas CSV.'];
        }

        $firstLine = $lines[0];
        $delimiter = substr_count($firstLine, ';') >= substr_count($firstLine, ',') ? ';' : ',';

        $tarunaRows = $this->db->table('master_taruna mt')
            ->select('mt.nomor_taruna, mt.nama_lengkap, pt.program_studi_id, pt.kelas_id, pt.tingkat, pt.semester_aktif, kls.nama_kelas')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = mt.nomor_taruna', 'left')
            ->join('ref_kelas kls', 'kls.id = pt.kelas_id', 'left')
            ->get()
            ->getResultArray();
        $tarunaMap = [];
        foreach ($tarunaRows as $tr) {
            $tarunaMap[(string) $tr['nomor_taruna']] = $tr;
        }

        $kelasRows = $this->db->table('ref_kelas')->get()->getResultArray();
        $kelasNameMap = [];
        foreach ($kelasRows as $kr) {
            $kelasNameMap[strtolower(trim((string) $kr['nama_kelas']))] = $kr;
        }

        $parsedRows  = [];
        $validRows   = [];
        $invalidCount = 0;
        $seenNim      = [];

        foreach ($lines as $idx => $line) {
            $lineTrim = trim($line);
            if ($lineTrim === '') {
                continue;
            }
            $cols = str_getcsv($lineTrim, $delimiter);
            $col0 = trim((string) ($cols[0] ?? ''));

            // Lewati baris header jika terdeteksi
            if ($idx === 0 && (str_contains(strtolower($col0), 'nomor') || str_contains(strtolower($col0), 'taruna') || str_contains(strtolower($col0), 'nim'))) {
                continue;
            }

            // Format standar: nomor_taruna, ip_semester [, sks_lulus [, nama_kelas]]
            // atau nomor_taruna, kode_tahun/id, nama_kelas, tingkat, semester, ip_semester, sks_lulus
            if (count($cols) >= 6) {
                $rawNim   = $col0;
                $rawKelas = trim((string) ($cols[2] ?? ''));
                $rawTkt   = (int) trim((string) ($cols[3] ?? '0'));
                $rawSmt   = (int) trim((string) ($cols[4] ?? '0'));
                $rawIp    = trim((string) ($cols[5] ?? ''));
                $rawSks   = (int) trim((string) ($cols[6] ?? '20'));
            } else {
                $rawNim   = $col0;
                $rawIp    = trim((string) ($cols[1] ?? ''));
                $rawSks   = (int) trim((string) ($cols[2] ?? '20'));
                $rawKelas = trim((string) ($cols[3] ?? ''));
                $rawTkt   = 0;
                $rawSmt   = 0;
            }

            $ipNormalized = str_replace(',', '.', $rawIp);
            $errors       = [];

            if ($rawNim === '' || !isset($tarunaMap[$rawNim])) {
                $errors[] = 'Nomor taruna tidak terdaftar.';
            } elseif (isset($seenNim[$rawNim])) {
                $errors[] = 'Nomor taruna ganda dalam berkas unggahan.';
            }

            if ($ipNormalized === '' || !is_numeric($ipNormalized)) {
                $errors[] = 'Format IP semester tidak valid.';
            } else {
                $ipFloat = (float) $ipNormalized;
                if ($ipFloat < 0.00 || $ipFloat > 4.00) {
                    $errors[] = 'IP semester harus berada di antara 0,00 dan 4,00.';
                }
            }

            $tarunaInfo = $tarunaMap[$rawNim] ?? null;
            $resolvedKelasId = $tarunaInfo ? (int) ($tarunaInfo['kelas_id'] ?? 0) : 0;
            $resolvedProdiId = $tarunaInfo ? (int) ($tarunaInfo['program_studi_id'] ?? 0) : 0;
            $resolvedKelasNama = $tarunaInfo['nama_kelas'] ?? '-';

            if ($rawKelas !== '' && isset($kelasNameMap[strtolower($rawKelas)])) {
                $kMatch = $kelasNameMap[strtolower($rawKelas)];
                $resolvedKelasId   = (int) $kMatch['id'];
                $resolvedProdiId   = (int) $kMatch['program_studi_id'];
                $resolvedKelasNama = (string) $kMatch['nama_kelas'];
            }

            if ($tarunaInfo && $resolvedKelasId <= 0) {
                $errors[] = 'Kelas taruna belum ditetapkan.';
            }

            $seenNim[$rawNim] = true;
            $isValid          = empty($errors);
            if (!$isValid) {
                $invalidCount++;
            }

            $rowEntry = [
                'baris'            => $idx + 1,
                'nomor_taruna'     => $rawNim,
                'nama_lengkap'     => $tarunaInfo['nama_lengkap'] ?? '-',
                'nama_kelas'       => $resolvedKelasNama,
                'program_studi_id' => $resolvedProdiId,
                'kelas_id'         => $resolvedKelasId,
                'tingkat'          => $rawTkt > 0 ? $rawTkt : (int) ($tarunaInfo['tingkat'] ?? 2),
                'semester'         => $rawSmt > 0 ? $rawSmt : (int) ($tarunaInfo['semester_aktif'] ?? 4),
                'ip_semester'      => is_numeric($ipNormalized) ? round((float) $ipNormalized, 2) : $rawIp,
                'sks_lulus'        => $rawSks > 0 ? $rawSks : 20,
                'status'           => $isValid ? 'valid' : 'invalid',
                'alasan_error'     => implode(' ', $errors),
            ];

            $parsedRows[] = $rowEntry;
            if ($isValid) {
                $validRows[] = $rowEntry;
            }
        }

        return [
            'status'            => true,
            'tahun_akademik_id' => $tahunAkademikId,
            'total_baris'       => count($parsedRows),
            'valid_count'       => count($validRows),
            'invalid_count'     => $invalidCount,
            'rows'              => $parsedRows,
            'valid_rows'        => $validRows,
        ];
    }

    /**
     * Menyimpan baris nilai IP yang valid ke tabel taruna_nilai_ip.
     *
     * @param array<int, array<string, mixed>> $validRows
     * @param int                              $tahunAkademikId
     * @param string                           $actorLabel
     * @return array<string, mixed>
     */
    public function importIpCsv(array $validRows, int $tahunAkademikId, string $actorLabel): array
    {
        if (empty($validRows)) {
            return ['status' => false, 'message' => 'Tidak ada baris nilai IP yang valid untuk disimpan.'];
        }

        $maxVersiRow = $this->db->table('taruna_nilai_ip')
            ->selectMax('versi', 'max_v')
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->get()
            ->getRowArray();
        $nextVersi = max(1, (int) ($maxVersiRow['max_v'] ?? 0) + 1);
        $now       = date('Y-m-d H:i:s');

        $this->db->transStart();
        try {
            $inserted = 0;
            foreach ($validRows as $row) {
                $ipVal = round((float) str_replace(',', '.', (string) ($row['ip_semester'] ?? 0)), 2);
                if ($ipVal < 0.00 || $ipVal > 4.00) {
                    continue;
                }
                $this->nilaiIpModel->insert([
                    'nomor_taruna'      => (string) $row['nomor_taruna'],
                    'tahun_akademik_id' => $tahunAkademikId,
                    'program_studi_id'  => (int) $row['program_studi_id'],
                    'kelas_id'          => (int) $row['kelas_id'],
                    'tingkat'           => (int) ($row['tingkat'] ?? 2),
                    'semester'          => (int) ($row['semester'] ?? 4),
                    'ip_semester'       => $ipVal,
                    'sks_lulus'         => max(1, (int) ($row['sks_lulus'] ?? 20)),
                    'sumber_data'       => 'Impor CSV Nilai IP Semester',
                    'versi'             => $nextVersi,
                    'status_validasi'   => 'tervalidasi',
                    'diimpor_oleh'      => $actorLabel,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);
                $inserted++;
            }
            $this->db->transComplete();

            if ($this->db->transStatus() === false || $inserted === 0) {
                return ['status' => false, 'message' => 'Gagal menyimpan daftar nilai IP semester.'];
            }

            return [
                'status'  => true,
                'count'   => $inserted,
                'message' => $inserted . ' baris nilai IP semester berhasil disimpan (Versi #' . $nextVersi . ').',
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Terjadi kendala saat menyimpan nilai IP semester.'];
        }
    }

    /**
     * Menghitung pemeringkatan Beasiswa Apresiasi per kelas berbasis IP semester tervalidasi.
     * Menahan finalisasi secara otomatis bila terjadi seri di peringkat batas kuota kelas tanpa aturan pemecah seri.
     *
     * @param int    $programId
     * @param int    $tahunAkademikId
     * @param int    $kelasId
     * @param string $actorLabel
     * @return array<string, mixed>
     */
    public function hitungPemeringkatanApresiasi(
        int $programId,
        int $tahunAkademikId,
        int $kelasId,
        string $actorLabel
    ): array {
        $program = $this->programModel->find($programId);
        if (!$program || ($program['jenis_beasiswa'] ?? '') !== 'apresiasi') {
            return ['status' => false, 'message' => 'Program Beasiswa Apresiasi tidak ditemukan.'];
        }

        $kelasRow = $this->db->table('ref_kelas kls')
            ->select('kls.*, ps.kode_prodi')
            ->join('ref_program_studi ps', 'ps.id = kls.program_studi_id', 'left')
            ->where('kls.id', $kelasId)
            ->get()
            ->getRowArray();
        if (!$kelasRow) {
            return ['status' => false, 'message' => 'Kelas tidak ditemukan.'];
        }

        // Ambil nilai IP semester terbaru per taruna di kelas & periode penilaian tersebut (bukan IPK)
        $rawIpRows = $this->db->table('taruna_nilai_ip nip')
            ->select('nip.*, mt.nama_lengkap')
            ->join('master_taruna mt', 'mt.nomor_taruna = nip.nomor_taruna', 'inner')
            ->where('nip.tahun_akademik_id', $tahunAkademikId)
            ->where('nip.kelas_id', $kelasId)
            ->where('nip.status_validasi', 'tervalidasi')
            ->orderBy('nip.versi', 'DESC')
            ->orderBy('nip.ip_semester', 'DESC')
            ->orderBy('nip.nomor_taruna', 'ASC')
            ->get()
            ->getResultArray();

        $latestPerTaruna = [];
        foreach ($rawIpRows as $r) {
            $nim = (string) $r['nomor_taruna'];
            if (!isset($latestPerTaruna[$nim])) {
                $latestPerTaruna[$nim] = $r;
            }
        }
        $students = array_values($latestPerTaruna);

        if (empty($students)) {
            return ['status' => false, 'message' => 'Belum ada nilai IP semester tervalidasi pada kelas ini.'];
        }

        $kuotaKelas = max(1, (int) ($program['jumlah_peringkat'] ?? 5));
        $aturanSeri = (string) ($program['aturan_seri'] ?? 'belum_ditetapkan');

        // Hitung metrik pendukung jika aturan pemecah seri sudah ditetapkan
        $tieMetrics = $this->buildTieBreakMetrics(array_column($students, 'nomor_taruna'));

        usort($students, static function (array $a, array $b) use ($aturanSeri, $tieMetrics): int {
            $ipA = (float) $a['ip_semester'];
            $ipB = (float) $b['ip_semester'];
            if (abs($ipA - $ipB) > 0.0001) {
                return $ipB <=> $ipA;
            }

            if ($aturanSeri === 'sks_tertinggi') {
                $cmp = ((int) $b['sks_lulus']) <=> ((int) $a['sks_lulus']);
                if ($cmp !== 0) {
                    return $cmp;
                }
            } elseif ($aturanSeri === 'ipk') {
                $avgA = (float) ($tieMetrics[$a['nomor_taruna']]['avg_ip'] ?? $ipA);
                $avgB = (float) ($tieMetrics[$b['nomor_taruna']]['avg_ip'] ?? $ipB);
                if (abs($avgA - $avgB) > 0.0001) {
                    return $avgB <=> $avgA;
                }
            } elseif ($aturanSeri === 'prestasi') {
                $cmp = ((int) ($tieMetrics[$b['nomor_taruna']]['prestasi_count'] ?? 0))
                    <=> ((int) ($tieMetrics[$a['nomor_taruna']]['prestasi_count'] ?? 0));
                if ($cmp !== 0) {
                    return $cmp;
                }
            } elseif ($aturanSeri === 'disiplin') {
                $cmp = ((int) ($tieMetrics[$a['nomor_taruna']]['pelanggaran_count'] ?? 0))
                    <=> ((int) ($tieMetrics[$b['nomor_taruna']]['pelanggaran_count'] ?? 0));
                if ($cmp !== 0) {
                    return $cmp;
                }
            }

            return strcmp((string) $a['nomor_taruna'], (string) $b['nomor_taruna']);
        });

        // Cek seri pada batas peringkat ke-$kuotaKelas (mis. rank 5 sama IP dengan rank 6)
        $cutoffIp = count($students) >= $kuotaKelas ? (float) $students[$kuotaKelas - 1]['ip_semester'] : null;
        $tiedIndicesOnCutoff = [];
        if ($cutoffIp !== null) {
            foreach ($students as $idx => $st) {
                if (abs((float) $st['ip_semester'] - $cutoffIp) < 0.0001) {
                    $tiedIndicesOnCutoff[] = $idx;
                }
            }
        }
        // Seri yang melampaui kuota kelas terjadi jika ada peserta sebelum/pada cutoff yang seri dengan peserta setelah cutoff
        $hasBoundaryTie = false;
        if (count($tiedIndicesOnCutoff) > 1 && count($students) > $kuotaKelas) {
            $lastIdxInTie  = max($tiedIndicesOnCutoff);
            $firstIdxInTie = min($tiedIndicesOnCutoff);
            if ($firstIdxInTie < $kuotaKelas && $lastIdxInTie >= $kuotaKelas) {
                $hasBoundaryTie = true;
            }
        }

        $isHeldByTie = $hasBoundaryTie && ($aturanSeri === 'belum_ditetapkan');

        $prevMax = $this->db->table('taruna_beasiswa_pemeringkatan')
            ->selectMax('versi', 'max_v')
            ->where('program_beasiswa_id', $programId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('kelas_id', $kelasId)
            ->get()
            ->getRowArray();
        $nextVersi = (int) ($prevMax['max_v'] ?? 0) + 1;
        $kodeVersi = 'APR-' . date('Y') . '-' . preg_replace('/[^A-Za-z0-9]/', '', (string) ($kelasRow['kode_prodi'] ?? 'KLS'))
            . '-' . substr(preg_replace('/[^A-Za-z0-9]/', '', (string) $kelasRow['nama_kelas']), -1)
            . '-V' . $nextVersi;

        $jumlahKandidat = 0;
        $hasilRows      = [];
        $now            = date('Y-m-d H:i:s');

        foreach ($students as $idx => $st) {
            $pos = $idx + 1;
            $onBoundary = $cutoffIp !== null && abs((float) $st['ip_semester'] - $cutoffIp) < 0.0001;

            if ($isHeldByTie && $onBoundary) {
                $rankShown      = $kuotaKelas;
                $statusKandidat = 'seri_kelima';
                $isSeri         = 1;
                $catatan        = 'IP sama pada batas peringkat ke-' . $kuotaKelas;
            } elseif ($pos <= $kuotaKelas) {
                $rankShown      = $pos;
                $statusKandidat = 'kandidat';
                $isSeri         = ($hasBoundaryTie && $onBoundary) ? 1 : 0;
                $catatan        = ($hasBoundaryTie && $onBoundary)
                    ? ('Ditetapkan melalui aturan pemecah seri: ' . (ketarunaan_aturan_seri_catalog()[$aturanSeri] ?? $aturanSeri))
                    : ('Masuk ' . $kuotaKelas . ' IP tertinggi kelas');
                $jumlahKandidat++;
            } else {
                $rankShown      = $pos;
                $statusKandidat = 'di_luar_5_besar';
                $isSeri         = ($hasBoundaryTie && $onBoundary) ? 1 : 0;
                $catatan        = ($hasBoundaryTie && $onBoundary)
                    ? ('Tidak terpilih berdasarkan aturan pemecah seri: ' . (ketarunaan_aturan_seri_catalog()[$aturanSeri] ?? $aturanSeri))
                    : null;
            }

            $hasilRows[] = [
                'nomor_taruna'    => (string) $st['nomor_taruna'],
                'nama_snapshot'   => (string) $st['nama_lengkap'],
                'kelas_snapshot'  => (string) $kelasRow['nama_kelas'],
                'ip_semester'     => (float) $st['ip_semester'],
                'peringkat'       => $rankShown,
                'status_kandidat' => $statusKandidat,
                'is_seri'         => $isSeri,
                'catatan'         => $catatan,
                'created_at'      => $now,
            ];
        }

        $alasanDitahan = $isHeldByTie
            ? ('IP sama (' . number_format((float) $cutoffIp, 2, ',', '.') . ') pada batas peringkat ke-' . $kuotaKelas . ' memerlukan aturan pemecah seri sebelum finalisasi.')
            : null;

        $this->db->transStart();
        try {
            $this->pemeringkatanModel->insert([
                'program_beasiswa_id'   => $programId,
                'tahun_akademik_id'     => $tahunAkademikId,
                'program_studi_id'      => (int) $kelasRow['program_studi_id'],
                'kelas_id'              => $kelasId,
                'kode_versi'            => $kodeVersi,
                'versi'                 => $nextVersi,
                'populasi_kelas'        => count($students),
                'jumlah_kandidat'       => $jumlahKandidat,
                'sumber_nilai'          => 'Nilai semester tervalidasi',
                'parameter_aturan_json' => json_encode([
                    'kuota_per_kelas' => $kuotaKelas,
                    'dasar'           => 'ip_semester',
                    'aturan_seri'     => $aturanSeri,
                ], JSON_UNESCAPED_UNICODE),
                'ada_seri_kelima'       => $isHeldByTie ? 1 : 0,
                'aturan_seri'           => $aturanSeri,
                'status_finalisasi'     => $isHeldByTie ? 'ditahan_seri' : 'draf',
                'alasan_ditahan'        => $alasanDitahan,
                'diproses_oleh'         => $actorLabel,
                'tanggal_hitung'        => date('Y-m-d'),
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
            $pemeringkatanId = (int) $this->pemeringkatanModel->getInsertID();

            foreach ($hasilRows as $hr) {
                $hr['pemeringkatan_id'] = $pemeringkatanId;
                $this->hasilPeringkatModel->insert($hr);
            }
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan hasil pemeringkatan kelas.'];
            }

            if ($isHeldByTie) {
                return [
                    'status'  => true,
                    'held'    => true,
                    'id'      => $pemeringkatanId,
                    'message' => 'Pemeringkatan dihitung (Versi #' . $nextVersi . '), namun finalisasi ditahan karena terdapat nilai IP sama di batas peringkat ke-' . $kuotaKelas . '.',
                ];
            }

            return [
                'status'  => true,
                'held'    => false,
                'id'      => $pemeringkatanId,
                'message' => 'Pemeringkatan kelas berhasil dihitung (' . $jumlahKandidat . ' kandidat siap diajukan).',
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Gagal memproses perhitungan pemeringkatan kelas.'];
        }
    }

    /**
     * Mengajukan daftar kandidat 5 besar dari pemeringkatan kelas ke antrean verifikasi.
     * Menolak pengajuan jika versi masih berstatus ditahan_seri.
     *
     * @param int    $pemeringkatanId
     * @param string $actorLabel
     * @return array<string, mixed>
     */
    public function ajukanPemeringkatanApresiasi(int $pemeringkatanId, string $actorLabel): array
    {
        $pemeringkatan = $this->pemeringkatanModel->find($pemeringkatanId);
        if (!$pemeringkatan) {
            return ['status' => false, 'message' => 'Data pemeringkatan tidak ditemukan.'];
        }
        if (($pemeringkatan['status_finalisasi'] ?? '') === 'ditahan_seri' || (int) ($pemeringkatan['ada_seri_kelima'] ?? 0) === 1) {
            return ['status' => false, 'message' => 'Kelas masih ditahan karena nilai IP sama pada batas peringkat ke-5. Tetapkan aturan pemecah seri lalu hitung ulang terlebih dahulu.'];
        }
        if (in_array((string) $pemeringkatan['status_finalisasi'], ['diajukan', 'tervalidasi'], true)) {
            return ['status' => false, 'message' => 'Daftar kandidat pemeringkatan ini sudah diajukan sebelumnya.'];
        }

        $program = $this->programModel->find((int) $pemeringkatan['program_beasiswa_id']);
        if (!$program) {
            return ['status' => false, 'message' => 'Program beasiswa terkait tidak ditemukan.'];
        }

        $kandidatRows = $this->db->table('taruna_beasiswa_hasil_peringkat')
            ->where('pemeringkatan_id', $pemeringkatanId)
            ->where('status_kandidat', 'kandidat')
            ->orderBy('peringkat', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($kandidatRows)) {
            return ['status' => false, 'message' => 'Tidak ada kandidat yang memenuhi syarat pada versi pemeringkatan ini.'];
        }

        $this->db->transStart();
        try {
            $addedCount   = 0;
            $skippedCount = 0;
            $today        = date('Y-m-d');

            foreach ($kandidatRows as $kr) {
                $existsActive = $this->db->table('taruna_penerima_beasiswa')
                    ->where('program_beasiswa_id', (int) $program['id'])
                    ->where('nomor_taruna', $kr['nomor_taruna'])
                    ->where('status_penetapan !=', 'dibatalkan')
                    ->countAllResults();

                if ($existsActive > 0) {
                    $skippedCount++;
                    continue;
                }

                $riwayat = [[
                    'tanggal' => $today,
                    'pelaku'  => $actorLabel,
                    'aksi'    => 'Mengajukan Kandidat Pemeringkatan',
                    'status'  => 'diajukan',
                    'catatan' => 'Peringkat #' . $kr['peringkat'] . ' ' . $kr['kelas_snapshot'] . ' (' . ($pemeringkatan['kode_versi'] ?? '-') . ')',
                ]];

                $this->penerimaModel->insert([
                    'program_beasiswa_id' => (int) $program['id'],
                    'nomor_taruna'        => $kr['nomor_taruna'],
                    'tahun_akademik_id'   => $program['tahun_akademik_id'] ?: null,
                    'sumber_klaim'        => 'pemeringkatan',
                    'tanggal_pengajuan'   => $today,
                    'dicatat_oleh'        => $actorLabel,
                    'pemeringkatan_id'    => $pemeringkatanId,
                    'ip_semester'         => $kr['ip_semester'],
                    'peringkat_kelas'     => $kr['peringkat'],
                    'kelas_snapshot'      => $kr['kelas_snapshot'],
                    'uraian_pengajuan'    => $kr['catatan'] ?: ('Kandidat peringkat ' . $kr['peringkat'] . ' kelas ' . $kr['kelas_snapshot']),
                    'status_pengajuan'    => 'diajukan',
                    'hasil_verifikasi'    => 'belum_diperiksa',
                    'status_penetapan'    => 'kandidat',
                    'status_validasi'     => 'diajukan',
                    'diajukan_oleh'       => $actorLabel,
                    'riwayat_proses_json' => json_encode($riwayat, JSON_UNESCAPED_UNICODE),
                ]);
                $addedCount++;
            }

            $this->pemeringkatanModel->update($pemeringkatanId, [
                'status_finalisasi' => 'diajukan',
                'catatan_validasi'  => 'Kandidat telah diajukan ke antrean verifikasi dan penetapan.',
            ]);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal mengajukan kandidat pemeringkatan.'];
            }

            $infoSkip = $skippedCount > 0 ? ' (' . $skippedCount . ' taruna dilewati karena sudah tercatat).' : '.';
            return [
                'status'  => true,
                'message' => $addedCount . ' kandidat peringkat berhasil dikirim ke antrean verifikasi' . $infoSkip,
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Gagal mengirim kandidat pemeringkatan ke antrean.'];
        }
    }

    /**
     * Mengambil daftar pengajuan/klaim beasiswa (tab Klaim Beasiswa).
     *
     * @param array<string, mixed> $filters
     * @param bool                 $viewerCanSeeAllEconomy
     * @param string|null          $viewerNomorTaruna
     * @return array<string, mixed>
     */
    public function listKlaim(
        array $filters = [],
        bool $viewerCanSeeAllEconomy = false,
        ?string $viewerNomorTaruna = null
    ): array {
        $builder = $this->db->table('taruna_penerima_beasiswa tpb')
            ->select('tpb.*, prog.nama_program, prog.jenis_beasiswa, prog.pemberi, prog.nominal,
                      mt.nama_lengkap, ps.kode_prodi, ps.nama_prodi, kls.nama_kelas')
            ->join('taruna_program_beasiswa prog', 'prog.id = tpb.program_beasiswa_id', 'inner')
            ->join('master_taruna mt', 'mt.nomor_taruna = tpb.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = tpb.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_kelas kls', 'kls.id = pt.kelas_id', 'left');

        if ($viewerNomorTaruna !== null && $viewerNomorTaruna !== '') {
            $builder->where('tpb.nomor_taruna', $viewerNomorTaruna);
        }

        $jenis   = trim((string) ($filters['jenis_beasiswa'] ?? 'all'));
        $sumber  = trim((string) ($filters['sumber_klaim'] ?? 'all'));
        $verif   = trim((string) ($filters['hasil_verifikasi'] ?? 'all'));
        $progId  = (int) ($filters['program_beasiswa_id'] ?? 0);
        $tahunId = trim((string) ($filters['tahun_akademik_id'] ?? 'all'));
        $q       = trim((string) ($filters['q'] ?? ''));

        if ($jenis !== '' && $jenis !== 'all') {
            $builder->where('prog.jenis_beasiswa', $jenis);
        } else {
            $builder->whereIn('prog.jenis_beasiswa', ['prestasi', 'daerah_3tp', 'tidak_mampu']);
        }
        if ($sumber !== '' && $sumber !== 'all') {
            $builder->where('tpb.sumber_klaim', $sumber);
        }
        if ($verif !== '' && $verif !== 'all') {
            $builder->where('tpb.hasil_verifikasi', $verif);
        }
        if ($progId > 0) {
            $builder->where('tpb.program_beasiswa_id', $progId);
        }
        if ($tahunId !== '' && $tahunId !== 'all' && (int) $tahunId > 0) {
            $builder->where('prog.tahun_akademik_id', (int) $tahunId);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('mt.nama_lengkap', $q)
                ->orLike('tpb.nomor_taruna', $q)
                ->orLike('tpb.nama_prestasi', $q)
                ->orLike('tpb.kabupaten_kota', $q)
                ->orLike('prog.nama_program', $q)
                ->groupEnd();
        }

        $sortBy    = trim((string) ($filters['sort_by'] ?? ''));
        $sortOrder = strtolower(trim((string) ($filters['sort_order'] ?? 'desc'))) === 'asc' ? 'ASC' : 'DESC';
        $sortMap   = [
            'taruna'           => 'mt.nama_lengkap',
            'program'          => 'prog.nama_program',
            'sumber_klaim'     => 'tpb.sumber_klaim',
            'hasil_verifikasi' => 'tpb.hasil_verifikasi',
            'status_penetapan' => 'tpb.status_penetapan',
            'tanggal'          => 'tpb.tanggal_pengajuan',
        ];
        if (isset($sortMap[$sortBy])) {
            $builder->orderBy($sortMap[$sortBy], $sortOrder)->orderBy('tpb.id', 'DESC');
        } else {
            $builder->orderBy('tpb.id', 'DESC');
        }

        $allRows = $builder->get()->getResultArray();
        foreach ($allRows as $idx => $row) {
            $canViewRow = $viewerCanSeeAllEconomy || ($viewerNomorTaruna !== null && strcasecmp($viewerNomorTaruna, (string) $row['nomor_taruna']) === 0);
            $allRows[$idx] = $this->maskRowEconomy($row, $canViewRow);
        }

        $totalFiltered = count($allRows);
        $perPage       = max(1, (int) ($filters['per_page'] ?? 15));
        $totalPages    = max(1, (int) ceil($totalFiltered / $perPage));
        $page          = max(1, min($totalPages, (int) ($filters['page'] ?? 1)));
        $offset        = ($page - 1) * $perPage;
        $rows          = array_slice($allRows, $offset, $perPage);

        return [
            'rows'       => $rows,
            'pagination' => [
                'page'           => $page,
                'per_page'       => $perPage,
                'total_pages'    => $totalPages,
                'total_filtered' => $totalFiltered,
                'from'           => $totalFiltered > 0 ? $offset + 1 : 0,
                'to'             => min($offset + $perPage, $totalFiltered),
            ],
        ];
    }

    /**
     * Menyimpan pengajuan klaim Beasiswa Prestasi, Daerah 3TP, atau Tidak Mampu (baru / revisi draf & perbaikan).
     *
     * @param string               $jenis
     * @param array<string, mixed> $input
     * @param array<string, mixed> $filesInfo
     * @param string               $actorLabel
     * @param bool                 $isSelfTaruna
     * @param int|null             $editId
     * @return array<string, mixed>
     */
    public function submitKlaim(
        string $jenis,
        array $input,
        array $filesInfo,
        string $actorLabel,
        bool $isSelfTaruna = false,
        ?int $editId = null
    ): array {
        $jenisClean = strtolower(trim($jenis));
        if (!in_array($jenisClean, ['prestasi', 'daerah_3tp', 'tidak_mampu'], true)) {
            return ['status' => false, 'message' => 'Jenis klaim beasiswa tidak dikenali.'];
        }

        $programId = (int) ($input['program_beasiswa_id'] ?? 0);
        $program   = $programId > 0 ? $this->programModel->find($programId) : null;
        if (!$program || ($program['jenis_beasiswa'] ?? '') !== $jenisClean) {
            return ['status' => false, 'message' => 'Program beasiswa yang dipilih tidak sesuai jenis klaim.'];
        }
        if ($editId === null && ($program['status_program'] ?? '') === 'selesai') {
            return ['status' => false, 'message' => 'Program beasiswa yang dipilih sudah selesai dan tidak menerima pengajuan baru.'];
        }

        $nomorTaruna = trim((string) ($input['nomor_taruna'] ?? ''));
        $tarunaRow   = $nomorTaruna !== ''
            ? $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray()
            : null;
        if (!$tarunaRow) {
            return ['status' => false, 'message' => 'Taruna yang dipilih tidak ditemukan.'];
        }

        $existing = null;
        if ($editId !== null && $editId > 0) {
            $existing = $this->penerimaModel->find($editId);
            if (!$existing) {
                return ['status' => false, 'message' => 'Data klaim beasiswa tidak ditemukan.'];
            }
            if (($existing['sumber_klaim'] ?? '') === 'pemeringkatan') {
                return ['status' => false, 'message' => 'Kandidat hasil pemeringkatan Apresiasi tidak dapat diubah melalui formulir klaim.'];
            }
            if ($isSelfTaruna && strcasecmp((string) $existing['nomor_taruna'], $nomorTaruna) !== 0) {
                return ['status' => false, 'message' => 'Anda hanya dapat mengubah pengajuan milik Anda sendiri.'];
            }
            if (($existing['status_penetapan'] ?? '') !== 'kandidat') {
                return ['status' => false, 'message' => 'Pengajuan yang sudah ditetapkan atau dibatalkan tidak dapat diubah kembali.'];
            }
            if (!in_array((string) ($existing['status_validasi'] ?? ''), ['draf', 'perlu_perbaikan'], true)) {
                return ['status' => false, 'message' => 'Pengajuan yang sedang diverifikasi atau sudah tervalidasi tidak dapat diubah.'];
            }
        }

        // Cegah duplikasi pengajuan aktif (non-dibatalkan) untuk pasangan (program, taruna)
        $dupBuilder = $this->db->table('taruna_penerima_beasiswa')
            ->where('program_beasiswa_id', $programId)
            ->where('nomor_taruna', $nomorTaruna)
            ->where('status_penetapan !=', 'dibatalkan');
        if ($existing !== null) {
            $dupBuilder->where('id !=', (int) $existing['id']);
        }
        if ($dupBuilder->countAllResults() > 0) {
            return ['status' => false, 'message' => 'Taruna sudah memiliki pengajuan aktif pada program beasiswa ini.'];
        }

        $aksiSubmit  = strtolower(trim((string) ($input['aksi_submit'] ?? 'ajukan')));
        $isDraft     = ($aksiSubmit === 'draf');
        $sumberKlaim = $isSelfTaruna ? 'taruna' : (strtolower(trim((string) ($input['sumber_klaim'] ?? 'pic'))) === 'taruna' ? 'taruna' : 'pic');
        $today       = date('Y-m-d');

        $payload = [
            'program_beasiswa_id' => $programId,
            'nomor_taruna'        => $nomorTaruna,
            'tahun_akademik_id'   => $program['tahun_akademik_id'] ?: null,
            'sumber_klaim'        => $sumberKlaim,
            'tanggal_pengajuan'   => $existing['tanggal_pengajuan'] ?? $today,
            'dicatat_oleh'        => $actorLabel,
            'status_pengajuan'    => $isDraft ? 'diajukan' : 'diajukan',
            'hasil_verifikasi'    => 'belum_diperiksa',
            'status_penetapan'    => 'kandidat',
            'status_validasi'     => $isDraft ? 'draf' : 'diajukan',
            'diajukan_oleh'       => $actorLabel,
        ];

        if ($jenisClean === 'prestasi') {
            $prep = $this->preparePrestasiClaimData($input, $filesInfo, $existing, $nomorTaruna, $isDraft);
            if (!$prep['status']) {
                return $prep;
            }
            $payload = array_merge($payload, $prep['data']);
        } elseif ($jenisClean === 'daerah_3tp') {
            $prep = $this->prepareDaerah3tpClaimData($input, $filesInfo, $existing, $isDraft);
            if (!$prep['status']) {
                return $prep;
            }
            $payload = array_merge($payload, $prep['data']);
        } else {
            $prep = $this->prepareTidakMampuClaimData($input, $filesInfo, $existing, $isDraft);
            if (!$prep['status']) {
                return $prep;
            }
            $payload = array_merge($payload, $prep['data']);
        }

        $riwayat = [];
        if ($existing && !empty($existing['riwayat_proses_json'])) {
            $decoded = json_decode((string) $existing['riwayat_proses_json'], true);
            if (is_array($decoded)) {
                $riwayat = $decoded;
            }
        }
        $riwayat[] = [
            'tanggal' => $today,
            'pelaku'  => $actorLabel,
            'aksi'    => $isDraft ? 'Menyimpan Draf Klaim' : ($existing ? 'Memperbarui & Mengajukan Klaim' : 'Mengajukan Klaim Beasiswa'),
            'status'  => $isDraft ? 'draf' : 'diajukan',
            'catatan' => $isDraft ? 'Disimpan sebagai draf.' : 'Berkas pengajuan dikirim untuk verifikasi.',
        ];
        $payload['riwayat_proses_json'] = json_encode($riwayat, JSON_UNESCAPED_UNICODE);

        $this->db->transStart();
        try {
            if ($existing !== null) {
                $this->penerimaModel->update((int) $existing['id'], $payload);
                $savedId = (int) $existing['id'];
            } else {
                $this->penerimaModel->insert($payload);
                $savedId = (int) $this->penerimaModel->getInsertID();
            }
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan pengajuan klaim beasiswa.'];
            }

            $msg = $isDraft
                ? 'Draf pengajuan klaim berhasil disimpan.'
                : 'Klaim beasiswa berhasil diajukan untuk verifikasi.';

            if ($jenisClean === 'daerah_3tp' && !$isDraft && ($payload['status_pemeriksaan_3tp'] ?? '') === 'acuan_belum_ada') {
                $msg .= ' Catatan: wilayah domisili belum ditemukan pada daftar acuan 3TP sehingga akan diperiksa petugas.';
            }

            return ['status' => true, 'id' => $savedId, 'message' => $msg];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Gagal memproses penyimpanan klaim beasiswa.'];
        }
    }

    /**
     * Mengambil daftar antrean dan riwayat verifikasi klaim beasiswa (tab Verifikasi).
     *
     * @param array<string, mixed> $filters
     * @param bool                 $canViewEconomy
     * @return array<string, mixed>
     */
    public function listVerifikasi(array $filters = [], bool $canViewEconomy = false): array
    {
        $summaryRow = $this->db->table('taruna_penerima_beasiswa')
            ->select("SUM(CASE WHEN status_penetapan != 'dibatalkan' AND status_validasi != 'draf' AND hasil_verifikasi = 'belum_diperiksa' THEN 1 ELSE 0 END) AS belum_diperiksa,
                      SUM(CASE WHEN status_penetapan != 'dibatalkan' AND hasil_verifikasi = 'memenuhi' THEN 1 ELSE 0 END) AS memenuhi,
                      SUM(CASE WHEN status_penetapan != 'dibatalkan' AND hasil_verifikasi = 'perlu_perbaikan' THEN 1 ELSE 0 END) AS perlu_perbaikan,
                      SUM(CASE WHEN hasil_verifikasi = 'tidak_memenuhi' THEN 1 ELSE 0 END) AS tidak_memenuhi")
            ->get()
            ->getRowArray();

        $builder = $this->db->table('taruna_penerima_beasiswa tpb')
            ->select('tpb.*, prog.nama_program, prog.jenis_beasiswa, prog.pemberi,
                      mt.nama_lengkap, ps.kode_prodi, ps.nama_prodi, kls.nama_kelas')
            ->join('taruna_program_beasiswa prog', 'prog.id = tpb.program_beasiswa_id', 'inner')
            ->join('master_taruna mt', 'mt.nomor_taruna = tpb.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = tpb.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_kelas kls', 'kls.id = pt.kelas_id', 'left')
            ->where('tpb.status_validasi !=', 'draf');

        $hasil   = trim((string) ($filters['hasil_verifikasi'] ?? 'all'));
        $jenis   = trim((string) ($filters['jenis_beasiswa'] ?? 'all'));
        $progId  = (int) ($filters['program_beasiswa_id'] ?? 0);
        $tahunId = trim((string) ($filters['tahun_akademik_id'] ?? 'all'));
        $q       = trim((string) ($filters['q'] ?? ''));

        if ($hasil !== '' && $hasil !== 'all') {
            $builder->where('tpb.hasil_verifikasi', $hasil);
        }
        if ($jenis !== '' && $jenis !== 'all') {
            $builder->where('prog.jenis_beasiswa', $jenis);
        }
        if ($progId > 0) {
            $builder->where('tpb.program_beasiswa_id', $progId);
        }
        if ($tahunId !== '' && $tahunId !== 'all' && (int) $tahunId > 0) {
            $builder->where('prog.tahun_akademik_id', (int) $tahunId);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('mt.nama_lengkap', $q)
                ->orLike('tpb.nomor_taruna', $q)
                ->orLike('prog.nama_program', $q)
                ->groupEnd();
        }

        $sortBy    = trim((string) ($filters['sort_by'] ?? ''));
        $sortOrder = strtolower(trim((string) ($filters['sort_order'] ?? 'desc'))) === 'asc' ? 'ASC' : 'DESC';
        $sortMap   = [
            'taruna'           => 'mt.nama_lengkap',
            'program'          => 'prog.nama_program',
            'tanggal'          => 'tpb.tanggal_pengajuan',
            'hasil_verifikasi' => 'tpb.hasil_verifikasi',
            'status_penetapan' => 'tpb.status_penetapan',
        ];
        if (isset($sortMap[$sortBy])) {
            $builder->orderBy($sortMap[$sortBy], $sortOrder)->orderBy('tpb.id', 'DESC');
        } else {
            $builder
                ->orderBy("FIELD(tpb.hasil_verifikasi, 'belum_diperiksa', 'perlu_perbaikan', 'memenuhi', 'tidak_memenuhi')", '', false)
                ->orderBy('tpb.id', 'DESC');
        }

        $allRows = $builder->get()->getResultArray();
        foreach ($allRows as $idx => $row) {
            $allRows[$idx] = $this->maskRowEconomy($row, $canViewEconomy);
        }

        $totalFiltered = count($allRows);
        $perPage       = max(1, (int) ($filters['per_page'] ?? 15));
        $totalPages    = max(1, (int) ceil($totalFiltered / $perPage));
        $page          = max(1, min($totalPages, (int) ($filters['page'] ?? 1)));
        $offset        = ($page - 1) * $perPage;
        $rows          = array_slice($allRows, $offset, $perPage);

        return [
            'summary' => [
                'belum_diperiksa' => (int) ($summaryRow['belum_diperiksa'] ?? 0),
                'memenuhi'        => (int) ($summaryRow['memenuhi'] ?? 0),
                'perlu_perbaikan' => (int) ($summaryRow['perlu_perbaikan'] ?? 0),
                'tidak_memenuhi'  => (int) ($summaryRow['tidak_memenuhi'] ?? 0),
            ],
            'rows'       => $rows,
            'pagination' => [
                'page'           => $page,
                'per_page'       => $perPage,
                'total_pages'    => $totalPages,
                'total_filtered' => $totalFiltered,
                'from'           => $totalFiltered > 0 ? $offset + 1 : 0,
                'to'             => min($offset + $perPage, $totalFiltered),
            ],
        ];
    }

    /**
     * Menyimpan hasil pemeriksaan kelayakan & berkas klaim beasiswa (terpisah dari penetapan).
     *
     * @param int                  $id
     * @param array<string, mixed> $input
     * @param string               $actorLabel
     * @return array<string, mixed>
     */
    public function saveVerifikasi(int $id, array $input, string $actorLabel): array
    {
        $row = $this->db->table('taruna_penerima_beasiswa tpb')
            ->select('tpb.*, prog.jenis_beasiswa, prog.nama_program')
            ->join('taruna_program_beasiswa prog', 'prog.id = tpb.program_beasiswa_id', 'inner')
            ->where('tpb.id', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return ['status' => false, 'message' => 'Data klaim beasiswa tidak ditemukan.'];
        }
        if (($row['status_validasi'] ?? '') === 'draf') {
            return ['status' => false, 'message' => 'Pengajuan berstatus Draf belum dikirim oleh pemohon dan tidak dapat diperiksa.'];
        }
        if (($row['status_penetapan'] ?? '') === 'ditetapkan') {
            return ['status' => false, 'message' => 'Pengajuan sudah ditetapkan sebagai penerima dan tidak dapat diperiksa ulang.'];
        }
        if (($row['status_penetapan'] ?? '') === 'dibatalkan') {
            return ['status' => false, 'message' => 'Pengajuan yang telah dibatalkan tidak dapat diverifikasi kembali.'];
        }

        $hasil = strtolower(trim((string) ($input['hasil_verifikasi'] ?? '')));
        if (!in_array($hasil, ['memenuhi', 'perlu_perbaikan', 'tidak_memenuhi'], true)) {
            return ['status' => false, 'message' => 'Pilih keputusan pemeriksaan yang valid.'];
        }

        $catatan = trim((string) ($input['catatan_pemeriksa'] ?? ''));
        if (in_array($hasil, ['perlu_perbaikan', 'tidak_memenuhi'], true) && $catatan === '') {
            return ['status' => false, 'message' => 'Catatan pemeriksaan wajib diisi untuk status Perlu Perbaikan atau Tidak Memenuhi.'];
        }

        // Pengaman UAT-12: klaim Daerah 3TP tidak bisa ditandai memenuhi jika acuan wilayah 3TP tidak valid/belum ada
        $status3tp = (string) ($row['status_pemeriksaan_3tp'] ?? '');
        if (($row['jenis_beasiswa'] ?? '') === 'daerah_3tp' && $hasil === 'memenuhi') {
            $match3tp = $this->matchWilayah3tp((string) ($row['kode_wilayah'] ?? ''), (string) ($row['provinsi'] ?? ''), (string) ($row['kabupaten_kota'] ?? ''));
            if ($match3tp === null && $status3tp !== 'memenuhi') {
                return ['status' => false, 'message' => 'Wilayah domisili belum tercantum pada daftar acuan resmi 3TP. Gunakan status Perlu Perbaikan atau Tidak Memenuhi.'];
            }
            if ($match3tp !== null) {
                $status3tp = 'memenuhi';
            }
        }

        $checklist = [
            'identitas' => !empty($input['cek_identitas']),
            'kelayakan' => !empty($input['cek_kelayakan']),
            'dokumen'   => !empty($input['cek_dokumen']),
        ];

        $today   = date('Y-m-d');
        $riwayat = json_decode((string) ($row['riwayat_proses_json'] ?? '[]'), true);
        if (!is_array($riwayat)) {
            $riwayat = [];
        }

        $aksiLabel = match ($hasil) {
            'memenuhi'       => 'Verifikasi Memenuhi',
            'perlu_perbaikan'=> 'Kembalikan untuk Perbaikan',
            default          => 'Tolak Kelayakan Klaim',
        };
        $riwayat[] = [
            'tanggal' => $today,
            'pelaku'  => $actorLabel,
            'aksi'    => $aksiLabel,
            'status'  => $hasil,
            'catatan' => $catatan ?: 'Pemeriksaan kelayakan selesai.',
        ];

        $update = [
            'hasil_verifikasi'          => $hasil,
            'checklist_verifikasi_json' => json_encode($checklist, JSON_UNESCAPED_UNICODE),
            'catatan_pemeriksa'         => $catatan,
            'diperiksa_oleh'            => $actorLabel,
            'tanggal_pemeriksaan'       => $today,
            'riwayat_proses_json'       => json_encode($riwayat, JSON_UNESCAPED_UNICODE),
        ];

        if (($row['jenis_beasiswa'] ?? '') === 'daerah_3tp' && $status3tp !== '') {
            $update['status_pemeriksaan_3tp'] = $status3tp;
        }

        // Pastikan hasil verifikasi 'memenuhi' TIDAK langsung menetapkan penerima (BEA-09)
        if ($hasil === 'memenuhi') {
            $update['status_pengajuan'] = 'diverifikasi';
            $update['status_penetapan'] = 'kandidat';
            $update['status_validasi']  = 'diajukan';
        } elseif ($hasil === 'perlu_perbaikan') {
            $update['status_pengajuan'] = 'diajukan';
            $update['status_penetapan'] = 'kandidat';
            $update['status_validasi']  = 'perlu_perbaikan';
            $update['catatan_validasi'] = $catatan;
        } else {
            $update['status_pengajuan'] = 'ditolak';
            $update['status_penetapan'] = 'dibatalkan';
            $update['status_validasi']  = 'tervalidasi';
        }

        $this->db->transStart();
        try {
            $this->penerimaModel->update($id, $update);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan hasil pemeriksaan verifikasi.'];
            }

            $msg = $hasil === 'memenuhi'
                ? 'Hasil verifikasi disimpan (Memenuhi). Pengajuan masuk ke antrean Penetapan Penerima SK.'
                : 'Hasil pemeriksaan klaim beasiswa berhasil disimpan.';

            return ['status' => true, 'message' => $msg];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Terjadi kendala saat menyimpan verifikasi klaim.'];
        }
    }

    /**
     * Mengambil daftar penerima beasiswa & kandidat siap ditetapkan (tab Penerima).
     *
     * @param array<string, mixed> $filters
     * @param bool                 $canViewEconomy
     * @param string|null          $viewerNomorTaruna
     * @return array<string, mixed>
     */
    public function listPenerima(
        array $filters = [],
        bool $canViewEconomy = false,
        ?string $viewerNomorTaruna = null,
        bool $paginate = true
    ): array {
        $baseQuery = $this->db->table('taruna_penerima_beasiswa tpb')
            ->select('tpb.*, prog.nama_program, prog.jenis_beasiswa, prog.pemberi, prog.nominal,
                      ta.kode_tahun AS tahun_akademik_label,
                      mt.nama_lengkap, ps.kode_prodi, ps.nama_prodi, kls.nama_kelas')
            ->join('taruna_program_beasiswa prog', 'prog.id = tpb.program_beasiswa_id', 'inner')
            ->join('ref_tahun_akademik ta', 'ta.id = prog.tahun_akademik_id', 'left')
            ->join('master_taruna mt', 'mt.nomor_taruna = tpb.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = tpb.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_kelas kls', 'kls.id = pt.kelas_id', 'left');

        if ($viewerNomorTaruna !== null && $viewerNomorTaruna !== '') {
            $baseQuery->where('tpb.nomor_taruna', $viewerNomorTaruna);
        }

        $progId    = (int) ($filters['program_beasiswa_id'] ?? 0);
        $jenis     = trim((string) ($filters['jenis_beasiswa'] ?? 'all'));
        $penetapan = trim((string) ($filters['status_penetapan'] ?? 'all'));
        $validasi  = trim((string) ($filters['status_validasi'] ?? 'all'));
        $prodiId   = (int) ($filters['prodi_id'] ?? 0);
        $tahunId   = trim((string) ($filters['tahun_akademik_id'] ?? 'all'));
        $q         = trim((string) ($filters['q'] ?? ''));

        if ($progId > 0) {
            $baseQuery->where('tpb.program_beasiswa_id', $progId);
        }
        if ($jenis !== '' && $jenis !== 'all') {
            $baseQuery->where('prog.jenis_beasiswa', $jenis);
        }
        if ($penetapan !== '' && $penetapan !== 'all') {
            $baseQuery->where('tpb.status_penetapan', $penetapan);
        }
        if ($validasi !== '' && $validasi !== 'all') {
            $baseQuery->where('tpb.status_validasi', $validasi);
        }
        if ($prodiId > 0) {
            $baseQuery->where('pt.program_studi_id', $prodiId);
        }
        if ($tahunId !== '' && $tahunId !== 'all' && (int) $tahunId > 0) {
            $baseQuery->where('prog.tahun_akademik_id', (int) $tahunId);
        }
        if ($q !== '') {
            $baseQuery->groupStart()
                ->like('mt.nama_lengkap', $q)
                ->orLike('tpb.nomor_taruna', $q)
                ->orLike('tpb.nomor_keputusan', $q)
                ->orLike('prog.nama_program', $q)
                ->groupEnd();
        }

        $sortBy    = trim((string) ($filters['sort_by'] ?? ''));
        $sortOrder = strtolower(trim((string) ($filters['sort_order'] ?? 'desc'))) === 'asc' ? 'ASC' : 'DESC';
        $sortMap   = [
            'taruna'            => 'mt.nama_lengkap',
            'program'           => 'prog.nama_program',
            'hasil_verifikasi'  => 'tpb.hasil_verifikasi',
            'status_penetapan'  => 'tpb.status_penetapan',
            'status_validasi'   => 'tpb.status_validasi',
            'tanggal_penetapan' => 'tpb.tanggal_penetapan',
        ];
        if (isset($sortMap[$sortBy])) {
            $baseQuery->orderBy($sortMap[$sortBy], $sortOrder)->orderBy('tpb.id', 'DESC');
        } else {
            $baseQuery
                ->orderBy("FIELD(tpb.status_penetapan, 'ditetapkan', 'kandidat', 'dibatalkan')", '', false)
                ->orderBy('tpb.tanggal_penetapan', 'DESC')
                ->orderBy('tpb.id', 'DESC');
        }

        $allRows = $baseQuery->get()->getResultArray();

        $disahkan = 0;
        $unikMap  = [];
        $siapSk   = 0;
        $batal    = 0;

        foreach ($allRows as $idx => $r) {
            $canSeeRow = $canViewEconomy || ($viewerNomorTaruna !== null && strcasecmp($viewerNomorTaruna, (string) $r['nomor_taruna']) === 0);
            $allRows[$idx] = $this->maskRowEconomy($r, $canSeeRow);

            $tetap = (string) ($r['status_penetapan'] ?? 'kandidat');
            $valid = (string) ($r['status_validasi'] ?? 'draf');
            $verif = (string) ($r['hasil_verifikasi'] ?? 'belum_diperiksa');

            if ($tetap === 'ditetapkan' && $valid === 'tervalidasi') {
                $disahkan++;
                $unikMap[(string) $r['nomor_taruna']] = true;
            } elseif ($tetap === 'kandidat' && $verif === 'memenuhi') {
                $siapSk++;
            } elseif ($tetap === 'dibatalkan') {
                $batal++;
            }
        }

        $totalFiltered = count($allRows);
        $perPage       = max(1, (int) ($filters['per_page'] ?? 15));
        $totalPages    = max(1, (int) ceil($totalFiltered / $perPage));
        $page          = max(1, min($totalPages, (int) ($filters['page'] ?? 1)));
        $offset        = ($page - 1) * $perPage;
        $rows          = $paginate ? array_slice($allRows, $offset, $perPage) : $allRows;

        return [
            'summary' => [
                'penetapan_disahkan' => $disahkan,
                'penerima_unik'      => count($unikMap),
                'siap_penetapan'     => $siapSk,
                'dibatalkan'         => $batal,
                'total_rows'         => $totalFiltered,
            ],
            'rows'       => $rows,
            'pagination' => [
                'page'           => $page,
                'per_page'       => $perPage,
                'total_pages'    => $totalPages,
                'total_filtered' => $totalFiltered,
                'from'           => $totalFiltered > 0 ? ($paginate ? $offset + 1 : 1) : 0,
                'to'             => $paginate ? min($offset + $perPage, $totalFiltered) : $totalFiltered,
            ],
        ];
    }

    /**
     * Mengambil rincian lengkap satu pengajuan / penerima beasiswa (untuk Verifikasi & Detail Penerima).
     *
     * @param int         $id
     * @param bool        $canViewEconomy
     * @param string|null $viewerNomorTaruna
     * @return array<string, mixed>|null
     */
    public function getDetail(int $id, bool $canViewEconomy = false, ?string $viewerNomorTaruna = null): ?array
    {
        $row = $this->db->table('taruna_penerima_beasiswa tpb')
            ->select('tpb.*, prog.nama_program, prog.jenis_beasiswa, prog.pemberi, prog.nominal,
                      prog.kuota, prog.persyaratan, prog.penanggung_jawab, prog.aturan_seri,
                      ta.kode_tahun AS tahun_akademik_label,
                      mt.nama_lengkap, mt.jenis_kelamin, mt.status_terkini,
                      pt.tingkat, pt.semester_aktif, ps.kode_prodi, ps.nama_prodi,
                      kls.nama_kelas, ang.tahun_masuk AS angkatan')
            ->join('taruna_program_beasiswa prog', 'prog.id = tpb.program_beasiswa_id', 'inner')
            ->join('ref_tahun_akademik ta', 'ta.id = prog.tahun_akademik_id', 'left')
            ->join('master_taruna mt', 'mt.nomor_taruna = tpb.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = tpb.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_kelas kls', 'kls.id = pt.kelas_id', 'left')
            ->join('ref_angkatan ang', 'ang.id = pt.angkatan_id', 'left')
            ->where('tpb.id', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }

        $isOwner = $viewerNomorTaruna !== null && strcasecmp($viewerNomorTaruna, (string) $row['nomor_taruna']) === 0;
        $canSeeSensitive = $canViewEconomy || $isOwner;

        $dokumenList = $this->buildDocumentList($row, $canSeeSensitive);
        $checklist   = json_decode((string) ($row['checklist_verifikasi_json'] ?? '{}'), true);
        $riwayat     = json_decode((string) ($row['riwayat_proses_json'] ?? '[]'), true);
        $maskedRow   = $this->maskRowEconomy($row, $canSeeSensitive);

        return [
            'penerima'         => $maskedRow,
            'dokumen'          => $dokumenList,
            'checklist'        => is_array($checklist) ? $checklist : [],
            'riwayat'          => is_array($riwayat) ? $riwayat : [],
            'can_view_economy' => $canSeeSensitive,
            'is_sensitive'     => ($row['jenis_beasiswa'] ?? '') === 'tidak_mampu',
            'wilayah_3tp_match'=> ($row['jenis_beasiswa'] ?? '') === 'daerah_3tp'
                ? $this->matchWilayah3tp((string) ($row['kode_wilayah'] ?? ''), (string) ($row['provinsi'] ?? ''), (string) ($row['kabupaten_kota'] ?? ''))
                : null,
        ];
    }

    /**
     * Menetapkan penerima beasiswa dengan nomor keputusan & berkas SK (hanya jika hasil_verifikasi = memenuhi).
     *
     * @param int                  $id
     * @param array<string, mixed> $input
     * @param string|null          $dokumenKeputusan
     * @param string               $actorLabel
     * @return array<string, mixed>
     */
    public function savePenetapan(int $id, array $input, ?string $dokumenKeputusan, string $actorLabel): array
    {
        $row = $this->penerimaModel->find($id);
        if (!$row) {
            return ['status' => false, 'message' => 'Data penerima beasiswa tidak ditemukan.'];
        }
        if (($row['status_penetapan'] ?? '') === 'dibatalkan') {
            return ['status' => false, 'message' => 'Pengajuan yang telah dibatalkan tidak dapat ditetapkan kembali.'];
        }
        if (($row['status_penetapan'] ?? '') === 'ditetapkan' && ($row['status_validasi'] ?? '') === 'tervalidasi') {
            return ['status' => false, 'message' => 'Keputusan penetapan penerima ini sudah disahkan sebelumnya.'];
        }
        if (($row['hasil_verifikasi'] ?? '') !== 'memenuhi') {
            return ['status' => false, 'message' => 'Penerima hanya dapat ditetapkan setelah hasil verifikasi berstatus Memenuhi.'];
        }

        $program = $this->programModel->find((int) $row['program_beasiswa_id']);
        $kuota   = $program ? (int) ($program['kuota'] ?? 0) : 0;
        if ($kuota > 0 && ($row['status_penetapan'] ?? '') !== 'ditetapkan') {
            $terisi = (int) $this->db->table('taruna_penerima_beasiswa')
                ->where('program_beasiswa_id', (int) $row['program_beasiswa_id'])
                ->where('status_penetapan', 'ditetapkan')
                ->where('status_validasi', 'tervalidasi')
                ->where('id !=', $id)
                ->countAllResults();
            if ($terisi >= $kuota) {
                return ['status' => false, 'message' => 'Kuota program beasiswa telah terpenuhi (' . $terisi . ' / ' . $kuota . ').'];
            }
        }

        $nomorSk  = trim((string) ($input['nomor_keputusan'] ?? ''));
        $tglSk    = trim((string) ($input['tanggal_penetapan'] ?? ''));
        $pejabat  = trim((string) ($input['pejabat_penetap'] ?? ''));
        $catatan  = trim((string) ($input['catatan_penetapan'] ?? ''));
        $aksi     = strtolower(trim((string) ($input['aksi_penetapan'] ?? 'tetapkan_validasi')));

        if ($nomorSk === '' || $tglSk === '' || $pejabat === '') {
            return ['status' => false, 'message' => 'Nomor keputusan, tanggal penetapan, dan pejabat penetap wajib diisi.'];
        }

        $today   = date('Y-m-d');
        $riwayat = json_decode((string) ($row['riwayat_proses_json'] ?? '[]'), true);
        if (!is_array($riwayat)) {
            $riwayat = [];
        }

        $isValidated = ($aksi !== 'tetapkan_saja');
        $riwayat[]   = [
            'tanggal' => $today,
            'pelaku'  => $actorLabel,
            'aksi'    => $isValidated ? 'Menetapkan & Mengesahkan SK Penerima' : 'Mencatat Penetapan SK (Menunggu Validasi)',
            'status'  => $isValidated ? 'tervalidasi' : 'diajukan',
            'catatan' => 'Keputusan ' . $nomorSk . ' oleh ' . $pejabat . ($catatan !== '' ? ' — ' . $catatan : ''),
        ];

        $update = [
            'status_pengajuan'    => 'diterima',
            'status_penetapan'    => 'ditetapkan',
            'tanggal_penetapan'   => $tglSk,
            'nomor_keputusan'     => $nomorSk,
            'pejabat_penetap'     => $pejabat,
            'catatan_penetapan'   => $catatan,
            'status_validasi'     => $isValidated ? 'tervalidasi' : 'diajukan',
            'riwayat_proses_json' => json_encode($riwayat, JSON_UNESCAPED_UNICODE),
        ];

        if ($dokumenKeputusan !== null && $dokumenKeputusan !== '') {
            $update['dokumen_keputusan'] = $dokumenKeputusan;
        }
        if ($isValidated) {
            $update['divalidasi_oleh']  = $actorLabel;
            $update['tanggal_validasi'] = $today;
        }

        $this->db->transStart();
        try {
            $this->penerimaModel->update($id, $update);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan penetapan penerima beasiswa.'];
            }

            return [
                'status'  => true,
                'message' => $isValidated
                    ? 'Penerima beasiswa resmi ditetapkan dan disahkan.'
                    : 'SK penetapan tercatat dan menunggu validasi akhir.',
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Terjadi kendala saat menyimpan penetapan penerima.'];
        }
    }

    /**
     * Membatalkan penetapan penerima beasiswa dan mengeluarkannya dari hitungan resmi.
     *
     * @param int    $id
     * @param string $alasan
     * @param string $actorLabel
     * @return array<string, mixed>
     */
    public function cancelPenetapan(int $id, string $alasan, string $actorLabel): array
    {
        $row = $this->penerimaModel->find($id);
        if (!$row) {
            return ['status' => false, 'message' => 'Data penerima beasiswa tidak ditemukan.'];
        }
        if (($row['status_penetapan'] ?? '') !== 'ditetapkan') {
            return ['status' => false, 'message' => 'Hanya penerima yang telah ditetapkan yang dapat dibatalkan penetapannya.'];
        }

        $alasanClean = trim($alasan);
        if ($alasanClean === '') {
            return ['status' => false, 'message' => 'Alasan pembatalan wajib diisi.'];
        }

        $today   = date('Y-m-d');
        $riwayat = json_decode((string) ($row['riwayat_proses_json'] ?? '[]'), true);
        if (!is_array($riwayat)) {
            $riwayat = [];
        }
        $riwayat[] = [
            'tanggal' => $today,
            'pelaku'  => $actorLabel,
            'aksi'    => 'Membatalkan Penetapan Beasiswa',
            'status'  => 'dibatalkan',
            'catatan' => $alasanClean,
        ];

        $this->db->transStart();
        try {
            $this->penerimaModel->update($id, [
                'status_penetapan'    => 'dibatalkan',
                'status_pengajuan'    => 'ditolak',
                'status_validasi'     => 'tervalidasi',
                'catatan_penetapan'   => $alasanClean,
                'divalidasi_oleh'     => $actorLabel,
                'tanggal_validasi'    => $today,
                'riwayat_proses_json' => json_encode($riwayat, JSON_UNESCAPED_UNICODE),
            ]);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal membatalkan penetapan beasiswa.'];
            }

            return ['status' => true, 'message' => 'Penetapan beasiswa telah dibatalkan.'];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Gagal memproses pembatalan penetapan.'];
        }
    }

    /**
     * Menghapus pengajuan beasiswa yang masih berstatus draf (bukan penerima ditetapkan).
     *
     * @param int         $id
     * @param string|null $viewerNomorTaruna
     * @return array<string, mixed>
     */
    public function deleteDraft(int $id, ?string $viewerNomorTaruna = null): array
    {
        $row = $this->penerimaModel->find($id);
        if (!$row) {
            return ['status' => false, 'message' => 'Pengajuan beasiswa tidak ditemukan.'];
        }
        if ($viewerNomorTaruna !== null && strcasecmp((string) $row['nomor_taruna'], $viewerNomorTaruna) !== 0) {
            return ['status' => false, 'message' => 'Anda hanya dapat menghapus draf milik Anda sendiri.'];
        }
        if (($row['status_validasi'] ?? '') !== 'draf' || ($row['status_penetapan'] ?? '') === 'ditetapkan') {
            return ['status' => false, 'message' => 'Hanya pengajuan berstatus Draf yang dapat dihapus.'];
        }

        // Kumpulkan berkas fisik lokal lama (jika ada) untuk dibersihkan setelah delete berhasil
        $localFiles = [];
        foreach (['sertifikat_berkas', 'dokumentasi_berkas', 'dokumen_domisili', 'dokumen_pendukung_3tp', 'dokumen_keputusan'] as $col) {
            $val = trim((string) ($row[$col] ?? ''));
            if ($val !== '' && !ketarunaan_is_http_url($val, 2000)) {
                $localFiles[] = basename($val);
            }
        }
        if (!empty($row['dokumen_ekonomi_json'])) {
            $ekoDocs = json_decode((string) $row['dokumen_ekonomi_json'], true);
            if (is_array($ekoDocs)) {
                foreach ($ekoDocs as $ed) {
                    $val = trim((string) ($ed['nama_berkas'] ?? ''));
                    if ($val !== '' && !ketarunaan_is_http_url($val, 2000)) {
                        $localFiles[] = basename($val);
                    }
                }
            }
        }

        $this->db->transStart();
        try {
            $this->penerimaModel->delete($id);
            $this->db->transComplete();

            if ($this->db->transStatus() !== false) {
                $uploadDir = FCPATH . 'assets/uploads/ketarunaan/beasiswa/';
                foreach (array_unique($localFiles) as $fname) {
                    if ($fname !== '' && is_file($uploadDir . $fname)) {
                        @unlink($uploadDir . $fname);
                    }
                }
            }

            return ['status' => true, 'message' => 'Draf pengajuan beasiswa berhasil dihapus.'];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Gagal menghapus draf pengajuan.'];
        }
    }

    /**
     * Menyiapkan header dan baris ekspor CSV penerima beasiswa sesuai batas privasi ekonomi peran (UAT-19).
     *
     * @param array<string, mixed> $filters
     * @param bool                 $canViewEconomy
     * @return array<string, mixed>
     */
    public function getExportRows(array $filters, bool $canViewEconomy): array
    {
        $listData = $this->listPenerima($filters, $canViewEconomy, null, false);
        $catalog  = ketarunaan_jenis_beasiswa_catalog();

        $headers = [
            'No',
            'Nomor Taruna',
            'Nama Taruna',
            'Program Studi',
            'Kelas',
            'Nama Program',
            'Jenis Beasiswa',
            'Pemberi',
            'Nominal',
            'Hasil Verifikasi',
            'Status Penetapan',
            'Status Validasi',
            'Nomor Keputusan',
            'Tanggal Penetapan',
        ];
        if ($canViewEconomy) {
            $headers[] = 'Pekerjaan Orang Tua';
            $headers[] = 'Penghasilan Bulanan';
            $headers[] = 'Jumlah Tanggungan';
        }

        $exportRows = [];
        foreach ($listData['rows'] as $idx => $r) {
            $jenisLabel = $catalog[$r['jenis_beasiswa'] ?? '']['label'] ?? ($r['jenis_beasiswa'] ?? '-');
            $line = [
                $idx + 1,
                $r['nomor_taruna'] ?? '',
                $r['nama_lengkap'] ?? '',
                $r['nama_prodi'] ?? '',
                $r['nama_kelas'] ?: ($r['kelas_snapshot'] ?? '-'),
                $r['nama_program'] ?? '',
                $jenisLabel,
                $r['pemberi'] ?? '',
                (int) ($r['nominal'] ?? 0),
                $r['hasil_verifikasi'] ?? '',
                $r['status_penetapan'] ?? '',
                $r['status_validasi'] ?? '',
                $r['nomor_keputusan'] ?: '-',
                $r['tanggal_penetapan'] ?: '-',
            ];
            if ($canViewEconomy) {
                $isSensitive = ($r['jenis_beasiswa'] ?? '') === 'tidak_mampu';
                $line[] = $isSensitive ? ($r['pekerjaan_orang_tua'] ?: '-') : '-';
                $line[] = $isSensitive && !empty($r['penghasilan_bulanan']) ? (int) $r['penghasilan_bulanan'] : '-';
                $line[] = $isSensitive && $r['jumlah_tanggungan'] !== null ? (int) $r['jumlah_tanggungan'] : '-';
            }
            $exportRows[] = $line;
        }

        return [
            'headers' => $headers,
            'rows'    => $exportRows,
        ];
    }

    /**
     * Memeriksa kecocokan kode wilayah atau kabupaten/provinsi pada daftar referensi 3TP aktif.
     *
     * @param string $kodeWilayah
     * @param string $provinsi
     * @param string $kabupatenKota
     * @return array<string, mixed>|null
     */
    protected function matchWilayah3tp(string $kodeWilayah, string $provinsi, string $kabupatenKota): ?array
    {
        $kodeClean = trim($kodeWilayah);
        if ($kodeClean !== '') {
            $byKode = $this->wilayah3tpModel
                ->where('kode_wilayah', $kodeClean)
                ->where('is_aktif', 1)
                ->first();
            if ($byKode) {
                return $byKode;
            }
        }

        $kabClean  = strtolower(trim($kabupatenKota));
        $provClean = strtolower(trim($provinsi));
        if ($kabClean === '') {
            return null;
        }

        $all = $this->wilayah3tpModel->where('is_aktif', 1)->findAll();
        foreach ($all as $item) {
            $dbKab  = strtolower(trim((string) $item['kabupaten_kota']));
            $dbProv = strtolower(trim((string) $item['provinsi']));
            $kabHit = ($dbKab === $kabClean)
                || str_contains($dbKab, str_replace(['kabupaten ', 'kab. ', 'kota '], '', $kabClean))
                || str_contains($kabClean, str_replace(['kabupaten ', 'kab. ', 'kota '], '', $dbKab));

            if ($kabHit && ($provClean === '' || $dbProv === $provClean)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Menyiapkan field dan validasi berkas wajib untuk klaim Beasiswa Prestasi (UAT-11).
     *
     * @param array<string, mixed>      $input
     * @param array<string, mixed>      $filesInfo
     * @param array<string, mixed>|null $existing
     * @param string                    $nomorTaruna
     * @param bool                      $isDraft
     * @return array<string, mixed>
     */
    protected function preparePrestasiClaimData(
        array $input,
        array $filesInfo,
        ?array $existing,
        string $nomorTaruna,
        bool $isDraft
    ): array {
        $prestasiId = (int) ($input['prestasi_id'] ?? 0);
        $linked     = null;

        if ($prestasiId > 0) {
            $linked = $this->db->table('taruna_prestasi tp')
                ->select('tp.*')
                ->join('taruna_peserta_prestasi tpp', 'tpp.prestasi_id = tp.id', 'inner')
                ->where('tp.id', $prestasiId)
                ->where('tpp.nomor_taruna', $nomorTaruna)
                ->where('tp.status_validasi', 'tervalidasi')
                ->get()
                ->getRowArray();

            if (!$linked) {
                return ['status' => false, 'message' => 'Prestasi tervalidasi milik taruna tersebut tidak ditemukan.'];
            }
        }

        $namaPrestasi   = trim((string) ($input['nama_prestasi'] ?? ($linked['nama_prestasi'] ?? '')));
        $kategori       = trim((string) ($input['kategori_prestasi'] ?? ($linked['jenis_prestasi'] ?? 'Akademik')));
        $tingkat        = trim((string) ($input['tingkat_prestasi'] ?? ($linked['tingkat_prestasi'] ?? 'Nasional')));
        $peringkat      = trim((string) ($input['peringkat_prestasi'] ?? ($linked['peringkat'] ?? '')));
        $tglPrestasi    = trim((string) ($input['tanggal_prestasi'] ?? ($linked['tanggal_mulai'] ?? '')));
        if ($tglPrestasi !== '' && !$this->isValidDateYmd($tglPrestasi)) {
            return ['status' => false, 'message' => 'Format tanggal kegiatan prestasi tidak valid.'];
        }
        $penyelenggara  = trim((string) ($input['penyelenggara_prestasi'] ?? ($linked['penyelenggara'] ?? '')));
        $uraian         = trim((string) ($input['uraian_pengajuan'] ?? ''));

        $sertifikatFile  = trim((string) ($filesInfo['sertifikat_berkas'] ?? ''));
        if ($sertifikatFile === '') {
            $sertifikatFile = trim((string) ($existing['sertifikat_berkas'] ?? ($linked['bukti_berkas'] ?? '')));
        }

        $dokumentasiFile = trim((string) ($filesInfo['dokumentasi_berkas'] ?? ''));
        if ($dokumentasiFile === '') {
            $dokumentasiFile = trim((string) ($existing['dokumentasi_berkas'] ?? ''));
            if ($dokumentasiFile === '' && $linked && !empty($linked['dokumentasi_json'])) {
                $dokArr = json_decode((string) $linked['dokumentasi_json'], true);
                if (is_array($dokArr) && !empty($dokArr[0])) {
                    if (is_array($dokArr[0])) {
                        $dokumentasiFile = trim((string) ($dokArr[0]['url'] ?? $dokArr[0]['nama'] ?? ''));
                    } else {
                        $dokumentasiFile = trim((string) $dokArr[0]);
                    }
                }
            }
        }

        if (!$isDraft) {
            if ($namaPrestasi === '' || $peringkat === '' || $penyelenggara === '') {
                return ['status' => false, 'message' => 'Nama prestasi, peringkat, dan penyelenggara wajib diisi sebelum mengirim pengajuan.'];
            }
            if ($sertifikatFile === '' || $dokumentasiFile === '') {
                return ['status' => false, 'message' => 'Klaim Beasiswa Prestasi wajib melampirkan tautan sertifikat dan dokumentasi kegiatan.'];
            }
        }

        return [
            'status' => true,
            'data'   => [
                'prestasi_id'            => $prestasiId > 0 ? $prestasiId : null,
                'kategori_prestasi'      => ucfirst($kategori),
                'nama_prestasi'          => $namaPrestasi,
                'tingkat_prestasi'       => ucfirst($tingkat),
                'peringkat_prestasi'     => $peringkat,
                'tanggal_prestasi'       => $tglPrestasi !== '' ? $tglPrestasi : null,
                'penyelenggara_prestasi' => $penyelenggara,
                'sertifikat_berkas'      => $sertifikatFile !== '' ? $sertifikatFile : null,
                'dokumentasi_berkas'     => $dokumentasiFile !== '' ? $dokumentasiFile : null,
                'uraian_pengajuan'       => $uraian,
            ],
        ];
    }

    /**
     * Menyiapkan field dan verifikasi acuan wilayah untuk klaim Beasiswa Daerah 3TP (UAT-12).
     *
     * @param array<string, mixed>      $input
     * @param array<string, mixed>      $filesInfo
     * @param array<string, mixed>|null $existing
     * @param bool                      $isDraft
     * @return array<string, mixed>
     */
    protected function prepareDaerah3tpClaimData(
        array $input,
        array $filesInfo,
        ?array $existing,
        bool $isDraft
    ): array {
        $alamat    = trim((string) ($input['alamat_domisili'] ?? ''));
        $provinsi  = trim((string) ($input['provinsi'] ?? ''));
        $kabKota   = trim((string) ($input['kabupaten_kota'] ?? ''));
        $kecamatan = trim((string) ($input['kecamatan'] ?? ''));
        $desa      = trim((string) ($input['desa_kelurahan'] ?? ''));
        $kodeWil   = trim((string) ($input['kode_wilayah'] ?? ''));
        $tglAcuan  = trim((string) ($input['tanggal_acuan_domisili'] ?? ''));
        if ($tglAcuan !== '' && !$this->isValidDateYmd($tglAcuan)) {
            return ['status' => false, 'message' => 'Format tanggal acuan domisili tidak valid.'];
        }
        $jnsDokDom = trim((string) ($input['jenis_dokumen_domisili'] ?? 'Kartu Keluarga'));
        $jnsDok3tp = trim((string) ($input['jenis_dokumen_pendukung_3tp'] ?? ''));
        $uraian    = trim((string) ($input['uraian_pengajuan'] ?? ''));

        $dokDomisili  = trim((string) ($filesInfo['dokumen_domisili'] ?? ''));
        if ($dokDomisili === '') {
            $dokDomisili = trim((string) ($existing['dokumen_domisili'] ?? ''));
        }
        $dokPendukung = trim((string) ($filesInfo['dokumen_pendukung_3tp'] ?? ''));
        if ($dokPendukung === '' && empty($input['hapus_dokumen_pendukung_3tp'])) {
            $dokPendukung = trim((string) ($existing['dokumen_pendukung_3tp'] ?? ''));
        }

        if (!$isDraft) {
            if ($alamat === '' || $provinsi === '' || $kabKota === '' || $tglAcuan === '') {
                return ['status' => false, 'message' => 'Alamat domisili, provinsi, kabupaten/kota, dan tanggal acuan domisili wajib diisi.'];
            }
            if ($dokDomisili === '') {
                return ['status' => false, 'message' => 'Tautan bukti domisili wajib diisi sebelum mengirim klaim Daerah 3TP.'];
            }
        }

        $match3tp = $this->matchWilayah3tp($kodeWil, $provinsi, $kabKota);

        return [
            'status' => true,
            'data'   => [
                'alamat_domisili'            => $alamat,
                'provinsi'                   => $provinsi,
                'kabupaten_kota'             => $kabKota,
                'kecamatan'                  => $kecamatan,
                'desa_kelurahan'             => $desa,
                'kode_wilayah'               => $kodeWil !== '' ? $kodeWil : ($match3tp['kode_wilayah'] ?? ''),
                'tanggal_acuan_domisili'     => $tglAcuan !== '' ? $tglAcuan : null,
                'acuan_wilayah_3tp'          => $match3tp['dasar_acuan'] ?? null,
                'versi_acuan_3tp'            => $match3tp['versi_acuan'] ?? null,
                'status_pemeriksaan_3tp'     => $match3tp ? 'menunggu_verifikasi' : 'acuan_belum_ada',
                'dokumen_domisili'           => $dokDomisili !== '' ? $dokDomisili : null,
                'jenis_dokumen_domisili'     => $jnsDokDom,
                'dokumen_pendukung_3tp'      => $dokPendukung !== '' ? $dokPendukung : null,
                'jenis_dokumen_pendukung_3tp'=> $jnsDok3tp !== '' ? $jnsDok3tp : null,
                'uraian_pengajuan'           => $uraian,
            ],
        ];
    }

    /**
     * Menyiapkan field ekonomi & multi-dokumen terbatas untuk klaim Beasiswa Tidak Mampu (UAT-13).
     *
     * @param array<string, mixed>      $input
     * @param array<string, mixed>      $filesInfo
     * @param array<string, mixed>|null $existing
     * @param bool                      $isDraft
     * @return array<string, mixed>
     */
    protected function prepareTidakMampuClaimData(
        array $input,
        array $filesInfo,
        ?array $existing,
        bool $isDraft
    ): array {
        $pekerjaan   = trim((string) ($input['pekerjaan_orang_tua'] ?? ''));
        $penghasilan = $this->sanitizePositiveInt($input['penghasilan_bulanan'] ?? 0);
        $tanggungan  = max(0, (int) ($input['jumlah_tanggungan'] ?? 0));
        $uraian      = trim((string) ($input['uraian_pengajuan'] ?? ''));

        $dokList = [];
        $removeIndices = array_map('intval', (array) ($input['remove_ekonomi_indices'] ?? []));
        if ($existing && !empty($existing['dokumen_ekonomi_json'])) {
            $oldDocs = json_decode((string) $existing['dokumen_ekonomi_json'], true);
            if (is_array($oldDocs)) {
                foreach ($oldDocs as $idxOld => $od) {
                    if (in_array((int) $idxOld, $removeIndices, true)) {
                        continue;
                    }
                    $dokList[] = $od;
                }
            }
        }

        $newUploads = $filesInfo['dokumen_ekonomi_list'] ?? [];
        if (is_array($newUploads)) {
            foreach ($newUploads as $up) {
                if (!empty($up['nama_berkas'])) {
                    $dokList[] = $up;
                }
            }
        }

        if (!$isDraft) {
            if ($pekerjaan === '' || $tanggungan <= 0 || $uraian === '') {
                return ['status' => false, 'message' => 'Pekerjaan orang tua, jumlah tanggungan, dan uraian kondisi ekonomi wajib diisi.'];
            }
            if (empty($dokList)) {
                return ['status' => false, 'message' => 'Lampirkan minimal satu tautan dokumen pendukung ekonomi sebelum mengirim klaim.'];
            }
        }

        return [
            'status' => true,
            'data'   => [
                'pekerjaan_orang_tua'  => $pekerjaan,
                'penghasilan_bulanan'  => $penghasilan,
                'jumlah_tanggungan'    => $tanggungan,
                'uraian_pengajuan'     => $uraian,
                'dokumen_ekonomi_json' => json_encode(array_values($dokList), JSON_UNESCAPED_UNICODE),
            ],
        ];
    }

    /**
     * Mengambil metrik pendukung dari basis data untuk penyelesaian seri Apresiasi sesuai aturan resmi.
     *
     * @param array<int, string> $nomorTarunaList
     * @return array<string, array<string, float|int>>
     */
    protected function buildTieBreakMetrics(array $nomorTarunaList): array
    {
        if (empty($nomorTarunaList)) {
            return [];
        }

        $metrics = [];
        foreach ($nomorTarunaList as $nim) {
            $metrics[$nim] = [
                'avg_ip'            => 0.0,
                'prestasi_count'    => 0,
                'pelanggaran_count' => 0,
            ];
        }

        $ipAvgRows = $this->db->table('taruna_nilai_ip')
            ->select('nomor_taruna, AVG(ip_semester) AS avg_ip')
            ->whereIn('nomor_taruna', $nomorTarunaList)
            ->where('status_validasi', 'tervalidasi')
            ->groupBy('nomor_taruna')
            ->get()
            ->getResultArray();
        foreach ($ipAvgRows as $ir) {
            $metrics[$ir['nomor_taruna']]['avg_ip'] = round((float) $ir['avg_ip'], 3);
        }

        $presRows = $this->db->table('taruna_peserta_prestasi tpp')
            ->select('tpp.nomor_taruna, COUNT(tp.id) AS total')
            ->join('taruna_prestasi tp', 'tp.id = tpp.prestasi_id', 'inner')
            ->whereIn('tpp.nomor_taruna', $nomorTarunaList)
            ->where('tp.status_validasi', 'tervalidasi')
            ->groupBy('tpp.nomor_taruna')
            ->get()
            ->getResultArray();
        foreach ($presRows as $pr) {
            $metrics[$pr['nomor_taruna']]['prestasi_count'] = (int) $pr['total'];
        }

        $pelRows = $this->db->table('taruna_pelanggaran')
            ->select('nomor_taruna, COUNT(id) AS total')
            ->whereIn('nomor_taruna', $nomorTarunaList)
            ->where('status_penetapan', 'terbukti')
            ->where('status_validasi', 'tervalidasi')
            ->groupBy('nomor_taruna')
            ->get()
            ->getResultArray();
        foreach ($pelRows as $pl) {
            $metrics[$pl['nomor_taruna']]['pelanggaran_count'] = (int) $pl['total'];
        }

        return $metrics;
    }

    /**
     * Menyusun daftar berkas lampiran terstruktur beserta flag kendali akses dokumen sensitif.
     *
     * @param array<string, mixed> $row
     * @param bool                 $canViewEconomy
     * @return array<int, array<string, mixed>>
     */
    protected function buildDocumentList(array $row, bool $canViewEconomy): array
    {
        $docs  = [];
        $jenis = (string) ($row['jenis_beasiswa'] ?? '');
        $id    = (int) $row['id'];

        $makeEntry = static function (
            string $kategori,
            string $keterangan,
            string $rawVal,
            bool $sensitif,
            bool $canAccess
        ) use ($id): ?array {
            $clean = trim($rawVal);
            if ($clean === '') {
                return null;
            }
            $isUrl = ketarunaan_is_http_url($clean, 2000);
            $rawId = $isUrl ? $clean : basename($clean);
            if ($rawId === '') {
                return null;
            }
            $label = $canAccess
                ? ($isUrl ? ketarunaan_format_bukti_label($clean) : $rawId)
                : 'Berkas Terbatas';

            return [
                'kategori'      => $kategori,
                'keterangan'    => $keterangan,
                'nama_berkas'   => $canAccess ? $rawId : 'Berkas_Terbatas',
                'display_label' => $label,
                'is_url'        => $isUrl,
                'sensitif'      => $sensitif,
                'can_access'    => $canAccess,
                'download_url'  => $canAccess
                    ? ($isUrl ? $clean : base_url('ketarunaan/beasiswa/unduh-dokumen/' . $id . '?file=' . rawurlencode($rawId)))
                    : '#',
                'preview_url'   => $canAccess
                    ? ($isUrl ? $clean : base_url('ketarunaan/beasiswa/unduh-dokumen/' . $id . '?file=' . rawurlencode($rawId) . '&inline=1'))
                    : '#',
            ];
        };

        $nonEconomyRestricted = ($jenis === 'tidak_mampu' && !$canViewEconomy);

        if (!empty($row['sertifikat_berkas'])) {
            $entry = $makeEntry('Sertifikat Prestasi', (string) ($row['nama_prestasi'] ?: 'Piagam / Sertifikat'), (string) $row['sertifikat_berkas'], false, !$nonEconomyRestricted);
            if ($entry) {
                $docs[] = $entry;
            }
        }
        if (!empty($row['dokumentasi_berkas'])) {
            $entry = $makeEntry('Dokumentasi Kegiatan', 'Foto / Bukti Kegiatan Prestasi', (string) $row['dokumentasi_berkas'], false, !$nonEconomyRestricted);
            if ($entry) {
                $docs[] = $entry;
            }
        }
        if (!empty($row['dokumen_domisili'])) {
            $entry = $makeEntry((string) ($row['jenis_dokumen_domisili'] ?: 'Bukti Domisili 3TP'), trim(($row['kabupaten_kota'] ?? '') . ', ' . ($row['provinsi'] ?? ''), ', '), (string) $row['dokumen_domisili'], false, !$nonEconomyRestricted);
            if ($entry) {
                $docs[] = $entry;
            }
        }
        if (!empty($row['dokumen_pendukung_3tp'])) {
            $entry = $makeEntry((string) ($row['jenis_dokumen_pendukung_3tp'] ?: 'Pendukung Domisili 3TP'), 'Lampiran pendukung wilayah', (string) $row['dokumen_pendukung_3tp'], false, !$nonEconomyRestricted);
            if ($entry) {
                $docs[] = $entry;
            }
        }

        if (!empty($row['dokumen_ekonomi_json'])) {
            $ekoDocs = json_decode((string) $row['dokumen_ekonomi_json'], true);
            if (is_array($ekoDocs)) {
                foreach ($ekoDocs as $ed) {
                    $rawEko = trim((string) ($ed['nama_berkas'] ?? ''));
                    if ($rawEko === '') {
                        continue;
                    }
                    $ketEko = trim(($ed['nomor_dokumen'] ?? '') . (!empty($ed['penerbit']) ? ' - ' . $ed['penerbit'] : ''), ' -');
                    $entry  = $makeEntry((string) ($ed['jenis_dokumen'] ?? 'Dokumen Ekonomi'), $ketEko !== '' ? $ketEko : 'Bukti Kondisi Ekonomi', $rawEko, true, $canViewEconomy);
                    if ($entry) {
                        $docs[] = $entry;
                    }
                }
            }
        }

        if (!empty($row['dokumen_keputusan'])) {
            $entry = $makeEntry('Surat Keputusan Penerima', (string) ($row['nomor_keputusan'] ?: 'SK Penetapan'), (string) $row['dokumen_keputusan'], false, true);
            if ($entry) {
                $docs[] = $entry;
            }
        }

        return $docs;
    }

    /**
     * Menyamarkan data ekonomi sensitif untuk peran yang tidak memiliki kewenangan (NFR-02 & UAT-13).
     *
     * @param array<string, mixed> $row
     * @param bool                 $canViewEconomy
     * @return array<string, mixed>
     */
    protected function maskRowEconomy(array $row, bool $canViewEconomy): array
    {
        $row['economy_masked'] = false;
        if ($canViewEconomy || ($row['jenis_beasiswa'] ?? '') !== 'tidak_mampu') {
            return $row;
        }

        $row['economy_masked']      = true;
        $row['pekerjaan_orang_tua'] = 'Akses Terbatas';
        $row['penghasilan_bulanan'] = null;
        $row['jumlah_tanggungan']   = null;
        $row['uraian_pengajuan']    = 'Rincian kondisi ekonomi hanya dapat diakses oleh pengelola berwenang atau pemilik pengajuan.';
        $row['dokumen_ekonomi_json'] = null;

        return $row;
    }

    /**
     * Membersihkan input nominal rupiah atau bilangan bulat positif.
     *
     * @param mixed $val
     * @return int
     */
    protected function sanitizePositiveInt($val): int
    {
        $clean = preg_replace('/[^\d]/', '', (string) ($val ?? '0'));
        return max(0, (int) $clean);
    }

    /**
     * Memvalidasi format tanggal ISO Y-m-d secara ketat (mencegah tanggal kalender tidak sah).
     *
     * @param string $date
     * @return bool
     */
    protected function isValidDateYmd(string $date): bool
    {
        $dt = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        return $dt !== false && $dt->format('Y-m-d') === $date;
    }
}
