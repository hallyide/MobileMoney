<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class GainsController extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $db = \Config\Database::connect();
        $rows = $db->table('fraisMvmt f')
            ->select('f.id, f.valeur, f.date, f.typeOp, m.idCompte, c.nom, c.numero, t.type as type_label')
            ->join('mvmtCompte m', 'm.id = f.idMvmtCompt')
            ->join('compte c', 'c.id = m.idCompte')
            ->join('typeOperation t', 't.id = f.typeOp')
            ->orderBy('f.id', 'DESC')
            ->get()->getResultArray();

        return $this->respond($rows);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        $mvmtModel  = model('MvmtCompteModel');
        $fraisModel = model('FraisMvmtModel');
        $caisseModel= model('CaisseOpModel');
        $baremeModel= model('BaremeFraisModel');

        $idtypeOp = (int) $data['idtypeOp'];
        $montant  = (float) $data['montant'];
        $date     = $data['date'] ?? date('Y-m-d');
        $idType   = ($idtypeOp === 1) ? 1 : 2; // depot=debit, sinon credit

        $mvmtId = $mvmtModel->insert([
            'idCompte'  => $data['idCompte'],
            'valeur'    => $montant,
            'date'      => $date,
            'idType'    => $idType,
            'indTypeOp' => $idtypeOp,
        ]);

        $frais = $this->computeFrais($baremeModel, $idtypeOp, $montant);

        $fraisModel->insert([
            'idMvmtCompt' => $mvmtId,
            'valeur'      => $frais,
            'typeOp'      => $idtypeOp,
            'date'        => $date,
        ]);

        $caisse = $caisseModel->find(1);
        $caisseModel->update(1, ['gains' => $caisse['gains'] + $frais]);

        return $this->respondCreated(['frais' => $frais]);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        $fraisModel = model('FraisMvmtModel');
        $caisseModel= model('CaisseOpModel');

        $old = $fraisModel->find($id);
        $caisse = $caisseModel->find(1);
        $caisseModel->update(1, ['gains' => $caisse['gains'] - $old['valeur']]);

        $fraisModel->update($id, [
            'typeOp' => $data['typeOp'],
            'valeur' => $data['valeur'],
            'date'   => $data['date'],
        ]);

        $caisse2 = $caisseModel->find(1);
        $caisseModel->update(1, ['gains' => $caisse2['gains'] + $data['valeur']]);

        return $this->respond(['success' => true]);
    }

    public function delete($id = null)
    {
        $fraisModel = model('FraisMvmtModel');
        $caisseModel= model('CaisseOpModel');

        $old = $fraisModel->find($id);
        $caisse = $caisseModel->find(1);
        $caisseModel->update(1, ['gains' => $caisse['gains'] - $old['valeur']]);
        $fraisModel->delete($id);

        return $this->respondDeleted(['success' => true]);
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