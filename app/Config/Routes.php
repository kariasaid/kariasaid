<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 *
 * CATATAN:
 * Salin / gabungkan routes di bawah ini ke file app/Config/Routes.php
 * project CodeIgniter 4 Anda.
 */

$routes->get('/', 'DashboardController::index');

// =====================================================
// AHSP Management
// =====================================================
$routes->group('ahsp', static function ($routes) {
    $routes->get('/',              'AHSPController::index');
    $routes->get('create',         'AHSPController::create');
    $routes->post('store',         'AHSPController::store');
    $routes->get('edit/(:num)',    'AHSPController::edit/$1');
    $routes->post('update/(:num)', 'AHSPController::update/$1');
    $routes->get('delete/(:num)',  'AHSPController::delete/$1');
});

// =====================================================
// RAB Import & Rekapitulasi
// =====================================================
$routes->group('rab', static function ($routes) {
    $routes->get('/',                    'ImportRABController::index');
    $routes->post('upload',              'ImportRABController::upload');
    $routes->get('detail/(:num)',        'ImportRABController::detail/$1');
    $routes->get('rekap-bahan/(:num)',   'ImportRABController::rekapBahan/$1');
    $routes->get('rekap-upah/(:num)',    'ImportRABController::rekapUpah/$1');
    $routes->get('recalculate/(:num)',   'ImportRABController::recalculate/$1');
});
