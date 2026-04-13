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
    protected $allowedFields    = ['nama_unit', 'parent_unit'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'nama_unit'   => 'required|is_unique[unit_kerja.nama_unit,id,{id}]|max_length[255]',
        'parent_unit' => 'permit_empty|in_list[aak,kuk]',
    ];
    protected $validationMessages   = [
        'nama_unit' => [
            'required'   => 'Nama unit kerja tidak boleh kosong.',
            'is_unique'  => 'Nama unit kerja sudah ada.',
            'max_length' => 'Nama unit kerja terlalu panjang.',
        ],
        'parent_unit' => [
            'in_list' => 'Penanggung jawab harus AAK atau KUK.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
