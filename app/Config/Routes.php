<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ---------------- ROUTE PUBLIK / PENGUNJUNG (tanpa login) ----------------
// Halaman awal sekarang adalah landing page untuk pengunjung/calon penyewa
$routes->get('/', 'PublicSite::index');
$routes->post('/ajukan-sewa', 'Pengajuan::ajukanSewa');

$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::attemptRegister');

// ---------------- ROUTE YANG WAJIB LOGIN ----------------
// Menggunakan filter 'auth' (didaftarkan di Config/Filters.php)
$routes->group('', ['filter' => 'auth'], static function ($routes) {

    $routes->get('/dashboard', 'Dashboard::index');

    // CRUD Kamar
    $routes->get('/kamar', 'Kamar::index');
    $routes->get('/kamar/create', 'Kamar::create');
    $routes->post('/kamar/store', 'Kamar::store');
    $routes->get('/kamar/edit/(:num)', 'Kamar::edit/$1');
    $routes->post('/kamar/update/(:num)', 'Kamar::update/$1');
    $routes->get('/kamar/delete/(:num)', 'Kamar::delete/$1');

    // CRUD Penyewa
    $routes->get('/penyewa', 'Penyewa::index');
    $routes->get('/penyewa/create', 'Penyewa::create');
    $routes->post('/penyewa/store', 'Penyewa::store');
    $routes->get('/penyewa/edit/(:num)', 'Penyewa::edit/$1');
    $routes->post('/penyewa/update/(:num)', 'Penyewa::update/$1');
    $routes->get('/penyewa/delete/(:num)', 'Penyewa::delete/$1');

    // CRUD Pembayaran
    $routes->get('/pembayaran', 'Pembayaran::index');
    $routes->get('/pembayaran/create', 'Pembayaran::create');
    $routes->post('/pembayaran/store', 'Pembayaran::store');
    $routes->get('/pembayaran/edit/(:num)', 'Pembayaran::edit/$1');
    $routes->post('/pembayaran/update/(:num)', 'Pembayaran::update/$1');
    $routes->get('/pembayaran/delete/(:num)', 'Pembayaran::delete/$1');

    // Kelola Pengajuan Sewa (dari pengunjung)
    $routes->get('/pengajuan', 'Pengajuan::index');
    $routes->post('/pengajuan/update-status/(:num)', 'Pengajuan::updateStatus/$1');
    $routes->get('/pengajuan/delete/(:num)', 'Pengajuan::delete/$1');
});
