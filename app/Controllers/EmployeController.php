<?php

namespace App\Controllers;
use App\Libraries\SqliteDb;
class EmployeController extends BaseController
{
   protected function requireEmployeLogin()
    {
        if (!session()->get('employe_id')) {
            redirect()->to('/login')->send();
            exit;
        }
    }

    public function login()
    {
        if ($this->request->is('get')) {
            return view('login');
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = SqliteDb::fetchOne('SELECT * FROM employes WHERE email = :email LIMIT 1', [':email' => $email]);
        if ($user && password_verify($password, $user['password'])) {
            session()->set('employe_id', $user['id']);
            session()->set('employe_email', $user['email']);
            session()->set('employe_role', $user['role']);

            $role = strtoupper((string) ($user['role'] ?? 'EMPLOYE'));
            if ($role === 'ADMIN') {
                return redirect()->to('/admin/dashboard');
            }

            if ($role === 'RH') {
                return redirect()->to('/rh/dashboard');
            }

            return redirect()->to('/employe/dashboard');
        }

        session()->setFlashdata('error', 'Identifiants diso. Veuillez réessayer.');
        return redirect()->to('/login');
    }

    public function teste(){
        return view('employe/teste');
    }

    public function dashboard()
    {
        $this->requireEmployeLogin();

        $employeId = (int) session()->get('employe_id');

        $employe = SqliteDb::fetchOne('SELECT * FROM employes WHERE id = :id LIMIT 1', [':id' => $employeId]);
        if (!$employe) {
            return redirect()->to('/login');
        }

        $departement = null;
        if ($employe['departement_id']) {
            $departement = SqliteDb::fetchOne('SELECT * FROM departements WHERE id = :id LIMIT 1', [':id' => $employe['departement_id']]);
        }

        $annee = (int) date('Y');
        $congesAttente = SqliteDb::fetchAll(
            'SELECT c.*, t.libelle AS type_conge_libelle
             FROM conges c
             LEFT JOIN types_conge t ON t.id = c.type_conge_id
             WHERE c.employe_id = :id AND c.statut = :statut
             ORDER BY c.created_at DESC',
            [':id' => $employeId, ':statut' => 'En attente']
        );
        $congesAcceptes = SqliteDb::fetchAll(
            'SELECT c.*, t.libelle AS type_conge_libelle
             FROM conges c
             LEFT JOIN types_conge t ON t.id = c.type_conge_id
             WHERE c.employe_id = :id AND c.statut = :statut
             ORDER BY c.created_at DESC',
            [':id' => $employeId, ':statut' => 'Approuvé']
        );
        $congesRefuses = SqliteDb::fetchAll(
            'SELECT c.*, t.libelle AS type_conge_libelle
             FROM conges c
             LEFT JOIN types_conge t ON t.id = c.type_conge_id
             WHERE c.employe_id = :id AND c.statut = :statut
             ORDER BY c.created_at DESC',
            [':id' => $employeId, ':statut' => 'Refusé']
        );
        $congesRecents = SqliteDb::fetchAll(
            'SELECT c.*, t.libelle AS type_conge_libelle
             FROM conges c
             LEFT JOIN types_conge t ON t.id = c.type_conge_id
             WHERE c.employe_id = :id
             ORDER BY c.created_at DESC
             LIMIT 5',
            [':id' => $employeId]
        );
        $soldeResume = SqliteDb::fetchOne(
            'SELECT COALESCE(SUM(jours_attribues), 0) AS total_attribues,
                    COALESCE(SUM(jours_pris), 0) AS total_pris,
                    COALESCE(SUM(restant), 0) AS total_restant
             FROM soldes
             WHERE employe_id = :id AND annee = :annee',
            [':id' => $employeId, ':annee' => $annee]
        ) ?? ['total_attribues' => 0, 'total_pris' => 0, 'total_restant' => 0];
        $soldes = SqliteDb::fetchAll(
            'SELECT s.*, t.libelle, t.deductible, t.jours_annuels
             FROM soldes s
             LEFT JOIN types_conge t ON t.id = s.type_conge_id
             WHERE s.employe_id = :id AND s.annee = :annee
             ORDER BY t.libelle ASC',
            [':id' => $employeId, ':annee' => $annee]
        );

        $data = [
            'employe' => $employe,
            'departement' => $departement,
            'congesAttente' => $congesAttente,
            'congesAcceptes' => $congesAcceptes,
            'congesRefuses' => $congesRefuses,
            'congesRecents' => $congesRecents,
            'annee' => $annee,
            'soldeResume' => $soldeResume,
            'soldes' => $soldes,
        ];

        return view('employe/dashboard', $data);
    }

    public function historique()
    {
        $this->requireEmployeLogin();

        $employeId = (int) session()->get('employe_id');
        $employe = SqliteDb::fetchOne('SELECT * FROM employes WHERE id = :id LIMIT 1', [':id' => $employeId]);
        if (!$employe) {
            return redirect()->to('/login');
        }

        $departement = null;
        if (!empty($employe['departement_id'])) {
            $departement = SqliteDb::fetchOne('SELECT * FROM departements WHERE id = :id LIMIT 1', [':id' => $employe['departement_id']]);
        }

        return view('employe/historique', [
            'employe' => $employe,
            'departement' => $departement,
            'conges' => SqliteDb::fetchAll(
                'SELECT c.*, t.libelle AS type_conge_libelle
                 FROM conges c
                 LEFT JOIN types_conge t ON t.id = c.type_conge_id
                 WHERE c.employe_id = :id
                 ORDER BY c.created_at DESC',
                [':id' => $employeId]
            ),
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function profile()
    {
        $this->requireEmployeLogin();

        $employeId = (int) session()->get('employe_id');
        $employe = SqliteDb::fetchOne('SELECT * FROM employes WHERE id = :id LIMIT 1', [':id' => $employeId]);
        if (!$employe) {
            return redirect()->to('/login');
        }

        $departement = null;
        if (!empty($employe['departement_id'])) {
            $departement = SqliteDb::fetchOne('SELECT * FROM departements WHERE id = :id LIMIT 1', [':id' => $employe['departement_id']]);
        }

        $annee = (int) date('Y');
        $soldes = SqliteDb::fetchAll(
            'SELECT s.*, t.libelle
             FROM soldes s
             LEFT JOIN types_conge t ON t.id = s.type_conge_id
             WHERE s.employe_id = :id AND s.annee = :annee
             ORDER BY t.libelle ASC',
            [':id' => $employeId, ':annee' => $annee]
        );

        return view('employe/profile', [
            'employe' => $employe,
            'departement' => $departement,
            'soldes' => $soldes,
        ]);
    }
}
