<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\CompteModel;
use App\Models\MouvementCompteModel;
use App\Services\TransactionService;
use CodeIgniter\HTTP\RedirectResponse;
use DomainException;

class CompteController extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $compte = $this->compteConnecte();
        if ($compte instanceof RedirectResponse) {
            return $compte;
        }

        $mouvementModel = new MouvementCompteModel();

        return view('client/dashboard', [
            'titre' => 'Mon compte',
            'section' => 'compte',
            'compte' => $compte,
            'mouvements' => $mouvementModel->forAccount((int) $compte['id'], 10),
            'nombreMouvements' => $mouvementModel->where('idCompte', $compte['id'])->countAllResults(),
        ]);
    }

    public function depotForm(): string|RedirectResponse
    {
        $compte = $this->compteConnecte();
        if ($compte instanceof RedirectResponse) {
            return $compte;
        }

        return view('client/depot', [
            'titre' => 'Faire un dépôt',
            'section' => 'depot',
            'compte' => $compte,
            'baremes' => $this->baremesPour('depot'),
        ]);
    }

    public function depot(): RedirectResponse
    {
        $compte = $this->compteConnecte();
        if ($compte instanceof RedirectResponse) {
            return $compte;
        }

        try {
            $frais = (new TransactionService())->deposer(
                (int) $compte['id'],
                (float) $this->request->getPost('montant')
            );
            $message = 'Dépôt effectué avec succès. Frais : ' . number_format($frais, 0, ',', ' ') . ' Ar.';

            return redirect()->to('/client')->with('succes', $message);
        } catch (DomainException $exception) {
            return redirect()->back()->withInput()->with('erreur', $exception->getMessage());
        }
    }

    public function transfertForm(): string|RedirectResponse
    {
        $compte = $this->compteConnecte();
        if ($compte instanceof RedirectResponse) {
            return $compte;
        }

        return view('client/transfert', [
            'titre' => 'Faire un transfert',
            'section' => 'transfert',
            'compte' => $compte,
            'baremes' => $this->baremesPour('transfert'),
        ]);
    }

    public function transfert(): RedirectResponse
    {
        $compte = $this->compteConnecte();
        if ($compte instanceof RedirectResponse) {
            return $compte;
        }

        try {
            $frais = (new TransactionService())->transferer(
                (int) $compte['id'],
                (string) $this->request->getPost('numero'),
                (float) $this->request->getPost('montant')
            );
            $message = 'Transfert effectué. Frais : ' . number_format($frais, 0, ',', ' ') . ' Ar.';

            return redirect()->to('/client')->with('succes', $message);
        } catch (DomainException $exception) {
            return redirect()->back()->withInput()->with('erreur', $exception->getMessage());
        }
    }

    /** Retourne le compte de la session ou redirige vers la connexion. */
    private function compteConnecte(): array|RedirectResponse
    {
        $id = session()->get('client_id');
        $compte = $id ? (new CompteModel())->find((int) $id) : null;

        if ($compte === null) {
            session()->remove(['client_id', 'client_numero']);
            return redirect()->to('/client/login')->with('erreur', 'Veuillez vous connecter.');
        }

        return $compte;
    }

    /** Retourne le barème utilisé pour la petite estimation visuelle. */
    private function baremesPour(string $operation): array
    {
        return db_connect()->table('baremeFrais')
            ->select('baremeFrais.montant_min, baremeFrais.montant_max, baremeFrais.prix')
            ->join('typeOperation', 'typeOperation.id = baremeFrais.idtypeOp')
            ->where('typeOperation.type', $operation)
            ->orderBy('baremeFrais.montant_min', 'ASC')
            ->get()->getResultArray();
    }
}
