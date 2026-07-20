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
        $mouvementModel = new MouvementCompteModel();

        return view('client/dashboard', [
            'titre' => 'Mon compte',
            'section' => 'compte',
            'compte' => $compte,
            'mouvements' => $mouvementModel->forAccount($id, 10),
            'nombreMouvements' => $mouvementModel->where('idCompte', $id)->countAllResults(),
        ]);
    }

    public function depotForm(int $id): string
    {
        return view('client/depot', [
            'titre' => 'Faire un depot',
            'section' => 'depot',
            'compte' => $this->compteOuErreur($id),
            'baremes' => $this->baremesPour('depot'),
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
        return view('client/transfert', [
            'titre' => 'Faire un transfert',
            'section' => 'transfert',
            'compte' => $this->compteOuErreur($id),
            'baremes' => $this->baremesPour('transfert'),
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

    /** Retourne le bareme utilise pour afficher une estimation avant validation. */
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
