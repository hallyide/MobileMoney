<?= $this->extend('layouts/client') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div><p class="eyebrow">Envoyer des fonds</p><h1>Effectuer un transfert</h1><p>Solde actuel : <strong class="td-mono"><?= number_format((float) $compte['soldeActuel'], 2, ',', ' ') ?> Ar</strong></p></div>
    <a class="btn" href="<?= site_url('client') ?>">← Retour</a>
</div>

<div class="card card-pad form-page">
    <form method="post" action="<?= site_url('client/transfert/apercu') ?>">
        <div class="field">
            <label>Numéros destinataires</label>
            <div id="listeDestinataires">
                <?php $anciensNumeros = session()->getOldInput('numeros') ?: ['']; ?>
                <?php foreach ($anciensNumeros as $numero): ?>
                    <div class="field-row destinataire" style="margin-bottom:8px;">
                        <input class="input input-mono" name="numeros[]" value="<?= esc($numero) ?>" maxlength="10" pattern="[0-9]{10}" placeholder="ex : 0311234567" required>
                        <button class="btn btn-danger retirer-destinataire" type="button">Retirer</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="btn btn-sm" id="ajouterDestinataire" type="button">+ Ajouter un numéro</button>
            <span class="hint-sm">Tous les numéros doivent appartenir au même opérateur.</span>
        </div>

        <div class="field">
            <label for="montantTransfert">Montant total à partager</label>
            <input class="input input-mono" type="number" min="100" step="0.01" id="montantTransfert" name="montant" value="<?= esc(old('montant')) ?>" required>
        </div>

        <div class="field">
            <label style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="inclure_frais_retrait" value="1" <?= old('inclure_frais_retrait') ? 'checked' : '' ?> style="width:auto;">
                Inclure les frais de retrait pour les destinataires
            </label>
            <span class="hint-sm">Cette option est ignorée pour les autres opérateurs.</span>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Afficher le récapitulatif</button>
    </form>
</div>

<template id="modeleDestinataire">
    <div class="field-row destinataire" style="margin-bottom:8px;">
        <input class="input input-mono" name="numeros[]" maxlength="10" pattern="[0-9]{10}" placeholder="ex : 0311234567" required>
        <button class="btn btn-danger retirer-destinataire" type="button">Retirer</button>
    </div>
</template>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
(function () {
    const liste = document.getElementById('listeDestinataires');
    const modele = document.getElementById('modeleDestinataire');

    document.getElementById('ajouterDestinataire').addEventListener('click', function () {
        if (liste.children.length < 10) liste.appendChild(modele.content.cloneNode(true));
    });

    liste.addEventListener('click', function (event) {
        if (event.target.classList.contains('retirer-destinataire') && liste.children.length > 1) {
            event.target.closest('.destinataire').remove();
        }
    });
})();
</script>
<?= $this->endSection() ?>
