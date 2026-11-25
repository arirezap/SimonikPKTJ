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
$routes->setAutoRoute(false);

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
$routes->post('logout', 'Auth::logout');

// Tautan Singkat & Publik Tracer Study Alumni (contoh: /tracerstudy2026 atau /tracer/{token})
$routes->group('tracer', static function ($routes) {
    $routes->get('([a-zA-Z0-9\-]+)', 'Ketarunaan\TracerStudyPublicController::index/$1');
    $routes->post('([a-zA-Z0-9\-]+)/verifikasi', 'Ketarunaan\TracerStudyPublicController::verify/$1');
    $routes->post('([a-zA-Z0-9\-]+)/daftar', 'Ketarunaan\TracerStudyPublicController::register/$1');
    $routes->post('([a-zA-Z0-9\-]+)/simpan', 'Ketarunaan\TracerStudyPublicController::submit/$1');
    $routes->get('([a-zA-Z0-9\-]+)/selesai', 'Ketarunaan\TracerStudyPublicController::selesai/$1');
    $routes->post('([a-zA-Z0-9\-]+)/ganti-alumni', 'Ketarunaan\TracerStudyPublicController::resetSession/$1');
});
$routes->get('(tracer[a-z0-9\-]{2,48})', 'Ketarunaan\TracerStudyPublicController::index/$1');
$routes->post('(tracer[a-z0-9\-]{2,48})/verifikasi', 'Ketarunaan\TracerStudyPublicController::verify/$1');
$routes->post('(tracer[a-z0-9\-]{2,48})/daftar', 'Ketarunaan\TracerStudyPublicController::register/$1');
$routes->post('(tracer[a-z0-9\-]{2,48})/simpan', 'Ketarunaan\TracerStudyPublicController::submit/$1');
$routes->get('(tracer[a-z0-9\-]{2,48})/selesai', 'Ketarunaan\TracerStudyPublicController::selesai/$1');
$routes->post('(tracer[a-z0-9\-]{2,48})/ganti-alumni', 'Ketarunaan\TracerStudyPublicController::resetSession/$1');

// API Referensi Wilayah Indonesia (Read-Only, berbasis cahyadsn/wilayah Kepmendagri 2025)
$routes->group('api/wilayah', static function ($routes) {
    $routes->get('all', 'Api\WilayahController::all');
    $routes->get('provinsi', 'Api\WilayahController::provinsi');
    $routes->get('kabupaten', 'Api\WilayahController::kabupaten');
    $routes->get('kecamatan', 'Api\WilayahController::kecamatan');
    $routes->get('desa', 'Api\WilayahController::desa');
});

// Survei Pemetaan SDM (Eksklusif via Share Link — Khusus Pegawai ASN PKTJ)
$routes->group('survei-pemetaan', ['filter' => 'employee_only'], function ($routes) {
    $routes->get('/', 'User\SurveiPemetaanController::index');
    $routes->get('ringkasan', 'User\SurveiPemetaanController::ringkasan');
    $routes->get('ping-csrf', 'User\SurveiPemetaanController::pingCsrf');
    $routes->post('cek-identitas', 'User\SurveiPemetaanController::cekIdentitas');
    $routes->post('save-step', 'User\SurveiPemetaanController::saveStep');
    $routes->post('submit', 'User\SurveiPemetaanController::submit');
    $routes->post('keluar-sesi', 'User\SurveiPemetaanController::keluarSesi');
});

// Router Beranda Terautentikasi (Mengarahkan pegawai ke Dashboard Kinerja dan taruna/alumni ke Portal ECC 2.0)
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);

