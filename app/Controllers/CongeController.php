<?php

namespace App\Controllers;

use App\Libraries\SqliteDb;

class CongeController extends BaseController
{
    public function demande()
    {
        /*if (!session()->get('employe_id')) {
            return redirect()->to('/login');
        }*/

        $data['typesConge'] = SqliteDb::fetchAll('SELECT * FROM types_conge ORDER BY libelle ASC');

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
        return redirect()->to('/employe/dashboard');
    }

    public function approbation()
    {
        return redirect()->to('/rh/dashboard');
    }

    public function submitApprobation()
    {
        return redirect()->to('/rh/dashboard');
    }
}
