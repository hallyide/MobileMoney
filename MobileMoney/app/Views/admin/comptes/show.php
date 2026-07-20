<?= $this->extend('layouts/simple') ?>
<?= $this->section('contenu') ?>

<p><a href="<?= site_url('admin/comptes') ?>">&larr; Retour aux comptes</a></p>
<h1>Compte <?= esc($compte['numero']) ?></h1>

<section class="carte">
    <p><strong>Numero :</strong> <?= esc($compte['numero']) ?></p>
    <p><strong>Date de creation :</strong> <?= esc($compte['creation']) ?></p>
    <p>Solde actuel</p>
    <div class="solde"><?= number_format((float) $compte['soldeActuel'], 0, ',', ' ') ?> Ar</div>
</section>

<section class="carte">
    <h2>Mouvements du compte</h2>
    <?= $this->include('components/mouvements') ?>
</section>

<?= $this->endSection() ?>
