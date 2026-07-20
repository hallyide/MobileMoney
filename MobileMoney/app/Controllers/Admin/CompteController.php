<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CompteModel;
use App\Models\MouvementCompteModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CompteController extends BaseController
{
    public function index(): string
    {
        return view('admin/comptes/index', [
            'titre' => 'Situation des comptes clients',
            'comptes' => (new CompteModel())->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function show(int $id): string
    {
        $compte = (new CompteModel())->find($id);
        if ($compte === null) {
            throw PageNotFoundException::forPageNotFound('Compte introuvable.');
        }

        return view('admin/comptes/show', [
            'titre' => 'Detail du compte',
            'compte' => $compte,
            'mouvements' => (new MouvementCompteModel())->forAccount($id),
        ]);
    }
}
