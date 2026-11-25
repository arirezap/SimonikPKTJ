<?php

namespace App\Libraries;

use App\Models\WilayahModel;
use CodeIgniter\Database\BaseConnection;

/**
 * Layanan terpusat referensi wilayah Indonesia (Provinsi & Kabupaten/Kota)
 * berbasis dataset resmi https://github.com/cahyadsn/wilayah (Kepmendagri 2025).
 *
 * Menyediakan pencarian cepat, pemetaan alias nama lama (mis. DI Yogyakarta / NTB / Kab.),
 * serta dukungan siap-pakai untuk Level 3 (Kecamatan) & Level 4 (Desa/Kelurahan).
 */
class WilayahService
{
    protected BaseConnection $db;
    protected WilayahModel $model;

    /** @var array<string, string>|null */
    private static ?array $memoryMap = null;

    /**
     * Alias umum nama provinsi dari formulir lama / dokumen eksternal -> kode 2 digit Kepmendagri.
     *
     * @var array<string, string>
     */
    private const PROVINSI_ALIASES = [
        'dki jakarta'              => '31',
        'jakarta'                  => '31',
        'di yogyakarta'            => '34',
        'diy'                      => '34',
        'yogyakarta'               => '34',
        'bangka belitung'          => '19',
        'babel'                    => '19',
        'kepri'                    => '21',
        'ntb'                      => '52',
        'ntt'                      => '53',
        'papua pegunungan tengah'  => '95',
        'nad'                      => '11',
        'nanggroe aceh darussalam' => '11',
    ];

    public function __construct(?BaseConnection $db = null, ?WilayahModel $model = null)
    {
        $this->db    = $db ?? \Config\Database::connect();
        $this->model = $model ?? new WilayahModel();
    }

