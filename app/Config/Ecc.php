<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Ecc extends BaseConfig
{
    /**
     * Daftar Program Studi yang digunakan di seluruh aplikasi.
     * @var list<string>
     */
    public array $prodiList = ['RSTJ', 'TRO', 'TO'];
}