// Authenticated Routes ECC 1.0 (Khusus Pegawai / Pimpinan / Admin — Terisolasi dari Taruna & Alumni)
$routes->group('', ['filter' => ['auth', 'employee_only']], function ($routes) {
    // Profile Routes
    $routes->get('profile', 'Profile::index');
    $routes->post('profile/update', 'Profile::update');

// Notifications
$routes->get('notifications/fetch', 'NotificationController::fetch');
$routes->post('notifications/read/(:segment)', 'NotificationController::markAsRead/$1');
$routes->post('notifications/read-all', 'NotificationController::markAllAsRead');
    $routes->match(['GET', 'POST'], 'dashboard/api-detail-chart', 'Admin\Dashboard::apiDetailChart');
    $routes->get('monitoring', 'Admin\MonitoringController::index', ['filter' => 'auth:admin,direktur,wadir,kabag,kabag_aak,kabag_kuk,kepegawaian,manajemen']);
    $routes->get('monitoring/exportExcel/(:num)/(:num)', 'Admin\MonitoringController::exportExcel/$1/$2', ['filter' => 'auth:admin,direktur,wadir,kabag,kabag_aak,kabag_kuk,kepegawaian,manajemen']);
    $routes->get('monitoring/exportPdf/(:num)/(:num)', 'Admin\MonitoringController::exportPdf/$1/$2', ['filter' => 'auth:admin,direktur,wadir,kabag,kabag_aak,kabag_kuk,kepegawaian,manajemen']);

    $routes->get('remunerasi', 'Admin\RemunerasiController::index', ['filter' => 'auth:admin,kepegawaian']);
    $routes->post('remunerasi/store', 'Admin\RemunerasiController::store', ['filter' => 'auth:admin,kepegawaian']);
    
    // --- Rute untuk Kelola Pengguna (Users) & Daftar Pegawai ---
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('daftar-pegawai', 'User\DaftarPegawaiController::index');
    $routes->get('users/create', 'Admin\UserController::create');
    $routes->post('users/store', 'Admin\UserController::store');
    $routes->get('users/edit/(:num)', 'Admin\UserController::edit/$1');

    // Pengaturan Sistem
    $routes->get('settings', 'Admin\SettingsController::index');
    $routes->post('settings/store', 'Admin\SettingsController::store');
    $routes->get('settings/backup-db', 'Admin\SettingsController::backupDatabase');
    $routes->post('settings/purge-logs', 'Admin\SettingsController::purgeLogs');
    $routes->post('settings/purge-app-logs', 'Admin\SettingsController::purgeAppLogs');
    $routes->post('users/update', 'Admin\UserController::update');
    $routes->post('users/reset-kinerja', 'Admin\UserController::resetKinerja');
    $routes->post('users/delete/(:num)', 'Admin\UserController::delete/$1');
    $routes->get('users/export', 'Admin\UserController::exportExcel'); // Rute untuk Export
    $routes->post('users/import', 'Admin\UserController::importExcel'); // Rute untuk Import
    $routes->post('users/ajax_update_unit', 'Admin\UserController::ajaxUpdateUnit'); // Rute untuk AJAX update unit kerja
    $routes->post('users/batch_update', 'Admin\UserController::batch_update'); // Rute untuk Batch Edit
    $routes->post('users/rotasi-atasan', 'Admin\UserController::rotasiAtasanMassal'); // Rute Rotasi Atasan Massal per Pejabat/Unit
    $routes->get('users/riwayat-penugasan/(:num)', 'Admin\UserController::getRiwayatPenugasan/$1');
    $routes->post('users/riwayat-penugasan/store', 'Admin\UserController::storeRiwayatPenugasan');
    $routes->post('users/riwayat-penugasan/delete/(:num)', 'Admin\UserController::deleteRiwayatPenugasan/$1');

    // Log Keamanan Aktivitas
    $routes->get('admin/audit-logs', 'Admin\AuditLogController::index');
    $routes->get('admin/audit-logs/export', 'Admin\AuditLogController::exportExcel');
    $routes->get('admin/audit-logs/app-errors', 'Admin\AuditLogController::appErrors');
    $routes->post('admin/audit-logs/cleanup', 'Admin\AuditLogController::cleanup');

    // Master Data Group (Khusus admin, kepegawaian, spm)
    $routes->group('master-data', ['filter' => 'auth:admin,kepegawaian,spm'], function ($routes) {
        // Rute Hari Libur
        $routes->get('holidays', 'Admin\MasterDataController::holidays');
        $routes->post('holidays/sync', 'Admin\MasterDataController::syncHolidays');
        $routes->post('holidays/store', 'Admin\MasterDataController::storeHoliday');
        $routes->post('holidays/delete/(:num)', 'Admin\MasterDataController::deleteHoliday/$1');

        // Rute untuk Sasaran Program
        $routes->get('sasaran', 'Admin\MasterDataController::sasaran');
        $routes->post('sasaran/store', 'Admin\MasterDataController::storeSasaran');
        $routes->post('sasaran/update/(:num)', 'Admin\MasterDataController::updateSasaran/$1');
        $routes->post('sasaran/delete/(:num)', 'Admin\MasterDataController::deleteSasaran/$1');

        // Rute untuk Indikator Kinerja
        $routes->get('indikator', 'Admin\MasterDataController::indikator');
        $routes->post('indikator/store', 'Admin\MasterDataController::storeIndikator');
        $routes->post('indikator/update/(:num)', 'Admin\MasterDataController::updateIndikator/$1');
        $routes->post('indikator/delete/(:num)', 'Admin\MasterDataController::deleteIndikator/$1');

        // Rute untuk Satuan
        $routes->get('satuan', 'Admin\MasterDataController::satuan');
        $routes->post('satuan/store', 'Admin\MasterDataController::storeSatuan');
        $routes->post('satuan/update/(:num)', 'Admin\MasterDataController::updateSatuan/$1');
        $routes->post('satuan/delete/(:num)', 'Admin\MasterDataController::deleteSatuan/$1');

        // Rute untuk Unit Kerja
        $routes->get('unit-kerja', 'Admin\MasterDataController::unitKerja');
        $routes->post('unit-kerja/store', 'Admin\MasterDataController::storeUnitKerja');
        $routes->post('unit-kerja/update/(:num)', 'Admin\MasterDataController::updateUnitKerja/$1');
        $routes->post('unit-kerja/delete/(:num)', 'Admin\MasterDataController::deleteUnitKerja/$1');
        
        // Rute untuk Kriteria LED
        $routes->get('led', 'Admin\MasterDataController::led');
        $routes->post('led/store', 'Admin\MasterDataController::storeLed');
        $routes->post('led/update/(:num)', 'Admin\MasterDataController::updateLed/$1');
        $routes->post('led/delete/(:num)', 'Admin\MasterDataController::deleteLed/$1');
        $routes->post('led/deleteBatch', 'Admin\MasterDataController::deleteLedBatch');
        $routes->post('led/delete-batch', 'Admin\MasterDataController::deleteLedBatch');
        $routes->post('led/batchUpdate', 'Admin\MasterDataController::batchUpdateLed');
        $routes->post('led/batch-update', 'Admin\MasterDataController::batchUpdateLed');
        $routes->get('led/export', 'Admin\MasterDataController::exportLed');
        $routes->post('led/import', 'Admin\MasterDataController::importLed');

        // Rute untuk Standar LED
        $routes->get('led-standar', 'Admin\MasterDataController::ledStandar');
        
        // Rute untuk Aksi (Store, Update, Delete) Standar LED
        $routes->post('led-standar/store', 'Admin\MasterDataController::storeStandar');
        $routes->post('led-standar/update/(:num)', 'Admin\MasterDataController::updateStandar/$1');
        $routes->post('led-standar/delete/(:num)', 'Admin\MasterDataController::deleteStandar/$1');
    });
});

