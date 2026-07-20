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
            "INSERT INTO typeOperation VALUES (1, 'depot'), (2, 'retrait'), (3, 'transfert')",
            "INSERT INTO typeMvmtComp VALUES (1, 'debit'), (2, 'credit')",
            'INSERT INTO baremeFrais (idtypeOp, montant_min, montant_max, prix) VALUES (1, 100, 2000000, 0), (2, 100, 2000000, 100), (3, 100, 2000000, 50)',
            "INSERT INTO compte (id, numero, soldeActuel) VALUES (1, '0320000001', 10000), (2, '0330000002', 1000)",
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
