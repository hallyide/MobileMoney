<?= $this->extend('layouts/client') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div>
        <p class="eyebrow">Alimenter mon compte</p>
        <h1>Effectuer un dépôt</h1>
        <p>Solde actuel : <strong class="td-mono"><?= number_format((float) $compte['soldeActuel'], 0, ',', ' ') ?> Ar</strong></p>
    </div>
    <a class="btn" href="<?= site_url('client/' . $compte['id']) ?>">← Retour</a>
</div>

<div class="card card-pad form-page">
    <form method="post" action="<?= site_url('client/' . $compte['id'] . '/depot') ?>">
        <div class="field">
            <label for="montantDepot">Montant à déposer</label>
            <input class="input input-mono" type="number" min="1" step="1"
                   id="montantDepot" name="montant" value="<?= esc(old('montant')) ?>" placeholder="0" required>
        </div>
        <div class="summary-box">
            <div class="s-row"><span>Frais estimés</span><strong id="previewFrais">0 Ar</strong></div>
            <div class="s-row"><span>Total crédité</span><strong id="previewTotal">0 Ar</strong></div>
        </div>
        <button type="submit" class="btn btn-primary btn-block" style="margin-top:18px;">Valider le dépôt</button>
    </form>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
(function () {
    const baremes = <?= json_encode($baremes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const input = document.getElementById('montantDepot');
    const formatAr = montant => new Intl.NumberFormat('fr-FR').format(montant) + ' Ar';
    const fraisPour = montant => {
        const ligne = baremes.find(b => montant >= Number(b.montant_min) && montant <= Number(b.montant_max));
        return ligne ? Number(ligne.prix) : 0;
    };
    const afficherEstimation = () => {
        const montant = Number(input.value) || 0;
        const frais = fraisPour(montant);
        document.getElementById('previewFrais').textContent = formatAr(frais);
        document.getElementById('previewTotal').textContent = formatAr(Math.max(0, montant - frais));
    };
    input.addEventListener('input', afficherEstimation);
    afficherEstimation();
})();
</script>
<?= $this->endSection() ?>
