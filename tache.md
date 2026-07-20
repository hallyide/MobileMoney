# TODO List — Projet de Gestion de Transactions

## 1. Initialisation & Préparation

| Statut | Tâche | Responsable |
|--------|-------|-------------|
| ☑ | Configuration du dépôt Git (préparation du futur **Tag v1**) | **ETU1944** |
| ☐ | Recherche et intégration du template (Interface **Opérateur** et **Client**) | **ETU4269** |

---

## 2. Base de données & Données initiales

| Statut | Tâche | Responsable |
|--------|-------|-------------|
| ☐ | Création de la base de données | **ETU4269** |
| ☐ | Création des tables : Utilisateurs, Configuration des préfixes, Barème des frais, Transactions | **ETU4269** |
| ☑ | Insertion des données de test et du barème des frais (Seeding) | **ETU1944** |

---

## 3. Développement du Back-Office (Opérateur)

| Statut | Tâche | Responsable |
|--------|-------|-------------|
| ☑ | Module de gestion et configuration des préfixes téléphoniques | **ETU1944** |
| ☐ | Module de gestion du barème des frais par tranche | **ETU4269** |
| ☐ | Écran de suivi des gains de l'opérateur (frais de retrait et de transfert) | **ETU4269** |
| ☑ | Écran de visualisation de la situation des comptes clients | **ETU1944** |

---

## 4. Développement de l'Application (Client)

| Statut | Tâche | Responsable |
|--------|-------|-------------|
| ☐ | Système de connexion automatique par numéro de téléphone (avec vérification du préfixe) | **ETU4269** |
| ☑ | Affichage du solde en temps réel | **ETU1944** |
| ☑ | Module de dépôt (simulation automatique) | **ETU1944** |
| ☐ | Module de retrait (simulation automatique avec déduction des frais) | **ETU4269** |
| ☑ | Module de transfert de compte à compte (calcul et application dynamique des frais) | **ETU1944** |
| ☐ | Écran de consultation de l'historique des transactions | **ETU4269** |

---

## 5. Finalisation & Livraison

| Statut | Tâche | Responsable |
|--------|-------|-------------|
| ☐ | Tests globaux des calculs de frais et des transferts | **ETU4269** |
| ☐ | Création du **Tag Git v1** | **ETU1944** |
| ☐ | Déploiement ou livraison finale | **ETU1944** |

---

## Répartition des tâches

### ETU1944

- [x] Configuration du dépôt Git
- [x] Insertion des données de test (Seeding)
- [x] Gestion des préfixes téléphoniques
- [x] Visualisation des comptes clients
- [x] Affichage du solde
- [x] Module de dépôt
- [x] Module de transfert
- [ ] Création du Tag Git v1 (à faire après intégration)
- [ ] Déploiement / Livraison finale (à faire après intégration)

### ETU4269
- [x] Intégration du template
- [x] Création de la base de données
- [x] Création des tables
- [ ] Gestion du barème des frais
- [ ] Suivi des gains de l'opérateur
- [ ] Connexion automatique
- [ ] Module de retrait
- [ ] Historique des transactions
- [ ] Tests globaux

---

## Progression

- **Total des tâches :** 18
- **Terminées :** 7 / 18
- **En cours :** 0
- **Restantes :** 11

## Note d'intégration

Les pages client utilisent provisoirement la route `/client/{idCompte}`. Après la création
du module de connexion par ETU4269, l'identifiant devra être lu depuis la session utilisateur.
La logique des dépôts et des transferts dans `TransactionService` ne devra pas être modifiée.
