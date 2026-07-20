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
            $message = 'Dépôt effectué. Frais : ' . number_format($frais, 2, ',', ' ') . ' Ar.';
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
        ]);
    }

    public function transfertApercu(): string|RedirectResponse
    {
        $compte = $this->compteConnecte();
        if ($compte instanceof RedirectResponse) {
            return $compte;
        }

        $numeros = $this->request->getPost('numeros');
        $numeros = is_array($numeros) ? $numeros : [];
        $montant = (float) $this->request->getPost('montant');
        $inclureRetrait = $this->request->getPost('inclure_frais_retrait') === '1';

        try {
            $resume = (new TransactionService())->preparerTransfert(
                (int) $compte['id'],
                $numeros,
                $montant,
                $inclureRetrait
            );

            return view('client/transfert-apercu', [
                'titre' => 'Vérifier le transfert',
                'section' => 'transfert',
                'compte' => $compte,
                'resume' => $resume,
                'numeros' => $numeros,
            ]);
        } catch (DomainException $exception) {
            return redirect()->back()->withInput()->with('erreur', $exception->getMessage());
        }
    }

    public function transfert(): RedirectResponse
    {
        $compte = $this->compteConnecte();
        if ($compte instanceof RedirectResponse) {
            return $compte;
        }

        $numeros = $this->request->getPost('numeros');
        $numeros = is_array($numeros) ? $numeros : [];

        try {
            $resume = (new TransactionService())->transfererMultiple(
                (int) $compte['id'],
                $numeros,
                (float) $this->request->getPost('montant'),
                $this->request->getPost('inclure_frais_retrait') === '1'
            );
            $message = count($resume['destinataires']) . ' transfert(s) effectué(s). Total débité : '
                . number_format($resume['totalDebite'], 2, ',', ' ') . ' Ar.';

            return redirect()->to('/client')->with('succes', $message);
        } catch (DomainException $exception) {
            return redirect()->to('/client/transfert')->withInput()->with('erreur', $exception->getMessage());
        }
    }

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
