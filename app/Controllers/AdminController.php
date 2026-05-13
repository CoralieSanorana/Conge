<?php

namespace App\Controllers;

use App\Libraries\SqliteDb;

class AdminController extends BaseController
{
    protected function requireLogin()
    {
        if (!session()->get('employe_id')) {
            redirect()->to('/login')->send();
            exit;
        }
    }

    public function dashboard()
    {
        $this->requireLogin(); 

        $data = [
            'admin' => SqliteDb::fetchOne('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id WHERE e.id = :id LIMIT 1', [':id' => (int) session()->get('employe_id')]) ?? [],
            'employeeCount' => (int) (SqliteDb::fetchOne('SELECT COUNT(*) AS total FROM employes')['total'] ?? 0),
            'rhCount' => (int) (SqliteDb::fetchOne("SELECT COUNT(*) AS total FROM employes WHERE role = 'RH'")['total'] ?? 0),
            'adminCount' => (int) (SqliteDb::fetchOne("SELECT COUNT(*) AS total FROM employes WHERE role = 'ADMIN'")['total'] ?? 0),
            'pendingCount' => (int) (SqliteDb::fetchOne("SELECT COUNT(*) AS total FROM conges WHERE statut = 'En attente'")['total'] ?? 0),
            'approvedCount' => (int) (SqliteDb::fetchOne("SELECT COUNT(*) AS total FROM conges WHERE statut = 'Approuvé'")['total'] ?? 0),
            'rejectedCount' => (int) (SqliteDb::fetchOne("SELECT COUNT(*) AS total FROM conges WHERE statut = 'Refusé'")['total'] ?? 0),
            'recentEmployes' => SqliteDb::fetchAll('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id ORDER BY e.id DESC LIMIT 5'),
            'recentConges' => SqliteDb::fetchAll(
                'SELECT c.*, e.prenom, e.nom, t.libelle AS type_conge_libelle
                 FROM conges c
                 LEFT JOIN employes e ON e.id = c.employe_id
                 LEFT JOIN types_conge t ON t.id = c.type_conge_id
                 ORDER BY c.created_at DESC
                 LIMIT 5'
            ),
        ];

        return view('admin/dashboard', $data);
    }

    public function employes()
    {
        $this->requireLogin();

        $data = [
            'admin' => SqliteDb::fetchOne('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id WHERE e.id = :id LIMIT 1', [':id' => (int) session()->get('employe_id')]) ?? [],
            'employes' => SqliteDb::fetchAll('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id ORDER BY e.nom ASC, e.prenom ASC'),
        ];

        return view('admin/employes', $data);
    }

    public function conges()
    {
        $this->requireLogin();

        $data = [
            'admin' => SqliteDb::fetchOne('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id WHERE e.id = :id LIMIT 1', [':id' => (int) session()->get('employe_id')]) ?? [],
            'conges' => SqliteDb::fetchAll(
                'SELECT c.*, e.prenom, e.nom, t.libelle AS type_conge_libelle, d.nom AS departement_nom
                 FROM conges c
                 LEFT JOIN employes e ON e.id = c.employe_id
                 LEFT JOIN departements d ON d.id = e.departement_id
                 LEFT JOIN types_conge t ON t.id = c.type_conge_id
                 ORDER BY c.created_at DESC'
            ),
        ];

        return view('admin/conges', $data);
    }
}
