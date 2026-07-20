prefixeDispo :
  - int id;
  - VARCHAR prefixe;

typeOperation :
  - int id;
  - varchar type; (depot, retrait, transfert)

baremeFrais :
  - int id;
  - int idtypeOp;
  - FLOAT montant_min;
  - FLOAT montant_max;
  - FLOAT prix;

compte :
  - int id;
  - VARCHAR numero;
  - FLOAT soldeActuel;
  - Date creation;
(Update a chaque changement)

typeMvmtComp :
  - int id;
  - VARCHAR type;(debit(entree) ou credit(sortie))

mvmtCompte :
  - int id;
  - int idCompte;
  - FLOAT valeur;
  - Date date;
  - int idType; (debit(entree) ou credit(sortie))
  - int indTypeOp;

caisseOp :
  - int id;
  - FLOAT gains;

FraisMvmt :
  - int id;
  - int idMvmtCompt;
  - FLOAT valeur;
  - int typeOp;
  - date 