// User Kinerja Routes (Khusus Pegawai / Pimpinan / Admin — Terisolasi dari Taruna & Alumni)
$routes->group('', ['filter' => ['auth', 'employee_only']], function ($routes) {
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

    $routes->get('diklat', 'User\DiklatController::index');
    // --- Kelola Tim ---
    $routes->get('tim', 'User\TimController::index');
    $routes->post('tim/add', 'User\TimController::addStaf');
    $routes->post('tim/remove', 'User\TimController::removeStaf');
    $routes->post('tim/update_unit', 'User\TimController::updateUnit');

    // --- Laporan Kinerja Harian ---
    $routes->match(['GET', 'POST'], 'laporan-harian', 'User\LaporanHarianController::index');
    $routes->post('laporan-harian/get-previous-targets', 'User\LaporanHarianController::getPreviousTargets');
    $routes->post('laporan-harian/store', 'User\LaporanHarianController::store');
    $routes->post('laporan-harian/hapus', 'User\LaporanHarianController::hapus');
    $routes->post('laporan-harian/approve', 'User\LaporanHarianController::approve');
    $routes->post('laporan-harian/approve-all', 'User\LaporanHarianController::approveAll');
    $routes->post('laporan-harian/batal-approve', 'User\LaporanHarianController::cancelApprove'); // [SUPERADMIN] Batalkan persetujuan target

    // --- Log Kegiatan Harian ---
    $routes->match(['GET', 'POST'], 'log-kegiatan', 'User\LogKegiatanController::index');
    $routes->post('log-kegiatan/store', 'User\LogKegiatanController::store');
    $routes->post('log-kegiatan/hapus', 'User\LogKegiatanController::hapus');
    $routes->post('log-kegiatan/storeTugasTambahan', 'User\LogKegiatanController::storeTugasTambahan');
    $routes->post('log-kegiatan/hapusTugasTambahan', 'User\LogKegiatanController::hapusTugasTambahan');
    $routes->post('log-kegiatan/buka-kunci', 'User\LogKegiatanController::bukaKunci'); // [SUPERADMIN] Buka kunci laporan

    // --- Rekap & Penilaian Kinerja ---
    $routes->match(['GET', 'POST'], 'penilaian-kinerja', 'User\PenilaianKinerjaController::index');
    $routes->post('penilaian-kinerja/store', 'User\PenilaianKinerjaController::store');
    $routes->get('penilaian-kinerja/api-chart', 'User\PenilaianKinerjaController::getChartDataApi');

    // --- Panduan Penggunaan ---
    $routes->get('panduan', 'User\PanduanController::index');
});

