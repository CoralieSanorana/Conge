<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'EmployeController::login');
$routes->post('/login', 'EmployeController::login');
$routes->get('/logout', 'EmployeController::logout');

// Routes pour les employés
$routes->group('employe', function($routes) {
    $routes->get('dashboard', 'EmployeController::dashboard');
    $routes->get('teste', 'EmployeController::teste');
    $routes->get('logout', 'EmployeController::logout');
    $routes->get('profile', 'EmployeController::profile');
    $routes->get('conge/demande', 'CongeController::demande');
    $routes->post('conge/submit', 'CongeController::submitDemande');
    $routes->get('conge/historique', 'CongeController::historique');
});

// Routes pour les RH
$routes->group('rh', function($routes) {
    $routes->get('dashboard', 'RHController::dashboard');
    $routes->get('conge/approbation', 'CongeController::approbation');
    $routes->post('conge/approbation', 'CongeController::submitApprobation');
});

// Routes pour les administrateurs