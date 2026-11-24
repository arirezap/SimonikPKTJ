<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitKerja extends Model
{
    protected $table            = 'unit_kerja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_unit', 'parent_unit', 'parent_unit_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'nama_unit'      => 'required|is_unique[unit_kerja.nama_unit,id,{id}]|max_length[255]',
        'parent_unit'    => 'permit_empty|in_list[aak,kuk,pimpinan]',
        'parent_unit_id' => 'permit_empty|is_natural_no_zero',
    ];
    protected $validationMessages   = [
        'nama_unit' => [
            'required'   => 'Nama unit kerja tidak boleh kosong.',
            'is_unique'  => 'Nama unit kerja sudah ada.',
            'max_length' => 'Nama unit kerja terlalu panjang.',
        ],
        'parent_unit' => [
            'in_list' => 'Penanggung jawab harus Top Manajemen, AAK, atau KUK.',
        ],
        'parent_unit_id' => [
            'is_natural_no_zero' => 'Unit induk langsung tidak valid.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Menentukan apakah baris unit kerja merupakan Unit Induk Bagian (AAK / KUK).
     */
    public static function isUnitIndukBagian(array $unit): bool
    {
        $parent = strtolower(trim((string) ($unit['parent_unit'] ?? '')));
        $nama   = strtolower(trim((string) ($unit['nama_unit'] ?? '')));

        return in_array($parent, ['aak', 'kuk'], true) && str_starts_with($nama, 'bagian ');
    }

    /**
     * Mengambil daftar unit operasional yang dapat menjadi induk sub-unit (Tim Substansi, dll.).
     */
    public function getPotentialParentUnits(?int $excludeId = null): array
    {
        $rows = $this->whereIn('parent_unit', ['aak', 'kuk'])
                     ->where('parent_unit_id IS NULL', null, false)
                     ->orderBy('nama_unit', 'ASC')
                     ->findAll();

        $result = [];
        foreach ($rows as $row) {
            if ($excludeId !== null && (int) $row['id'] === $excludeId) {
                continue;
            }
            if (self::isUnitIndukBagian($row)) {
                continue;
            }
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Mengambil daftar ID sub-unit (mis. Pokja) di bawah suatu unit induk.
     *
     * @return int[]
     */
    public function getSubUnitIds(int $parentUnitId): array
    {
        if ($parentUnitId <= 0) {
            return [];
        }

        $rows = $this->select('id')
                     ->where('parent_unit_id', $parentUnitId)
                     ->findAll();

        return array_map(static fn (array $r): int => (int) $r['id'], $rows);
    }

    /**
     * Peta nama sub-unit => nama unit induknya (untuk penanda di direktori & daftar pegawai).
     *
     * @return array<string, string>
     */
    public function getParentUnitNameMap(): array
    {
        $rows = $this->select('unit_kerja.nama_unit, induk.nama_unit AS parent_nama_unit')
                     ->join('unit_kerja AS induk', 'induk.id = unit_kerja.parent_unit_id', 'inner')
                     ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $map[(string) $r['nama_unit']] = (string) $r['parent_nama_unit'];
        }

        return $map;
    }

    /**
     * Mengambil seluruh data unit kerja beserta jumlah pegawai langsung,
     * informasi Unit Induk Bagian, relasi Sub-Unit (Pokja -> Tim Substansi),
     * serta urutan pohon hierarki yang rapi.
     */
    public function getUnitsWithUserCount()
    {
        $rows = $this->select('unit_kerja.*, COUNT(users.id) as jumlah_pegawai')
                     ->join('users', 'users.unit_id = unit_kerja.id', 'left')
                     ->groupBy('unit_kerja.id')
                     ->orderBy('unit_kerja.nama_unit', 'ASC')
                     ->findAll();

        $byId = [];
        foreach ($rows as $r) {
            $byId[(int) $r['id']] = $r;
        }

        $subUnitCountsByBagian = ['aak' => 0, 'kuk' => 0];
        $operasionalUnitCountsByBagian = ['aak' => 0, 'kuk' => 0];
        $totalByParent = ['aak' => 0, 'kuk' => 0, 'pimpinan' => 0];
        $childrenByParentId = [];
        $childEmpSumByParentId = [];

        foreach ($rows as $row) {
            $id       = (int) $row['id'];
            $parent   = strtolower(trim((string) ($row['parent_unit'] ?? '')));
            $parentId = !empty($row['parent_unit_id']) ? (int) $row['parent_unit_id'] : null;
            $jml      = (int) ($row['jumlah_pegawai'] ?? 0);

            if (isset($totalByParent[$parent])) {
                $totalByParent[$parent] += $jml;
            }

            if ($parentId !== null && isset($byId[$parentId])) {
                $childrenByParentId[$parentId][] = $id;
                $childEmpSumByParentId[$parentId] = ($childEmpSumByParentId[$parentId] ?? 0) + $jml;
                if (isset($subUnitCountsByBagian[$parent])) {
                    $subUnitCountsByBagian[$parent]++;
                }
            } elseif (in_array($parent, ['aak', 'kuk'], true) && !self::isUnitIndukBagian($row)) {
                $operasionalUnitCountsByBagian[$parent]++;
            }
        }

        $enrichedById = [];
        foreach ($rows as $row) {
            $id           = (int) $row['id'];
            $parent       = strtolower(trim((string) ($row['parent_unit'] ?? '')));
            $parentId     = !empty($row['parent_unit_id']) ? (int) $row['parent_unit_id'] : null;
            $isInduk      = self::isUnitIndukBagian($row);
            $isSubUnit    = ($parentId !== null && isset($byId[$parentId]));
            $jmlLangsung  = (int) ($row['jumlah_pegawai'] ?? 0);
            $subIds       = $childrenByParentId[$id] ?? [];
            $subEmpSum    = $childEmpSumByParentId[$id] ?? 0;

            $row['is_unit_induk']              = $isInduk;
            $row['is_sub_unit']                = $isSubUnit;
            $row['parent_unit_id']             = $isSubUnit ? $parentId : null;
            $row['parent_unit_name']           = $isSubUnit ? (string) ($byId[$parentId]['nama_unit'] ?? '') : '';
            $row['jumlah_pegawai_langsung']    = $jmlLangsung;
            $row['sub_unit_count']             = count($subIds);
            $row['sub_unit_employee_count']    = $subEmpSum;
            $row['total_pegawai_dengan_sub']   = $jmlLangsung + $subEmpSum;
            $row['jumlah_unit_binaan']         = $isInduk ? ($operasionalUnitCountsByBagian[$parent] ?? 0) : 0;
            $row['jumlah_subunit_binaan']      = $isInduk ? ($subUnitCountsByBagian[$parent] ?? 0) : 0;
            $row['total_pegawai_binaan']       = $isInduk ? ($totalByParent[$parent] ?? $jmlLangsung) : $jmlLangsung;

            $enrichedById[$id] = $row;
        }

        // Susun pohon urutan: Top Manajemen -> Unit Induk Bagian -> Unit Pelaksana (dengan Sub-Unit tepat di bawah induknya)
        $topLevel = [];
        $childrenGrouped = [];
        foreach ($enrichedById as $item) {
            if (!empty($item['is_sub_unit'])) {
                $childrenGrouped[(int) $item['parent_unit_id']][] = $item;
            } else {
                $topLevel[] = $item;
            }
        }

        usort($topLevel, static function (array $a, array $b): int {
            $rank = static function (array $u): int {
                $p = strtolower(trim((string) ($u['parent_unit'] ?? '')));
                if ($p === 'pimpinan') {
                    return 1;
                }
                if (!empty($u['is_unit_induk'])) {
                    return 2;
                }
                return 3;
            };

            $ra = $rank($a);
            $rb = $rank($b);
            if ($ra !== $rb) {
                return $ra <=> $rb;
            }

            return strcasecmp((string) ($a['nama_unit'] ?? ''), (string) ($b['nama_unit'] ?? ''));
        });

        $ordered = [];
        foreach ($topLevel as $item) {
            $ordered[] = $item;
            $cid = (int) $item['id'];
            if (!empty($childrenGrouped[$cid])) {
                usort($childrenGrouped[$cid], static fn (array $a, array $b): int => strcasecmp((string) ($a['nama_unit'] ?? ''), (string) ($b['nama_unit'] ?? '')));
                foreach ($childrenGrouped[$cid] as $child) {
                    $ordered[] = $child;
                }
            }
        }

        return $ordered;
    }

    /**
     * Menyusun ringkasan 3 kelompok hierarki utama (Top Manajemen, AAK, KUK)
     * untuk tampilan Master Unit Kerja.
     */
    public function getHierarchyOverview(array $units): array
    {
        $summary = [
            'pimpinan' => ['label' => 'Top Manajemen', 'subtitle' => 'Direktur & Wakil Direktur', 'unit_count' => 0, 'sub_unit_count' => 0, 'employee_count' => 0],
            'aak'      => ['label' => 'Bagian AAK', 'subtitle' => 'Administrasi Akademik & Ketarunaan', 'unit_count' => 0, 'sub_unit_count' => 0, 'employee_count' => 0],
            'kuk'      => ['label' => 'Bagian KUK', 'subtitle' => 'Keuangan, Umum & Kerjasama', 'unit_count' => 0, 'sub_unit_count' => 0, 'employee_count' => 0],
        ];

        foreach ($units as $u) {
            $parent = strtolower(trim((string) ($u['parent_unit'] ?? '')));
            if (!isset($summary[$parent])) {
                continue;
            }
            $summary[$parent]['employee_count'] += (int) ($u['jumlah_pegawai'] ?? 0);
            if (!empty($u['is_sub_unit'])) {
                $summary[$parent]['sub_unit_count']++;
            } elseif ($parent === 'pimpinan' || empty($u['is_unit_induk'])) {
                $summary[$parent]['unit_count']++;
            }
        }

        return $summary;
    }
}
