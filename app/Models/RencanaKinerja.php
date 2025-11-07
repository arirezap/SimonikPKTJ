<?php

namespace App\Models;

use CodeIgniter\Model;

class RencanaKinerja extends Model
{
    protected $table            = 'rencana_kinerja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields    = [
        'user_id',
        'sasaran_program',
        'indikator_kinerja',
        'satuan',
        'target_utama',
        'kegiatan',
        'target_bulanan',
        'realisasi_bulanan',
        'tahun_anggaran'
    ];
    
    // PERBAIKAN: Tambahkan Callbacks untuk JSON
    protected $allowCallbacks = true;
    protected $afterFind      = ['decodeJsonColumns'];
    protected $beforeInsert   = ['encodeJsonColumns'];
    protected $beforeUpdate   = ['encodeJsonColumns'];

    /**
     * Mengubah kolom JSON (string) menjadi array (array) setelah data diambil.
     */
    protected function decodeJsonColumns(array $data)
    {
        if (empty($data['data'])) {
            return $data;
        }

        // Jika ini adalah satu data (find())
        if ($data['singleton']) {
            $data['data'] = $this->jsonHandler($data['data']);
        } else {
            // Jika ini adalah banyak data (findAll())
            foreach ($data['data'] as $key => $row) {
                $data['data'][$key] = $this->jsonHandler($row);
            }
        }
        return $data;
    }

    /**
     * Mengubah kolom array (array) menjadi JSON (string) sebelum disimpan.
     */
    protected function encodeJsonColumns(array $data)
    {
        if (isset($data['data']['target_bulanan']) && is_array($data['data']['target_bulanan'])) {
            $data['data']['target_bulanan'] = json_encode($data['data']['target_bulanan']);
        }
        if (isset($data['data']['realisasi_bulanan']) && is_array($data['data']['realisasi_bulanan'])) {
            $data['data']['realisasi_bulanan'] = json_encode($data['data']['realisasi_bulanan']);
        }
        return $data;
    }
    
    /**
     * Helper privat untuk $afterFind
     */
    private function jsonHandler(array $row): array
    {
        if (isset($row['target_bulanan']) && is_string($row['target_bulanan'])) {
            $row['target_bulanan'] = json_decode($row['target_bulanan'], true) ?? array_fill(0, 12, 0);
        } else if (!isset($row['target_bulanan'])) {
            $row['target_bulanan'] = array_fill(0, 12, 0);
        }
        
        if (isset($row['realisasi_bulanan']) && is_string($row['realisasi_bulanan'])) {
            $row['realisasi_bulanan'] = json_decode($row['realisasi_bulanan'], true) ?? array_fill(0, 12, null);
        } else if (!isset($row['realisasi_bulanan'])) {
             $row['realisasi_bulanan'] = array_fill(0, 12, null);
        }

        return $row;
    }
}