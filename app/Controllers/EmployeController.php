<?php

namespace App\Controllers;
use App\Libraries\SqliteDb;
class EmployeController extends BaseController
{
    private function roleLabel(string $role): string
    {
        $role = strtoupper($role);

        return match ($role) {
            'ADMIN' => 'Administrateur',
            'RH' => 'Responsable RH',
            default => 'Employe',
        };
    }

    private function buildInitiales(string $prenom, string $nom): string
    {
        $prenom = trim($prenom);
        $nom = trim($nom);

        $first = $prenom !== '' ? strtoupper(substr($prenom, 0, 1)) : '';
        $last = $nom !== '' ? strtoupper(substr($nom, 0, 1)) : '';

        return ($first . $last) !== '' ? $first . $last : '--';
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
            if(strtoupper($user['role']) === 'ADMIN'){
                return redirect()->to('/admin/dashboard');
            } elseif (strtoupper($user['role']) === 'RH') {
                return redirect()->to('/rh/dashboard');
            }
            return redirect()->to('/employe/dashboard');
        }

        session()->setFlashdata('error', 'Identifiants diso. Veuillez réessayer.');
        return redirect()->to('/login');
    }

    public function historique(){
        $employeId = session()->get('employe_id')?:1;
        if (!$employeId) {
            return redirect()->to('/login');
        }
        $historique = SqliteDb::fetchAll(
            'SELECT c.*, t.libelle AS type_conge_libelle
             FROM conges c
             LEFT JOIN types_conge t ON t.id = c.type_conge_id
             WHERE c.employe_id = :id
             ORDER BY c.created_at DESC',
            [':id' => $employeId]
        );
        return view('employe/historique', ['conges' => $historique]);
    }

    public function dashboard()
    {
        $employeId = session()->get('employe_id')?:1;
        if (!$employeId) {
            return redirect()->to('/login');
        }

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

    public function calendar()
    {
        $employeId = session()->get('employe_id')?:1;
        if (!$employeId) {
            return redirect()->to('/login');
        }

        $employe = SqliteDb::fetchOne('SELECT * FROM employes WHERE id = :id LIMIT 1', [':id' => $employeId]);
        if (!$employe) {
            return redirect()->to('/login');
        }

        $departement = null;
        if ($employe['departement_id']) {
            $departement = SqliteDb::fetchOne('SELECT * FROM departements WHERE id = :id LIMIT 1', [':id' => $employe['departement_id']]);
        }

        $conges = SqliteDb::fetchAll(
            'SELECT c.*, t.libelle AS type_conge_libelle
             FROM conges c
             LEFT JOIN types_conge t ON t.id = c.type_conge_id
             WHERE c.employe_id = :id
             ORDER BY c.created_at DESC',
            [':id' => $employeId]
        );

        $nomComplet = trim(($employe['prenom'] ?? '') . ' ' . ($employe['nom'] ?? '')) ?: 'Employé';
        $departementNom = $departement['nom'] ?? 'Aucun département';
        $totalDemandes = count($conges);

        $data = [
            'employe' => $employe,
            'departement' => $departement,
            'nomComplet' => $nomComplet,
            'departementNom' => $departementNom,
            'totalDemandes' => $totalDemandes,
        ];

        return view('employe/calendar', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function profile()
    {
        $employeId = session()->get('employe_id')?:1;
        if (!$employeId) {
            return redirect()->to('/login');
        }
        $employe = SqliteDb::fetchOne('SELECT * FROM employes WHERE id = :id LIMIT 1', [':id' => $employeId]);

        return view('employe/profile', ['employe' => $employe]);
    }

    public function employeForm()
    {
        $annee = (int) date('Y');
        $departements = SqliteDb::fetchAll('SELECT * FROM departements ORDER BY nom ASC');
        $employes = SqliteDb::fetchAll(
            'SELECT e.*, d.nom AS departement_nom,
                    COALESCE(SUM(s.jours_attribues), 0) AS solde_total_attribue,
                    COALESCE(SUM(s.restant), 0) AS solde_total_restant
             FROM employes e
             LEFT JOIN departements d ON d.id = e.departement_id
             LEFT JOIN soldes s ON s.employe_id = e.id AND s.annee = :annee
             GROUP BY e.id
             ORDER BY e.nom ASC, e.prenom ASC',
            [':annee' => $annee]
        );

        foreach ($employes as &$employe) {
            $employe['initiales'] = $this->buildInitiales((string) ($employe['prenom'] ?? ''), (string) ($employe['nom'] ?? ''));
            $employe['role_label'] = $this->roleLabel((string) ($employe['role'] ?? 'EMPLOYE'));
            $employe['statut_label'] = ((int) ($employe['actif'] ?? 0) === 1) ? 'actif' : 'inactif';
            $employe['statut_class'] = ((int) ($employe['actif'] ?? 0) === 1) ? 's-approuvee' : 's-refusee';
        }
        unset($employe);

        $data['departements'] = $departements;
        $data['employes'] = $employes;
        $data['annee'] = $annee;
        $data['isEdit'] = false;
        $data['formAction'] = site_url('admin/employe/submit');
        $data['submitLabel'] = 'Créer l\'employé';

        return view('admin/employeForm', $data);
    }

    public function submitEmploye()
    {
        $nom = trim((string) $this->request->getPost('nom'));
        $prenom = trim((string) $this->request->getPost('prenom'));
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');
        $departementId = (int) $this->request->getPost('departement_id');
        $role = strtolower(trim((string) $this->request->getPost('role')));
        $dateEmbauche = $this->request->getPost('date_embauche');

        if (!$nom || !$prenom || !$email || !$password || !$departementId || !$role || !$dateEmbauche) {
            session()->setFlashdata('error', 'Tous les champs sont requis.');
            return redirect()->to('/admin/employe/form')->withInput();
        }

        if (SqliteDb::fetchOne('SELECT id FROM employes WHERE email = :email LIMIT 1', [':email' => $email])) {
            session()->setFlashdata('error', 'Cet email est déjà utilisé par un autre employé.');
            return redirect()->to('/admin/employe/form')->withInput();
        }

        SqliteDb::execute(
            'INSERT INTO employes (nom, prenom, email, password, role, departement_id, date_embauche, actif)
             VALUES (:nom, :prenom, :email, :password, :role, :departement_id, :date_embauche, :actif)',
            [
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_BCRYPT),
                ':role' => strtoupper($role),
                ':departement_id' => $departementId,
                ':date_embauche' => $dateEmbauche,
                ':actif' => 1,
            ]
        );

        $newEmploye = SqliteDb::fetchOne('SELECT id FROM employes WHERE email = :email LIMIT 1', [':email' => $email]);
        if ($newEmploye) {
            $annee = (int) date('Y');
            $typesConge = SqliteDb::fetchAll('SELECT id, jours_annuels FROM types_conge');

            foreach ($typesConge as $typeConge) {
                $jours = (int) ($typeConge['jours_annuels'] ?? 0);
                SqliteDb::execute(
                    'INSERT INTO soldes (employe_id, type_conge_id, annee, jours_attribues, jours_pris, restant)
                     VALUES (:employe_id, :type_conge_id, :annee, :jours_attribues, :jours_pris, :restant)',
                    [
                        ':employe_id' => (int) $newEmploye['id'],
                        ':type_conge_id' => (int) $typeConge['id'],
                        ':annee' => $annee,
                        ':jours_attribues' => $jours,
                        ':jours_pris' => 0,
                        ':restant' => $jours,
                    ]
                );
            }
        }

        session()->setFlashdata('success', 'Employé créé avec succès.');
        return redirect()->to('/admin/employe/form');
    }

    public function updateEmploye($id){
        $existing = SqliteDb::fetchOne('SELECT * FROM employes WHERE id = :id LIMIT 1', [':id' => (int) $id]);
        if (!$existing) {
            session()->setFlashdata('error', 'Employé introuvable.');
            return redirect()->to('/admin/employes');
        }

        $nom = trim((string) $this->request->getPost('nom'));
        $prenom = trim((string) $this->request->getPost('prenom'));
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');
        $departementId = (int) $this->request->getPost('departement_id');
        $role = strtolower(trim((string) $this->request->getPost('role')));
        $dateEmbauche = $this->request->getPost('date_embauche');

        if (!$nom || !$prenom || !$email || !$departementId || !$role || !$dateEmbauche) {
            session()->setFlashdata('error', 'Tous les champs sont requis.');
            return redirect()->to('/admin/employe/edit/' . $id)->withInput();
        }

        $passwordHash = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : (string) ($existing['password'] ?? '');

        SqliteDb::execute(
            'UPDATE employes SET nom=:nom, prenom=:prenom, email=:email, 
            password=:password, role=:role, departement_id=:departement_id, date_embauche=:date_embauche, actif=:actif
            WHERE id = :id',
            [
                ':id' => $id,
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':password' => $passwordHash,
                ':role' => strtoupper($role),
                ':departement_id' => $departementId,
                ':date_embauche' => $dateEmbauche,
                ':actif' => 1,
            ]
        );

        session()->setFlashdata('success', 'Employé mis à jour avec succès.');
        return redirect()->to('/admin/employes');
    }

    public function editEmploye($id){
        $employe = SqliteDb::fetchOne('SELECT * FROM employes WHERE id = :id LIMIT 1', [':id' => (int) $id]);
        if (!$employe) {
            session()->setFlashdata('error', 'Employé introuvable.');
            return redirect()->to('/admin/employes');
        }

        $departements = SqliteDb::fetchAll('SELECT * FROM departements ORDER BY nom ASC');
        $annee = (int) date('Y');
        $employes = SqliteDb::fetchAll(
            'SELECT e.*, d.nom AS departement_nom,
                    COALESCE(SUM(s.jours_attribues), 0) AS solde_total_attribue,
                    COALESCE(SUM(s.restant), 0) AS solde_total_restant
             FROM employes e
             LEFT JOIN departements d ON d.id = e.departement_id
             LEFT JOIN soldes s ON s.employe_id = e.id AND s.annee = :annee
             GROUP BY e.id
             ORDER BY e.nom ASC, e.prenom ASC',
            [':annee' => $annee]
        );

        foreach ($employes as &$item) {
            $item['initiales'] = $this->buildInitiales((string) ($item['prenom'] ?? ''), (string) ($item['nom'] ?? ''));
            $item['role_label'] = $this->roleLabel((string) ($item['role'] ?? 'EMPLOYE'));
            $item['statut_label'] = ((int) ($item['actif'] ?? 0) === 1) ? 'actif' : 'inactif';
            $item['statut_class'] = ((int) ($item['actif'] ?? 0) === 1) ? 's-approuvee' : 's-refusee';
        }
        unset($item);

        return view('admin/employeForm', [
            'employe' => $employe,
            'departements' => $departements,
            'employes' => $employes,
            'annee' => $annee,
            'isEdit' => true,
            'formAction' => site_url('admin/employe/update/' . $id),
            'submitLabel' => 'Mettre à jour l\'employé',
        ]);
    }
}
