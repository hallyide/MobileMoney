# Tâches — Projet Mobile Money

☑ ☐

## 1. Initialisation et préparation

| Statut | Tâche | Responsable |
|---|---|---|
| ☑ | Configuration du dépôt Git | **ETU1944** |
| ☑ | Recherche et intégration du template opérateur/client | **ETU4269** |

## 2. Base de données et données initiales

| Statut | Tâche | Responsable |
|---|---|---|
| ☑ | Création de la base SQLite embarquée | **ETU4269** |
| ☑ | Création des tables : comptes, préfixes, barèmes, mouvements, frais et caisse | **ETU4269** |
| ☑ | Insertion des données de test et du barème des frais | **ETU1944** |

## 3. Back-office opérateur

| Statut | Tâche | Responsable |
|---|---|---|
| ☑ | Gestion des préfixes téléphoniques | **ETU1944** |
| ☑ | Gestion du barème des frais par tranche | **ETU4269** |
| ☑ | Suivi des gains de l’opérateur pour les retraits et transferts | **ETU4269** |
| ☑ | Visualisation de la situation et des mouvements des comptes clients | **ETU1944** |

## 4. Application client

| Statut | Tâche | Responsable |
|---|---|---|
| ☑ | Connexion automatique par numéro avec vérification du préfixe et création du compte | **ETU4269** |
| ☑ | Affichage du solde actuel | **ETU1944** |
| ☑ | Dépôt automatique avec frais actuellement fixés à zéro | **ETU1944** |
| ☑ | Retrait automatique avec déduction des frais | **ETU4269** |
| ☑ | Transfert de compte à compte avec calcul dynamique des frais | **ETU1944** |
| ☑ | Consultation de l’historique des transactions | **ETU4269** |

## 5. Finalisation et livraison

| Statut | Tâche | Responsable |
|---|---|---|
| ☑ | Tests globaux des dépôts, retraits, transferts et frais | **ETU4269** |
| ☐ | Création du tag Git **v1** après validation finale | **ETU1944** |
| ☐ | Livraison finale sur la branche principale | **ETU1944** |

## Répartition

### ETU1944

- [x] Configuration du dépôt Git
- [x] Données de test et seeder rejouable
- [x] Gestion des préfixes
- [x] Situation des comptes clients
- [x] Affichage du solde
- [x] Dépôt
- [x] Transfert
- [x] Tag Git v1 après validation
- [ ] Livraison finale après validation

### ETU4269

- [x] Intégration du template
- [x] Base SQLite et tables
- [x] Gestion du barème
- [x] Suivi des gains
- [x] Connexion automatique
- [x] Retrait
- [x] Historique des transactions
- [x] Tests globaux

## Progression

- **Total :** 18 tâches
- **Terminées :** 16 / 18
- **Restantes :** tag `v1` et livraison finale, après validation de l’application

## Choix techniques après audit

- Les données et les tableaux sont produits par PHP depuis SQLite.
- JavaScript est limité aux modales, notifications, recherche locale et estimation visuelle des frais.
- Toutes les opérations financières sont exécutées dans des transactions SQL.
- Le client est identifié par la session ; aucun identifiant de compte sensible n’est placé dans l’URL.
- `base.sql`, situé à la racine du dépôt, est l’unique script de création et de données initiales.
