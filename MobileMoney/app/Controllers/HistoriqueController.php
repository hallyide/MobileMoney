<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\MouvementCompteModel;
use CodeIgniter\HTTP\RedirectResponse;

class HistoriqueController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $id = session()->get('client_id');
        $compte = $id ? (new CompteModel())->find((int) $id) : null;

        if ($compte === null) {
            return redirect()->to('/client/login')->with('erreur', 'Veuillez vous connecter.');
        }

        return view('client/historique', [
            'titre' => 'Historique des transactions',
            'section' => 'historique',
            'compte' => $compte,
            'mouvements' => (new MouvementCompteModel())->forAccount((int) $compte['id']),
        ]);
    }
}
