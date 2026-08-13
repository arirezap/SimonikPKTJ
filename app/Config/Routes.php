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
// Admin Routes (Group)
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');

// Notifications
$routes->get('notifications/fetch', 'NotificationController::fetch');
$routes->post('notifications/read/(:segment)', 'NotificationController::markAsRead/$1');
    $routes->get('dashboard/api-detail-chart', 'Admin\Dashboard::apiDetailChart');
    $routes->get('monitoring', 'Admin\MonitoringController::index');
    $routes->get('monitoring/exportExcel/(:num)/(:num)', 'Admin\MonitoringController::exportExcel/$1/$2');
    $routes->get('monitoring/exportPdf/(:num)/(:num)', 'Admin\MonitoringController::exportPdf/$1/$2');

    $routes->get('remunerasi', 'Admin\RemunerasiController::index');
    $routes->post('remunerasi/store', 'Admin\RemunerasiController::store');
    
    // --- Rute untuk Kelola Pengguna (Users) & Daftar Pegawai ---
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('daftar-pegawai', 'User\DaftarPegawaiController::index');
    $routes->get('users/create', 'Admin\UserController::create');
    $routes->post('users/store', 'Admin\UserController::store');
    $routes->get('users/edit/(:num)', 'Admin\UserController::edit/$1');

    // Pengaturan Sistem
    $routes->get('settings', 'Admin\SettingsController::index');
    $routes->post('settings/store', 'Admin\SettingsController::store');
    $routes->post('users/update', 'Admin\UserController::update');
    $routes->post('users/reset-kinerja', 'Admin\UserController::resetKinerja');
    $routes->match(['get', 'post'], 'users/delete/(:num)', 'Admin\UserController::delete/$1');
    $routes->get('users/export', 'Admin\UserController::exportExcel'); // Rute untuk Export
    $routes->post('users/import', 'Admin\UserController::importExcel'); // Rute untuk Import
    $routes->post('users/ajax_update_unit', 'Admin\UserController::ajaxUpdateUnit'); // Rute untuk AJAX update unit kerja
    $routes->post('users/batch_update', 'Admin\UserController::batch_update'); // Rute untuk Batch Edit

    // Log Keamanan Aktivitas
    $routes->get('admin/audit-logs', 'Admin\AuditLogController::index');

    // Master Data Group
    $routes->group('master-data', function ($routes) {
        // Rute Hari Libur
        $routes->get('holidays', 'Admin\MasterDataController::holidays');
        $routes->post('holidays/sync', 'Admin\MasterDataController::syncHolidays');
        $routes->post('holidays/store', 'Admin\MasterDataController::storeHoliday');
        $routes->get('holidays/delete/(:num)', 'Admin\MasterDataController::deleteHoliday/$1');

        // Rute untuk Sasaran Program
        $routes->get('sasaran', 'Admin\MasterDataController::sasaran');
        $routes->post('sasaran/store', 'Admin\MasterDataController::storeSasaran');
        $routes->post('sasaran/update/(:num)', 'Admin\MasterDataController::updateSasaran/$1');
        $routes->match(['get', 'post'], 'sasaran/delete/(:num)', 'Admin\MasterDataController::deleteSasaran/$1');

        // Rute untuk Indikator Kinerja
        $routes->get('indikator', 'Admin\MasterDataController::indikator');
        $routes->post('indikator/store', 'Admin\MasterDataController::storeIndikator');
        $routes->post('indikator/update/(:num)', 'Admin\MasterDataController::updateIndikator/$1');
        $routes->match(['get', 'post'], 'indikator/delete/(:num)', 'Admin\MasterDataController::deleteIndikator/$1');

        // Rute untuk Satuan
        $routes->get('satuan', 'Admin\MasterDataController::satuan');
        $routes->post('satuan/store', 'Admin\MasterDataController::storeSatuan');
        $routes->post('satuan/update/(:num)', 'Admin\MasterDataController::updateSatuan/$1');
        $routes->match(['get', 'post'], 'satuan/delete/(:num)', 'Admin\MasterDataController::deleteSatuan/$1');

        // Rute untuk Unit Kerja
        $routes->get('unit-kerja', 'Admin\MasterDataController::unitKerja');
        $routes->post('unit-kerja/store', 'Admin\MasterDataController::storeUnitKerja');
        $routes->post('unit-kerja/update/(:num)', 'Admin\MasterDataController::updateUnitKerja/$1');
        $routes->match(['get', 'post'], 'unit-kerja/delete/(:num)', 'Admin\MasterDataController::deleteUnitKerja/$1');
        
        // Rute untuk Kriteria LED
        $routes->get('led', 'Admin\MasterDataController::led');
        $routes->post('led/store', 'Admin\MasterDataController::storeLed');
        $routes->post('led/update/(:num)', 'Admin\MasterDataController::updateLed/$1');
        $routes->match(['get', 'post'], 'led/delete/(:num)', 'Admin\MasterDataController::deleteLed/$1');
        $routes->post('led/deleteBatch', 'Admin\MasterDataController::deleteLedBatch');
        $routes->post('led/batchUpdate', 'Admin\MasterDataController::batchUpdateLed');
        $routes->get('led/export', 'Admin\MasterDataController::exportLed');
        $routes->post('led/import', 'Admin\MasterDataController::importLed');

        // Rute untuk Standar LED
        $routes->get('led-standar', 'Admin\MasterDataController::ledStandar');
        
        // Rute untuk Aksi (Store, Update, Delete) Standar LED
        $routes->post('led-standar/store', 'Admin\MasterDataController::storeStandar');
        $routes->post('led-standar/update/(:num)', 'Admin\MasterDataController::updateStandar/$1');
        $routes->match(['get', 'post'], 'led-standar/delete/(:num)', 'Admin\MasterDataController::deleteStandar/$1');
    });
});

