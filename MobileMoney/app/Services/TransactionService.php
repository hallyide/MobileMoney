<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use DomainException;

/**
 * Regroupe la logique des operations d'argent afin que les controleurs
 * restent courts et que les futures pages puissent reutiliser ces methodes.
 */
class TransactionService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    /**
     * Ajoute un depot et met a jour le solde dans une seule transaction.
     *
     * Le barème du dépôt est actuellement configuré à zéro. Le compte reçoit
     * le montant net afin que le code reste correct si ce barème évolue.
     */
    public function deposer(int $compteId, float $montant): float
    {
        $montant = $this->montantValide($montant);
        $compte = $this->trouverCompte($compteId);
        $typeEntreeId = $this->idTypeMouvement('debit');
        $depotId = $this->idOperation('depot');
        $frais = $this->calculerFrais($depotId, $montant);
        $montantNet = $montant - $frais;

        if ($montantNet <= 0) {
            throw new DomainException('Les frais ne peuvent pas etre superieurs ou egaux au depot.');
        }

        $this->db->transStart();

        $this->db->table('compte')->where('id', $compteId)->update([
            'soldeActuel' => round((float) $compte['soldeActuel'] + $montantNet, 2),
        ]);

        // La valeur du mouvement correspond exactement a la variation du solde.
        $this->db->table('mvmtCompte')->insert([
            'idCompte' => $compteId,
            'valeur' => $montantNet,
            'idType' => $typeEntreeId,
            'indTypeOp' => $depotId,
        ]);
        $mouvementId = (int) $this->db->insertID();

        // Une ligne est conservee meme lorsque le depot est gratuit. Cela
        // permet de prouver que le bareme a bien ete consulte (frais = 0).
        $this->enregistrerFrais($mouvementId, $depotId, $frais);

        $this->db->transComplete();
        $this->verifierTransaction();

        return $frais;
    }

    /**
     * Transfere le montant au destinataire, retire les frais a l'expediteur
     * et verse ces frais dans la caisse de l'operateur.
     */
    public function transferer(int $expediteurId, string $numeroDestinataire, float $montant): float
    {
        $montant = $this->montantValide($montant);
        $expediteur = $this->trouverCompte($expediteurId);
        $destinataire = $this->db->table('compte')
            ->where('numero', trim($numeroDestinataire))->get()->getRowArray();

        if ($destinataire === null) {
            throw new DomainException('Le compte destinataire est introuvable.');
        }
        if ((int) $destinataire['id'] === $expediteurId) {
            throw new DomainException('Vous ne pouvez pas effectuer un transfert vers votre propre compte.');
        }

        $operationId = $this->idOperation('transfert');
        $frais = $this->calculerFrais($operationId, $montant);
        $total = $montant + $frais;
        $typeEntreeId = $this->idTypeMouvement('debit');
        $typeSortieId = $this->idTypeMouvement('credit');

        if ((float) $expediteur['soldeActuel'] < $total) {
            throw new DomainException('Solde insuffisant pour couvrir le montant et les frais.');
        }

        $this->db->transStart();

        $this->db->table('compte')->where('id', $expediteurId)->update([
            'soldeActuel' => round((float) $expediteur['soldeActuel'] - $total, 2),
        ]);
        $this->db->table('compte')->where('id', $destinataire['id'])->update([
            'soldeActuel' => round((float) $destinataire['soldeActuel'] + $montant, 2),
        ]);

        // Le solde de l'expediteur diminue du montant ET des frais. La ligne
        // de mouvement contient donc le debit total, ce qui facilite l'audit.
        $this->db->table('mvmtCompte')->insert([
            'idCompte' => $expediteurId,
            'valeur' => $total,
            'idType' => $typeSortieId,
            'indTypeOp' => $operationId,
        ]);
        $mouvementExpediteurId = (int) $this->db->insertID();

        // Mouvement d'entree du destinataire.
        $this->db->table('mvmtCompte')->insert([
            'idCompte' => $destinataire['id'],
            'valeur' => $montant,
            'idType' => $typeEntreeId,
            'indTypeOp' => $operationId,
        ]);

        // FraisMvmt detaille la part "frais" incluse dans le debit total.
        $this->enregistrerFrais($mouvementExpediteurId, $operationId, $frais);

        $this->db->transComplete();
        $this->verifierTransaction();

        return $frais;
    }

    /** Retire le montant et les frais du compte dans une transaction unique. */
    public function retirer(int $compteId, float $montant): float
    {
        $montant = $this->montantValide($montant);
        $compte = $this->trouverCompte($compteId);
        $operationId = $this->idOperation('retrait');
        $typeSortieId = $this->idTypeMouvement('credit');
        $frais = $this->calculerFrais($operationId, $montant);
        $total = $montant + $frais;

        if ((float) $compte['soldeActuel'] < $total) {
            throw new DomainException('Solde insuffisant pour couvrir le retrait et les frais.');
        }

        $this->db->transStart();

        $this->db->table('compte')->where('id', $compteId)->update([
            'soldeActuel' => round((float) $compte['soldeActuel'] - $total, 2),
        ]);

        // La valeur correspond à la diminution réelle du solde : montant + frais.
        $this->db->table('mvmtCompte')->insert([
            'idCompte' => $compteId,
            'valeur' => $total,
            'idType' => $typeSortieId,
            'indTypeOp' => $operationId,
        ]);
        $mouvementId = (int) $this->db->insertID();

        $this->enregistrerFrais($mouvementId, $operationId, $frais);

        $this->db->transComplete();
        $this->verifierTransaction();

        return $frais;
    }

    private function montantValide(float $montant): float
    {
        if (! is_finite($montant) || $montant <= 0) {
            throw new DomainException('Le montant doit etre superieur a zero.');
        }

        return round($montant, 2);
    }

    private function trouverCompte(int $id): array
    {
        $compte = $this->db->table('compte')->where('id', $id)->get()->getRowArray();

        if ($compte === null) {
            throw new DomainException('Le compte est introuvable.');
        }

        return $compte;
    }

    private function idOperation(string $type): int
    {
        $operation = $this->db->table('typeOperation')->where('type', $type)->get()->getRowArray();

        if ($operation === null) {
            throw new DomainException("Le type d'operation {$type} n'est pas configure.");
        }

        return (int) $operation['id'];
    }

    private function idTypeMouvement(string $type): int
    {
        $mouvement = $this->db->table('typeMvmtComp')->where('type', $type)->get()->getRowArray();

        if ($mouvement === null) {
            throw new DomainException("Le type de mouvement {$type} n'est pas configure.");
        }

        return (int) $mouvement['id'];
    }

    private function calculerFrais(int $operationId, float $montant): float
    {
        $bareme = $this->db->table('baremeFrais')
            ->where('idtypeOp', $operationId)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->get()->getRowArray();

        if ($bareme === null) {
            throw new DomainException("Aucun bareme de frais ne correspond a ce montant.");
        }

        return (float) $bareme['prix'];
    }

    /** Enregistre le detail des frais et les ajoute aux gains operateur. */
    private function enregistrerFrais(int $mouvementId, int $operationId, float $frais): void
    {
        $this->db->table('FraisMvmt')->insert([
            'idMvmtCompt' => $mouvementId,
            'valeur' => $frais,
            'typeOp' => $operationId,
        ]);

        // La caisse est initialisee si le seeder n'a pas encore ete execute.
        $this->db->query('INSERT OR IGNORE INTO caisseOp (id, gains) VALUES (1, 0)');
        $this->db->query('UPDATE caisseOp SET gains = gains + ? WHERE id = 1', [$frais]);
    }

    private function verifierTransaction(): void
    {
        if (! $this->db->transStatus()) {
            throw new DomainException("L'operation a echoue. Aucune modification n'a ete enregistree.");
        }
    }
}
