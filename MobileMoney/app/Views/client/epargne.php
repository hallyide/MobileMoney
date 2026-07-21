<?= $this->extend('layouts/client') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div>
        <p class="eyebrow">Epargne</p>
        <h1>Configurer votre epargne</h1>
        <p>Epargne actuel : <strong class="td-mono"><?= number_format((float) $compte['soldeActuel'], 0, ',', ' ') ?> Ar</strong></p>
    </div>
    <a class="btn" href="<?= site_url('client') ?>">← Retour</a>
</div>

<div class="card card-pad form-page">
    <form method="post" action="<?= site_url('client/epargne') ?>">
        <div class="field">
            <label for="valeurEpargne">Epargne en pourcent</label>
            <input class="input input-mono" type="number" min="0" max="100" step="1"
                   id="montantRetrait" name="epargne" value="<?= esc(old('montant')) ?>" placeholder="0" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block" style="margin-top:18px;">Valider</button>
    </form>
</div>

<?= $this->endSection() ?>