// User Routes (Group)
$routes->group('', ['filter' => 'auth'], function ($routes) {
    // $routes->get('dashboard', 'User\Dashboard::index'); // Ditangani oleh DashboardController

    // Kinerja Group
    $routes->group('rencana', function ($routes) {
        $routes->get('input', 'User\InputRencana::index');
        $routes->post('store', 'User\InputRencana::store');
    });

    // Kontrak Kinerja
    $routes->get('kontrak', 'User\KontrakController::index');

    // Pakta Integritas (Pindahkan kesini biar rapi)
    $routes->get('pakta', 'User\PaktaController::index');

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

    // --- PERBAIKAN DISINI (MASUKKAN KE DALAM GRUP) ---
    // Hapus 'user/' di depannya karena sudah otomatis ikut grup user
    $routes->get('skp', 'User\Skp::index');          // URL: user/skp
    $routes->post('skp/store', 'User\Skp::store');   // URL: user/skp/store
    $routes->get('skp/detail/(:num)', 'User\Skp::detail/$1');
    // Di dalam $routes->group('user' ...
    $routes->post('skp/target/store', 'User\Skp::storeTarget');
    $routes->match(['get', 'post'], 'skp/delete/(:num)', 'User\Skp::delete/$1');
    // --- Kelola Tim ---
    $routes->get('tim', 'User\TimController::index');
    $routes->post('tim/add', 'User\TimController::addStaf');
    $routes->post('tim/remove', 'User\TimController::removeStaf');
    $routes->post('tim/update_unit', 'User\TimController::updateUnit');

    // --- Laporan Kinerja Harian ---
    $routes->match(['get', 'post'], 'laporan-harian', 'User\LaporanHarianController::index');
    $routes->post('laporan-harian/store', 'User\LaporanHarianController::store');
    $routes->post('laporan-harian/hapus', 'User\LaporanHarianController::hapus');
    $routes->post('laporan-harian/approve', 'User\LaporanHarianController::approve');
    $routes->post('laporan-harian/approve-all', 'User\LaporanHarianController::approveAll');
    $routes->post('laporan-harian/batal-approve', 'User\LaporanHarianController::cancelApprove'); // [SUPERADMIN] Batalkan persetujuan target

    // --- Log Kegiatan Harian ---
    $routes->match(['get', 'post'], 'log-kegiatan', 'User\LogKegiatanController::index');
    $routes->post('log-kegiatan/store', 'User\LogKegiatanController::store');
    $routes->post('log-kegiatan/hapus', 'User\LogKegiatanController::hapus');
    $routes->post('log-kegiatan/storeTugasTambahan', 'User\LogKegiatanController::storeTugasTambahan');
    $routes->post('log-kegiatan/hapusTugasTambahan', 'User\LogKegiatanController::hapusTugasTambahan');
    $routes->post('log-kegiatan/buka-kunci', 'User\LogKegiatanController::bukaKunci'); // [SUPERADMIN] Buka kunci laporan

    // --- Rekap & Penilaian Kinerja ---
    $routes->match(['get', 'post'], 'penilaian-kinerja', 'User\PenilaianKinerjaController::index');
    $routes->match(['get', 'post'], 'penilaian-staf', 'User\PenilaianKinerjaController::index');
    $routes->post('penilaian-kinerja/store', 'User\PenilaianKinerjaController::store');
    $routes->get('penilaian-kinerja/api-chart', 'User\PenilaianKinerjaController::getChartDataApi');

    // --- Panduan Penggunaan ---
    $routes->get('panduan', 'User\PanduanController::index');

});

// ECC Routes
$routes->group('ecc', ['filter' => 'auth'], function ($routes) {
    $routes->get('led', 'Admin\MasterDataController::eccLed');
    $routes->post('led/store', 'EccController::storeLed');
    $routes->match(['get', 'post'], 'deleteLedLink/(:num)', 'EccController::deleteLedLink/$1');

    $routes->get('simulasi', 'EccController::simulasi');
    $routes->post('simulasi/store', 'EccController::storeSimulasi');
    $routes->get('detailStandar/(:num)/(:any)/(:any)', 'EccController::detailStandar/$1/$2/$3');
    $routes->get('lkps', 'EccController::lkps');
});

// Kepegawaian Routes (Multi-Role: kepegawaian, admin)
$routes->group('kepegawaian', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Kepegawaian\DashboardKepegawaian::index');
    $routes->get('export-excel', 'Kepegawaian\DashboardKepegawaian::exportExcel');
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




$routes->get('test', 'TestController::index');

$routes->get('test3', 'TestController3::index');
