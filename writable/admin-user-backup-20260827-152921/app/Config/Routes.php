<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('trial/install', 'TrialInstall::index');
$routes->post('trial/install', 'TrialInstall::start');
$routes->post('trial/install/stop', 'TrialInstall::stop');

$routes->post(
    'trial/install/bootstrap',
    'TrialInstall::bootstrapTrial'
);

$routes->post(
    'trial/install/recover-credential',
    'TrialInstall::recoverCredential'
);

$routes->post(
    'trial/install/validate',
    'TrialInstall::validateTrial'
);
$routes->get('trial/upgrade', 'TrialInstall::upgrade');
$routes->get('trial/upgrade/process', 'TrialInstall::upgradeProcess');
$routes->get('trial/upgrade/status', 'TrialInstall::upgradeStatus');
$routes->post('trial/upgrade', 'TrialInstall::submitUpgrade');
$routes->post('trial/upgrade/bootstrap', 'TrialInstall::bootstrapUpgrade');
$routes->post('trial/upgrade/activate', 'TrialInstall::activateUpgrade');



// Dashboard umum
$routes->get('beranda', 'Beranda::index');
$routes->get('beranda/data', 'Beranda::getRealtimeData');
$routes->get('beranda/chart-data', 'Beranda::chartData');
$routes->get('beranda/stats', 'Beranda::getStats');
$routes->get('beranda/izin', 'Beranda::izin');
$routes->get('beranda/sakit', 'Beranda::sakit');
$routes->get('beranda/alpha', 'Beranda::alpha');

$routes->get('cron/tandaiAlpha', 'CronController::tandaiAlpha');

// Absensi
$routes->get('absensi', 'AbsensiController::index');
$routes->match(['GET','POST'], 'absensi/proses', 'AbsensiController::proses');

// Auth
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');

// Izin umum
$routes->get('izin', 'IzinController::index');
$routes->post('izin/submit', 'IzinController::submit');

// Siswa
$routes->group('siswa', ['filter' => ['role:siswa', 'license']], function($routes) {
    $routes->get('dashboard', 'StudentController::dashboard');
});

// Orang Tua
$routes->group('ortu', ['filter' => ['role:ortu', 'license']], function($routes) {
    $routes->get('dashboard', 'OrtuController::dashboard');
    $routes->post('izin', 'OrtuController::izin');
    $routes->get('riwayat-izin', 'OrtuController::riwayatIzin');
    $routes->get('pesan', 'OrtuController::pesan');
});

// Wali
$routes->group('wali', ['filter' => ['role:wali', 'license']], function($routes) {
    $routes->get('dashboard', 'Wali::dashboard');
});

// Wali Kelas
$routes->group('walikelas', ['namespace' => 'App\Controllers\Walas', 'filter' => ['role:walikelas', 'license']], function($routes) {
    $routes->get('dashboard', 'Walikelas::dashboard');
    $routes->get('siswa', 'Walikelas::siswa');
    $routes->get('kehadiran', 'Walikelas::kehadiran');
    $routes->get('izin', 'Walikelas::izin');
    $routes->get('pesan', 'Walikelas::pesan');
});

