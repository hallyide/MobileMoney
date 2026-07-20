<?= $this->extend('layouts/simple') ?>
<?= $this->section('contenu') ?>

<p><a href="<?= site_url('client/' . $compte['id']) ?>">&larr; Retour au compte</a></p>
<h1>Faire un depot</h1>

<section class="carte">
    <p>Compte : <strong><?= esc($compte['numero']) ?></strong></p>
    <form method="post" action="<?= site_url('client/' . $compte['id'] . '/depot') ?>">
        <div class="champ">
            <label for="montant">Montant du depot (Ar)</label>
            <input type="number" id="montant" name="montant" min="1" step="1"
                   value="<?= esc(old('montant')) ?>" required>
        </div>
        <button type="submit">Valider le depot</button>
    </form>
</section>

<?= $this->endSection() ?>
