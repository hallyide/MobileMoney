<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use DomainException;

class TransactionService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    public function deposer(int $compteId, float $montant): float
    {
        $montant = $this->montantValide($montant);
        $compte = $this->trouverCompte($compteId);
        $operationId = $this->idOperation('depot');
        $frais = $this->calculerFrais($operationId, $montant);
        $montantNet = $montant - $frais;

        if ($montantNet <= 0) {
            throw new DomainException('Les frais ne peuvent pas dépasser le montant du dépôt.');
        }

        $this->db->transStart();
        $this->db->table('compte')->where('id', $compteId)->update([
            'soldeActuel' => round((float) $compte['soldeActuel'] + $montantNet, 2),
        ]);

        $mouvementId = $this->ajouterMouvement(
            $compteId,
            $montantNet,
            $this->idTypeMouvement('debit'),
            $operationId,
            null
        );
        $this->enregistrerFrais($mouvementId, $operationId, $frais);

        $this->db->transComplete();
        $this->verifierTransaction();

        return $frais;
    }

    public function retirer(int $compteId, float $montant): float
    {
        $montant = $this->montantValide($montant);
        $compte = $this->trouverCompte($compteId);
        $operationId = $this->idOperation('retrait');
        $frais = $this->calculerFrais($operationId, $montant);
        $total = $montant + $frais;

        if ((float) $compte['soldeActuel'] < $total) {
            throw new DomainException('Solde insuffisant pour couvrir le retrait et les frais.');
        }

        $this->db->transStart();
        $this->db->table('compte')->where('id', $compteId)->update([
            'soldeActuel' => round((float) $compte['soldeActuel'] - $total, 2),
        ]);

        $mouvementId = $this->ajouterMouvement(
            $compteId,
            $total,
            $this->idTypeMouvement('credit'),
            $operationId,
            $compte['numero']
        );
        $this->enregistrerFrais($mouvementId, $operationId, $frais);

        $this->db->transComplete();
        $this->verifierTransaction();

        return $frais;
    }

    /** Garde la méthode utilisée par la V1 pour un transfert simple. */
    public function transferer(int $expediteurId, string $numero, float $montant): float
    {
        $resultat = $this->transfererMultiple($expediteurId, [$numero], $montant, false);

        return $resultat['totalFraisTransfert'] + $resultat['totalCommissions'];
    }

    /**
     * Prépare les montants affichés dans le récapitulatif sans écrire en base.
     */
    public function preparerTransfert(
        int $expediteurId,
        array $numeros,
        float $montantTotal,
        bool $inclureFraisRetrait
    ): array {
        $expediteur = $this->trouverCompte($expediteurId);
        $montantTotal = $this->montantValide($montantTotal);
        $numeros = $this->nettoyerNumeros($numeros);
        $parts = $this->partagerMontant($montantTotal, count($numeros));
        $transfertId = $this->idOperation('transfert');
        $retraitId = $this->idOperation('retrait');

        $destinataires = [];
        $groupeAttendu = null;
        $totalDebite = 0.0;
        $totalFraisTransfert = 0.0;
        $totalFraisRetrait = 0.0;
        $totalCommissions = 0.0;
        $totalAReverser = 0.0;

        foreach ($numeros as $index => $numero) {
            $destination = $this->identifierDestination($numero, $expediteurId);

            if (count($numeros) > 1 && $destination['type'] === 'externe') {
                throw new DomainException(
                    'L’envoi multiple est réservé aux numéros de notre opérateur.'
                );
            }

            $groupe = $destination['type'] === 'interne'
                ? 'interne'
                : 'externe-' . $destination['operateurId'];

            if ($groupeAttendu === null) {
                $groupeAttendu = $groupe;
            } elseif ($groupeAttendu !== $groupe) {
                throw new DomainException('Tous les destinataires doivent appartenir au même opérateur.');
            }

            $part = $parts[$index];
            $fraisTransfert = $this->calculerFrais($transfertId, $part);
            $fraisRetrait = 0.0;
            $commission = 0.0;
            $montantRecu = $part;
            $montantAReverser = 0.0;

            if ($destination['type'] === 'interne' && $inclureFraisRetrait) {
                $fraisRetrait = $this->calculerFrais($retraitId, $part);
                $montantRecu += $fraisRetrait;
            }

            if ($destination['type'] === 'externe') {
                $commission = round($part * $destination['pourcentage'] / 100, 2);
                $montantAReverser = $part;
            }

            // Pour un envoi externe, la commission est payée par l'envoyeur.
            $debit = $part + $fraisTransfert + $fraisRetrait + $commission;
            $totalDebite += $debit;
            $totalFraisTransfert += $fraisTransfert;
            $totalFraisRetrait += $fraisRetrait;
            $totalCommissions += $commission;
            $totalAReverser += $montantAReverser;

            $destinataires[] = array_merge($destination, [
                'numero' => $numero,
                'part' => $part,
                'fraisTransfert' => $fraisTransfert,
                'fraisRetrait' => $fraisRetrait,
                'commission' => $commission,
                'montantRecu' => round($montantRecu, 2),
                'montantAReverser' => $montantAReverser,
                'debit' => round($debit, 2),
            ]);
        }

        $totalDebite = round($totalDebite, 2);
        if ((float) $expediteur['soldeActuel'] < $totalDebite) {
            throw new DomainException('Solde insuffisant pour couvrir le montant et tous les frais.');
        }

        return [
            'type' => str_starts_with((string) $groupeAttendu, 'externe-') ? 'externe' : 'interne',
            'montantTotal' => $montantTotal,
            'inclureFraisRetrait' => $groupeAttendu === 'interne' && $inclureFraisRetrait,
            'destinataires' => $destinataires,
            'totalFraisTransfert' => round($totalFraisTransfert, 2),
            'totalFraisRetrait' => round($totalFraisRetrait, 2),
            'totalCommissions' => round($totalCommissions, 2),
            'totalAReverser' => round($totalAReverser, 2),
            'totalDebite' => $totalDebite,
        ];
    }

    public function transfererMultiple(
        int $expediteurId,
        array $numeros,
        float $montantTotal,
        bool $inclureFraisRetrait
    ): array {
        $resume = $this->preparerTransfert(
            $expediteurId,
            $numeros,
            $montantTotal,
            $inclureFraisRetrait
        );

        $compte = $this->trouverCompte($expediteurId);
        $operationId = $this->idOperation('transfert');
        $typeEntree = $this->idTypeMouvement('debit');
        $typeSortie = $this->idTypeMouvement('credit');

        $this->db->transStart();
        $this->db->table('compte')->where('id', $expediteurId)->update([
            'soldeActuel' => round((float) $compte['soldeActuel'] - $resume['totalDebite'], 2),
        ]);

        foreach ($resume['destinataires'] as $destination) {
            $mouvementId = $this->ajouterMouvement(
                $expediteurId,
                $destination['debit'],
                $typeSortie,
                $operationId,
                $destination['numero']
            );
            $this->enregistrerFrais($mouvementId, $operationId, $destination['fraisTransfert']);

            if ($destination['type'] === 'interne') {
                $this->db->query(
                    'UPDATE compte SET soldeActuel = soldeActuel + ? WHERE id = ?',
                    [$destination['montantRecu'], $destination['compteId']]
                );
                $this->ajouterMouvement(
                    $destination['compteId'],
                    $destination['montantRecu'],
                    $typeEntree,
                    $operationId,
                    $destination['numero']
                );
            } else {
                $this->db->table('transfertExterne')->insert([
                    'idMouvement' => $mouvementId,
                    'idCompteEnvoyeur' => $expediteurId,
                    'idOperateur' => $destination['operateurId'],
                    'numeroDestinataire' => $destination['numero'],
                    'montantEnvoye' => $destination['part'],
                    'commission' => $destination['commission'],
                    'montantAReverser' => $destination['montantAReverser'],
                ]);
                $this->ajouterGain($destination['commission']);
            }
        }

        $this->db->transComplete();
        $this->verifierTransaction();

        return $resume;
    }

    private function identifierDestination(string $numero, int $expediteurId): array
    {
        $prefixe = substr($numero, 0, 3);
        $prefixeLocal = $this->db->table('prefixeDispo')->where('prefixe', $prefixe)
            ->countAllResults() > 0;

        if ($prefixeLocal) {
            $compte = $this->db->table('compte')->where('numero', $numero)->get()->getRowArray();
            if ($compte === null) {
                throw new DomainException("Le compte {$numero} n’existe pas chez notre opérateur.");
            }
            if ((int) $compte['id'] === $expediteurId) {
                throw new DomainException('Vous ne pouvez pas envoyer de l’argent à votre propre compte.');
            }

            return ['type' => 'interne', 'operateur' => 'Notre opérateur', 'compteId' => (int) $compte['id']];
        }

        $operateur = $this->db->table('prefixeOperateur')
            ->select('operateurExterne.id, operateurExterne.nom, COALESCE(commissionOperateur.pourcentage, 0) AS pourcentage')
            ->join('operateurExterne', 'operateurExterne.id = prefixeOperateur.idOperateur')
            ->join('commissionOperateur', 'commissionOperateur.idOperateur = operateurExterne.id', 'left')
            ->where('prefixeOperateur.prefixe', $prefixe)
            ->get()->getRowArray();

        if ($operateur === null) {
            throw new DomainException("Le préfixe du numéro {$numero} n’est pas reconnu.");
        }

        return [
            'type' => 'externe',
            'operateur' => $operateur['nom'],
            'operateurId' => (int) $operateur['id'],
            'pourcentage' => (float) $operateur['pourcentage'],
        ];
    }

    private function nettoyerNumeros(array $numeros): array
    {
        $numeros = array_values(array_filter(array_map(
            static fn ($numero) => trim((string) $numero),
            $numeros
        )));

        if ($numeros === []) {
            throw new DomainException('Ajoutez au moins un numéro destinataire.');
        }
        foreach ($numeros as $numero) {
            if (! preg_match('/^[0-9]{10}$/', $numero)) {
                throw new DomainException("Le numéro {$numero} doit contenir exactement 10 chiffres.");
            }
        }
        if (count($numeros) !== count(array_unique($numeros))) {
            throw new DomainException('Un même numéro ne peut apparaître qu’une seule fois.');
        }

        return $numeros;
    }

    /** Le reste est distribué sur les premières parts pour garder le total exact. */
    private function partagerMontant(float $montant, int $nombre): array
    {
        $unites = (int) round($montant * 100);
        $base = intdiv($unites, $nombre);
        $reste = $unites % $nombre;
        $parts = [];

        for ($index = 0; $index < $nombre; $index++) {
            $parts[] = ($base + ($index < $reste ? 1 : 0)) / 100;
        }

        return $parts;
    }

    private function montantValide(float $montant): float
    {
        if (! is_finite($montant) || $montant <= 0) {
            throw new DomainException('Le montant doit être supérieur à zéro.');
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
            throw new DomainException("Le type d’opération {$type} n’est pas configuré.");
        }

        return (int) $operation['id'];
    }

    private function idTypeMouvement(string $type): int
    {
        $mouvement = $this->db->table('typeMvmtComp')->where('type', $type)->get()->getRowArray();
        if ($mouvement === null) {
            throw new DomainException("Le type de mouvement {$type} n’est pas configuré.");
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
            throw new DomainException('Aucun barème ne correspond au montant demandé.');
        }

        $pourcent = [];
        $pourcent['pourcentage'] = 0;

        if ($operationId == 3) {
            $pourcent = $this->db->table('promo')->where('id =', 1)->get()->getRowArray();
        }

        return (float) $bareme['prix']*(1-($pourcent['pourcentage']/100));
        // return (float) $bareme['prix'];
    }

    private function ajouterMouvement(
        int $compteId,
        float $valeur,
        int $typeId,
        int $operationId,
        ?string $numeroDestinataire
    ): int
    {
        $this->db->table('mvmtCompte')->insert([
            'idCompte' => $compteId,
            'valeur' => round($valeur, 2),
            'idType' => $typeId,
            'indTypeOp' => $operationId,
            'numeroDestinataire' => $numeroDestinataire,
        ]);

        return (int) $this->db->insertID();
    }

    private function enregistrerFrais(int $mouvementId, int $operationId, float $frais): void
    {
        $this->db->table('FraisMvmt')->insert([
            'idMvmtCompt' => $mouvementId,
            'valeur' => $frais,
            'typeOp' => $operationId,
        ]);
        $this->ajouterGain($frais);
    }

    private function ajouterGain(float $gain): void
    {
        $this->db->query('INSERT OR IGNORE INTO caisseOp (id, gains) VALUES (1, 0)');
        $this->db->query('UPDATE caisseOp SET gains = gains + ? WHERE id = 1', [$gain]);
    }

    private function verifierTransaction(): void
    {
        if (! $this->db->transStatus()) {
            throw new DomainException('L’opération a échoué. Aucune modification n’a été enregistrée.');
        }
    }
}
