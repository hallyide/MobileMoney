Operateur (Admin) :
Dashboards :
    - Header(avec bouton  : bareme des frais, Historique gains, Comptes clients, deconnexion)
    - Valeur des gains
    - nombre de clients
    - prefixes disponnible (tableau qui liste celle ci(avec bouton modifier(pop up formulaire modif), supprimer et ajout(pop up formulaire ajout)))
    - Statistiques des transactions par jour(courbe, modifiable par jour, par semaine mois anees)

Bareme des frais :
    - tableau qui liste les bareme des frais avec leurs types ecrit(et action a la fin(bouton modif(formulaire) et supprimer))
    - bouton ajout (formulaire);

Historique gains :
    - tableau de frais mvmt avec type d'operations et le client(action modif avec supprimer)
    - bouton ajout.


Situation des comptes :
 - au dessous liste des clients recèment visionner

 - En appuyant sur un client une page pour afficher le detailes d'un client : 
    - Information des comptes (nom, solde, ... etc)
    - Tableau contenant les historique des transaction mouvement :
        - int id;
        - int idCompte;
        - FLOAT valeur;
        - Date date;
        - int idType; (debit(entree) ou credit(sortie))
        - int indTypeOp;


Page profile client :  
- Affichage du solde en temps réel
- Affichage de l'information du compte

Client : 
Login :
    - numero(type varchar)
    - bouton valider
Dashboards :
    - Header(avec bouton  : depot, retrait, Transfert, deconnexion)
    - affichage du solde actuelle
    - nombre de transactions(grace a la table mvmtCompte)
    - tableau des 10 dernieres transactions
    - bouton afficher plus de transactions.

Historique des transactions :
    - tableau des transactions.

Depot :
    - formulaire de depot(insertion : montant);
Retrait :
    - formulaire de retrait(insertion : montant);
Transfert :
    - formulaire de transfert(num destinataire, montant);


