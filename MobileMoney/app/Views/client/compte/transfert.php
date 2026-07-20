<?= $this->extend('layouts/simple') ?>
<?= $this->section('contenu') ?>

<p><a href="<?= site_url('client/' . $compte['id']) ?>">&larr; Retour au compte</a></p>
<h1>Faire un transfert</h1>

<section class="carte">
    <p>Solde disponible : <strong><?= number_format((float) $compte['soldeActuel'], 0, ',', ' ') ?> Ar</strong></p>
    <form method="post" action="<?= site_url('client/' . $compte['id'] . '/transfert') ?>">
        <div class="champ">
            <label for="numero">Numero du destinataire</label>
            <input id="numero" name="numero" maxlength="10" pattern="[0-9]{10}"
                   value="<?= esc(old('numero')) ?>" placeholder="0332345678" required>
        </div>
        <div class="champ">
            <label for="montant">Montant a transferer (Ar)</label>
            <input type="number" id="montant" name="montant" min="100" max="2000000" step="1"
                   value="<?= esc(old('montant')) ?>" required>
            <small>Les frais sont calcules automatiquement selon le bareme.</small>
        </div>
        <button type="submit">Valider le transfert</button>
    </form>
</section>

<?= $this->endSection() ?>
