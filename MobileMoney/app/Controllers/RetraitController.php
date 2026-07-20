<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class RetraitController extends ResourceController
{
    protected $format = 'json';

    public function process()
    {
        $data = $this->request->getJSON(true);
        $compteModel = model('CompteModel');
        $mvmtModel   = model('MvmtCompteModel');
        $fraisModel  = model('FraisMvmtModel');
        $caisseModel = model('CaisseOpModel');
        $baremeModel = model('BaremeFraisModel');

        $numero  = session()->get('client_numero');
        $compte  = $compteModel->where('numero', $numero)->first();
        if (!$compte) return $this->fail('Session expirée.', 401);

        $montant = (float) $data['montant'];
        if ($montant <= 0) return $this->fail('Montant invalide.');

        $idtypeOp = 2; // retrait
        $frais = $this->computeFrais($baremeModel, $idtypeOp, $montant);

        if (($montant + $frais) > $compte['soldeActuel']) {
            return $this->fail('Solde insuffisant (frais inclus : ' . $frais . ').');
        }

        $date = date('Y-m-d');
        $mvmtId = $mvmtModel->insert([
            'idCompte'  => $compte['id'],
            'valeur'    => $montant,
            'date'      => $date,
            'idType'    => 2, // credit (sortie)
            'indTypeOp' => $idtypeOp,
        ]);

        $fraisModel->insert([
            'idMvmtCompt' => $mvmtId,
            'valeur'      => $frais,
            'typeOp'      => $idtypeOp,
            'date'        => $date,
        ]);

        $caisse = $caisseModel->find(1);
        $caisseModel->update(1, ['gains' => $caisse['gains'] + $frais]);

        $nouveauSolde = $compte['soldeActuel'] - ($montant + $frais);
        $compteModel->update($compte['id'], ['soldeActuel' => $nouveauSolde]);

        return $this->respond(['success' => true, 'frais' => $frais, 'nouveauSolde' => $nouveauSolde]);
    }

    private function computeFrais($baremeModel, $idtypeOp, $montant)
    {
        $lignes = $baremeModel->where('idtypeOp', $idtypeOp)->findAll();
        foreach ($lignes as $b) {
            if ($montant >= $b['montant_min'] && $montant <= $b['montant_max']) {
                return $b['prix'];
            }
        }
        return end($lignes)['prix'] ?? 0;
    }
}