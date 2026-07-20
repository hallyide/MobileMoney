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
        $comptes = (new CompteModel())->orderBy('id', 'DESC')->findAll();
        $idsRecents = session()->get('comptes_recents') ?? [];
        $recents = array_values(array_filter($comptes, static fn ($compte) => in_array((int) $compte['id'], $idsRecents, true)));
        usort($recents, static fn ($a, $b) => array_search((int) $a['id'], $idsRecents, true) <=> array_search((int) $b['id'], $idsRecents, true));

        return view('admin/comptes', [
            'titre' => 'Situation des comptes clients',
            'section' => 'comptes',
            'comptes' => $comptes,
            'recents' => array_slice($recents, 0, 8),
        ]);
    }

    public function show(int $id): string
    {
        $compte = (new CompteModel())->find($id);
        if ($compte === null) {
            throw PageNotFoundException::forPageNotFound('Compte introuvable.');
        }

        $recents = session()->get('comptes_recents') ?? [];
        $recents = array_values(array_diff($recents, [$id]));
        array_unshift($recents, $id);
        session()->set('comptes_recents', array_slice($recents, 0, 8));

        return view('admin/client-detail', [
            'titre' => 'Detail du compte',
            'section' => 'comptes',
            'compte' => $compte,
            'mouvements' => (new MouvementCompteModel())->forAccount($id),
        ]);
    }
}
