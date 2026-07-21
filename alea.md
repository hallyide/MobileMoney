Aleas :
    - promotion pour le frais de transfert :
        - en pourcentage meme operateur(creation de cette config dans base)

        TABLE promo :
            - id int;
            - pourcentage FLOAT NOT NULL DEFAULT 0
            
            CREATE TABLE promo (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                pourcentage REAL NOT NULL DEFAULT 0 CHECK (pourcentage >= 0 AND pourcentage <= 100)
            );



Client manana epargne, safidy en % -> injecter anaty base

rehefa misy vola tonga (transfert) any aminy de mihatra ilay epargne dia mandeha @ solde principale et solde epargne


table epargne
- id
- idCompte
- pourcentage

CREATE TABLE epargne (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    idCompte INTEGER NOT NULL,
    pourcentage REAL NOT NULL DEFAULT 0 CHECK (pourcentage >= 0 AND pourcentage <= 100),

    FOREIGN KEY (idCompte)
        REFERENCES compte(id)
);




table soldeEpargne
- id
- idCompte
- soldeEp

CREATE TABLE soldeEpargne (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    idCompte INTEGER NOT NULL,
    soldeEp REAL NOT NULL DEFAULT 0,

    FOREIGN KEY (idCompte)
        REFERENCES compte(id)
);