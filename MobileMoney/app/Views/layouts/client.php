<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titre ?? 'Mon compte') ?> — Mobile Money</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body class="theme-client">
<header class="topbar">
    <div class="topbar-inner">
        <a class="brand" href="<?= site_url('client/' . $compte['id']) ?>">
            <span class="brand-mark">MM</span>
            <span>Mobile Money<small>Espace client</small></span>
        </a>
        <nav class="nav-flow" aria-label="Navigation client">
            <a href="<?= site_url('client/' . $compte['id']) ?>"
               class="<?= ($section ?? '') === 'compte' ? 'active' : '' ?>">Mon compte</a>
            <a href="<?= site_url('client/' . $compte['id'] . '/depot') ?>"
               class="<?= ($section ?? '') === 'depot' ? 'active' : '' ?>">Dépôt</a>
            <a href="<?= site_url('client/' . $compte['id'] . '/transfert') ?>"
               class="<?= ($section ?? '') === 'transfert' ? 'active' : '' ?>">Transfert</a>
            <span class="nav-sep"></span>
            <a href="<?= site_url('admin/comptes') ?>" class="btn-logout">Changer de compte</a>
        </nav>
    </div>
</header>

<main class="app-shell">
    <?= $this->renderSection('contenu') ?>
</main>

<p class="footer-note">Mobile Money · Espace client</p>

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
