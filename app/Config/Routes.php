<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setDefaultNamespace('App\Controllers');

// Rute Publik (Hanya untuk yang belum login)
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
});

// --- Grup Rute User ---
$routes->group('user', ['filter' => 'auth:user'], static function ($routes) {
    $routes->get('dashboard', 'User\Dashboard::index');

    // Alur Input Rencana
    $routes->get('rencana/input', 'User\InputRencana::index');
    $routes->post('rencana/store', 'User\InputRencana::store');

    // RUTE BARU: Untuk Input Realisasi
    $routes->get('realisasi/input', 'User\InputRealisasi::index');
    $routes->post('realisasi/store', 'User\InputRealisasi::store');

    // Alur Kelola Rencana
    $routes->get('kinerja/update', 'User\DaftarRencana::index');
    $routes->post('rencana/update/(:num)', 'User\DaftarRencana::update/$1');
    $routes->post('rencana/delete/(:num)', 'User\DaftarRencana::delete/$1');

    // Alur Alokasi Bulanan
    $routes->get('alokasi/bulanan', 'User\AlokasiController::index');
    $routes->post('alokasi/update', 'User\AlokasiController::update');
});
