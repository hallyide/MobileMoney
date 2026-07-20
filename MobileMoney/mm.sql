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

ALTER TABLE compte ADD COLUMN nom VARCHAR(150) NOT NULL DEFAULT '';

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

-- caisseOp :
--   - int id;
--   - FLOAT gains;

-- FraisMvmt :
--   - int id;
--   - int idMvmtCompt;
--   - FLOAT valeur;
--   - int typeOp;
--   - Date date 
