<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Services\TransactionService;
use CodeIgniter\HTTP\RedirectResponse;
use DomainException;

class RetraitController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $compte = $this->compteConnecte();
        if ($compte instanceof RedirectResponse) {
            return $compte;
        }

        return view('client/retrait', [
            'titre' => 'Faire un retrait',
            'section' => 'retrait',
            'compte' => $compte,
            'baremes' => $this->baremesPourRetrait(),
        ]);
    }

    public function process(): RedirectResponse
    {
        $compte = $this->compteConnecte();
        if ($compte instanceof RedirectResponse) {
            return $compte;
        }

        try {
            $frais = (new TransactionService())->retirer(
                (int) $compte['id'],
                (float) $this->request->getPost('montant')
            );
            $message = 'Retrait effectué. Frais : ' . number_format($frais, 0, ',', ' ') . ' Ar.';

            return redirect()->to('/client')->with('succes', $message);
        } catch (DomainException $exception) {
            return redirect()->back()->withInput()->with('erreur', $exception->getMessage());
        }
    }

    private function compteConnecte(): array|RedirectResponse
    {
        $id = session()->get('client_id');
        $compte = $id ? (new CompteModel())->find((int) $id) : null;

        return $compte ?? redirect()->to('/client/login')->with('erreur', 'Veuillez vous connecter.');
    }

    private function baremesPourRetrait(): array
    {
        return db_connect()->table('baremeFrais')
            ->select('baremeFrais.montant_min, baremeFrais.montant_max, baremeFrais.prix')
            ->join('typeOperation', 'typeOperation.id = baremeFrais.idtypeOp')
            ->where('typeOperation.type', 'retrait')
            ->orderBy('baremeFrais.montant_min', 'ASC')
            ->get()->getResultArray();
    }
}
