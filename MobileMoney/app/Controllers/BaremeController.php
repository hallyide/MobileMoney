<?php

namespace App\Controllers;

use App\Models\BaremeFraisModel;
use App\Models\TypeOperationModel;
use CodeIgniter\HTTP\RedirectResponse;

class BaremeController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();

        return view('admin/bareme', [
            'titre' => 'Barème des frais',
            'section' => 'baremes',
            'types' => (new TypeOperationModel())->orderBy('id')->findAll(),
            'baremes' => $db->table('baremeFrais')
                ->select('baremeFrais.*, typeOperation.type AS operation')
                ->join('typeOperation', 'typeOperation.id = baremeFrais.idtypeOp')
                ->orderBy('typeOperation.id')->orderBy('baremeFrais.montant_min')
                ->get()->getResultArray(),
        ]);
    }

    public function create(): RedirectResponse
    {
        $donnees = $this->donneesValides();
        if (is_string($donnees)) {
            return redirect()->back()->withInput()->with('erreur', $donnees);
        }

        (new BaremeFraisModel())->insert($donnees);
        return redirect()->to('/admin/baremes')->with('succes', 'La tranche a été ajoutée.');
    }

    public function update(int $id): RedirectResponse
    {
        $model = new BaremeFraisModel();
        if ($model->find($id) === null) {
            return redirect()->to('/admin/baremes')->with('erreur', 'Tranche introuvable.');
        }

        $donnees = $this->donneesValides($id);
        if (is_string($donnees)) {
            return redirect()->back()->withInput()->with('erreur', $donnees);
        }

        $model->update($id, $donnees);
        return redirect()->to('/admin/baremes')->with('succes', 'La tranche a été modifiée.');
    }

    public function delete(int $id): RedirectResponse
    {
        (new BaremeFraisModel())->delete($id);
        return redirect()->to('/admin/baremes')->with('succes', 'La tranche a été supprimée.');
    }

    /** Valide les nombres et interdit deux tranches qui se chevauchent. */
    private function donneesValides(?int $idExclu = null): array|string
    {
        $typeId = (int) $this->request->getPost('idtypeOp');
        $minimum = (float) $this->request->getPost('montant_min');
        $maximum = (float) $this->request->getPost('montant_max');
        $prix = (float) $this->request->getPost('prix');

        if ((new TypeOperationModel())->find($typeId) === null) {
            return 'Le type d’opération est invalide.';
        }
        if ($minimum < 0 || $maximum < $minimum || $prix < 0) {
            return 'Les montants et le prix doivent être positifs, et le maximum doit dépasser le minimum.';
        }

        $builder = db_connect()->table('baremeFrais')
            ->where('idtypeOp', $typeId)
            ->where('montant_min <=', $maximum)
            ->where('montant_max >=', $minimum);
        if ($idExclu !== null) {
            $builder->where('id !=', $idExclu);
        }
        if ($builder->countAllResults() > 0) {
            return 'Cette tranche chevauche une tranche existante.';
        }

        return ['idtypeOp' => $typeId, 'montant_min' => $minimum, 'montant_max' => $maximum, 'prix' => $prix];
    }
}