// Admin & Operator
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => ['role:admin,operator', 'license']], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('license', 'License::index');
    $routes->get('license/renew', 'License::renewForm');
    $routes->post('license/renew', 'License::submitRenewal');
    $routes->get('get-stats', 'Dashboard::getStats');
    $routes->get('get-jurusan-stats', 'Dashboard::getJurusanStats');
    $routes->get('get-kelas-stats', 'Dashboard::getKelasStats');
    $routes->get('get-latest-izin', 'Dashboard::getLatestIzin');

    // Routes untuk Tapel (Tahun Pelajaran)
    $routes->get('tapel', 'Tapel::index');
    $routes->post('tapel/create', 'Tapel::create');
    $routes->get('tapel/edit/(:num)', 'Tapel::edit/$1');
    $routes->post('tapel/update/(:num)', 'Tapel::update/$1');
    $routes->get('tapel/setActive/(:num)', 'Tapel::setActive/$1');


    // Jurusan
    $routes->get('jurusan', 'Jurusan::index');
    $routes->get('jurusan/create', 'Jurusan::create');
    $routes->post('jurusan/store', 'Jurusan::store');
    $routes->get('jurusan/edit/(:num)', 'Jurusan::edit/$1');
    $routes->post('jurusan/update/(:num)', 'Jurusan::update/$1');
    $routes->get('jurusan/delete/(:num)', 'Jurusan::delete/$1');

    // Guru
    $routes->get('guru', 'Guru::index');
    $routes->get('guru/create', 'Guru::create');
    $routes->post('guru/store', 'Guru::store');
    $routes->get('guru/edit/(:num)', 'Guru::edit/$1');
    $routes->post('guru/update/(:num)', 'Guru::update/$1');
    $routes->get('guru/delete/(:num)', 'Guru::delete/$1');

    // Wali Kelas
    // $routes->get('plotkelas', 'Walikelas::index');
    // $routes->get('plotkelas/create', 'Walikelas::create');
    // $routes->post('plotkelas/store', 'Walikelas::store');
    // $routes->get('plotkelas/edit/(:num)', 'Walikelas::edit/$1');
    // $routes->post('plotkelas/update/(:num)', 'Walikelas::update/$1');
    // $routes->get('plotkelas/delete/(:num)', 'Walikelas::delete/$1');

    // Kelas
    $routes->get('kelas', 'Kelas::index');
    $routes->get('kelas/create', 'Kelas::create');
    $routes->post('kelas/store', 'Kelas::store');
    $routes->get('kelas/edit/(:num)', 'Kelas::edit/$1');
    $routes->post('kelas/update/(:num)', 'Kelas::update/$1');
    $routes->get('kelas/delete/(:num)', 'Kelas::delete/$1');

    // Siswa
    $routes->get('siswa', 'Siswa::index');
    $routes->get('siswa/create', 'Siswa::create');
    $routes->post('siswa/store', 'Siswa::store');
    $routes->get('siswa/edit/(:num)', 'Siswa::edit/$1');
    $routes->post('siswa/update/(:num)', 'Siswa::update/$1');
    $routes->get('siswa/delete/(:num)', 'Siswa::delete/$1');

    // // Plot Kelas
    // $routes->get('plotkelas', 'PlotKelas::index');
    // $routes->get('plotkelas/create', 'PlotKelas::create');
    // $routes->post('plotkelas/store', 'PlotKelas::store');
    // $routes->get('plotkelas/edit/(:num)', 'PlotKelas::edit/$1');
    // $routes->post('plotkelas/update/(:num)', 'PlotKelas::update/$1');
    // $routes->post('plotkelas/delete/(:num)', 'PlotKelas::delete/$1');
    // $routes->get('semester', 'PlotKelas::copyForm');
    // $routes->post('semester/proses', 'PlotKelas::copySemester');

    //     // Naik Kelas
    // $routes->get('promote', 'PlotKelas::promoteForm');
    // $routes->post('promote/proses', 'PlotKelas::promoteClass');
    // $routes->post('promote/preview', 'PlotKelas::promotePreview');

    // Plot Kelas (siswa baru masuk)
    $routes->get('plotkelas', 'PlotKelas::index');
    $routes->get('plotkelas/create', 'PlotKelas::create');
    $routes->post('plotkelas/store', 'PlotKelas::store');
    $routes->get('plotkelas/edit/(:num)', 'PlotKelas::edit/$1');
    $routes->post('plotkelas/update/(:num)', 'PlotKelas::update/$1');
    $routes->post('plotkelas/delete/(:num)', 'PlotKelas::delete/$1');

    // =========================
    // SISWA AKADEMIK (PLOT KELAS)
    // =========================
    $routes->get('siswa-akademik', 'SiswaAkademik::index');
    $routes->get('siswa-akademik/create', 'SiswaAkademik::create');
    $routes->post('siswa-akademik/store', 'SiswaAkademik::store');
    $routes->get('siswa-akademik/edit/(:num)', 'SiswaAkademik::edit/$1');
    $routes->post('siswa-akademik/update/(:num)', 'SiswaAkademik::update/$1');
    $routes->get('siswa-akademik/delete/(:num)', 'SiswaAkademik::delete/$1');

    // halaman list
    $routes->get('promote', 'Promote::index');

    $routes->get('promote/naik/(:num)', 'Promote::confirmPromote/$1');
    $routes->post('promote/naik/execute/(:num)', 'Promote::promoteIndividu/$1');

    $routes->get('promote/kelas/(:num)', 'Promote::confirmPromoteKelas/$1');
    $routes->post('promote/kelas/execute/(:num)', 'Promote::promoteKelas/$1');

    $routes->get('promote/lulus/(:num)', 'Promote::graduate/$1');





    // Maping Kelas
    $routes->get('mapping', 'KelasMapping::index');              // daftar mapping
    $routes->get('mapping/create', 'KelasMapping::create');        // form tambah
    $routes->post('mapping/store', 'KelasMapping::store');         // simpan tambah
    $routes->get('mapping/edit/(:num)', 'KelasMapping::edit/$1');  // form edit
    $routes->post('mapping/update/(:num)', 'KelasMapping::update/$1'); // simpan edit
    $routes->post('mapping/delete/(:num)', 'KelasMapping::delete/$1'); // hapus





    // Kehadiran
    //$routes->get('kehadiran', 'Kehadiran::index');
    $routes->get('kehadiran/edit/(:num)', 'Kehadiran::edit/$1');
    $routes->post('kehadiran/update/(:num)', 'Kehadiran::update/$1');
    $routes->get('kehadiran/presensi/(:num)', 'Kehadiran::presensi/$1');
    $routes->post('kehadiran/prestore', 'Kehadiran::prestore');
    $routes->get('kehadiran', 'Kehadiran::index'); 
    $routes->get('presensi', 'Kehadiran::siswa');

    // Laporan
    $routes->get('laporan', 'Laporan::index');
    $routes->get('laporan/exportExcel', 'Laporan::exportExcel');

    // Pengaturan


    // Wapikey
    $routes->get('wapikey', 'Wapikey::index');
    $routes->get('wapikey/create', 'Wapikey::create');
    $routes->post('wapikey/store', 'Wapikey::store');
    $routes->get('wapikey/edit/(:num)', 'Wapikey::edit/$1');
    $routes->post('wapikey/update/(:num)', 'Wapikey::update/$1');
    $routes->get('wapikey/delete/(:num)', 'Wapikey::delete/$1');
    $routes->get('wapikey/setActive/(:num)', 'Wapikey::setActive/$1');
    $routes->get('wapikey/setInactive/(:num)', 'Wapikey::setInactive/$1');

    // Pesan
    $routes->get('pesan', 'Pesan::index');

    // Izin
    $routes->get('izin', 'Izin::index');
    $routes->get('izin/create', 'Izin::create');
    $routes->get('izin/cariSiswa', 'Izin::cariSiswa');
    $routes->post('izin/store', 'Izin::store');
    $routes->get('izin/edit/(:num)', 'Izin::edit/$1');
    $routes->post('izin/update/(:num)', 'Izin::update/$1');
    $routes->get('izin/delete/(:num)', 'Izin::delete/$1');

    // Informasi
    $routes->get('informasi', 'Informasi::index');

    // Broadcast
    $routes->get('broadcast-view', 'Broadcast::index');
    $routes->get('broadcast-create', 'Broadcast::create');
    $routes->get('broadcast/getPhoneByNis', 'Broadcast::getPhoneByNis');
    $routes->post('broadcast/store', 'Broadcast::store');
    $routes->get('broadcast/send/(:num)', 'Broadcast::send/$1');
    $routes->get('broadcast/delete/(:num)', 'Broadcast::delete/$1');

    // Aplikasi 
    $routes->get('aplikasi', 'Aplikasi::index');
    $routes->post('aplikasi/update', 'Aplikasi::update'); 
  
    

    // Upload Data (submenu Pengaturan)
    $routes->get('upload', 'Aplikasi::upload');  
    $routes->post('upload/process', 'Aplikasi::process_upload'); 
    $routes->get('upload/template', 'Aplikasi::download_template'); 

    // Generate QR Code
    $routes->get('qrcode', 'GenerateQR::index');
    $routes->post('qrcode/generate', 'GenerateQR::generate');
    $routes->get('qrcode/generate/(:num)', 'GenerateQR::generate/$1');
    $routes->get('qrcode/download/(:num)', 'GenerateQR::download/$1');
    $routes->get('qrcode/exportPdf', 'GenerateQR::exportPdf');
    $routes->get('qrcode/print', 'GenerateQR::printView');


    // Routes untuk Libur Sekolah
    $routes->get('libursekolah', 'Libursekolah::index');                // daftar libur sekolah
    $routes->get('libursekolah/create', 'Libursekolah::create');          // form tambah libur
    $routes->post('libursekolah/store', 'Libursekolah::store');            // simpan libur
    
    // --- TAMBAHKAN DUA BARIS INI ---
    $routes->get('libursekolah/edit/(:num)', 'Libursekolah::edit/$1');     // form edit
    $routes->post('libursekolah/update/(:num)', 'Libursekolah::update/$1'); // proses update
    // ------------------------------

    $routes->get('libursekolah/delete/(:num)', 'Libursekolah::delete/$1');  // hapus libur

});
