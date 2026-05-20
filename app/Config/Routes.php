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
    $routes->get('conge/calendar', 'EmployeController::calendar');
    $routes->get('conge/events', 'CongeController::events');
    $routes->get('conge/historique', 'EmployeController::historique');
    $routes->get('conge/demande', 'CongeController::demande');
    $routes->post('conge/submit', 'CongeController::submitDemande');
});

// Routes pour les RH
$routes->group('rh', function($routes) {
    $routes->get('dashboard', 'RHController::dashboard');
    $routes->get('conge/approbation', 'RHController::approbation');
    $routes->post('conge/approbation', 'CongeController::submitApprobation');
    $routes->get('conges', 'AdminController::conges');
});

// Routes pour les administrateurs
$routes->group('admin', function($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->get('employe/form', 'EmployeController::employeForm');
    $routes->post('employe/submit', 'EmployeController::submitEmploye');
    $routes->get('employe/edit/(:num)', 'EmployeController::editEmploye/$1');
    $routes->post('employe/update/(:num)', 'EmployeController::updateEmploye/$1');
    $routes->get('departements', 'AdminController::departements');
    $routes->get('departement/form', 'AdminController::departementForm');
    $routes->get('departement/edit/(:num)', 'AdminController::editDepartement/$1');
    $routes->post('departement/submit', 'AdminController::submitDepartement');
    $routes->post('departement/update/(:num)', 'AdminController::updateDepartement/$1');
    $routes->get('type-conges', 'AdminController::typeConges');
    $routes->get('type-conge/form', 'AdminController::typeCongeForm');
    $routes->get('type-conge/edit/(:num)', 'AdminController::editTypeConge/$1');
    $routes->post('type-conge/submit', 'AdminController::submitTypeConge');
    $routes->post('type-conge/update/(:num)', 'AdminController::updateTypeConge/$1');

    $routes->get('/', 'AdminController::dashboard');
    $routes->get('dashbord', 'AdminController::dashboard');
    $routes->get('employes', 'AdminController::employes');
    $routes->get('conges', 'AdminController::conges');
});