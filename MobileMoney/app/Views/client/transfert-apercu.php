<?= $this->extend('layouts/client') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div><p class="eyebrow">Dernière vérification</p><h1>Récapitulatif du transfert</h1><p>Type : <strong><?= $resume['type'] === 'interne' ? 'Notre opérateur' : 'Autre opérateur' ?></strong></p></div>
    <a class="btn" href="<?= site_url('client/transfert') ?>">← Modifier</a>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="table-wrap"><table class="tbl">
        <thead><tr><th>Destinataire</th><th>Opérateur</th><th>Part</th><th>Frais transfert</th><th>Frais retrait</th><th>Commission</th><th><?= $resume['type'] === 'interne' ? 'Montant reçu' : 'Net à reverser' ?></th></tr></thead>
        <tbody>
        <?php foreach ($resume['destinataires'] as $destination): ?><tr>
            <td class="td-mono"><?= esc($destination['numero']) ?></td><td><?= esc($destination['operateur']) ?></td>
            <td class="td-mono"><?= number_format($destination['part'], 2, ',', ' ') ?> Ar</td><td class="td-mono"><?= number_format($destination['fraisTransfert'], 2, ',', ' ') ?> Ar</td>
            <td class="td-mono"><?= number_format($destination['fraisRetrait'], 2, ',', ' ') ?> Ar</td><td class="td-mono"><?= number_format($destination['commission'], 2, ',', ' ') ?> Ar</td>
            <td class="td-mono"><?= number_format($resume['type'] === 'interne' ? $destination['montantRecu'] : $destination['montantAReverser'], 2, ',', ' ') ?> Ar</td>
        </tr><?php endforeach; ?>
        </tbody>
    </table></div>
</div>

<div class="card card-pad form-page">
    <div class="summary-box">
        <div class="s-row"><span>Montant partagé</span><strong><?= number_format($resume['montantTotal'], 2, ',', ' ') ?> Ar</strong></div>
        <div class="s-row"><span>Frais de transfert</span><strong><?= number_format($resume['totalFraisTransfert'], 2, ',', ' ') ?> Ar</strong></div>
        <div class="s-row"><span>Frais de retrait inclus</span><strong><?= number_format($resume['totalFraisRetrait'], 2, ',', ' ') ?> Ar</strong></div>
        <div class="s-row"><span>Commissions retenues</span><strong><?= number_format($resume['totalCommissions'], 2, ',', ' ') ?> Ar</strong></div>
        <div class="s-row"><span>Total débité</span><strong><?= number_format($resume['totalDebite'], 2, ',', ' ') ?> Ar</strong></div>
    </div>

    <form method="post" action="<?= site_url('client/transfert') ?>" style="margin-top:18px;">
        <?php foreach ($numeros as $numero): ?><input type="hidden" name="numeros[]" value="<?= esc($numero) ?>"><?php endforeach; ?>
        <input type="hidden" name="montant" value="<?= esc($resume['montantTotal']) ?>">
        <input type="hidden" name="inclure_frais_retrait" value="<?= $resume['inclureFraisRetrait'] ? '1' : '0' ?>">
        <button class="btn btn-primary btn-block" type="submit">Confirmer le transfert</button>
    </form>
</div>

<?= $this->endSection() ?>
