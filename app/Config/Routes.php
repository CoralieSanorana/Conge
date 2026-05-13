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
    $routes->get('logout', 'EmployeController::logout');
    $routes->get('profile', 'EmployeController::profile');
    $routes->get('conge/historique', 'EmployeController::historique');
    $routes->get('conge/demande', 'CongeController::demande');
    $routes->post('conge/submit', 'CongeController::submitDemande');
});

// Routes pour les RH
$routes->group('rh', function($routes) {
    $routes->get('dashboard', 'RHController::dashboard');
    $routes->get('conge/approbation', 'RHController::approbation');
    $routes->post('conge/approbation', 'CongeController::submitApprobation');
});

// Routes pour les administrateurs
$routes->group('admin', function($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->get('employe/form', 'EmployeController::employeForm');
    $routes->post('employe/submit', 'EmployeController::submitEmploye');
    $routes->get('departement/form', 'AdminController::departementForm');
    $routes->post('departement/submit', 'AdminController::submitDepartement');
    $routes->get('type-conge/form', 'AdminController::typeCongeForm');
    $routes->post('type-conge/submit', 'AdminController::submitTypeConge');

    $routes->get('/', 'AdminController::dashboard');
    $routes->get('dashbord', 'AdminController::dashboard');
    $routes->get('employes', 'AdminController::employes');
    $routes->get('conges', 'AdminController::conges');
});