<?= $this->extend('layouts/simple') ?>
<?= $this->section('contenu') ?>

<h1>Bienvenue, <?= esc($compte['numero']) ?></h1>

<section class="carte">
    <p>Votre solde actuel</p>
    <div class="solde"><?= number_format((float) $compte['soldeActuel'], 0, ',', ' ') ?> Ar</div>
    <p><small>Mis a jour apres chaque operation.</small></p>
    <div class="actions">
        <a class="bouton" href="<?= site_url('client/' . $compte['id'] . '/depot') ?>">Faire un depot</a>
        <a class="bouton" href="<?= site_url('client/' . $compte['id'] . '/transfert') ?>">Faire un transfert</a>
    </div>
</section>

<section class="carte">
    <h2>Les 10 derniers mouvements</h2>
    <?= $this->include('components/mouvements') ?>
</section>

<?= $this->endSection() ?>
