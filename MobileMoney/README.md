# Mobile Money — Version 2

Application CodeIgniter 4 utilisant une base SQLite embarquée.

## Démarrage

Depuis le dossier `MobileMoney` :

```bash
php spark serve
```

Ouvrir ensuite `http://localhost:8080`.

- Espace opérateur : `/admin/dashboard`
- Connexion client : `/client/login`

Un numéro client valide doit contenir dix chiffres et commencer par un préfixe
configuré. Lors de sa première connexion, son compte est créé automatiquement.

La version 2 ajoute la gestion des opérateurs externes, de leurs préfixes et de
leurs commissions. Un client peut envoyer un montant vers plusieurs numéros du
même opérateur. Pour un opérateur externe, la commission est retirée du montant
à reverser et aucun compte externe n'est créé dans l'application.

Pages opérateur ajoutées :

- `/admin/operateurs` : opérateurs externes, préfixes et commissions ;
- `/admin/versements` : montants à reverser, détails et filtres ;
- `/admin/gains` : séparation des frais habituels et commissions externes.

## Base de données

- Base utilisée par l’application : `writable/mobile_money.db`
- Script unique de création et de données : `../base.sql`
- Seeder rejouable sans doublons :

```bash
php spark db:seed MobileMoneySeeder
```

## Tests

```bash
composer test -- --no-coverage
```

La logique des dépôts, retraits et transferts est centralisée dans
`app/Services/TransactionService.php`. Les calculs et écritures financières sont
réalisés côté PHP dans des transactions SQL ; JavaScript reste limité à l’interface.
