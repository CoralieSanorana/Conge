<?php

namespace App\Controllers;

use App\Libraries\SqliteDb;

class RHController extends BaseController
{
    public function dashboard()
    {
        if (!session()->get('employe_id')) {
            return redirect()->to('/login');
        }

        $data = [
            'rh' => SqliteDb::fetchOne('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id WHERE e.id = :id LIMIT 1', [':id' => (int) session()->get('employe_id')]) ?? [],
            'pendingCount' => (int) (SqliteDb::fetchOne("SELECT COUNT(*) AS total FROM conges WHERE statut = 'En attente'")['total'] ?? 0),
            'approvedCount' => (int) (SqliteDb::fetchOne("SELECT COUNT(*) AS total FROM conges WHERE statut = 'Approuvé'")['total'] ?? 0),
            'rejectedCount' => (int) (SqliteDb::fetchOne("SELECT COUNT(*) AS total FROM conges WHERE statut = 'Refusé'")['total'] ?? 0),
            'pendingConges' => SqliteDb::fetchAll(
                'SELECT c.*, e.prenom, e.nom, t.libelle AS type_conge_libelle
                 FROM conges c
                 LEFT JOIN employes e ON e.id = c.employe_id
                 LEFT JOIN types_conge t ON t.id = c.type_conge_id
                 WHERE c.statut = :statut
                 ORDER BY c.created_at DESC',
                [':statut' => 'En attente']
            ),
        ];

        return view('rh/dashboard', $data);
    }

    public function approbation()
    {
        if (!session()->get('employe_id')) {
            return redirect()->to('/login');
        }

        $rh = SqliteDb::fetchOne('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id WHERE e.id = :id LIMIT 1', [':id' => (int) session()->get('employe_id')]) ?? [];

        return view('rh/conges', [
            'rh' => $rh,
            'conges' => SqliteDb::fetchAll(
                'SELECT c.*, e.prenom, e.nom, t.libelle AS type_conge_libelle
                 FROM conges c
                 LEFT JOIN employes e ON e.id = c.employe_id
                 LEFT JOIN types_conge t ON t.id = c.type_conge_id
                 WHERE c.statut = :statut
                 ORDER BY c.created_at DESC',
                [':statut' => 'En attente']
            ),
        ]);
    }
}
