<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CommissionOperateurModel;
use App\Models\OperateurExterneModel;
use App\Models\PrefixeOperateurModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\RedirectResponse;

class OperateurController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $operateurs = $db->table('operateurExterne')
            ->select('operateurExterne.*, COALESCE(commissionOperateur.pourcentage, 0) AS pourcentage')
            ->join('commissionOperateur', 'commissionOperateur.idOperateur = operateurExterne.id', 'left')
            ->orderBy('operateurExterne.nom')
            ->get()->getResultArray();

        foreach ($operateurs as &$operateur) {
            $operateur['prefixes'] = $db->table('prefixeOperateur')
                ->where('idOperateur', $operateur['id'])
                ->orderBy('prefixe')->get()->getResultArray();
        }

        return view('admin/operateurs', [
            'titre' => 'Autres opérateurs',
            'section' => 'operateurs',
            'operateurs' => $operateurs,
        ]);
    }

    public function store(): RedirectResponse
    {
        $nom = trim((string) $this->request->getPost('nom'));
        if ($nom === '') {
            return $this->retourErreur('Le nom de l’opérateur est obligatoire.');
        }

        $model = new OperateurExterneModel();
        if ($model->where('nom', $nom)->first() !== null) {
            return $this->retourErreur('Cet opérateur existe déjà.');
        }

        $id = (int) $model->insert(['nom' => $nom], true);
        (new CommissionOperateurModel())->insert(['idOperateur' => $id, 'pourcentage' => 0]);

        return redirect()->to('/admin/operateurs')->with('succes', 'L’opérateur a été ajouté.');
    }

    public function update(int $id): RedirectResponse
    {
        $model = new OperateurExterneModel();
        if ($model->find($id) === null) {
            return $this->retourErreur('Opérateur introuvable.');
        }

        $nom = trim((string) $this->request->getPost('nom'));
        $doublon = $model->where('nom', $nom)->where('id !=', $id)->first();
        if ($nom === '' || $doublon !== null) {
            return $this->retourErreur('Le nom est vide ou déjà utilisé.');
        }

        $model->update($id, ['nom' => $nom]);
        return redirect()->to('/admin/operateurs')->with('succes', 'L’opérateur a été modifié.');
    }

    public function delete(int $id): RedirectResponse
    {
        $db = db_connect();
        $utilise = $db->table('transfertExterne')->where('idOperateur', $id)->countAllResults() > 0;
        if ($utilise) {
            return $this->retourErreur('Cet opérateur possède déjà des transferts et doit rester dans l’historique.');
        }

        try {
            $db->transStart();
            $db->table('prefixeOperateur')->where('idOperateur', $id)->delete();
            $db->table('commissionOperateur')->where('idOperateur', $id)->delete();
            $db->table('operateurExterne')->where('id', $id)->delete();
            $db->transComplete();
        } catch (DatabaseException) {
            return $this->retourErreur('L’opérateur ne peut pas être supprimé.');
        }

        return redirect()->to('/admin/operateurs')->with('succes', 'L’opérateur a été supprimé.');
    }

    public function storePrefixe(int $operateurId): RedirectResponse
    {
        if ((new OperateurExterneModel())->find($operateurId) === null) {
            return $this->retourErreur('Opérateur introuvable.');
        }

        $prefixe = trim((string) $this->request->getPost('prefixe'));
        $erreur = $this->verifierPrefixe($prefixe);
        if ($erreur !== null) {
            return $this->retourErreur($erreur);
        }

        (new PrefixeOperateurModel())->insert(['idOperateur' => $operateurId, 'prefixe' => $prefixe]);
        return redirect()->to('/admin/operateurs')->with('succes', 'Le préfixe a été ajouté.');
    }

    public function updatePrefixe(int $id): RedirectResponse
    {
        $model = new PrefixeOperateurModel();
        if ($model->find($id) === null) {
            return $this->retourErreur('Préfixe introuvable.');
        }

        $prefixe = trim((string) $this->request->getPost('prefixe'));
        $erreur = $this->verifierPrefixe($prefixe, $id);
        if ($erreur !== null) {
            return $this->retourErreur($erreur);
        }

        $model->update($id, ['prefixe' => $prefixe]);
        return redirect()->to('/admin/operateurs')->with('succes', 'Le préfixe a été modifié.');
    }

    public function deletePrefixe(int $id): RedirectResponse
    {
        (new PrefixeOperateurModel())->delete($id);
        return redirect()->to('/admin/operateurs')->with('succes', 'Le préfixe a été supprimé.');
    }

    public function updateCommission(int $operateurId): RedirectResponse
    {
        if ((new OperateurExterneModel())->find($operateurId) === null) {
            return $this->retourErreur('Opérateur introuvable.');
        }

        $pourcentage = filter_var($this->request->getPost('pourcentage'), FILTER_VALIDATE_FLOAT);
        if ($pourcentage === false || $pourcentage < 0 || $pourcentage > 100) {
            return $this->retourErreur('La commission doit être comprise entre 0 et 100 %.');
        }

        $model = new CommissionOperateurModel();
        $commission = $model->where('idOperateur', $operateurId)->first();
        if ($commission === null) {
            $model->insert(['idOperateur' => $operateurId, 'pourcentage' => $pourcentage]);
        } else {
            $model->update($commission['id'], ['pourcentage' => $pourcentage]);
        }

        return redirect()->to('/admin/operateurs')->with('succes', 'La commission a été mise à jour.');
    }

    private function verifierPrefixe(string $prefixe, ?int $idExclu = null): ?string
    {
        if (! preg_match('/^[0-9]{3}$/', $prefixe)) {
            return 'Le préfixe doit contenir exactement trois chiffres.';
        }
        if (db_connect()->table('prefixeDispo')->where('prefixe', $prefixe)->countAllResults() > 0) {
            return 'Ce préfixe appartient déjà à notre opérateur.';
        }

        $model = new PrefixeOperateurModel();
        $model->where('prefixe', $prefixe);
        if ($idExclu !== null) {
            $model->where('id !=', $idExclu);
        }

        return $model->first() === null ? null : 'Ce préfixe est déjà utilisé.';
    }

    private function retourErreur(string $message): RedirectResponse
    {
        return redirect()->to('/admin/operateurs')->withInput()->with('erreur', $message);
    }
}
