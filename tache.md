# Tâches — Projet Mobile Money

# Version 1

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
- [ ] Tag Git v1 après validation
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

## Progression V1

- **Total :** 18 tâches
- **Terminées :** 16 / 18
- **Restantes :** tag `v1` et livraison finale

---

# Version 2

## Règles de gestion

- Le solde des autres opérateurs n’est pas géré dans l’application.
- La commission est retirée du montant envoyé vers un autre opérateur.
- Aucun frais de retrait n’est appliqué pour un transfert vers un autre opérateur.
- L’envoi multiple est autorisé uniquement vers des numéros du même opérateur.
- Le montant total d’un envoi multiple est partagé entre les destinataires.

## 1. Base de données et données initiales

| Statut | Tâche | Responsable |
|---|---|---|
| ☐ | Création de la table des autres opérateurs | **ETU1944** |
| ☐ | Création de la table des préfixes des autres opérateurs | **ETU1944** |
| ☐ | Création de la table des commissions par opérateur | **ETU1944** |
| ☐ | Création de la table des transferts vers les autres opérateurs | **ETU1944** |
| ☐ | Création des modèles correspondants | **ETU1944** |
| ☐ | Ajout des opérateurs, préfixes et commissions de test dans le seeder | **ETU1944** |
| ☐ | Tests de création de la base V2 avec `base.sql` | **ETU1944** |

## 2. Back-office opérateur

| Statut | Tâche | Responsable |
|---|---|---|
| ☐ | Liste des autres opérateurs | **ETU4269** |
| ☐ | Ajout, modification et suppression d’un opérateur | **ETU4269** |
| ☐ | Gestion des préfixes par opérateur | **ETU4269** |
| ☐ | Configuration du pourcentage de commission par opérateur | **ETU4269** |
| ☐ | Séparation des gains internes et des commissions externes | **ETU4269** |
| ☐ | Affichage des montants à envoyer à chaque opérateur | **ETU4269** |
| ☐ | Affichage du détail par numéro destinataire | **ETU4269** |
| ☐ | Filtres par opérateur et par période | **ETU4269** |
| ☐ | Tests des pages et formulaires opérateur | **ETU4269** |

## 3. Transferts vers les autres opérateurs

| Statut | Tâche | Responsable |
|---|---|---|
| ☐ | Détection de l’opérateur avec le préfixe du destinataire | **ETU1944** |
| ☐ | Calcul de la commission en pourcentage | **ETU1944** |
| ☐ | Déduction de la commission sur le montant envoyé | **ETU1944** |
| ☐ | Enregistrement du montant à envoyer à l’autre opérateur | **ETU1944** |
| ☐ | Enregistrement du numéro destinataire | **ETU1944** |
| ☐ | Ajout de la commission dans les gains de l’opérateur local | **ETU1944** |
| ☐ | Débit du compte local sans création d’un compte externe | **ETU1944** |
| ☐ | Conservation du transfert interne de la V1 | **ETU1944** |
| ☐ | Gestion des erreurs et annulation complète de la transaction | **ETU1944** |
| ☐ | Tests des transferts internes et externes | **ETU1944** |

## 4. Application client

| Statut | Tâche | Responsable |
|---|---|---|
| ☐ | Ajout de l’option « inclure les frais de retrait » | **ETU1944** |
| ☐ | Application des frais de retrait pour un transfert interne | **ETU1944** |
| ☐ | Suppression des frais de retrait pour un transfert externe | **ETU1944** |
| ☐ | Ajout de plusieurs numéros destinataires | **ETU1944** |
| ☐ | Vérification que les destinataires utilisent le même opérateur | **ETU1944** |
| ☐ | Partage du montant total entre les destinataires | **ETU1944** |
| ☐ | Gestion des arrondis lors du partage | **ETU1944** |
| ☐ | Affichage du récapitulatif avant validation | **ETU1944** |
| ☐ | Tests de l’envoi multiple | **ETU1944** |

## 5. Finalisation et livraison

| Statut | Tâche | Responsable |
|---|---|---|
| ☐ | Intégration des routes et des liens V2 | **ETU1944** |
| ☐ | Tests globaux des versions 1 et 2 | **ETU1944** |
| ☐ | Vérification des commissions, montants à envoyer et gains | **ETU1944** |
| ☐ | Vérification de l’utilisation minimale de JavaScript | **ETU1944** |
| ☐ | Mise à jour du README et du fichier des tâches | **ETU1944** |
| ☐ | Création du tag Git **v2** | **ETU1944** |
| ☐ | Livraison finale de la version 2 | **ETU1944** |

## Répartition V2

### ETU1944

- [ ] Tables, modèles et données initiales V2
- [ ] Gestion des transferts internes et externes
- [ ] Calcul et enregistrement des commissions
- [ ] Option d’inclusion des frais de retrait
- [ ] Envoi multiple vers le même opérateur
- [ ] Tests métier et intégration finale
- [ ] Tag Git v2 et livraison

### ETU4269

- [ ] Gestion des autres opérateurs
- [ ] Gestion des préfixes externes
- [ ] Configuration des commissions
- [ ] Séparation des gains
- [ ] Situation des montants à envoyer
- [ ] Détail par destinataire
- [ ] Tests du back-office V2

## Organisation du merge

- **ETU1944 :** base, modèles, services, contrôleurs client et vues client.
- **ETU4269 :** contrôleurs opérateur et vues opérateur.
- `Routes.php`, les layouts, `tache.md` et `README.md` seront intégrés uniquement par **ETU1944**.

## Progression V2

- **Total :** 42 tâches
- **Terminées :** 0 / 42
- **Restantes :** 42
