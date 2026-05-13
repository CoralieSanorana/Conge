<?php

namespace App\Controllers;
use \App\Models\TypeConge;
class CongeController extends BaseController
{
    public function demande()
    {
        $typeCongeModel = new TypeConge();
        return view('employe/demande');
    }


    public function submitDemande()
    {
        // Logique pour traiter la demande de congé
        // Valider les données, enregistrer la demande, etc.
        return redirect()->to('/employe/conge/historique');
    }

    public function historique()
    {
        // Logique pour afficher l'historique des demandes de congé
        return view('employe/historique_conge');
    }

    public function approbation()
    {
        // Logique pour afficher les demandes de congé en attente d'approbation
        return view('rh/approbation_conge');
    }

    public function submitApprobation()
    {
        // Logique pour traiter l'approbation ou le refus d'une demande de congé
        return redirect()->to('/rh/dashboard');
    }
}