// --- Modul ECC 2.0 (Ketarunaan & Alumni — Mendukung Taruna, Alumni, dan Staf/Pimpinan) ---
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->group('ketarunaan', function ($routes) {
        $routes->get('/', 'Ketarunaan\DashboardController::index');
        $routes->get('dashboard', 'Ketarunaan\DashboardController::index');
        $routes->post('dashboard/filter-data', 'Ketarunaan\DashboardController::filterData');
        $routes->get('keamanan-akun', 'Ketarunaan\AkunController::index');
        $routes->post('keamanan-akun/kata-sandi', 'Ketarunaan\AkunController::updateKataSandi');

        // Placeholder route endpoints untuk sub-modul lanjutan
        // Modul 1: Database Taruna
        $routes->get('taruna', 'Ketarunaan\TarunaController::index');
        $routes->post('taruna/store', 'Ketarunaan\TarunaController::store');
        $routes->get('taruna/impor', 'Ketarunaan\TarunaController::impor');
        $routes->post('taruna/impor/preview', 'Ketarunaan\TarunaController::previewImpor');
        $routes->post('taruna/impor/proses', 'Ketarunaan\TarunaController::prosesImpor');
        $routes->get('taruna/impor/template', 'Ketarunaan\TarunaController::unduhTemplate');
        $routes->get('taruna/profil/(:segment)', 'Ketarunaan\TarunaController::profil/$1');
        $routes->post('taruna/profil/(:segment)/identitas', 'Ketarunaan\TarunaController::updateIdentitas/$1');
        $routes->post('taruna/profil/(:segment)/pendidikan', 'Ketarunaan\TarunaController::updatePendidikan/$1');
        $routes->post('taruna/profil/(:segment)/kontak', 'Ketarunaan\TarunaController::updateKontak/$1');
        $routes->post('taruna/profil/(:segment)/riwayat-pendidikan', 'Ketarunaan\TarunaController::storeRiwayatPendidikan/$1');
        $routes->get('taruna/ubah-status/(:segment)', 'Ketarunaan\TarunaController::formUbahStatus/$1');
        $routes->post('taruna/ubah-status/(:segment)', 'Ketarunaan\TarunaController::storeUbahStatus/$1');
        $routes->get('taruna/unduh-sk/(:num)', 'Ketarunaan\TarunaController::unduhSkStatus/$1');
        $routes->post('taruna/status/(:num)/ajukan', 'Ketarunaan\TarunaController::ajukanDraftStatus/$1');
        $routes->post('taruna/status/(:num)/hapus-draf', 'Ketarunaan\TarunaController::hapusDraftStatus/$1');
        // Modul 2: Rekam Jejak Taruna
        $routes->get('rekam-jejak', 'Ketarunaan\RekamJejakController::index');
        $routes->get('rekam-jejak/ekspor', 'Ketarunaan\RekamJejakController::ekspor');
        $routes->get('rekam-jejak/tambah-capaian', 'Ketarunaan\RekamJejakController::createCapaian');
        $routes->get('rekam-jejak/tambah-capaian/(:segment)', 'Ketarunaan\RekamJejakController::createCapaian/$1');
        $routes->post('rekam-jejak/simpan-capaian', 'Ketarunaan\RekamJejakController::storeCapaian');
        $routes->get('rekam-jejak/detail/(:segment)', 'Ketarunaan\RekamJejakController::detail/$1');
        $routes->get('rekam-jejak/capaian/(:num)', 'Ketarunaan\RekamJejakController::showCapaian/$1');
        $routes->get('rekam-jejak/edit-capaian/(:num)', 'Ketarunaan\RekamJejakController::editCapaian/$1');
        $routes->post('rekam-jejak/update-capaian/(:num)', 'Ketarunaan\RekamJejakController::updateCapaian/$1');
        $routes->post('rekam-jejak/hapus-capaian/(:num)', 'Ketarunaan\RekamJejakController::deleteCapaian/$1');
        $routes->post('rekam-jejak/validasi-capaian/(:num)', 'Ketarunaan\RekamJejakController::validateCapaian/$1');
        $routes->get('rekam-jejak/unduh-bukti/(:segment)/(:num)', 'Ketarunaan\RekamJejakController::unduhBukti/$1/$2');
        // Modul 3: Prestasi Taruna
        $routes->get('prestasi', 'Ketarunaan\PrestasiController::index');
        $routes->get('prestasi/ekspor', 'Ketarunaan\PrestasiController::ekspor');
        $routes->get('prestasi/tambah', 'Ketarunaan\PrestasiController::create');
        $routes->post('prestasi/simpan', 'Ketarunaan\PrestasiController::store');
        $routes->get('prestasi/edit/(:num)', 'Ketarunaan\PrestasiController::edit/$1');
        $routes->post('prestasi/update/(:num)', 'Ketarunaan\PrestasiController::update/$1');
        $routes->get('prestasi/peserta/(:num)', 'Ketarunaan\PrestasiController::peserta/$1');
        $routes->post('prestasi/peserta/(:num)/tambah', 'Ketarunaan\PrestasiController::addPeserta/$1');
        $routes->post('prestasi/peserta/(:num)/hapus/(:num)', 'Ketarunaan\PrestasiController::removePeserta/$1/$2');
        $routes->get('prestasi/dokumen/(:num)', 'Ketarunaan\PrestasiController::dokumen/$1');
        $routes->post('prestasi/dokumen/(:num)/simpan', 'Ketarunaan\PrestasiController::saveDokumen/$1');
        $routes->post('prestasi/dokumen/(:num)/hapus-foto', 'Ketarunaan\PrestasiController::removeDokumentasi/$1');
        $routes->get('prestasi/detail/(:num)', 'Ketarunaan\PrestasiController::detail/$1');
        $routes->post('prestasi/validasi/(:num)', 'Ketarunaan\PrestasiController::validasi/$1');
        $routes->post('prestasi/hapus/(:num)', 'Ketarunaan\PrestasiController::delete/$1');
        $routes->get('prestasi/unduh-dokumen/(:num)', 'Ketarunaan\PrestasiController::unduhDokumen/$1');
        $routes->get('prestasi/unduh-ringkasan/(:num)', 'Ketarunaan\PrestasiController::unduhRingkasan/$1');
        // Modul 4: Pelanggaran Taruna
        $routes->get('pelanggaran', 'Ketarunaan\PelanggaranController::index');
        $routes->get('pelanggaran/ekspor', 'Ketarunaan\PelanggaranController::ekspor');
        $routes->get('pelanggaran/tambah', 'Ketarunaan\PelanggaranController::create');
        $routes->post('pelanggaran/simpan', 'Ketarunaan\PelanggaranController::store');
        $routes->get('pelanggaran/edit/(:num)', 'Ketarunaan\PelanggaranController::edit/$1');
        $routes->post('pelanggaran/update/(:num)', 'Ketarunaan\PelanggaranController::update/$1');
        $routes->get('pelanggaran/pemeriksaan/(:num)', 'Ketarunaan\PelanggaranController::pemeriksaan/$1');
        $routes->post('pelanggaran/pemeriksaan/(:num)/simpan', 'Ketarunaan\PelanggaranController::savePemeriksaan/$1');
        $routes->post('pelanggaran/pemeriksaan/(:num)/pihak/tambah', 'Ketarunaan\PelanggaranController::addPihak/$1');
        $routes->post('pelanggaran/pemeriksaan/(:num)/pihak/hapus/(:num)', 'Ketarunaan\PelanggaranController::removePihak/$1/$2');
        $routes->get('pelanggaran/penetapan/(:num)', 'Ketarunaan\PelanggaranController::penetapan/$1');
        $routes->post('pelanggaran/penetapan/(:num)/simpan', 'Ketarunaan\PelanggaranController::savePenetapan/$1');
        $routes->get('pelanggaran/dokumen/(:num)', 'Ketarunaan\PelanggaranController::dokumen/$1');
        $routes->post('pelanggaran/dokumen/(:num)/simpan', 'Ketarunaan\PelanggaranController::saveDokumen/$1');
        $routes->post('pelanggaran/dokumen/(:num)/hapus', 'Ketarunaan\PelanggaranController::removeDokumen/$1');
        $routes->get('pelanggaran/detail/(:num)', 'Ketarunaan\PelanggaranController::detail/$1');
        $routes->post('pelanggaran/detail/(:num)/tindak-lanjut', 'Ketarunaan\PelanggaranController::addTindakLanjut/$1');
        $routes->post('pelanggaran/validasi/(:num)', 'Ketarunaan\PelanggaranController::validasi/$1');
        $routes->post('pelanggaran/hapus/(:num)', 'Ketarunaan\PelanggaranController::delete/$1');
        $routes->get('pelanggaran/unduh-dokumen/(:num)', 'Ketarunaan\PelanggaranController::unduhDokumen/$1');
        $routes->get('pelanggaran/unduh-ringkasan/(:num)', 'Ketarunaan\PelanggaranController::unduhRingkasan/$1');
        $routes->get('beasiswa', 'Ketarunaan\BeasiswaController::index');
        $routes->get('beasiswa/program', 'Ketarunaan\BeasiswaController::program');
        $routes->get('beasiswa/kelola-program', 'Ketarunaan\BeasiswaController::kelolaProgram');
        $routes->get('beasiswa/kelola-program/(:num)', 'Ketarunaan\BeasiswaController::kelolaProgram/$1');
        $routes->post('beasiswa/program/simpan', 'Ketarunaan\BeasiswaController::saveProgram');
        $routes->get('beasiswa/apresiasi', 'Ketarunaan\BeasiswaController::apresiasi');
        $routes->post('beasiswa/apresiasi/hitung', 'Ketarunaan\BeasiswaController::hitungApresiasi');
        $routes->post('beasiswa/apresiasi/ajukan/(:num)', 'Ketarunaan\BeasiswaController::ajukanApresiasi/$1');
        $routes->post('beasiswa/apresiasi/impor-ip/preview', 'Ketarunaan\BeasiswaController::previewIpCsv');
        $routes->post('beasiswa/apresiasi/impor-ip/simpan', 'Ketarunaan\BeasiswaController::importIpCsv');
        $routes->post('beasiswa/apresiasi/impor-ip/batal', 'Ketarunaan\BeasiswaController::cancelIpPreview');
        $routes->get('beasiswa/apresiasi/impor-ip/templat', 'Ketarunaan\BeasiswaController::downloadIpCsvTemplate');
        $routes->get('beasiswa/klaim', 'Ketarunaan\BeasiswaController::klaim');
        $routes->get('beasiswa/klaim/ajukan/(:segment)', 'Ketarunaan\BeasiswaController::formKlaim/$1');
        $routes->post('beasiswa/klaim/simpan/(:segment)', 'Ketarunaan\BeasiswaController::saveKlaim/$1');
        $routes->get('beasiswa/verifikasi', 'Ketarunaan\BeasiswaController::verifikasi');
        $routes->get('beasiswa/verifikasi/(:num)', 'Ketarunaan\BeasiswaController::verifikasiDetail/$1');
        $routes->post('beasiswa/verifikasi/(:num)/simpan', 'Ketarunaan\BeasiswaController::saveVerifikasi/$1');
        $routes->get('beasiswa/penerima', 'Ketarunaan\BeasiswaController::penerima');
        $routes->get('beasiswa/detail/(:num)', 'Ketarunaan\BeasiswaController::detail/$1');
        $routes->post('beasiswa/penetapan/(:num)/simpan', 'Ketarunaan\BeasiswaController::savePenetapan/$1');
        $routes->post('beasiswa/penetapan/(:num)/batal', 'Ketarunaan\BeasiswaController::cancelPenetapan/$1');
        $routes->post('beasiswa/hapus/(:num)', 'Ketarunaan\BeasiswaController::deleteDraft/$1');
        $routes->get('beasiswa/unduh-dokumen/(:num)', 'Ketarunaan\BeasiswaController::unduhDokumen/$1');
        $routes->get('beasiswa/ekspor', 'Ketarunaan\BeasiswaController::ekspor');
        $routes->get('beasiswa/api/prestasi-taruna/(:segment)', 'Ketarunaan\BeasiswaController::apiPrestasiTaruna/$1');
        // Modul 6: Lowongan Pekerjaan
        $routes->get('lowongan', 'Ketarunaan\LowonganController::index');
        $routes->get('lowongan/ekspor', 'Ketarunaan\LowonganController::ekspor');
        $routes->get('lowongan/tambah', 'Ketarunaan\LowonganController::create');
        $routes->post('lowongan/simpan', 'Ketarunaan\LowonganController::store');
        $routes->get('lowongan/edit/(:num)', 'Ketarunaan\LowonganController::edit/$1');
        $routes->post('lowongan/update/(:num)', 'Ketarunaan\LowonganController::update/$1');
        $routes->get('lowongan/detail/(:num)', 'Ketarunaan\LowonganController::detail/$1');
        $routes->get('lowongan/perusahaan', 'Ketarunaan\LowonganController::perusahaan');
        $routes->post('lowongan/perusahaan/simpan', 'Ketarunaan\LowonganController::savePerusahaan');
        $routes->get('lowongan/dokumen', 'Ketarunaan\LowonganController::dokumen');
        $routes->get('lowongan/dokumen/(:num)', 'Ketarunaan\LowonganController::dokumen/$1');
        $routes->post('lowongan/dokumen/(:num)/simpan', 'Ketarunaan\LowonganController::saveDokumen/$1');
        $routes->post('lowongan/dokumen/hapus/(:num)', 'Ketarunaan\LowonganController::removeDokumen/$1');
        $routes->get('lowongan/unduh-dokumen/(:num)', 'Ketarunaan\LowonganController::unduhDokumen/$1');
        $routes->get('lowongan/verifikasi', 'Ketarunaan\LowonganController::verifikasi');
        $routes->get('lowongan/verifikasi/(:num)', 'Ketarunaan\LowonganController::verifikasi/$1');
        $routes->post('lowongan/verifikasi/(:num)/simpan', 'Ketarunaan\LowonganController::saveVerifikasi/$1');
        $routes->post('lowongan/publikasi/(:num)', 'Ketarunaan\LowonganController::publikasi/$1');
        $routes->post('lowongan/tutup/(:num)', 'Ketarunaan\LowonganController::tutup/$1');
        $routes->get('lowongan/riwayat', 'Ketarunaan\LowonganController::riwayat');
        $routes->get('lowongan/riwayat/(:num)', 'Ketarunaan\LowonganController::riwayat/$1');
        // Modul 7: Alumni — Profil dasar (ALM-01..03, UAT-04, UAT-05, UAT-17)
        $routes->get('alumni', 'Ketarunaan\AlumniController::index');
        $routes->get('alumni/ekspor', 'Ketarunaan\AlumniController::ekspor');
        $routes->get('alumni/detail/(:segment)', 'Ketarunaan\AlumniController::detail/$1');
        $routes->post('alumni/kontak/(:segment)', 'Ketarunaan\AlumniController::updateKontak/$1');
        $routes->get('alumni/kelulusan', 'Ketarunaan\AlumniController::kelulusan');
        $routes->post('alumni/kelulusan/ajukan', 'Ketarunaan\AlumniController::ajukanKelulusan');
        $routes->post('alumni/kelulusan/ajukan-batch', 'Ketarunaan\AlumniController::ajukanKelulusanBatch');
        $routes->post('alumni/kelulusan/validasi/(:num)', 'Ketarunaan\AlumniController::validasiKelulusan/$1');
        $routes->post('alumni/kelulusan/validasi-batch', 'Ketarunaan\AlumniController::validasiKelulusanBatch');
        $routes->get('alumni/koreksi', 'Ketarunaan\AlumniController::koreksi');
        $routes->post('alumni/koreksi/ajukan', 'Ketarunaan\AlumniController::ajukanKoreksi');
        $routes->post('alumni/koreksi/putuskan/(:num)', 'Ketarunaan\AlumniController::putuskanKoreksi/$1');

        // Modul 7B: Tracer Study (Bundel Pertanyaan Reusable, Kontrol Buka/Tutup Periode, Progres & Hasil)
        $routes->get('tracer-study', 'Ketarunaan\TracerStudyController::index');
        $routes->get('tracer-study/ekspor', 'Ketarunaan\TracerStudyController::ekspor');
        $routes->get('tracer-study/periode', 'Ketarunaan\TracerStudyController::periode');
        $routes->post('tracer-study/periode/simpan', 'Ketarunaan\TracerStudyController::savePeriode');
        $routes->post('tracer-study/periode/(:num)/status', 'Ketarunaan\TracerStudyController::changePeriodeStatus/$1');
        $routes->post('tracer-study/periode/(:num)/reset-token', 'Ketarunaan\TracerStudyController::resetPublicToken/$1');
        $routes->get('tracer-study/periode/(:num)/pertanyaan', 'Ketarunaan\TracerStudyController::pertanyaanPeriode/$1');
        $routes->post('tracer-study/periode/(:num)/pertanyaan/simpan', 'Ketarunaan\TracerStudyController::savePertanyaanPeriode/$1');
        $routes->post('tracer-study/periode/(:num)/pertanyaan/(:num)/hapus', 'Ketarunaan\TracerStudyController::deletePertanyaanPeriode/$1/$2');
        $routes->post('tracer-study/periode/(:num)/simpan-bundel', 'Ketarunaan\TracerStudyController::savePeriodeAsBundle/$1');
        $routes->get('tracer-study/bundel', 'Ketarunaan\TracerStudyController::bundel');
        $routes->post('tracer-study/bundel/simpan', 'Ketarunaan\TracerStudyController::saveBundle');
        $routes->post('tracer-study/bundel/(:num)/duplikat', 'Ketarunaan\TracerStudyController::duplicateBundle/$1');
        $routes->get('tracer-study/bundel/(:num)', 'Ketarunaan\TracerStudyController::bundelDetail/$1');
        $routes->post('tracer-study/bundel/(:num)/pertanyaan/simpan', 'Ketarunaan\TracerStudyController::savePertanyaanBundle/$1');
        $routes->post('tracer-study/bundel/(:num)/pertanyaan/(:num)/hapus', 'Ketarunaan\TracerStudyController::deletePertanyaanBundle/$1/$2');
        $routes->get('tracer-study/responden', 'Ketarunaan\TracerStudyController::responden');
        $routes->get('tracer-study/respons/(:num)', 'Ketarunaan\TracerStudyController::detailRespons/$1');
        $routes->post('tracer-study/respons/(:num)/setujui', 'Ketarunaan\TracerStudyController::approveRespons/$1');
        $routes->post('tracer-study/respons/(:num)/tolak', 'Ketarunaan\TracerStudyController::rejectRespons/$1');
        $routes->get('tracer-study/isi/(:num)', 'Ketarunaan\TracerStudyController::formIsi/$1');
        $routes->post('tracer-study/isi/(:num)/simpan', 'Ketarunaan\TracerStudyController::simpanRespons/$1');

        // Modul 8: Tata Kelola ECC 2.0 (Data Dosen, Validasi Data, Laporan, Pengaturan)
        $routes->get('data-dosen', 'Ketarunaan\DataDosenController::index');
        $routes->get('data-dosen/ekspor', 'Ketarunaan\DataDosenController::ekspor');
        $routes->post('data-dosen/sinkron', 'Ketarunaan\DataDosenController::sinkron');
        $routes->post('data-dosen/simpan', 'Ketarunaan\DataDosenController::simpan');
        $routes->get('data-dosen/detail/(:num)', 'Ketarunaan\DataDosenController::detail/$1');
        $routes->post('data-dosen/detail/(:num)/catatan-pimpinan', 'Ketarunaan\DataDosenController::saveCatatanPimpinan/$1');
        $routes->post('data-dosen/detail/(:num)/kualifikasi/simpan', 'Ketarunaan\DataDosenController::saveKualifikasi/$1');
        $routes->post('data-dosen/detail/(:num)/kualifikasi/(:num)/hapus', 'Ketarunaan\DataDosenController::deleteKualifikasi/$1/$2');
        $routes->post('data-dosen/detail/(:num)/kualifikasi/(:num)/verifikasi', 'Ketarunaan\DataDosenController::verifyKualifikasi/$1/$2');

        $routes->get('validasi', 'Ketarunaan\ValidasiController::index');
        $routes->post('validasi/status/(:num)', 'Ketarunaan\ValidasiController::putuskanStatus/$1');
        $routes->post('validasi/capaian/(:num)', 'Ketarunaan\ValidasiController::putuskanCapaian/$1');
        $routes->post('validasi/foto/(:segment)', 'Ketarunaan\ValidasiController::putuskanFoto/$1');

        $routes->get('laporan', 'Ketarunaan\LaporanController::index');
        $routes->get('laporan/ekspor', 'Ketarunaan\LaporanController::ekspor');

        $routes->get('pengaturan', 'Ketarunaan\PengaturanController::index');
        $routes->post('pengaturan/referensi/simpan', 'Ketarunaan\PengaturanController::simpanReferensi');
        $routes->post('pengaturan/referensi/toggle/([a-z0-9_]+)/(:num)', 'Ketarunaan\PengaturanController::toggleReferensi/$1/$2');
        $routes->post('pengaturan/operasional/simpan', 'Ketarunaan\PengaturanController::simpanOperasional');
    });
});

