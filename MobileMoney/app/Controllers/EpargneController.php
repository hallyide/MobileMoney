<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Services\TransactionService;
use CodeIgniter\HTTP\RedirectResponse;
use DomainException;

class EpargneController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $compte = $this->compteConnecte();
        if ($compte instanceof RedirectResponse) {
            return $compte;
        }

        return view('client/epargne', [
            'titre' => 'Epargne',
            'section' => 'Epargne',
            'compte' => $compte,
        ]);
    }

    public function process(): RedirectResponse
    {
        $compte = $this->compteConnecte();
        if ($compte instanceof RedirectResponse) {
            return $compte;
        }

        try {
            
            $compteId = (int) $compte['id'];
            $epargne = (float) $this->request->getPost('epargne');
            
            $db = db_connect();

            //$db->query("");


            $message = 'Epargne configuré';

            return redirect()->to('/client')->with('succes', $message);
        } catch (DomainException $exception) {
            return redirect()->back()->withInput()->with('erreur', $exception->getMessage());
        }
    }

    private function compteConnecte(): array|RedirectResponse
    {
        $id = session()->get('client_id');
        $compte = $id ? (new CompteModel())->find((int) $id) : null;

        return $compte ?? redirect()->to('/client/login')->with('erreur', 'Veuillez vous connecter.');
    }

    
}
