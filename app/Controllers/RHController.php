<?php

namespace App\Controllers;

class RHController extends BaseController
{
    public function dashboard()
    {
        if (!session()->get('employe_id')) {
            return redirect()->to('/login');
        }

        $data = [
            'pendingCount' => 0,
            'approvedCount' => 0,
            'rejectedCount' => 0,
        ];

        return view('rh/dashboard', $data);
    }
}
