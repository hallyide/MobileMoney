<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('admin/dashboard', 'Home::dash');


// --- ETU4269 : Barème des frais ---
$routes->get('api/bareme', 'BaremeController::index');
$routes->post('api/bareme', 'BaremeController::create');
$routes->post('api/bareme/(:num)', 'BaremeController::update/$1');
$routes->delete('api/bareme/(:num)', 'BaremeController::delete/$1');

// --- ETU4269 : Gains opérateur ---
$routes->get('api/gains', 'GainsController::index');
$routes->post('api/gains', 'GainsController::create');
$routes->post('api/gains/(:num)', 'GainsController::update/$1');
$routes->delete('api/gains/(:num)', 'GainsController::delete/$1');

// --- ETU4269 : Connexion client automatique ---
$routes->post('api/auth/login', 'AuthController::login');

// --- ETU4269 : Retrait client ---
$routes->post('api/retrait', 'RetraitController::process');

// --- ETU4269 : Historique transactions client ---
$routes->get('api/historique/(:segment)', 'HistoriqueController::index/$1');
