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
        $typeCongeId = $this->request->getPost('type_conge_id');
        $dateDebut = $this->request->getPost('date_debut');
        $dateFin = $this->request->getPost('date_fin');
        $motif = $this->request->getPost('motif');

        $nbJours = (int) ((strtotime($dateFin) - strtotime($dateDebut)) / (60 * 60 * 24) + 1);

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
}
