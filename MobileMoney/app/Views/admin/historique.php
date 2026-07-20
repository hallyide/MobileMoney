<?= $this->extend('layouts/admin') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div>
        <p class="eyebrow">Caisse opérateur</p>
        <h1>Historique des gains</h1>
        <p>Frais réellement perçus sur les retraits et les transferts.</p>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom:16px;">
    <div class="card kpi">
        <div class="k-label">Total des gains</div>
        <div class="k-value gold"><?= number_format($totalGains, 0, ',', ' ') ?> Ar</div>
        <div class="k-foot">Valeur actuelle de caisseOp</div>
    </div>
</div>

<div class="card">
    <div class="card-head"><h2>Frais encaissés</h2><div class="hint"><?= count($gains) ?> ligne(s)</div></div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Réf.</th><th>Client</th><th>Opération</th><th>Frais</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($gains as $gain): ?>
                <tr>
                    <td class="td-mono td-muted">#<?= esc($gain['id']) ?></td>
                    <td class="td-mono"><?= esc($gain['numero']) ?></td>
                    <td><span class="badge <?= esc($gain['operation']) ?>"><?= esc(ucfirst($gain['operation'])) ?></span></td>
                    <td class="td-mono"><?= number_format((float) $gain['valeur'], 0, ',', ' ') ?> Ar</td>
                    <td class="td-muted"><?= esc($gain['date']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($gains === []): ?>
                <tr><td colspan="5"><div class="empty-state">Aucun gain enregistré.</div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
