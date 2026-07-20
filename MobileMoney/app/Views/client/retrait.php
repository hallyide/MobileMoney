<?= $this->extend('layouts/client') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div>
        <p class="eyebrow">Retirer des fonds</p>
        <h1>Effectuer un retrait</h1>
        <p>Solde actuel : <strong class="td-mono"><?= number_format((float) $compte['soldeActuel'], 0, ',', ' ') ?> Ar</strong></p>
    </div>
    <a class="btn" href="<?= site_url('client') ?>">← Retour</a>
</div>

<div class="card card-pad form-page">
    <form method="post" action="<?= site_url('client/retrait') ?>">
        <div class="field">
            <label for="montantRetrait">Montant à retirer</label>
            <input class="input input-mono" type="number" min="100" max="2000000" step="1"
                   id="montantRetrait" name="montant" value="<?= esc(old('montant')) ?>" placeholder="0" required>
        </div>
        <div class="summary-box">
            <div class="s-row"><span>Frais estimés</span><strong id="previewFrais">0 Ar</strong></div>
            <div class="s-row"><span>Total débité</span><strong id="previewTotal">0 Ar</strong></div>
        </div>
        <button type="submit" class="btn btn-primary btn-block" style="margin-top:18px;">Valider le retrait</button>
    </form>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
(function () {
    const baremes = <?= json_encode($baremes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const input = document.getElementById('montantRetrait');
    const formatAr = montant => new Intl.NumberFormat('fr-FR').format(montant) + ' Ar';
    const afficher = () => {
        const montant = Number(input.value) || 0;
        const ligne = baremes.find(b => montant >= Number(b.montant_min) && montant <= Number(b.montant_max));
        const frais = ligne ? Number(ligne.prix) : 0;
        document.getElementById('previewFrais').textContent = formatAr(frais);
        document.getElementById('previewTotal').textContent = formatAr(montant + frais);
    };
    input.addEventListener('input', afficher);
    afficher();
})();
</script>
<?= $this->endSection() ?>
