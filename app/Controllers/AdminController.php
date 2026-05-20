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

    public function departements()
    {
        $this->requireLogin();

        $data = [
            'admin' => SqliteDb::fetchOne('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id WHERE e.id = :id LIMIT 1', [':id' => (int) session()->get('employe_id')]) ?? [],
            'departements' => SqliteDb::fetchAll('SELECT * FROM departements ORDER BY nom ASC'),
        ];

        return view('admin/departements', $data);
    }

    public function typeConges()
    {
        $this->requireLogin();

        $data = [
            'admin' => SqliteDb::fetchOne('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id WHERE e.id = :id LIMIT 1', [':id' => (int) session()->get('employe_id')]) ?? [],
            'typesConge' => SqliteDb::fetchAll('SELECT * FROM types_conge ORDER BY libelle ASC'),
        ];

        return view('admin/typeConge', $data);
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
            'statsMonthly' => $this->getMonthlyStats(),
            'statsDaily' => $this->getDailyStats(),
        ];

        return view('admin/conges', $data);
    }

    private function getMonthlyStats()
    {
        $currentYear = date('Y');
        $monthlyData = array_fill(0, 12, 0); // Initialize all months (0-11) with 0
        
        $results = SqliteDb::fetchAll(
            "SELECT strftime('%m', date_debut) as month, COUNT(*) as count
             FROM conges
             WHERE strftime('%Y', date_debut) = :year
             GROUP BY strftime('%m', date_debut)
             ORDER BY month",
            [':year' => $currentYear]
        );

        foreach ($results as $row) {
            $monthNum = (int) $row['month'];
            $monthlyData[$monthNum - 1] = (int) $row['count'];
        }

        $monthLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        
        return [
            'labels' => $monthLabels,
            'data' => array_values($monthlyData)
        ];
    }

    private function getDailyStats()
    {
        // We'll count each day covered by approved leaves. Initialize counters for Sun(0)..Sat(6)
        $dailyData = array_fill(0, 7, 0);

        // Fetch only approved leaves
        $conges = SqliteDb::fetchAll(
            "SELECT date_debut, date_fin FROM conges WHERE statut = 'Approuvé'"
        );

        foreach ($conges as $c) {
            if (empty($c['date_debut'])) continue;

            $start = new \DateTime($c['date_debut']);
            $end = !empty($c['date_fin']) ? new \DateTime($c['date_fin']) : clone $start;

            // ensure end >= start
            if ($end < $start) continue;

            $period = new \DatePeriod($start, new \DateInterval('P1D'), $end->modify('+1 day'));
            foreach ($period as $dt) {
                $w = (int) $dt->format('w'); // 0 (Sun) - 6 (Sat)
                $dailyData[$w]++;
            }
        }

        // Reorder to start from Monday
        $orderedLabels = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $orderedData = [
            $dailyData[1], // Monday
            $dailyData[2], // Tuesday
            $dailyData[3], // Wednesday
            $dailyData[4], // Thursday
            $dailyData[5], // Friday
            $dailyData[6], // Saturday
            $dailyData[0]  // Sunday
        ];

        return [
            'labels' => $orderedLabels,
            'data' => $orderedData
        ];
    }

    public function departementForm(){
        $this->requireLogin();
        return view('admin/departementForm', [
            'admin' => SqliteDb::fetchOne('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id WHERE e.id = :id LIMIT 1', [':id' => (int) session()->get('employe_id')]) ?? [],
            'isEdit' => false,
            'formAction' => site_url('admin/departement/submit'),
            'submitLabel' => 'Soumettre',
            'departement' => [],
        ]);
    }

    public function submitDepartement(){
        $this->requireLogin();
        $nom = trim((string) $this->request->getPost('nom'));
        $description = trim((string) $this->request->getPost('description'));
        if ($nom) {
            SqliteDb::execute(
                'INSERT INTO departements (nom, description) VALUES (:nom, :description)',
                [
                    ':nom' => $nom,
                    ':description' => $description !== '' ? $description : null,
                ]
            );
        }
        return redirect()->to('/admin/departement/form')->with('success', 'Département ajouté avec succès');
    }

    public function editDepartement($id)
    {
        $this->requireLogin();
        $departement = SqliteDb::fetchOne('SELECT * FROM departements WHERE id = :id LIMIT 1', [':id' => (int) $id]);
        if (!$departement) {
            return redirect()->to('/admin/departements')->with('error', 'Département introuvable.');
        }

        return view('admin/departementForm', [
            'admin' => SqliteDb::fetchOne('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id WHERE e.id = :id LIMIT 1', [':id' => (int) session()->get('employe_id')]) ?? [],
            'departement' => $departement,
            'isEdit' => true,
            'formAction' => site_url('admin/departement/update/' . $id),
            'submitLabel' => 'Mettre à jour',
        ]);
    }

    public function updateDepartement($id)
    {
        $this->requireLogin();
        $nom = trim((string) $this->request->getPost('nom'));
        $description = trim((string) $this->request->getPost('description'));

        if ($nom === '') {
            return redirect()->to('/admin/departement/edit/' . $id)->with('error', 'Le nom du département est obligatoire.')->withInput();
        }

        SqliteDb::execute(
            'UPDATE departements SET nom = :nom, description = :description WHERE id = :id',
            [
                ':id' => (int) $id,
                ':nom' => $nom,
                ':description' => $description !== '' ? $description : null,
            ]
        );

        return redirect()->to('/admin/departements')->with('success', 'Département mis à jour avec succès.');
    }

    public function typeCongeForm()
    {
        $this->requireLogin();

        return view('admin/type_conge_form', [
            'admin' => SqliteDb::fetchOne('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id WHERE e.id = :id LIMIT 1', [':id' => (int) session()->get('employe_id')]) ?? [],
            'isEdit' => false,
            'formAction' => site_url('admin/type-conge/submit'),
            'submitLabel' => 'Soumettre',
            'typeConge' => [],
        ]);
    }

    public function submitTypeConge()
    {
        $this->requireLogin();
        $libelle = trim((string) $this->request->getPost('libelle'));
        $joursAnnuels = (int) $this->request->getPost('jours_annuels');
        $deductible = (int) $this->request->getPost('deductible');

        if ($libelle === '') {
            return redirect()->to('/admin/type-conge/form')->with('error', 'Le libellé est obligatoire.')->withInput();
        }

        SqliteDb::execute(
            'INSERT INTO types_conge (libelle, jours_annuels, deductible) VALUES (:libelle, :jours_annuels, :deductible)',
            [
                ':libelle' => $libelle,
                ':jours_annuels' => $joursAnnuels,
                ':deductible' => $deductible,
            ]
        );

        return redirect()->to('/admin/type-conge/form')->with('success', 'Type de congé ajouté avec succès.');
    }

    public function editTypeConge($id)
    {
        $this->requireLogin();
        $typeConge = SqliteDb::fetchOne('SELECT * FROM types_conge WHERE id = :id LIMIT 1', [':id' => (int) $id]);
        if (!$typeConge) {
            return redirect()->to('/admin/type-conges')->with('error', 'Type de congé introuvable.');
        }

        return view('admin/type_conge_form', [
            'admin' => SqliteDb::fetchOne('SELECT e.*, d.nom AS departement_nom FROM employes e LEFT JOIN departements d ON d.id = e.departement_id WHERE e.id = :id LIMIT 1', [':id' => (int) session()->get('employe_id')]) ?? [],
            'typeConge' => $typeConge,
            'isEdit' => true,
            'formAction' => site_url('admin/type-conge/update/' . $id),
            'submitLabel' => 'Mettre à jour',
        ]);
    }

    public function updateTypeConge($id)
    {
        $this->requireLogin();
        $libelle = trim((string) $this->request->getPost('libelle'));
        $joursAnnuels = (int) $this->request->getPost('jours_annuels');
        $deductible = (int) $this->request->getPost('deductible');

        if ($libelle === '') {
            return redirect()->to('/admin/type-conge/edit/' . $id)->with('error', 'Le libellé est obligatoire.')->withInput();
        }

        SqliteDb::execute(
            'UPDATE types_conge SET libelle = :libelle, jours_annuels = :jours_annuels, deductible = :deductible WHERE id = :id',
            [
                ':id' => (int) $id,
                ':libelle' => $libelle,
                ':jours_annuels' => $joursAnnuels,
                ':deductible' => $deductible,
            ]
        );

        return redirect()->to('/admin/type-conges')->with('success', 'Type de congé mis à jour avec succès.');
    }

}
