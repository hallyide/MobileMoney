<?= $this->extend('layouts/admin') ?>
<?= $this->section('contenu') ?>

<div class="page-head"><div><p class="eyebrow">Caisse opérateur</p><h1>Situation des gains</h1><p>Les frais habituels et les commissions externes sont présentés séparément.</p></div></div>

<div class="grid grid-3" style="margin-bottom:16px;">
    <div class="card kpi"><div class="k-label">Total en caisse</div><div class="k-value gold"><?= number_format($totalGains, 2, ',', ' ') ?> Ar</div></div>
    <div class="card kpi"><div class="k-label">Frais de l’opérateur</div><div class="k-value"><?= number_format($totalFrais, 2, ',', ' ') ?> Ar</div><div class="k-foot">Dépôts, retraits et transferts</div></div>
    <div class="card kpi"><div class="k-label">Commissions externes</div><div class="k-value mint"><?= number_format($totalCommissions, 2, ',', ' ') ?> Ar</div><div class="k-foot">Retenues sur les montants externes</div></div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-head"><h2>Frais de l’opérateur</h2><div class="hint"><?= count($frais) ?> ligne(s)</div></div>
    <div class="table-wrap"><table class="tbl">
        <thead><tr><th>Réf.</th><th>Client</th><th>Opération</th><th>Origine</th><th>Frais</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($frais as $gain): ?><tr>
            <td class="td-mono td-muted">#<?= esc($gain['id']) ?></td><td class="td-mono"><?= esc($gain['numero']) ?></td>
            <td><span class="badge <?= esc($gain['operation']) ?>"><?= esc(ucfirst($gain['operation'])) ?></span></td>
            <td><?= $gain['transfertExterneId'] ? 'Transfert externe' : 'Opération interne' ?></td>
            <td class="td-mono"><?= number_format((float) $gain['valeur'], 2, ',', ' ') ?> Ar</td><td class="td-muted"><?= esc($gain['date']) ?></td>
        </tr><?php endforeach; ?>
        <?php if ($frais === []): ?><tr><td colspan="6"><div class="empty-state">Aucun frais encaissé.</div></td></tr><?php endif; ?>
        </tbody>
    </table></div>
</div>

<div class="card">
    <div class="card-head"><h2>Commissions sur les autres opérateurs</h2><div class="hint"><?= count($commissions) ?> ligne(s)</div></div>
    <div class="table-wrap"><table class="tbl">
        <thead><tr><th>Réf.</th><th>Opérateur</th><th>Envoyeur</th><th>Destinataire</th><th>Commission</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($commissions as $commission): ?><tr>
            <td class="td-mono td-muted">#<?= esc($commission['id']) ?></td><td><?= esc($commission['operateur']) ?></td>
            <td class="td-mono"><?= esc($commission['envoyeur']) ?></td><td class="td-mono"><?= esc($commission['numeroDestinataire']) ?></td>
            <td class="td-mono"><?= number_format((float) $commission['commission'], 2, ',', ' ') ?> Ar</td><td class="td-muted"><?= esc($commission['date']) ?></td>
        </tr><?php endforeach; ?>
        <?php if ($commissions === []): ?><tr><td colspan="6"><div class="empty-state">Aucune commission externe.</div></td></tr><?php endif; ?>
        </tbody>
    </table></div>
</div>

<?= $this->endSection() ?>
