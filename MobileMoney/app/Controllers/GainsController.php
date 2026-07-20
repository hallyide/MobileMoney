<?php

namespace App\Controllers;

class GainsController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $caisse = $db->table('caisseOp')->where('id', 1)->get()->getRowArray();

        $frais = $db->table('FraisMvmt')
            ->select('FraisMvmt.id, FraisMvmt.valeur, FraisMvmt.date, compte.numero, typeOperation.type AS operation, transfertExterne.id AS transfertExterneId')
            ->join('mvmtCompte', 'mvmtCompte.id = FraisMvmt.idMvmtCompt')
            ->join('compte', 'compte.id = mvmtCompte.idCompte')
            ->join('typeOperation', 'typeOperation.id = FraisMvmt.typeOp')
            ->join('transfertExterne', 'transfertExterne.idMouvement = mvmtCompte.id', 'left')
            ->where('FraisMvmt.valeur >', 0)
            ->orderBy('FraisMvmt.id', 'DESC')->get()->getResultArray();

        $commissions = $db->table('transfertExterne')
            ->select('transfertExterne.id, transfertExterne.commission, transfertExterne.date, transfertExterne.numeroDestinataire, compte.numero AS envoyeur, operateurExterne.nom AS operateur')
            ->join('compte', 'compte.id = transfertExterne.idCompteEnvoyeur')
            ->join('operateurExterne', 'operateurExterne.id = transfertExterne.idOperateur')
            ->where('transfertExterne.commission >', 0)
            ->orderBy('transfertExterne.id', 'DESC')->get()->getResultArray();

        return view('admin/historique', [
            'titre' => 'Gains de l’opérateur',
            'section' => 'gains',
            'totalGains' => (float) ($caisse['gains'] ?? 0),
            'totalFrais' => array_sum(array_column($frais, 'valeur')),
            'totalCommissions' => array_sum(array_column($commissions, 'commission')),
            'frais' => $frais,
            'commissions' => $commissions,
        ]);
    }
}