    /**
     * Memuat seluruh peta Level 1 (38 Provinsi) & Level 2 (514 Kabupaten/Kota) ke memori proses.
     * Prioritas dari tabel ref_wilayah; fallback ke berkas data statis bila migrasi belum dijalankan.
     *
     * @return array<string, string> kode => nama
     */
    public function getLevel12Map(): array
    {
        if (self::$memoryMap !== null) {
            return self::$memoryMap;
        }

        $map = [];
        try {
            if ($this->db->tableExists('ref_wilayah')) {
                $rows = $this->db->table('ref_wilayah')
                    ->select('kode, nama')
                    ->where('CHAR_LENGTH(kode) <=', 5)
                    ->orderBy('kode', 'ASC')
                    ->get()
                    ->getResultArray();

                foreach ($rows as $row) {
                    $k = trim((string) ($row['kode'] ?? ''));
                    $n = trim((string) ($row['nama'] ?? ''));
                    if ($k !== '' && $n !== '') {
                        $map[$k] = $n;
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('warning', 'WilayahService fallback ke data statis: ' . $e->getMessage());
        }

        if (empty($map)) {
            $dataFile = APPPATH . 'Database/Data/wilayah_level_1_2.php';
            if (is_file($dataFile)) {
                $loaded = require $dataFile;
                if (is_array($loaded)) {
                    foreach ($loaded as $k => $n) {
                        $map[(string) $k] = (string) $n;
                    }
                }
            }
        }

        self::$memoryMap = $map;
        return self::$memoryMap;
    }

    /**
     * Mengembalikan daftar 38 Provinsi Indonesia terurut sesuai kode Kepmendagri.
     *
     * @return list<array{kode:string, nama:string}>
     */
    public function getProvinsiList(): array
    {
        $out = [];
        foreach ($this->getLevel12Map() as $kode => $nama) {
            if (strlen($kode) === 2) {
                $out[] = ['kode' => $kode, 'nama' => $nama];
            }
        }
        return $out;
    }

    /**
     * Mengembalikan daftar nama 38 Provinsi saja (list<string>).
     *
     * @return list<string>
     */
    public function getProvinsiNames(): array
    {
        return array_map(static fn (array $p): string => $p['nama'], $this->getProvinsiList());
    }

    /**
     * Mengembalikan daftar Kabupaten/Kota (semua 514 atau difilter provinsi & kata kunci).
     *
     * @param string|null $provinsiKodeOrNama Kode 2 digit (mis. '33') atau nama provinsi (mis. 'Jawa Tengah')
     * @param string|null $keyword            Filter pencarian nama opsional
     * @return list<array{kode:string, nama:string, provinsi_kode:string, provinsi_nama:string}>
     */
    public function getKabupatenList(?string $provinsiKodeOrNama = null, ?string $keyword = null): array
    {
        $map      = $this->getLevel12Map();
        $provKode = null;
        if ($provinsiKodeOrNama !== null && trim($provinsiKodeOrNama) !== '') {
            $provKode = $this->resolveProvinsiKode($provinsiKodeOrNama);
            if ($provKode === null) {
                return [];
            }
        }

        $qLower = ($keyword !== null && trim($keyword) !== '') ? mb_strtolower(trim($keyword)) : '';
        $out    = [];

        foreach ($map as $kode => $nama) {
            if (strlen($kode) !== 5) {
                continue;
            }
            $pk = substr($kode, 0, 2);
            if ($provKode !== null && $pk !== $provKode) {
                continue;
            }
            if ($qLower !== '' && !str_contains(mb_strtolower($nama), $qLower)) {
                continue;
            }
            $out[] = [
                'kode'          => $kode,
                'nama'          => $nama,
                'provinsi_kode' => $pk,
                'provinsi_nama' => $map[$pk] ?? '',
            ];
        }

        return $out;
    }

    /**
     * Payload ringkas Provinsi + Kabupaten/Kota untuk pemuatan 1 kali pada sisi klien.
     *
     * @return array{
     *   provinsi: list<array{kode:string, nama:string}>,
     *   kabupaten: list<array{kode:string, nama:string, provinsi_kode:string, provinsi_nama:string}>
     * }
     */
    public function getAllPayload(): array
    {
        return [
            'provinsi'  => $this->getProvinsiList(),
            'kabupaten' => $this->getKabupatenList(),
        ];
    }

    /**
     * Mengembalikan daftar Kecamatan berdasarkan kode Kabupaten/Kota (xx.xx) bila data Level 3 tersedia di ref_wilayah.
     *
     * @return list<array{kode:string, nama:string}>
     */
    public function getKecamatanList(string $kabKode): array
    {
        $clean = trim($kabKode);
        if (!preg_match('/^\d{2}\.\d{2}$/', $clean) || !$this->db->tableExists('ref_wilayah')) {
            return [];
        }

        $rows = $this->db->table('ref_wilayah')
            ->select('kode, nama')
            ->like('kode', $clean . '.', 'after')
            ->where('CHAR_LENGTH(kode)', 8)
            ->orderBy('nama', 'ASC')
            ->get()
            ->getResultArray();

        return array_map(static fn (array $r): array => [
            'kode' => (string) $r['kode'],
            'nama' => (string) $r['nama'],
        ], $rows);
    }

    /**
     * Mengembalikan daftar Desa/Kelurahan berdasarkan kode Kecamatan (xx.xx.xx) bila data Level 4 tersedia di ref_wilayah.
     *
     * @return list<array{kode:string, nama:string}>
     */
    public function getDesaList(string $kecKode): array
    {
        $clean = trim($kecKode);
        if (!preg_match('/^\d{2}\.\d{2}\.\d{2}$/', $clean) || !$this->db->tableExists('ref_wilayah')) {
            return [];
        }

        $rows = $this->db->table('ref_wilayah')
            ->select('kode, nama')
            ->like('kode', $clean . '.', 'after')
            ->where('CHAR_LENGTH(kode)', 13)
            ->orderBy('nama', 'ASC')
            ->get()
            ->getResultArray();

        return array_map(static fn (array $r): array => [
            'kode' => (string) $r['kode'],
            'nama' => (string) $r['nama'],
        ], $rows);
    }

    /**
     * Mencocokkan kode (2 digit) atau nama provinsi (termasuk alias singkatan umum) ke kode 2 digit Kepmendagri.
     */
    public function resolveProvinsiKode(string $input): ?string
    {
        $clean = trim($input);
        if ($clean === '') {
            return null;
        }

        $map = $this->getLevel12Map();
        if (preg_match('/^\d{2}$/', $clean) && isset($map[$clean])) {
            return $clean;
        }

        $low = mb_strtolower(preg_replace('/\s+/', ' ', $clean) ?? $clean);
        if (isset(self::PROVINSI_ALIASES[$low]) && isset($map[self::PROVINSI_ALIASES[$low]])) {
            return self::PROVINSI_ALIASES[$low];
        }

        foreach ($map as $kode => $nama) {
            if (strlen($kode) === 2 && mb_strtolower($nama) === $low) {
                return $kode;
            }
        }

        // Pencocokan parsial aman bila diawali "Provinsi ..."
        $stripped = trim((string) preg_replace('/^provinsi\s+/i', '', $low));
        if ($stripped !== $low) {
            if (isset(self::PROVINSI_ALIASES[$stripped])) {
                return self::PROVINSI_ALIASES[$stripped];
            }
            foreach ($map as $kode => $nama) {
                if (strlen($kode) === 2 && mb_strtolower($nama) === $stripped) {
                    return $kode;
                }
            }
        }

        return null;
    }

    /**
     * Mencocokkan kode (xx.xx) atau nama Kabupaten/Kota (mendukung variasi "Kab." / "Kabupaten").
     *
     * @return array{kode:string, nama:string, provinsi_kode:string, provinsi_nama:string}|null
     */
    public function findKabupaten(string $input, ?string $provKodeOrNama = null): ?array
    {
        $clean = trim($input);
        if ($clean === '') {
            return null;
        }

        $map = $this->getLevel12Map();
        if (preg_match('/^\d{2}\.\d{2}$/', $clean) && isset($map[$clean])) {
            $pk = substr($clean, 0, 2);
            return [
                'kode'          => $clean,
                'nama'          => $map[$clean],
                'provinsi_kode' => $pk,
                'provinsi_nama' => $map[$pk] ?? '',
            ];
        }

        $provKode = ($provKodeOrNama !== null && trim($provKodeOrNama) !== '')
            ? $this->resolveProvinsiKode($provKodeOrNama)
            : null;

        $norm = $this->normalizeRegionName($clean);
        foreach ($map as $kode => $nama) {
            if (strlen($kode) !== 5) {
                continue;
            }
            $pk = substr($kode, 0, 2);
            if ($provKode !== null && $pk !== $provKode) {
                continue;
            }
            if ($this->normalizeRegionName($nama) === $norm) {
                return [
                    'kode'          => $kode,
                    'nama'          => $nama,
                    'provinsi_kode' => $pk,
                    'provinsi_nama' => $map[$pk] ?? '',
                ];
            }
        }

        return null;
    }

    private function normalizeRegionName(string $name): string
    {
        $low = mb_strtolower(trim($name));
        $low = preg_replace('/^kab\.\s*/i', 'kabupaten ', $low) ?? $low;
        $low = preg_replace('/^kab\s+/i', 'kabupaten ', $low) ?? $low;
        $low = preg_replace('/\s+/', ' ', $low) ?? $low;
        return trim($low);
    }
}
