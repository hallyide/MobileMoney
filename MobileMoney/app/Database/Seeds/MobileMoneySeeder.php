<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Donnees necessaires pour tester la version 1.
 * Les tables doivent déjà avoir été créées avec le fichier base.sql.
 */
class MobileMoneySeeder extends Seeder
{
    public function run(): void
    {
        $this->insertIfMissing('prefixeDispo', 'prefixe', [
            ['prefixe' => '032'],
            ['prefixe' => '033'],
            ['prefixe' => '034'],
            ['prefixe' => '037'],
            ['prefixe' => '038'],
        ]);

        $this->insertIfMissing('typeOperation', 'type', [
            ['type' => 'depot'],
            ['type' => 'retrait'],
            ['type' => 'transfert'],
        ]);

        $this->insertIfMissing('typeMvmtComp', 'type', [
            ['type' => 'debit'],
            ['type' => 'credit'],
        ]);

        $this->insertIfMissing('compte', 'numero', [
            ['numero' => '0321234567', 'soldeActuel' => 500000],
            ['numero' => '0332345678', 'soldeActuel' => 300000],
            ['numero' => '0343456789', 'soldeActuel' => 150000],
            ['numero' => '0374567890', 'soldeActuel' => 750000],
        ]);

        $this->db->query('INSERT OR IGNORE INTO caisseOp (id, gains) VALUES (1, 0)');
        $this->seedBaremes();
        $this->seedInitialMovements();
    }

    /** Insere uniquement les valeurs qui ne sont pas encore presentes. */
    private function insertIfMissing(string $table, string $uniqueColumn, array $rows): void
    {
        foreach ($rows as $row) {
            $exists = $this->db->table($table)
                ->where($uniqueColumn, $row[$uniqueColumn])
                ->countAllResults() > 0;

            if (! $exists) {
                $this->db->table($table)->insert($row);
            }
        }
    }

    private function seedBaremes(): void
    {
        $tranches = [
            [100, 1000, 50],
            [1001, 5000, 50],
            [5001, 10000, 100],
            [10001, 25000, 200],
            [25001, 50000, 400],
            [50001, 100000, 800],
            [100001, 250000, 1500],
            [250001, 500000, 1500],
            [500001, 1000000, 2500],
            [1000001, 2000000, 3000],
        ];

        $operations = $this->db->table('typeOperation')
            ->whereIn('type', ['depot', 'retrait', 'transfert'])
            ->get()->getResultArray();

        foreach ($operations as $operation) {
            foreach ($tranches as [$minimum, $maximum, $prix]) {
                // Le depot est configure dans le bareme, mais reste gratuit.
                $prixApplique = $operation['type'] === 'depot' ? 0 : $prix;
                $exists = $this->db->table('baremeFrais')
                    ->where('idtypeOp', $operation['id'])
                    ->where('montant_min', $minimum)
                    ->where('montant_max', $maximum)
                    ->countAllResults() > 0;

                if (! $exists) {
                    $this->db->table('baremeFrais')->insert([
                        'idtypeOp' => $operation['id'],
                        'montant_min' => $minimum,
                        'montant_max' => $maximum,
                        'prix' => $prixApplique,
                    ]);
                }
            }
        }
    }

    private function seedInitialMovements(): void
    {
        $debit = $this->db->table('typeMvmtComp')->where('type', 'debit')->get()->getRowArray();
        $depot = $this->db->table('typeOperation')->where('type', 'depot')->get()->getRowArray();
        $comptes = $this->db->table('compte')->get()->getResultArray();

        foreach ($comptes as $compte) {
            $exists = $this->db->table('mvmtCompte')
                ->where('idCompte', $compte['id'])
                ->countAllResults() > 0;

            if (! $exists) {
                $this->db->table('mvmtCompte')->insert([
                    'idCompte' => $compte['id'],
                    'valeur' => $compte['soldeActuel'],
                    'idType' => $debit['id'],
                    'indTypeOp' => $depot['id'],
                ]);
            }
        }
    }
}
