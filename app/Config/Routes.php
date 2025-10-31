<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setDefaultNamespace('App\Controllers');

// Rute Publik
$routes->get('/', 'Auth::index');
$routes->get('/login', 'Auth::index');
$routes->post('/login', 'Auth::prosesLogin');
$routes->get('/logout', 'Auth::logout');

// Rute yang Membutuhkan Login (Semua Peran)
$routes->get('/profile', 'Profile::index', ['filter' => 'auth']);
$routes->post('/profile', 'Profile::update', ['filter' => 'auth']);

// --- Grup Rute Admin ---
// Akses untuk Admin, Manajemen, dan Kabag
$routes->group('admin', ['filter' => 'auth:admin,manajemen,kabag_aak,kabag_kuk'], static function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('monitoring', 'Admin\MonitoringController::index');
    $routes->get('monitoring/excel/(:num)/(:num)', 'Admin\MonitoringController::exportExcel/$1/$2');
    $routes->get('monitoring/pdf/(:num)/(:num)', 'Admin\MonitoringController::exportPdf/$1/$2');

    $routes->get('users', 'Admin\UserController::index');
    $routes->post('users/store', 'Admin\UserController::store');
    $routes->post('users/update/(:num)', 'Admin\UserController::update/$1');
    $routes->post('users/delete/(:num)', 'Admin\UserController::delete/$1');

    // Grup Rute Master Data
    $routes->group('master-data', static function ($routes) {
        // Sasaran
        $routes->get('sasaran', 'Admin\MasterDataController::sasaran');
        $routes->post('sasaran/store', 'Admin\MasterDataController::storeSasaran');
        $routes->post('sasaran/update/(:num)', 'Admin\MasterDataController::updateSasaran/$1');
        $routes->post('sasaran/delete/(:num)', 'Admin\MasterDataController::deleteSasaran/$1');

        // Indikator
        $routes->get('indikator', 'Admin\MasterDataController::indikator');
        $routes->post('indikator/store', 'Admin\MasterDataController::storeIndikator');
        $routes->post('indikator/update/(:num)', 'Admin\MasterDataController::updateIndikator/$1');
        $routes->post('indikator/delete/(:num)', 'Admin\MasterDataController::deleteIndikator/$1');

        // Satuan
        $routes->get('satuan', 'Admin\MasterDataController::satuan');
        $routes->post('satuan/store', 'Admin\MasterDataController::storeSatuan');
        $routes->post('satuan/update/(:num)', 'Admin\MasterDataController::updateSatuan/$1');
        $routes->post('satuan/delete/(:num)', 'Admin\MasterDataController::deleteSatuan/$1');

        // Kriteria LED
        $routes->get('led', 'Admin\MasterDataController::led');
        $routes->post('led/store', 'Admin\MasterDataController::storeLed');
        $routes->post('led/update/(:num)', 'Admin\MasterDataController::updateLed/$1');
        $routes->post('led/delete/(:num)', 'Admin\MasterDataController::deleteLed/$1');
        $routes->post('led/delete-batch', 'Admin\MasterDataController::deleteLedBatch');
        $routes->post('led/import', 'Admin\MasterDataController::importLed');

        // Kategori LED
        $routes->get('led-kategori', 'Admin\MasterDataController::led_kategori');
        $routes->post('led-kategori/store', 'Admin\MasterDataController::storeKategori');
        $routes->post('led-kategori/update/(:num)', 'Admin\MasterDataController::updateKategori/$1');
        $routes->post('led-kategori/delete/(:num)', 'Admin\MasterDataController::deleteKategori/$1');
    });
});

// --- Grup Rute User ---
// Akses untuk Admin, Manajemen, Kabag, AAK, KUK, dan SPM
$routes->group('user', ['filter' => 'auth:admin,manajemen,kabag_aak,kabag_kuk,aak,kuk,spm'], static function ($routes) {
    $routes->get('dashboard', 'User\Dashboard::index');
    $routes->get('rencana/input', 'User\InputRencana::index');
    $routes->post('rencana/store', 'User\InputRencana::store');
    $routes->get('realisasi/input', 'User\InputRealisasi::index');
    $routes->post('realisasi/store', 'User\InputRealisasi::store');
    $routes->get('kinerja/update', 'User\DaftarRencana::index');
    $routes->post('rencana/update/(:num)', 'User\DaftarRencana::update/$1');
    $routes->post('rencana/delete/(:num)', 'User\DaftarRencana::delete/$1');
    $routes->get('alokasi/bulanan', 'User\AlokasiController::index');
    $routes->post('alokasi/update', 'User\AlokasiController::update');
    $routes->get('keuangan/input', 'User\InputKeuangan::index');
    $routes->post('keuangan/store', 'User\InputKeuangan::store');
    
    $routes->group('akademik', static function ($routes) {
        $routes->get('/', 'User\AkademikController::index');
        $routes->get('jadwal', 'User\AkademikController::jadwal');
        $routes->post('jadwal/store', 'User\AkademikController::storeJadwal');
    });

    $routes->get('ketarunaan', 'User\KetarunaanController::index');

    $routes->get('diklat', 'User\DiklatController::index');
    $routes->post('diklat/store', 'User\DiklatController::store');
    $routes->post('diklat/update/(:num)', 'User\DiklatController::update/$1');
    $routes->post('diklat/delete/(:num)', 'User\DiklatController::delete/$1');
});

// --- Grup Rute ECC ---
// PERBAIKAN: Rute ECC dipecah

// Rute ECC - Publik (Bisa diakses AAK, KUK, Manajemen, Admin, Kabag, dan SPM)
$routes->group('ecc', ['filter' => 'auth:admin,manajemen,kabag_aak,kabag_kuk,aak,kuk,spm'], static function ($routes) {
    $routes->get('/', 'EccController::index'); // Dashboard ECC
    $routes->get('lkps', 'EccController::lkps');
    // Rute LED dipindah ke sini agar bisa diakses AAK/KUK
    $routes->get('led', 'EccController::led');
    $routes->post('led/store', 'EccController::storeLed');
});

// Rute ECC - Terbatas (Hanya untuk SPM)
$routes->group('ecc', ['filter' => 'auth:spm'], static function ($routes) {
    $routes->get('simulasi', 'EccController::simulasi');
    $routes->post('simulasi/store', 'EccController::storeSimulasi');
});
