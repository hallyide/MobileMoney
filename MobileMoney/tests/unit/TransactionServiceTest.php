<?php

namespace Tests\Unit;

use App\Services\TransactionService;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use DomainException;
use PHPUnit\Framework\TestCase;

class TransactionServiceTest extends TestCase
{
    private BaseConnection $db;
    private TransactionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->db = Database::connect([
            'database' => ':memory:',
            'DBDriver' => 'SQLite3',
            'DBPrefix' => '',
            'DBDebug' => true,
            'foreignKeys' => true,
        ], false);

        $this->creerBase();
        $this->service = new TransactionService($this->db);
    }

    public function testDepotGratuit(): void
    {
        $frais = $this->service->deposer(1, 1000);

        $this->assertSame(0.0, $frais);
        $this->assertSame(11000.0, $this->solde(1));
        $this->assertSame(0.0, $this->dernierFrais());
    }

    public function testRetraitDeduitMontantEtFrais(): void
    {
        $frais = $this->service->retirer(1, 1000);

        $this->assertSame(100.0, $frais);
        $this->assertSame(8900.0, $this->solde(1));
        $this->assertSame(1100.0, $this->dernierMouvement(1));
        $this->assertSame(100.0, $this->gains());
    }

    public function testTransfertMetAJourLesDeuxComptes(): void
    {
        $frais = $this->service->transferer(1, '0330000002', 1000);

        $this->assertSame(50.0, $frais);
        $this->assertSame(8950.0, $this->solde(1));
        $this->assertSame(2000.0, $this->solde(2));
        $this->assertSame(1050.0, $this->dernierMouvement(1));
        $this->assertSame(50.0, $this->gains());
    }

    public function testTransfertRefuseSiLeSoldeEstInsuffisant(): void
    {
        $this->expectException(DomainException::class);

        try {
            $this->service->transferer(2, '0320000001', 5000);
        } finally {
            $this->assertSame(1000.0, $this->solde(2));
            $this->assertSame(0.0, $this->gains());
        }
    }

    public function testFraisDeRetraitInclusDansUnTransfertInterne(): void
    {
        $resume = $this->service->transfererMultiple(1, ['0330000002'], 1000, true);

        $this->assertSame(1150.0, $resume['totalDebite']);
        $this->assertSame(8850.0, $this->solde(1));
        $this->assertSame(2100.0, $this->solde(2));
        $this->assertSame(50.0, $this->gains());
    }

    public function testTransfertExterneDeduitLaCommissionDuMontant(): void
    {
        $resume = $this->service->transfererMultiple(1, ['0310000003'], 1000, true);
        $externe = $this->db->table('transfertExterne')->get()->getRowArray();

        $this->assertSame('externe', $resume['type']);
        $this->assertSame(1050.0, $resume['totalDebite']);
        $this->assertSame(100.0, (float) $externe['commission']);
        $this->assertSame(900.0, (float) $externe['montantAReverser']);
        $this->assertSame(150.0, $this->gains());
        $this->assertSame(0.0, $resume['totalFraisRetrait']);
    }

    public function testEnvoiMultipleConserveLeMontantExact(): void
    {
        $resume = $this->service->transfererMultiple(
            1,
            ['0310000003', '0310000004', '0310000005'],
            1000,
            false
        );

        $parts = array_column($resume['destinataires'], 'part');
        $this->assertSame(1000.0, array_sum($parts));
        $this->assertCount(3, $this->db->table('transfertExterne')->get()->getResultArray());
    }

    public function testEnvoiMultipleRefuseDeuxOperateursDifferents(): void
    {
        $this->expectException(DomainException::class);
        $this->service->preparerTransfert(1, ['0310000003', '0350000004'], 1000, false);
    }

    public function testCommissionAccepteLesBornesZeroEtCentPourCent(): void
    {
        $this->db->table('commissionOperateur')->where('idOperateur', 1)
            ->update(['pourcentage' => 0]);
        $sansCommission = $this->service->preparerTransfert(1, ['0310000003'], 1000, false);

        $this->assertSame(0.0, $sansCommission['totalCommissions']);
        $this->assertSame(1000.0, $sansCommission['totalAReverser']);

        $this->db->table('commissionOperateur')->where('idOperateur', 1)
            ->update(['pourcentage' => 100]);
        $commissionComplete = $this->service->preparerTransfert(1, ['0310000003'], 1000, false);

        $this->assertSame(1000.0, $commissionComplete['totalCommissions']);
        $this->assertSame(0.0, $commissionComplete['totalAReverser']);
    }

    public function testErreurDeValidationNeLaisseAucuneEcriture(): void
    {
        try {
            $this->service->transfererMultiple(1, ['0310000003', '0350000004'], 1000, false);
            $this->fail('Le transfert devait etre refuse.');
        } catch (DomainException) {
            $this->assertSame(10000.0, $this->solde(1));
            $this->assertSame(0, $this->db->table('mvmtCompte')->countAllResults());
            $this->assertSame(0, $this->db->table('FraisMvmt')->countAllResults());
            $this->assertSame(0, $this->db->table('transfertExterne')->countAllResults());
            $this->assertSame(0.0, $this->gains());
        }
    }

    private function creerBase(): void
    {
        $requetes = [
            'CREATE TABLE typeOperation (id INTEGER PRIMARY KEY, type TEXT NOT NULL UNIQUE)',
            'CREATE TABLE typeMvmtComp (id INTEGER PRIMARY KEY, type TEXT NOT NULL UNIQUE)',
            'CREATE TABLE baremeFrais (id INTEGER PRIMARY KEY AUTOINCREMENT, idtypeOp INTEGER NOT NULL, montant_min REAL NOT NULL, montant_max REAL NOT NULL, prix REAL NOT NULL)',
            'CREATE TABLE compte (id INTEGER PRIMARY KEY, numero TEXT NOT NULL UNIQUE, soldeActuel REAL NOT NULL, creation DATE DEFAULT CURRENT_DATE)',
            'CREATE TABLE mvmtCompte (id INTEGER PRIMARY KEY AUTOINCREMENT, idCompte INTEGER NOT NULL, valeur REAL NOT NULL, date DATE DEFAULT CURRENT_DATE, idType INTEGER NOT NULL, indTypeOp INTEGER NOT NULL)',
            'CREATE TABLE caisseOp (id INTEGER PRIMARY KEY, gains REAL NOT NULL DEFAULT 0)',
            'CREATE TABLE FraisMvmt (id INTEGER PRIMARY KEY AUTOINCREMENT, idMvmtCompt INTEGER NOT NULL, valeur REAL NOT NULL, typeOp INTEGER NOT NULL, date DATE DEFAULT CURRENT_DATE)',
            'CREATE TABLE prefixeDispo (id INTEGER PRIMARY KEY AUTOINCREMENT, prefixe TEXT NOT NULL UNIQUE)',
            'CREATE TABLE operateurExterne (id INTEGER PRIMARY KEY, nom TEXT NOT NULL UNIQUE)',
            'CREATE TABLE prefixeOperateur (id INTEGER PRIMARY KEY AUTOINCREMENT, idOperateur INTEGER NOT NULL, prefixe TEXT NOT NULL UNIQUE)',
            'CREATE TABLE commissionOperateur (id INTEGER PRIMARY KEY AUTOINCREMENT, idOperateur INTEGER NOT NULL UNIQUE, pourcentage REAL NOT NULL)',
            'CREATE TABLE transfertExterne (id INTEGER PRIMARY KEY AUTOINCREMENT, idMouvement INTEGER NOT NULL UNIQUE, idCompteEnvoyeur INTEGER NOT NULL, idOperateur INTEGER NOT NULL, numeroDestinataire TEXT NOT NULL, montantEnvoye REAL NOT NULL, commission REAL NOT NULL, montantAReverser REAL NOT NULL, date DATE DEFAULT CURRENT_DATE)',
            "INSERT INTO typeOperation VALUES (1, 'depot'), (2, 'retrait'), (3, 'transfert')",
            "INSERT INTO typeMvmtComp VALUES (1, 'debit'), (2, 'credit')",
            'INSERT INTO baremeFrais (idtypeOp, montant_min, montant_max, prix) VALUES (1, 100, 2000000, 0), (2, 100, 2000000, 100), (3, 100, 2000000, 50)',
            "INSERT INTO compte (id, numero, soldeActuel) VALUES (1, '0320000001', 10000), (2, '0330000002', 1000)",
            "INSERT INTO prefixeDispo (prefixe) VALUES ('032'), ('033')",
            "INSERT INTO operateurExterne VALUES (1, 'Operateur 031'), (2, 'Operateur 035')",
            "INSERT INTO prefixeOperateur (idOperateur, prefixe) VALUES (1, '031'), (2, '035')",
            'INSERT INTO commissionOperateur (idOperateur, pourcentage) VALUES (1, 10), (2, 5)',
            'INSERT INTO caisseOp (id, gains) VALUES (1, 0)',
        ];

        foreach ($requetes as $requete) {
            $this->db->query($requete);
        }
    }

    private function solde(int $id): float
    {
        return (float) $this->db->table('compte')->where('id', $id)->get()->getRow('soldeActuel');
    }

    private function dernierMouvement(int $compteId): float
    {
        return (float) $this->db->table('mvmtCompte')->where('idCompte', $compteId)
            ->orderBy('id', 'DESC')->get()->getRow('valeur');
    }

    private function dernierFrais(): float
    {
        return (float) $this->db->table('FraisMvmt')->orderBy('id', 'DESC')->get()->getRow('valeur');
    }

    private function gains(): float
    {
        return (float) $this->db->table('caisseOp')->where('id', 1)->get()->getRow('gains');
    }
}
