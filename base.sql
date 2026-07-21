-- Script unique de création et de données initiales du projet.
PRAGMA foreign_keys = ON;

CREATE TABLE prefixeDispo (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE
);

CREATE TABLE typeOperation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type TEXT NOT NULL UNIQUE
);

CREATE TABLE baremeFrais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    idtypeOp INTEGER NOT NULL,
    montant_min REAL NOT NULL,
    montant_max REAL NOT NULL,
    prix REAL NOT NULL,

    FOREIGN KEY (idtypeOp)
        REFERENCES typeOperation(id)
);

CREATE TABLE compte (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero TEXT NOT NULL UNIQUE,
    soldeActuel REAL NOT NULL DEFAULT 0,
    creation DATE NOT NULL DEFAULT CURRENT_DATE
);

CREATE TABLE typeMvmtComp (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type TEXT NOT NULL UNIQUE
);

CREATE TABLE mvmtCompte (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    idCompte INTEGER NOT NULL,
    valeur REAL NOT NULL,
    date DATE NOT NULL DEFAULT CURRENT_DATE,
    idType INTEGER NOT NULL,
    indTypeOp INTEGER NOT NULL,
    numeroDestinataire TEXT NULL,

    FOREIGN KEY (idCompte)
        REFERENCES compte(id),

    FOREIGN KEY (idType)
        REFERENCES typeMvmtComp(id),

    FOREIGN KEY (indTypeOp)
        REFERENCES typeOperation(id)
);

CREATE TABLE caisseOp (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    gains REAL NOT NULL DEFAULT 0
);

CREATE TABLE FraisMvmt (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    idMvmtCompt INTEGER NOT NULL,
    valeur REAL NOT NULL,
    typeOp INTEGER NOT NULL,
    date DATE NOT NULL DEFAULT CURRENT_DATE,

    FOREIGN KEY (idMvmtCompt)
        REFERENCES mvmtCompte(id),

    FOREIGN KEY (typeOp)
        REFERENCES typeOperation(id)
);

-- Données propres aux transferts vers les autres opérateurs.
CREATE TABLE operateurExterne (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE
);

CREATE TABLE prefixeOperateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    idOperateur INTEGER NOT NULL,
    prefixe TEXT NOT NULL UNIQUE CHECK (length(prefixe) = 3),

    FOREIGN KEY (idOperateur)
        REFERENCES operateurExterne(id) ON DELETE RESTRICT
);

CREATE TABLE commissionOperateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    idOperateur INTEGER NOT NULL UNIQUE,
    pourcentage REAL NOT NULL DEFAULT 0 CHECK (pourcentage >= 0 AND pourcentage <= 100),

    FOREIGN KEY (idOperateur)
        REFERENCES operateurExterne(id) ON DELETE RESTRICT
);

CREATE TABLE transfertExterne (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    idMouvement INTEGER NOT NULL UNIQUE,
    idCompteEnvoyeur INTEGER NOT NULL,
    idOperateur INTEGER NOT NULL,
    numeroDestinataire TEXT NOT NULL,
    montantEnvoye REAL NOT NULL CHECK (montantEnvoye > 0),
    commission REAL NOT NULL DEFAULT 0 CHECK (commission >= 0),
    montantAReverser REAL NOT NULL CHECK (montantAReverser >= 0),
    date DATE NOT NULL DEFAULT CURRENT_DATE,

    FOREIGN KEY (idMouvement)
        REFERENCES mvmtCompte(id),
    FOREIGN KEY (idCompteEnvoyeur)
        REFERENCES compte(id),
    FOREIGN KEY (idOperateur)
        REFERENCES operateurExterne(id) ON DELETE RESTRICT
);

-- ==================================================================
-- Donnees initiales (ETU1944)
-- Les INSERT OR IGNORE permettent de rejouer ce fichier sans creer
-- de doublons dans les tables ayant une contrainte UNIQUE.

INSERT OR IGNORE INTO prefixeDispo (prefixe) VALUES
    ('032'), ('033'), ('034'), ('037'), ('038');

INSERT OR IGNORE INTO typeOperation (type) VALUES
    ('depot'), ('retrait'), ('transfert');

INSERT OR IGNORE INTO typeMvmtComp (type) VALUES
    ('debit'), ('credit');

