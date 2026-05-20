<?php

namespace App\Controllers;

use App\Libraries\SqliteDb;
use App\Models\Solde;

class CongeController extends BaseController
{
    public function demande()
    {
        if (!session()->get('employe_id')) {
            return redirect()->to('/login');
        }

        $employeId = (int) session()->get('employe_id');
        $employe = SqliteDb::fetchOne('SELECT * FROM employes WHERE id = :id LIMIT 1', [':id' => $employeId]) ?? [];
        $departement = !empty($employe['departement_id']) ? SqliteDb::fetchOne('SELECT * FROM departements WHERE id = :id LIMIT 1', [':id' => $employe['departement_id']]) : null;
        $annee = (int) date('Y');

        $data = [
            'employe' => $employe,
            'departement' => $departement,
            'typesConge' => SqliteDb::fetchAll('SELECT * FROM types_conge ORDER BY libelle ASC'),
            'soldes' => SqliteDb::fetchAll(
                'SELECT s.*, t.libelle
                 FROM soldes s
                 LEFT JOIN types_conge t ON t.id = s.type_conge_id
                 WHERE s.employe_id = :id AND s.annee = :annee
                 ORDER BY t.libelle ASC',
                [':id' => $employeId, ':annee' => $annee]
            ),
            'soldeResume' => SqliteDb::fetchOne(
                'SELECT COALESCE(SUM(jours_attribues), 0) AS total_attribues,
                        COALESCE(SUM(jours_pris), 0) AS total_pris,
                        COALESCE(SUM(restant), 0) AS total_restant
                 FROM soldes
                 WHERE employe_id = :id AND annee = :annee',
                [':id' => $employeId, ':annee' => $annee]
            ) ?? ['total_attribues' => 0, 'total_pris' => 0, 'total_restant' => 0],
        ];

        return view('employe/demande', $data);
    }

    public function submitDemande()
    {
        if (!session()->get('employe_id')) {
            return redirect()->to('/login');
        }

        $userId = (int) session()->get('employe_id');
        $typeCongeId = (int) $this->request->getPost('type_conge_id');
        $dateDebut = trim((string) $this->request->getPost('date_debut'));
        $dateFin = trim((string) $this->request->getPost('date_fin'));
        $motif = trim((string) $this->request->getPost('motif'));

        // Validation des champs obligatoires
        if (!$typeCongeId || !$dateDebut || !$dateFin) {
            session()->setFlashdata('error', 'Tous les champs obligatoires doivent être complétés.');
            return redirect()->back();
        }

        // Validation des dates
        if (strtotime($dateDebut) === false || strtotime($dateFin) === false) {
            session()->setFlashdata('error', 'Les dates saisies sont invalides.');
            return redirect()->back();
        }

        if (strtotime($dateDebut) > strtotime($dateFin)) {
            session()->setFlashdata('error', 'La date de début ne peut pas être après la date de fin.');
            return redirect()->back();
        }

        // Calcul du nombre de jours
        $nbJours = (int) ((strtotime($dateFin) - strtotime($dateDebut)) / (60 * 60 * 24) + 1);

        if ($nbJours <= 0) {
            session()->setFlashdata('error', 'La durée de la demande doit être d\'au moins 1 jour.');
            return redirect()->back();
        }

        // Vérifier que le type de congé existe
        $typeConge = SqliteDb::fetchOne(
            'SELECT id, libelle FROM types_conge WHERE id = :id LIMIT 1',
            [':id' => $typeCongeId]
        );

        if (!$typeConge) {
            session()->setFlashdata('error', 'Le type de congé sélectionné est invalide.');
            return redirect()->back();
        }

        // Vérifier le solde disponible
        $annee = (int) date('Y');
        $solde = SqliteDb::fetchOne(
            'SELECT restant, jours_attribues FROM soldes WHERE employe_id = :employe_id AND type_conge_id = :type_conge_id AND annee = :annee LIMIT 1',
            [':employe_id' => $userId, ':type_conge_id' => $typeCongeId, ':annee' => $annee]
        );

        if (!$solde) {
            session()->setFlashdata('error', 'Vous n\'avez pas d\'allocation pour ce type de congé cette année.');
            return redirect()->back();
        }

        $soldeRestant = (int) ($solde['restant'] ?? 0);

        if ($nbJours > $soldeRestant) {
            session()->setFlashdata(
                'error',
                'Solde insuffisant pour cette demande. Vous avez ' . $soldeRestant . ' jour(s) restant(s) pour ' . 
                esc($typeConge['libelle']) . ', mais vous en demandez ' . $nbJours . '.'
            );
            return redirect()->back();
        }

        // Créer la demande si toutes les validations sont passées
        SqliteDb::execute(
            'INSERT INTO conges (employe_id, type_conge_id, date_debut, date_fin, nb_jours, motif, statut, created_at)
             VALUES (:employe_id, :type_conge_id, :date_debut, :date_fin, :nb_jours, :motif, :statut, :created_at)',
            [
                ':employe_id' => $userId,
                ':type_conge_id' => $typeCongeId,
                ':date_debut' => $dateDebut,
                ':date_fin' => $dateFin,
                ':nb_jours' => $nbJours,
                ':motif' => $motif,
                ':statut' => 'En attente',
                ':created_at' => date('Y-m-d H:i:s'),
            ]
        );

        return redirect()->to('/employe/conge/demande')->with('success', 'Votre demande de congé a été soumise avec succès.');
    }

