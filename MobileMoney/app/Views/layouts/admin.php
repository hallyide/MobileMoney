<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titre ?? 'Espace opérateur') ?> — Mobile Money</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body class="ink">
<header class="topbar">
    <div class="topbar-inner">
        <a class="brand" href="<?= site_url('admin/dashboard') ?>">
            <span class="brand-mark">MM</span>
            <span>Mobile Money<small>Espace opérateur</small></span>
        </a>
        <nav class="nav-flow" aria-label="Navigation opérateur">
            <a href="<?= site_url('admin/dashboard') ?>"
               class="<?= ($section ?? '') === 'prefixes' ? 'active' : '' ?>">Tableau de bord</a>
            <a href="<?= site_url('admin/baremes') ?>"
               class="<?= ($section ?? '') === 'baremes' ? 'active' : '' ?>">Barème des frais</a>
            <a href="<?= site_url('admin/gains') ?>"
               class="<?= ($section ?? '') === 'gains' ? 'active' : '' ?>">Gains</a>
            <a href="<?= site_url('admin/operateurs') ?>"
               class="<?= ($section ?? '') === 'operateurs' ? 'active' : '' ?>">Autres opérateurs</a>
            <a href="<?= site_url('admin/versements') ?>"
               class="<?= ($section ?? '') === 'versements' ? 'active' : '' ?>">Montants à envoyer</a>
            <a href="<?= site_url('admin/comptes') ?>"
               class="<?= ($section ?? '') === 'comptes' ? 'active' : '' ?>">Comptes clients</a>
            <span class="nav-sep"></span>
            <a href="<?= site_url('/') ?>" class="btn-logout">Quitter</a>
        </nav>
    </div>
</header>

<main class="app-shell app-shell--wide">
    <?= $this->renderSection('contenu') ?>
</main>

<p class="footer-note">Mobile Money · Espace opérateur</p>

<?php if (session()->getFlashdata('succes')): ?>
    <div class="toast-stack">
        <div class="toast success"><span class="dot"></span><span><?= esc(session()->getFlashdata('succes')) ?></span></div>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('erreur')): ?>
    <div class="toast-stack">
        <div class="toast error"><span class="dot"></span><span><?= esc(session()->getFlashdata('erreur')) ?></span></div>
    </div>
<?php endif; ?>

<script src="<?= base_url('js/script.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
