<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('login');
    }

    public function typeCongeForm(){
        return view('admin/type_conge_form');
    }

    public function submitTypeConge(){
    $this->requireLogin();
        $libelle =$this->request->getPost('libelle');
        $jours_annuels = $this->request->getPost('jours_annuels');
        $deductible = $this->request->getPost('deductible');

        if (empty($libelle) || empty($jours_annuels) || empty($deductible)) {
            return redirect()->back()->with('error', 'Donnees incomplet');
        }
        SqliteDb::execute('INSERT INTO types_conge (libelle, jours_annuels, deductible) VALUES (:libelle, :jours_annuels, :deductible)', [
            ':libelle' => $libelle,
            ':jours_annuels' => $jours_annuels,
            ':deductible' => $deductible
        ]);
        
        return redirect()->back()->with('success', 'Département ajouté avec succès');
    }
}