-- Memes tranches pour les trois operations. Le depot est gratuit pour
-- l'instant, tandis que le retrait et le transfert utilisent le bareme v1.
INSERT INTO baremeFrais (idtypeOp, montant_min, montant_max, prix)
SELECT
    op.id,
    b.montant_min,
    b.montant_max,
    CASE WHEN op.type = 'depot' THEN 0 ELSE b.prix END
FROM typeOperation op
CROSS JOIN (
    SELECT 100 AS montant_min, 1000 AS montant_max, 50 AS prix
    UNION ALL SELECT 1001, 5000, 50
    UNION ALL SELECT 5001, 10000, 100
    UNION ALL SELECT 10001, 25000, 200
    UNION ALL SELECT 25001, 50000, 400
    UNION ALL SELECT 50001, 100000, 800
    UNION ALL SELECT 100001, 250000, 1500
    UNION ALL SELECT 250001, 500000, 1500
    UNION ALL SELECT 500001, 1000000, 2500
    UNION ALL SELECT 1000001, 2000000, 3000
) b
WHERE op.type IN ('depot', 'retrait', 'transfert')
  AND NOT EXISTS (
      SELECT 1 FROM baremeFrais f
      WHERE f.idtypeOp = op.id
        AND f.montant_min = b.montant_min
        AND f.montant_max = b.montant_max
  );

INSERT OR IGNORE INTO compte (numero, soldeActuel) VALUES
    ('0321234567', 500000),
    ('0332345678', 300000),
    ('0343456789', 150000),
    ('0374567890', 750000);

INSERT OR IGNORE INTO caisseOp (id, gains) VALUES (1, 0);

INSERT OR IGNORE INTO operateurExterne (nom) VALUES
    ('Operateur 031'),
    ('Operateur 035');

INSERT OR IGNORE INTO prefixeOperateur (idOperateur, prefixe)
SELECT id, '031' FROM operateurExterne WHERE nom = 'Operateur 031';

INSERT OR IGNORE INTO prefixeOperateur (idOperateur, prefixe)
SELECT id, '035' FROM operateurExterne WHERE nom = 'Operateur 035';

INSERT OR IGNORE INTO commissionOperateur (idOperateur, pourcentage)
SELECT id, 5 FROM operateurExterne WHERE nom = 'Operateur 031';

INSERT OR IGNORE INTO commissionOperateur (idOperateur, pourcentage)
SELECT id, 4 FROM operateurExterne WHERE nom = 'Operateur 035';

-- Un mouvement initial explique le solde de chaque compte de test.
INSERT INTO mvmtCompte (idCompte, valeur, idType, indTypeOp)
SELECT c.id, c.soldeActuel, tm.id, op.id
FROM compte c
JOIN typeMvmtComp tm ON tm.type = 'debit'
JOIN typeOperation op ON op.type = 'depot'
WHERE NOT EXISTS (
    SELECT 1 FROM mvmtCompte m WHERE m.idCompte = c.id
);

=== ALEA 2
CREATE TABLE soldeEpargne (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    idCompte INTEGER NOT NULL,
    soldeEp REAL NOT NULL DEFAULT 0,

    FOREIGN KEY (idCompte)
        REFERENCES compte(id)
);

CREATE TABLE epargne (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    idCompte INTEGER NOT NULL,
    pourcentage REAL NOT NULL DEFAULT 0 CHECK (pourcentage >= 0 AND pourcentage <= 100),

    FOREIGN KEY (idCompte)
        REFERENCES compte(id)
);




-- ==================================================================
-- prefixeDispo :
--   - int id;
--   - VARCHAR prefixe;

-- typeOperation :
--   - int id;
--   - varchar type; (depot, retrait, transfert)

-- baremeFrais :
--   - int id;
--   - int idtypeOp;
--   - FLOAT montant_min;
--   - FLOAT montant_max;
--   - FLOAT prix;

-- compte :
--   - int id;
--   - VARCHAR numero;
--   - FLOAT soldeActuel;
--   - Date creation;

-- typeMvmtComp :
--   - int id;
--   - VARCHAR type;(debit(entree) ou credit(sortie))

-- mvmtCompte :
--   - int id;
--   - int idCompte;
--   - FLOAT valeur;
--   - Date date;
--   - int idType; (debit(entree) ou credit(sortie))
--   - int indTypeOp;
--   - VARCHAR numeroDestinataire; (NULL pour un depot)

-- caisseOp :
--   - int id;
--   - FLOAT gains;

-- FraisMvmt :
--   - int id;
--   - int idMvmtCompt;
--   - FLOAT valeur;
--   - int typeOp;
--   - Date date 
