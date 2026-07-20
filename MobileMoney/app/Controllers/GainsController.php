<?php

namespace App\Controllers;

class GainsController extends BaseController
{
    /** Les frais sont un journal financier : ils sont consultables, pas éditables. */
    public function index(): string
    {
        $db = db_connect();
        $caisse = $db->table('caisseOp')->where('id', 1)->get()->getRowArray();

        $gains = $db->table('FraisMvmt')
            ->select('FraisMvmt.id, FraisMvmt.valeur, FraisMvmt.date, compte.numero, typeOperation.type AS operation')
            ->join('mvmtCompte', 'mvmtCompte.id = FraisMvmt.idMvmtCompt')
            ->join('compte', 'compte.id = mvmtCompte.idCompte')
            ->join('typeOperation', 'typeOperation.id = FraisMvmt.typeOp')
            ->where('FraisMvmt.valeur >', 0)
            ->orderBy('FraisMvmt.id', 'DESC')
            ->get()->getResultArray();

        return view('admin/historique', [
            'titre' => 'Gains de l’opérateur',
            'section' => 'gains',
            'totalGains' => (float) ($caisse['gains'] ?? 0),
            'gains' => $gains,
        ]);
    }
}
