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
$routes->group('admin', ['filter' => 'auth:admin'], static function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('monitoring', 'Admin\Monitoring::index');
});

// --- Grup Rute User ---
// di dalam app/Config/Routes.php

$routes->group('user', ['filter' => 'auth:user'], static function ($routes) {
    $routes->get('dashboard', 'User\Dashboard::index');

    // Alur Input Rencana
    $routes->get('rencana/input', 'User\InputRencana::index');
    $routes->post('rencana/store', 'User\InputRencana::store');

    // Alur Kelola Rencana
    $routes->get('kinerja/update', 'User\DaftarRencana::index');
    $routes->post('rencana/update/(:num)', 'User\DaftarRencana::update/$1');

    // PERBAIKI BARIS INI: Pastikan menunjuk ke DaftarRencana::delete
    $routes->post('rencana/delete/(:num)', 'User\DaftarRencana::delete/$1');

    // Alur Alokasi Bulanan
    $routes->get('alokasi/bulanan', 'User\AlokasiController::index');
    $routes->post('alokasi/update', 'User\AlokasiController::update');
});
