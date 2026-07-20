<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OperateurExterneModel;

class VersementController extends BaseController
{
    public function index(): string
    {
        $operateurId = (int) $this->request->getGet('operateur');
        $dateDebut = trim((string) $this->request->getGet('date_debut'));
        $dateFin = trim((string) $this->request->getGet('date_fin'));

        $base = db_connect()->table('transfertExterne')
            ->select('transfertExterne.*, operateurExterne.nom AS operateur, compte.numero AS envoyeur')
            ->join('operateurExterne', 'operateurExterne.id = transfertExterne.idOperateur')
            ->join('compte', 'compte.id = transfertExterne.idCompteEnvoyeur');

        if ($operateurId > 0) {
            $base->where('transfertExterne.idOperateur', $operateurId);
        }
        if ($this->dateValide($dateDebut)) {
            $base->where('transfertExterne.date >=', $dateDebut);
        }
        if ($this->dateValide($dateFin)) {
            $base->where('transfertExterne.date <=', $dateFin);
        }

        $transferts = $base->orderBy('transfertExterne.id', 'DESC')->get()->getResultArray();
        $totaux = [];
        foreach ($transferts as $transfert) {
            $id = (int) $transfert['idOperateur'];
            if (! isset($totaux[$id])) {
                $totaux[$id] = [
                    'operateur' => $transfert['operateur'],
                    'montantEnvoye' => 0.0,
                    'commission' => 0.0,
                    'montantAReverser' => 0.0,
                    'nombre' => 0,
                ];
            }
            $totaux[$id]['montantEnvoye'] += (float) $transfert['montantEnvoye'];
            $totaux[$id]['commission'] += (float) $transfert['commission'];
            $totaux[$id]['montantAReverser'] += (float) $transfert['montantAReverser'];
            $totaux[$id]['nombre']++;
        }

        return view('admin/versements', [
            'titre' => 'Montants à envoyer',
            'section' => 'versements',
            'operateurs' => (new OperateurExterneModel())->orderBy('nom')->findAll(),
            'transferts' => $transferts,
            'totaux' => array_values($totaux),
            'filtres' => ['operateur' => $operateurId, 'date_debut' => $dateDebut, 'date_fin' => $dateFin],
        ]);
    }

    private function dateValide(string $date): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1;
    }
}
