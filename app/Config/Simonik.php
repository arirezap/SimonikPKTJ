<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Simonik extends BaseConfig
{
    /**
     * Daftar Program Studi yang digunakan di seluruh aplikasi.
     * @var list<string>
     */
    public array $prodiList = ['RSTJ', 'TRO', 'TO'];
}