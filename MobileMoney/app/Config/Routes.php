<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Admin\CompteController::index');

// Back-office operateur : prefixes et situation des comptes.
$routes->group('admin', static function ($routes) {
    $routes->get('prefixes', 'Admin\PrefixeController::index');
    $routes->post('prefixes', 'Admin\PrefixeController::store');
    $routes->post('prefixes/(:num)', 'Admin\PrefixeController::update/$1');
    $routes->post('prefixes/(:num)/supprimer', 'Admin\PrefixeController::delete/$1');

    $routes->get('comptes', 'Admin\CompteController::index');
    $routes->get('comptes/(:num)', 'Admin\CompteController::show/$1');
});

// Espace client. L'identifiant sera remplace par la session apres integration
// du module de connexion automatique realise par ETU4269.
$routes->get('client/(:num)', 'Client\CompteController::index/$1');
$routes->get('client/(:num)/depot', 'Client\CompteController::depotForm/$1');
$routes->post('client/(:num)/depot', 'Client\CompteController::depot/$1');
$routes->get('client/(:num)/transfert', 'Client\CompteController::transfertForm/$1');
$routes->post('client/(:num)/transfert', 'Client\CompteController::transfert/$1');
