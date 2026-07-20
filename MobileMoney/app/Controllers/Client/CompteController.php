<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\CompteModel;
use App\Models\MouvementCompteModel;
use App\Services\TransactionService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use DomainException;

class CompteController extends BaseController
{
    public function index(int $id): string
    {
        $compte = $this->compteOuErreur($id);

        return view('client/compte/index', [
            'titre' => 'Mon compte',
            'compte' => $compte,
            'mouvements' => (new MouvementCompteModel())->forAccount($id, 10),
        ]);
    }

    public function depotForm(int $id): string
    {
        return view('client/compte/depot', [
            'titre' => 'Faire un depot',
            'compte' => $this->compteOuErreur($id),
        ]);
    }

    public function depot(int $id): RedirectResponse
    {
        try {
            $frais = (new TransactionService())->deposer(
                $id,
                (float) $this->request->getPost('montant')
            );

            $message = 'Depot effectue avec succes.';
            if ($frais > 0) {
                $message .= ' Frais : ' . number_format($frais, 0, ',', ' ') . ' Ar.';
            }

            return redirect()->to("/client/{$id}")->with('succes', $message);
        } catch (DomainException $exception) {
            return redirect()->back()->withInput()->with('erreur', $exception->getMessage());
        }
    }

    public function transfertForm(int $id): string
    {
        return view('client/compte/transfert', [
            'titre' => 'Faire un transfert',
            'compte' => $this->compteOuErreur($id),
        ]);
    }

    public function transfert(int $id): RedirectResponse
    {
        try {
            $frais = (new TransactionService())->transferer(
                $id,
                (string) $this->request->getPost('numero'),
                (float) $this->request->getPost('montant')
            );

            $message = 'Transfert effectue. Frais : ' . number_format($frais, 0, ',', ' ') . ' Ar.';
            return redirect()->to("/client/{$id}")->with('succes', $message);
        } catch (DomainException $exception) {
            return redirect()->back()->withInput()->with('erreur', $exception->getMessage());
        }
    }

    private function compteOuErreur(int $id): array
    {
        $compte = (new CompteModel())->find($id);
        if ($compte === null) {
            throw PageNotFoundException::forPageNotFound('Compte introuvable.');
        }

        return $compte;
    }
}
