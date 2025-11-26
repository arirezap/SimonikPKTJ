<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// Auth
$routes->get('/login', 'Auth::index');
$routes->post('/login', 'Auth::prosesLogin');
$routes->get('/logout', 'Auth::logout');

// Profil
$routes->match(['get', 'post'], 'profile', 'Profile::index');
$routes->post('profile/update', 'Profile::update'); 

// --- GROUP ADMIN ---
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    
    // User Management
    $routes->get('users', 'Admin\UserController::index');
    $routes->post('users/store', 'Admin\UserController::store');
    $routes->post('users/update/(:num)', 'Admin\UserController::update/$1');
    $routes->post('users/delete/(:num)', 'Admin\UserController::delete/$1');

    // Remunerasi
    $routes->get('remunerasi', 'Admin\RemunerasiController::index');
    $routes->post('remunerasi/store', 'Admin\RemunerasiController::store');

    // Monitoring
    $routes->get('monitoring', 'Admin\MonitoringController::index');
    $routes->get('monitoring/excel/(:num)/(:any)', 'Admin\MonitoringController::exportExcel/$1/$2');
    $routes->get('monitoring/pdf/(:num)/(:any)', 'Admin\MonitoringController::exportPdf/$1/$2');

    // Master Data
    $routes->group('master-data', function($routes) {
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

        // LED Criteria
        $routes->get('led', 'Admin\MasterDataController::led');
        $routes->post('led/store', 'Admin\MasterDataController::storeLed');
        $routes->post('led/update/(:num)', 'Admin\MasterDataController::updateLed/$1');
        $routes->post('led/delete/(:num)', 'Admin\MasterDataController::deleteLed/$1');
        $routes->post('led/delete-batch', 'Admin\MasterDataController::deleteLedBatch');
        $routes->post('led/batch-update', 'Admin\MasterDataController::batchUpdateLed');
        $routes->get('led/export', 'Admin\MasterDataController::exportLed');
        $routes->post('led/import', 'Admin\MasterDataController::importLed');

        // LED Standar
        $routes->get('led-standar', 'Admin\MasterDataController::led_standar');
        $routes->post('led-standar/store', 'Admin\MasterDataController::storeStandar');
        $routes->post('led-standar/update/(:num)', 'Admin\MasterDataController::updateStandar/$1');
        $routes->post('led-standar/delete/(:num)', 'Admin\MasterDataController::deleteStandar/$1');
    });
});

// --- GROUP USER ---
$routes->group('user', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'User\Dashboard::index');

    // Rencana Kinerja & Realisasi
    $routes->get('rencana/input', 'User\InputRencana::index');
    $routes->post('rencana/store', 'User\InputRencana::store');
    $routes->post('rencana/update/(:num)', 'User\DaftarRencana::update/$1'); // Modal edit di daftar
    $routes->post('rencana/delete/(:num)', 'User\DaftarRencana::delete/$1');

    $routes->get('realisasi/input', 'User\InputRealisasi::index');
    $routes->post('realisasi/store', 'User\InputRealisasi::store');

    $routes->get('kinerja/update', 'User\DaftarRencana::index'); // Menu Kelola Target & Realisasi
    
    $routes->get('alokasi/bulanan', 'User\AlokasiController::index');
    $routes->post('alokasi/update', 'User\AlokasiController::update');

    // Keuangan
    $routes->get('keuangan/input', 'User\InputKeuangan::index');
    $routes->post('keuangan/store', 'User\InputKeuangan::store');

    // Akademik & Lainnya
    $routes->get('akademik', 'User\AkademikController::index');
    $routes->get('akademik/jadwal', 'User\AkademikController::jadwal');
    $routes->post('akademik/jadwal/store', 'User\AkademikController::storeJadwal');
    
    $routes->get('ketarunaan', 'User\KetarunaanController::index');
    
    $routes->get('diklat', 'User\DiklatController::index');
    $routes->post('diklat/store', 'User\DiklatController::store');
    $routes->post('diklat/update/(:num)', 'User\DiklatController::update/$1');
    $routes->post('diklat/delete/(:num)', 'User\DiklatController::delete/$1');
});

// --- GROUP ECC ---
$routes->group('ecc', ['filter' => 'auth'], function($routes) {
    // LED
    $routes->get('led', 'EccController::led');
    $routes->post('led/store', 'EccController::storeLed');
    
    // HAPUS LINK (Perbaikan: Menambahkan route ini agar error 404 hilang)
    $routes->get('deleteLedLink/(:num)', 'EccController::deleteLedLink/$1');
    $routes->get('delete-link/(:num)', 'EccController::deleteLedLink/$1'); // Cadangan jika pakai format dash

    // LKPS
    $routes->get('lkps', 'EccController::lkps');

    // Simulasi
    $routes->get('simulasi', 'EccController::simulasi');
    $routes->post('simulasi/store', 'EccController::storeSimulasi');

    // Detail Standar
    $routes->get('detail/(:num)/(:segment)/(:num)', 'EccController::detailStandar/$1/$2/$3');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}