<?= $this->extend('layouts/client') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div>
        <p class="eyebrow">Mouvements du compte</p>
        <h1>Historique des transactions</h1>
        <p>Opérations du compte <?= esc($compte['numero']) ?>, de la plus récente à la plus ancienne.</p>
    </div>
    <a class="btn" href="<?= site_url('client') ?>">← Retour au tableau de bord</a>
</div>

<div class="card">
    <?= $this->include('components/mouvements') ?>
</div>

<?= $this->endSection() ?>
