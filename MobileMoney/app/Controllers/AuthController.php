<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\PrefixeModel;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if (session()->has('client_id')) {
            return redirect()->to('/client');
        }

        return view('client/login', ['titre' => 'Connexion client']);
    }

    /**
     * Connecte automatiquement le numero. S'il respecte un prefixe autorise
     * mais n'existe pas encore, son compte est cree avec un solde nul.
     */
    public function login(): RedirectResponse
    {
        $numero = trim((string) $this->request->getPost('numero'));

        if (! preg_match('/^[0-9]{10}$/', $numero)) {
            return redirect()->back()->withInput()->with('erreur', 'Le numéro doit contenir exactement 10 chiffres.');
        }

        $prefixe = substr($numero, 0, 3);
        if ((new PrefixeModel())->where('prefixe', $prefixe)->first() === null) {
            return redirect()->back()->withInput()->with('erreur', 'Ce préfixe téléphonique n’est pas autorisé.');
        }

        $compteModel = new CompteModel();
        $compte = $compteModel->where('numero', $numero)->first();

        if ($compte === null) {
            $id = $compteModel->insert(['numero' => $numero, 'soldeActuel' => 0], true);
            $compte = $compteModel->find($id);
        }

        session()->regenerate();
        session()->set([
            'client_id' => (int) $compte['id'],
            'client_numero' => $compte['numero'],
        ]);

        return redirect()->to('/client')->with('succes', 'Connexion réussie.');
    }

    public function logout(): RedirectResponse
    {
        session()->remove(['client_id', 'client_numero']);
        session()->regenerate();

        return redirect()->to('/client/login')->with('succes', 'Vous êtes déconnecté.');
    }
}