    public function historique()
    {
        return redirect()->to('/employe/conge/historique');
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

    public function submitApprobation()
    {
        if (!session()->get('employe_id')) {
            return redirect()->to('/login');
        }

        $congeId = (int) $this->request->getPost('conge_id');
        $action = (string) $this->request->getPost('action');
        $commentaire = (string) $this->request->getPost('commentaire_rh');
        $nouveauStatut = $action === 'refuse' ? 'Refusé' : 'Approuvé';

        $conge = SqliteDb::fetchOne('SELECT * FROM conges WHERE id = :id LIMIT 1', [':id' => $congeId]);
        if (!$conge) {
            return redirect()->to('/rh/conge/approbation')->with('error', 'Demande introuvable.');
        }

        SqliteDb::execute(
            'UPDATE conges SET statut = :statut, commentaire_rh = :commentaire, traite_par = :traite_par WHERE id = :id',
            [
                ':statut' => $nouveauStatut,
                ':commentaire' => $commentaire !== '' ? $commentaire : null,
                ':traite_par' => (int) session()->get('employe_id'),
                ':id' => $congeId,
            ]
        );

        if ($nouveauStatut === 'Approuvé') {
            $solde = new Solde();
            $solde->mettreAJourSolde(
                (int) $conge['employe_id'],
                (int) $conge['type_conge_id'],
                (int) date('Y', strtotime($conge['date_debut'] ?? date('Y-m-d'))),
                (int) ($conge['nb_jours'] ?? 0)
            );
        }

        return redirect()->to('/rh/conge/approbation')->with('success', 'La demande de congé a été mise à jour.');
    }

    /**
     * Retourne les congés de l'employé connecté au format JSON pour FullCalendar
     */
    public function events()
    {
        if (!session()->get('employe_id')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $employeId = (int) session()->get('employe_id');

        $rows = SqliteDb::fetchAll(
            'SELECT c.*, t.libelle AS type_conge_libelle
             FROM conges c
             LEFT JOIN types_conge t ON t.id = c.type_conge_id
             WHERE c.employe_id = :id',
            [':id' => $employeId]
        );

        $events = [];
        foreach ($rows as $r) {
            $title = ($r['type_conge_libelle'] ?? 'Congé') . ' — ' . ($r['statut'] ?? '');
            $start = $r['date_debut'] ?? null;
            $end = $r['date_fin'] ?? null;

            // Si ce sont des dates au format YYYY-MM-DD, FullCalendar attend un end exclusif -> ajouter 1 jour
            if ($start && preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) && $end && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
                $endDt = new \DateTime($end);
                $endDt->modify('+1 day');
                $end = $endDt->format('Y-m-d');
            }

            $statut = strtolower(trim((string) ($r['statut'] ?? '')));
            $class = 'conge-attente';
            if (strpos($statut, 'appr') !== false) {
                $class = 'conge-approuve';
            } elseif (strpos($statut, 'refus') !== false || strpos($statut, 'annul') !== false) {
                $class = 'conge-refuse';
            }

            $events[] = [
                'id' => $r['id'] ?? null,
                'title' => $title,
                'start' => $start,
                'end' => $end,
                'allDay' => true,
                'classNames' => [$class],
                'extendedProps' => [
                    'type' => $r['type_conge_libelle'] ?? null,
                    'motif' => $r['motif'] ?? null,
                    'statut' => $r['statut'] ?? null,
                ],
            ];
        }

        return $this->response->setJSON($events);
    }
}
