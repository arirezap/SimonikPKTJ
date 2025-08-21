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
$routes->group('admin', ['filter' => 'auth:admin,manajemen'], static function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('monitoring', 'Admin\MonitoringController::index');
    $routes->get('monitoring/detail/(:num)/(:num)', 'Admin\MonitoringController::detail/$1/$2');
    $routes->get('monitoring/excel/(:num)/(:num)', 'Admin\MonitoringController::exportExcel/$1/$2');
    $routes->get('monitoring/pdf/(:num)/(:num)', 'Admin\MonitoringController::exportPdf/$1/$2');

    $routes->get('users', 'Admin\UserController::index');
    $routes->post('users/store', 'Admin\UserController::store');
    $routes->post('users/update/(:num)', 'Admin\UserController::update/$1');
    $routes->post('users/delete/(:num)', 'Admin\UserController::delete/$1');

    // PERBAIKAN: Menambahkan grup untuk Master Data
    $routes->group('master-data', static function ($routes) {

        $routes->get('sasaran', 'Admin\MasterDataController::sasaran');
        $routes->post('sasaran/store', 'Admin\MasterDataController::storeSasaran');
        // RUTE BARU UNTUK UPDATE & DELETE
        $routes->post('sasaran/update/(:num)', 'Admin\MasterDataController::updateSasaran/$1');
        $routes->post('sasaran/delete/(:num)', 'Admin\MasterDataController::deleteSasaran/$1');

        $routes->get('indikator', 'Admin\MasterDataController::indikator');
        $routes->post('indikator/store', 'Admin\MasterDataController::storeIndikator');
        $routes->get('satuan', 'Admin\MasterDataController::satuan');
        $routes->post('satuan/store', 'Admin\MasterDataController::storeSatuan');
    });
});

// --- Grup Rute User ---
$routes->group('user', ['filter' => 'auth:admin,manajemen,aak,kuk'], static function ($routes) {
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
