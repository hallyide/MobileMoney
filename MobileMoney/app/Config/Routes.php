<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Espace operateur.
$routes->group('admin', static function (RouteCollection $routes): void {
    $routes->get('/', 'Admin\PrefixeController::index');
    $routes->get('dashboard', 'Admin\PrefixeController::index');

    $routes->get('prefixes', 'Admin\PrefixeController::index');
    $routes->post('prefixes', 'Admin\PrefixeController::store');
    $routes->post('prefixes/(:num)', 'Admin\PrefixeController::update/$1');
    $routes->post('prefixes/(:num)/supprimer', 'Admin\PrefixeController::delete/$1');

    $routes->get('baremes', 'BaremeController::index');
    $routes->post('baremes', 'BaremeController::create');
    $routes->post('baremes/(:num)', 'BaremeController::update/$1');
    $routes->post('baremes/(:num)/supprimer', 'BaremeController::delete/$1');

    $routes->get('gains', 'GainsController::index');
    $routes->get('operateurs', 'Admin\OperateurController::index');
    $routes->post('operateurs', 'Admin\OperateurController::store');
    $routes->post('operateurs/(:num)', 'Admin\OperateurController::update/$1');
    $routes->post('operateurs/(:num)/supprimer', 'Admin\OperateurController::delete/$1');
    $routes->post('operateurs/(:num)/prefixes', 'Admin\OperateurController::storePrefixe/$1');
    $routes->post('prefixes-externes/(:num)', 'Admin\OperateurController::updatePrefixe/$1');
    $routes->post('prefixes-externes/(:num)/supprimer', 'Admin\OperateurController::deletePrefixe/$1');
    $routes->post('operateurs/(:num)/commission', 'Admin\OperateurController::updateCommission/$1');
    $routes->get('versements', 'Admin\VersementController::index');
    $routes->get('comptes', 'Admin\CompteController::index');
    $routes->get('comptes/(:num)', 'Admin\CompteController::show/$1');
});

// Connexion automatique : un numero valide ouvre ou cree son compte.
$routes->get('client/login', 'AuthController::index');
$routes->post('client/login', 'AuthController::login');
$routes->get('client/logout', 'AuthController::logout');

// Espace client identifie par la session, jamais par un identifiant dans l'URL.
$routes->get('client', 'Client\CompteController::index');
$routes->get('client/depot', 'Client\CompteController::depotForm');
$routes->post('client/depot', 'Client\CompteController::depot');
$routes->get('client/transfert', 'Client\CompteController::transfertForm');
$routes->post('client/transfert/apercu', 'Client\CompteController::transfertApercu');
$routes->post('client/transfert', 'Client\CompteController::transfert');
$routes->get('client/retrait', 'RetraitController::index');
$routes->post('client/retrait', 'RetraitController::process');
$routes->get('client/historique', 'HistoriqueController::index');
