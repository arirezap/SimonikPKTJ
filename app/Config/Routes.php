<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// app/Config/Routes.php

// ...
$routes->setDefaultNamespace('App\Controllers');

// Rute Publik
$routes->get('/', 'Auth::index');
$routes->get('/login', 'Auth::index');
$routes->post('/login', 'Auth::prosesLogin');
$routes->get('/logout', 'Auth::logout');
$routes->get('/profile', 'Profile::index'); // Rute untuk profil

// Grup Rute Admin
$routes->group('admin', ['filter' => 'auth:admin'], static function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('monitoring', 'Admin\Monitoring::index');
});

// Grup Rute User
$routes->group('user', ['filter' => 'auth:user'], static function ($routes) {
    $routes->get('dashboard', 'User\Dashboard::index');
    $routes->get('rencana/input', 'User\InputRencana::index');
    $routes->get('kinerja/update', 'User\UpdateKinerja::index');
});