// ECC Routes (Khusus Pegawai / Pimpinan / Admin)
$routes->group('ecc', ['filter' => ['auth', 'employee_only']], function ($routes) {
    $routes->get('led', 'Admin\MasterDataController::eccLed');
    $routes->post('led/store', 'EccController::storeLed');
    $routes->post('deleteLedLink/(:num)', 'EccController::deleteLedLink/$1');

    $routes->get('simulasi', 'EccController::simulasi');
    $routes->post('simulasi/store', 'EccController::storeSimulasi');
    $routes->get('detailStandar/(:num)/(:any)/(:any)', 'EccController::detailStandar/$1/$2/$3');
    $routes->get('lkps', 'EccController::lkps');
});

// Kepegawaian Routes (Role-Restricted: kepegawaian, admin, direktur, wadir, kabag, kabag_aak, kabag_kuk)
$routes->group('kepegawaian', ['filter' => ['auth', 'employee_only']], function ($routes) {
    // Submenu 1: Monitoring Target Kinerja Bulanan
    $routes->match(['GET', 'POST'], 'target-kinerja', 'Kepegawaian\MonitoringTargetController::index');
    $routes->get('target-kinerja/detail-pegawai', 'Kepegawaian\MonitoringTargetController::getDetailTarget');
    $routes->get('target-kinerja/export-excel', 'Kepegawaian\MonitoringTargetController::exportExcel');
    $routes->get('target-kinerja/export-pdf', 'Kepegawaian\MonitoringTargetController::exportPdf');

    // Submenu 2: Monitoring Penilaian Kinerja
    $routes->match(['GET', 'POST'], '/', 'Kepegawaian\DashboardKepegawaian::index');
    $routes->match(['GET', 'POST'], 'monitoring-penilaian', 'Kepegawaian\DashboardKepegawaian::index');
    $routes->get('export-excel', 'Kepegawaian\DashboardKepegawaian::exportExcel');
    $routes->get('export-pdf', 'Kepegawaian\DashboardKepegawaian::exportPdf');
    $routes->get('detail-pegawai', 'Kepegawaian\DashboardKepegawaian::getDetailPegawai');

    // Submenu 3: Hasil Survei Pemetaan SDM & Delegasi Staf Kabag KUK
    $routes->get('survei-pemetaan', 'Kepegawaian\HasilSurveiController::index');
    $routes->get('survei-pemetaan/detail-responden', 'Kepegawaian\HasilSurveiController::detailResponden');
    $routes->get('survei-pemetaan/compare-responden', 'Kepegawaian\HasilSurveiController::compareResponden');
    $routes->get('survei-pemetaan/export-excel', 'Kepegawaian\HasilSurveiController::exportExcel');
    $routes->post('survei-pemetaan/delegasi/store', 'Kepegawaian\HasilSurveiController::storeDelegasi');
    $routes->post('survei-pemetaan/delegasi/revoke', 'Kepegawaian\HasilSurveiController::revokeDelegasi');
    $routes->post('survei-pemetaan/toggle-periode', 'Kepegawaian\HasilSurveiController::togglePeriode');
    $routes->post('survei-pemetaan/sync-profil', 'Kepegawaian\HasilSurveiController::syncProfil');
    $routes->post('survei-pemetaan/reset-draf', 'Kepegawaian\HasilSurveiController::resetDraf');
    $routes->post('survei-pemetaan/buka-kunci', 'Kepegawaian\HasilSurveiController::bukaKunci');
    $routes->post('survei-pemetaan/arsipkan', 'Kepegawaian\HasilSurveiController::arsipkanRespon');
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
