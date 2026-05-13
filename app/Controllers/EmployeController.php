<?php

namespace App\Controllers;
use App\Models\Employe;
use App\Models\Departement;

use App\Models\Solde;
use App\Models\Conge;
class EmployeController extends BaseController
{
    public function login(): string{
    public function login()
    {
        $departementModel = new Departement();
        $password = $this->request->getPost('password');

        $user = $employeModel->where('email', $email)->first();
        if ($user && $this->passwordValide($password, $user['password'])) {
        if ($user && password_verify($password, $user['password'])) {
            session()->set('employe_role', $user['role']);
            return redirect()->to('employe/dashboard');
            return redirect()->to('/employe/dashboard');
    }

        session()->setFlashdata('error', 'Identifiants incorrects. Veuillez réessayer.');
        return view('login');
    }

    public function dashboard()
    {
        $employeModel = new Employe();
        $departementModel = new Departement();
        $soldeModel = new Solde();
        $congeModel = new Conge();

        $employeId = session()->get('employe_id');
        $employe = $employeModel->find($employeId);
        if (!$employe) {
            return redirect()->to('/login');
        }

        $departement = null;
        if ($employe['departement_id']) {
            $departement = $departementModel->find($employe['departement_id']);
        }

        $annee = (int) date('Y');
        $congesAttente = $congeModel->getCongesAttenteByEmploye($employeId);
        $congesAcceptes = $congeModel->getCongesAcceptesByEmploye($employeId);
        $congesRefuses = $congeModel->getCongesRefusesByEmploye($employeId);
        $congesRecents = $congeModel->getDernieresDemandesByEmploye($employeId, 5);
        $soldeResume = $soldeModel->getSoldeTotal($employeId, $annee);
        $soldes = $soldeModel->getSoldesByEmployeAndAnnee($employeId, $annee);

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

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function profile()
    {
        $employeModel = new Employe();
        $departementModel = new Departement();

        $employeId = session()->get('employe_id');
        $employe = $employeModel->find($employeId);
        if (!$employe) {
            return redirect()->to('/login');
        }

        $departement = null;
        if ($employe['departement_id']) {
            $departement = $departementModel->find($employe['departement_id']);
        }

        $data = [
            'employe' => $employe,
            'departement' => $departement,
        ];

        return view('employe/profile', $data);
    }
}
