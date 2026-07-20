<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PrefixeModel;
use CodeIgniter\HTTP\RedirectResponse;

class PrefixeController extends BaseController
{
    public function index(): string
    {
        return view('admin/prefixes/index', [
            'titre' => 'Configuration des prefixes',
            'prefixes' => (new PrefixeModel())->orderBy('prefixe')->findAll(),
        ]);
    }

    public function store(): RedirectResponse
    {
        $prefixe = trim((string) $this->request->getPost('prefixe'));
        $erreur = $this->verifierPrefixe($prefixe);

        if ($erreur !== null) {
            return redirect()->back()->withInput()->with('erreur', $erreur);
        }

        (new PrefixeModel())->insert(['prefixe' => $prefixe]);

        return redirect()->to('/admin/prefixes')->with('succes', 'Le prefixe a ete ajoute.');
    }

    public function update(int $id): RedirectResponse
    {
        $model = new PrefixeModel();
        if ($model->find($id) === null) {
            return redirect()->to('/admin/prefixes')->with('erreur', 'Prefixe introuvable.');
        }

        $prefixe = trim((string) $this->request->getPost('prefixe'));
        $erreur = $this->verifierPrefixe($prefixe, $id);

        if ($erreur !== null) {
            return redirect()->back()->withInput()->with('erreur', $erreur);
        }

        $model->update($id, ['prefixe' => $prefixe]);

        return redirect()->to('/admin/prefixes')->with('succes', 'Le prefixe a ete modifie.');
    }

    public function delete(int $id): RedirectResponse
    {
        (new PrefixeModel())->delete($id);

        return redirect()->to('/admin/prefixes')->with('succes', 'Le prefixe a ete supprime.');
    }

    private function verifierPrefixe(string $prefixe, ?int $idExclu = null): ?string
    {
        if (! preg_match('/^[0-9]{3}$/', $prefixe)) {
            return 'Le prefixe doit contenir exactement 3 chiffres (exemple : 033).';
        }

        $model = new PrefixeModel();
        $model->where('prefixe', $prefixe);
        if ($idExclu !== null) {
            $model->where('id !=', $idExclu);
        }

        return $model->first() === null ? null : 'Ce prefixe existe deja.';
    }
}
