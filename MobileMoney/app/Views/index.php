<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titre) ?></title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body class="theme-auth">
<div class="auth-card" style="max-width:420px;">
    <div class="auth-mark">MM</div>
    <h1>Mobile Money</h1>
    <p class="sub">Simulation d’un opérateur de mobile money</p>
    <div style="display:flex;flex-direction:column;gap:12px;">
        <a class="btn btn-primary btn-block" href="<?= site_url('admin/dashboard') ?>">Espace opérateur</a>
        <a class="btn btn-block" href="<?= site_url('client/login') ?>">Espace client</a>
    </div>
</div>
</body>
</html>
