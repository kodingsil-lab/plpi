<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Public\\HomeController::index');

$routes->group('', ['filter' => 'guest'], static function ($routes): void {
    $routes->get('login', 'Auth\\AuthController::loginForm');
    $routes->post('login', 'Auth\\AuthController::login');
});
$routes->post('logout', 'Auth\\AuthController::logout', ['filter' => 'sessionauth']);

$routes->get('dashboard', 'Admin\\DashboardController::index', ['filter' => ['sessionauth', 'role:superadmin,admin_jurnal']]);

$routes->group('admin', ['filter' => ['sessionauth', 'role:superadmin,admin_jurnal']], static function ($routes): void {
    $routes->get('loa-requests', 'Admin\\LoaRequestController::index');
    $routes->get('loa-requests/export/csv', 'Admin\\LoaRequestController::exportCsv');
    $routes->post('loa-requests/(\\d+)/quick-approve', 'Admin\\LoaRequestController::quickApprove/$1');
    $routes->get('loa-requests/(\\d+)', 'Admin\\LoaRequestController::show/$1');
    $routes->post('loa-requests/(\\d+)/approve', 'Admin\\LoaRequestController::approve/$1');
    $routes->post('loa-requests/(\\d+)/reject', 'Admin\\LoaRequestController::reject/$1');
    $routes->delete('loa-requests/(\\d+)', 'Admin\\LoaRequestController::destroy/$1');
    $routes->post('loa-requests/(\\d+)/delete', 'Admin\\LoaRequestController::destroy/$1');
    $routes->post('loa-requests/bulk-delete', 'Admin\\LoaRequestController::bulkDelete');

    $routes->get('loa-letters', 'Admin\\LoaLetterController::index');
    $routes->get('loa-letters/export/csv', 'Admin\\LoaLetterController::exportCsv');
    $routes->get('loa-letters/(\\d+)/edit', 'Admin\\LoaLetterController::edit/$1');
    $routes->put('loa-letters/(\\d+)', 'Admin\\LoaLetterController::update/$1');
    $routes->post('loa-letters/(\\d+)', 'Admin\\LoaLetterController::update/$1');
    $routes->put('loa-letters/(\\d+)/regenerate', 'Admin\\LoaLetterController::regenerate/$1');
    $routes->post('loa-letters/(\\d+)/regenerate', 'Admin\\LoaLetterController::regenerate/$1');
    $routes->delete('loa-letters/(\\d+)', 'Admin\\LoaLetterController::destroy/$1');
    $routes->post('loa-letters/(\\d+)/delete', 'Admin\\LoaLetterController::destroy/$1');
    $routes->post('loa-letters/bulk-delete', 'Admin\\LoaLetterController::bulkDelete');

    $routes->get('notifikasi', 'Admin\\NotificationController::index');
    $routes->post('notifikasi/(\\d+)/kirim-email', 'Admin\\NotificationController::sendEmail/$1');
    $routes->delete('notifikasi/(\\d+)', 'Admin\\NotificationController::destroy/$1');
    $routes->post('notifikasi/(\\d+)/delete', 'Admin\\NotificationController::destroy/$1');
    $routes->post('notifikasi/bulk-delete', 'Admin\\NotificationController::bulkDelete');

    $routes->get('journals', 'Admin\\JournalController::index');
    $routes->get('journals/create', 'Admin\\JournalController::create', ['filter' => 'role:superadmin']);
    $routes->post('journals', 'Admin\\JournalController::store', ['filter' => 'role:superadmin']);
    $routes->get('journals/(\\d+)/edit', 'Admin\\JournalController::edit/$1');
    $routes->put('journals/(\\d+)', 'Admin\\JournalController::update/$1');
    $routes->delete('journals/(\\d+)', 'Admin\\JournalController::destroy/$1', ['filter' => 'role:superadmin']);
    $routes->post('journals/(\\d+)/delete', 'Admin\\JournalController::destroy/$1', ['filter' => 'role:superadmin']);
    $routes->post('journals/bulk-delete', 'Admin\\JournalController::bulkDelete', ['filter' => 'role:superadmin']);

    $routes->get('publishers', 'Admin\\PublisherController::index', ['filter' => 'role:superadmin']);
    $routes->get('publishers/create', 'Admin\\PublisherController::create', ['filter' => 'role:superadmin']);
    $routes->post('publishers', 'Admin\\PublisherController::store', ['filter' => 'role:superadmin']);
    $routes->get('publishers/(\\d+)/edit', 'Admin\\PublisherController::edit/$1', ['filter' => 'role:superadmin']);
    $routes->put('publishers/(\\d+)', 'Admin\\PublisherController::update/$1', ['filter' => 'role:superadmin']);
    $routes->delete('publishers/(\\d+)', 'Admin\\PublisherController::destroy/$1', ['filter' => 'role:superadmin']);
    $routes->post('publishers/bulk-delete', 'Admin\\PublisherController::bulkDelete', ['filter' => 'role:superadmin']);

    $routes->get('users', 'Admin\\UserController::index', ['filter' => 'role:superadmin']);
    $routes->get('users/create', 'Admin\\UserController::create', ['filter' => 'role:superadmin']);
    $routes->post('users', 'Admin\\UserController::store', ['filter' => 'role:superadmin']);
    $routes->get('users/(\\d+)/edit', 'Admin\\UserController::edit/$1', ['filter' => 'role:superadmin']);
    $routes->put('users/(\\d+)', 'Admin\\UserController::update/$1', ['filter' => 'role:superadmin']);
    $routes->put('users/(\\d+)/password', 'Admin\\UserController::updatePassword/$1', ['filter' => 'role:superadmin']);
    $routes->post('users/(\\d+)/password', 'Admin\\UserController::updatePassword/$1', ['filter' => 'role:superadmin']);
    $routes->delete('users/(\\d+)', 'Admin\\UserController::destroy/$1', ['filter' => 'role:superadmin']);
    $routes->post('users/(\\d+)/delete', 'Admin\\UserController::destroy/$1', ['filter' => 'role:superadmin']);
    $routes->post('users/bulk-delete', 'Admin\\UserController::bulkDelete', ['filter' => 'role:superadmin']);
});

$routes->group('superadmin/settings', ['filter' => ['sessionauth', 'role:superadmin']], static function ($routes): void {
    $routes->get('journals', 'Admin\\Settings\\JournalProfileController::index');
    $routes->get('journals/create', 'Admin\\Settings\\JournalProfileController::create');
    $routes->post('journals', 'Admin\\Settings\\JournalProfileController::store');
    $routes->get('journals/(\\d+)/edit', 'Admin\\Settings\\JournalProfileController::edit/$1');
    $routes->put('journals/(\\d+)', 'Admin\\Settings\\JournalProfileController::update/$1');
    $routes->delete('journals/(\\d+)', 'Admin\\Settings\\JournalProfileController::destroy/$1');
});

$routes->get('loa/request', 'Public\\LoaRequestController::create');
$routes->post('loa/request', 'Public\\LoaRequestController::store');
$routes->get('loa/status/(:segment)', 'Public\\LoaRequestController::status/$1');
$routes->get('loa/v/(:segment)', 'Public\\LoaLetterController::show/$1');
$routes->get('loa/v/(:segment)/preview', 'Public\\LoaLetterController::preview/$1');
$routes->get('loa/v/(:segment)/download', 'Public\\LoaLetterController::download/$1');
$routes->get('loa/verify', 'Public\\LoaVerifyController::form');
$routes->post('loa/verify', 'Public\\LoaVerifyController::submit');
$routes->get('loa/verify/result', 'Public\\LoaVerifyController::result');

