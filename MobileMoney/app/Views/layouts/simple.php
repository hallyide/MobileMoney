<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($titre ?? 'Mobile Money') ?></title>
    <style>
        :root { color-scheme: light; --primaire: #146c43; --fond: #f4f7f6; --danger: #b42318; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; color: #26312d; background: var(--fond); }
        header { background: var(--primaire); color: white; padding: 18px 0; }
        nav, main { width: min(1050px, 92%); margin: auto; }
        nav { display: flex; gap: 18px; align-items: center; flex-wrap: wrap; }
        nav strong { margin-right: auto; }
        nav a { color: white; text-decoration: none; }
        main { padding: 28px 0; }
        .carte { background: white; padding: 22px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px #0000000d; }
        .solde { color: var(--primaire); font-size: 2rem; font-weight: bold; }
        .actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: end; }
        a.bouton, button { display: inline-block; border: 0; border-radius: 6px; padding: 10px 15px; background: var(--primaire); color: white; text-decoration: none; cursor: pointer; }
        button.danger { background: var(--danger); }
        input { width: 100%; max-width: 380px; padding: 10px; border: 1px solid #c9d1cd; border-radius: 6px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        .champ { margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border-bottom: 1px solid #e4e9e7; text-align: left; }
        th { background: #e9f4ef; }
        .tableau { overflow-x: auto; }
        .message { padding: 12px; border-radius: 6px; margin-bottom: 18px; }
        .succes { background: #dff6e9; color: #12643d; }
        .erreur { background: #fee4e2; color: #912018; }
        .entree { color: #087443; font-weight: bold; }
        .sortie { color: #b42318; font-weight: bold; }
        small { color: #66736d; }
    </style>
</head>
<body>
<header>
    <nav>
        <strong>Mobile Money</strong>
        <a href="<?= site_url('admin/comptes') ?>">Comptes clients</a>
        <a href="<?= site_url('admin/prefixes') ?>">Prefixes</a>
    </nav>
</header>
<main>
    <?php if (session()->getFlashdata('succes')): ?>
        <div class="message succes"><?= esc(session()->getFlashdata('succes')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('erreur')): ?>
        <div class="message erreur"><?= esc(session()->getFlashdata('erreur')) ?></div>
    <?php endif; ?>

    <?= $this->renderSection('contenu') ?>
</main>
</body>
</html>
