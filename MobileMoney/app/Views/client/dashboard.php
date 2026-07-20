<?= $this->extend('layouts/client') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div>
        <p class="eyebrow">Bonjour</p>
        <h1>Compte <?= esc($compte['numero']) ?></h1>
        <p>Client depuis le <?= esc($compte['creation']) ?></p>
    </div>
</div>

<div class="balance-hero" style="margin-bottom:16px;">
    <div class="b-label"><span class="pulse-dot"></span>Solde disponible</div>
    <div class="b-value"><?= number_format((float) $compte['soldeActuel'], 0, ',', ' ') ?> <sup>Ar</sup></div>
    <div class="b-meta"><span>Nombre de transactions : <strong><?= $nombreMouvements ?></strong></span></div>
</div>

<div class="quick-actions" style="margin-bottom:16px;">
    <a class="qa" href="<?= site_url('client/depot') ?>">
        <div class="qa-ico">↓</div>
        <div><div class="qa-title">Dépôt</div><div class="qa-sub">Alimenter mon compte</div></div>
    </a>
    <a class="qa" href="<?= site_url('client/retrait') ?>">
        <div class="qa-ico">↑</div>
        <div><div class="qa-title">Retrait</div><div class="qa-sub">Retirer des fonds</div></div>
    </a>
    <a class="qa" href="<?= site_url('client/transfert') ?>">
        <div class="qa-ico">⇄</div>
        <div><div class="qa-title">Transfert</div><div class="qa-sub">Envoyer à un autre compte</div></div>
    </a>
</div>

<div class="card">
    <div class="card-head"><h2>Les dix derniers mouvements</h2><div class="hint">Solde mis à jour après chaque opération</div></div>
    <?= $this->include('components/mouvements') ?>
</div>

<div style="text-align:center;margin-top:18px;">
    <a class="btn" href="<?= site_url('client/historique') ?>">Afficher tout l’historique</a>
</div>

<?= $this->endSection() ?>
