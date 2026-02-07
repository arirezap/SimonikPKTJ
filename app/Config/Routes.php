<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

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
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// Authentication Routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::processLogin');
$routes->get('logout', 'Auth::logout');

// Profile Routes
$routes->get('profile', 'Profile::index');
$routes->post('profile/update', 'Profile::update');
$routes->get('user/pakta', 'User\PaktaController::index');
// Admin Routes (Group)
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('monitoring', 'Admin\MonitoringController::index');
    $routes->get('remunerasi', 'Admin\RemunerasiController::index');
    // app/Config/Routes.php (Di dalam group 'admin')

    // app/Config/Routes.php (Di dalam group 'admin')

    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/create', 'Admin\UserController::create');   // Route Create Baru
    $routes->post('users/store', 'Admin\UserController::store');    // Route Store Baru
    $routes->get('users/edit/(:num)', 'Admin\UserController::edit/$1');
    $routes->post('users/update', 'Admin\UserController::update');
    $routes->get('users/delete/(:num)', 'Admin\UserController::delete/$1');
    // Master Data Group
    $routes->group('master-data', function ($routes) {
        $routes->get('sasaran', 'Admin\MasterDataController::sasaran');
        $routes->get('indikator', 'Admin\MasterDataController::indikator');
        $routes->get('satuan', 'Admin\MasterDataController::satuan');
        $routes->get('led', 'Admin\MasterDataController::led');
        $routes->get('led-standar', 'Admin\MasterDataController::ledStandar');
    });
});

// User Routes (Group)
$routes->group('user', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'User\Dashboard::index');

    // Kinerja Group
    $routes->group('rencana', function ($routes) {
        $routes->get('input', 'User\InputRencana::index');
        $routes->post('store', 'User\InputRencana::store');
    });

    // START UPDATE: Route untuk Kontrak Kinerja
    $routes->get('kontrak', 'User\KontrakController::index');
    // END UPDATE

    $routes->group('realisasi', function ($routes) {
        $routes->get('input', 'User\InputRealisasi::index');
        $routes->post('store', 'User\InputRealisasi::store');
    });

    $routes->group('kinerja', function ($routes) {
        $routes->get('update', 'User\AlokasiController::index');
    });

    $routes->group('keuangan', function ($routes) {
        $routes->get('input', 'User\InputKeuangan::index');
    });

    // Akademik & Data Lain
    $routes->group('akademik', function ($routes) {
        $routes->get('/', 'User\AkademikController::index');
        $routes->get('jadwal', 'User\AkademikController::jadwal');
    });

    $routes->get('ketarunaan', 'User\KetarunaanController::index');
    $routes->get('diklat', 'User\DiklatController::index');
});

// ECC Routes
$routes->group('ecc', ['filter' => 'auth'], function ($routes) {
    $routes->get('led', 'EccController::led');
    $routes->get('simulasi', 'EccController::simulasi');
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
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
