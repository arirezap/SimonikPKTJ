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
    
    // Callbacks
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

        if ($data['singleton']) {
            $data['data'] = $this->jsonHandler($data['data']);
        } else {
            foreach ($data['data'] as $key => $row) {
                $data['data'][$key] = $this->jsonHandler($row);
            }
        }
        return $data;
    }

    /**
     * Mengubah kolom array menjadi JSON sebelum disimpan.
     * UPDATE: Menambahkan pengecekan is_array agar tidak double-encode jika Controller sudah mengirim string.
     */
    protected function encodeJsonColumns(array $data)
    {
        // Target Bulanan
        if (isset($data['data']['target_bulanan'])) {
            if (is_array($data['data']['target_bulanan'])) {
                $data['data']['target_bulanan'] = json_encode($data['data']['target_bulanan']);
            }
        }

        // Realisasi Bulanan
        if (isset($data['data']['realisasi_bulanan'])) {
            if (is_array($data['data']['realisasi_bulanan'])) {
                $data['data']['realisasi_bulanan'] = json_encode($data['data']['realisasi_bulanan']);
            }
        }

        return $data;
    }
    
    private function jsonHandler(array $row): array
    {
        // Decode Target
        if (isset($row['target_bulanan'])) {
            if (is_string($row['target_bulanan'])) {
                $decoded = json_decode($row['target_bulanan'], true);
                $row['target_bulanan'] = is_array($decoded) ? $decoded : array_fill(0, 12, 0);
            } elseif (!is_array($row['target_bulanan'])) {
                $row['target_bulanan'] = array_fill(0, 12, 0);
            }
        } else {
            $row['target_bulanan'] = array_fill(0, 12, 0);
        }
        
        // Decode Realisasi
        if (isset($row['realisasi_bulanan'])) {
            if (is_string($row['realisasi_bulanan'])) {
                $decoded = json_decode($row['realisasi_bulanan'], true);
                $row['realisasi_bulanan'] = is_array($decoded) ? $decoded : array_fill(0, 12, null);
            } elseif (!is_array($row['realisasi_bulanan'])) {
                $row['realisasi_bulanan'] = array_fill(0, 12, null);
            }
        } else {
             $row['realisasi_bulanan'] = array_fill(0, 12, null);
        }

        return $row;
    }
}