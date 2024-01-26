<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

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
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// $routes->get('/', 'Home::index');
$routes->group('', function ($routes) {
    $routes->get('/', 'Login::index');
    $routes->post('/login', 'Login::login');
    $routes->get('/logout', 'Login::logout');
});

$routes->group('home', ['filter' => 'Auth'], function ($routes) {
    $routes->get('', 'Home::index');
});

$routes->group('user', ['filter' => 'Auth'], function ($routes) {
    $routes->get('', 'User::index');
    $routes->get('all', 'User::all');
    $routes->post('create', 'User::create');
    $routes->post('update/(:num)', 'User::update/$1');
    $routes->post('delete', 'User::remove');
});