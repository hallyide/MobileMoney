<?= $this->extend('layouts/admin') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div>
        <p class="eyebrow">Transferts externes</p>
        <h1>Montants à envoyer</h1>
        <p>Ces montants sont à remettre aux autres opérateurs. Aucun solde externe n’est géré ici.</p>
    </div>
</div>

<div class="card card-pad" style="margin-bottom:16px;">
    <form method="get" action="<?= site_url('admin/versements') ?>" class="field-row">
        <div class="field"><label>Opérateur</label><select class="input" name="operateur"><option value="0">Tous</option>
            <?php foreach ($operateurs as $operateur): ?><option value="<?= $operateur['id'] ?>" <?= (int) $filtres['operateur'] === (int) $operateur['id'] ? 'selected' : '' ?>><?= esc($operateur['nom']) ?></option><?php endforeach; ?>
        </select></div>
        <div class="field"><label>Date de début</label><input class="input" type="date" name="date_debut" value="<?= esc($filtres['date_debut']) ?>"></div>
        <div class="field"><label>Date de fin</label><input class="input" type="date" name="date_fin" value="<?= esc($filtres['date_fin']) ?>"></div>
        <div class="field"><button class="btn btn-primary" type="submit" style="margin-top:20px;">Filtrer</button></div>
    </form>
</div>

<div class="grid grid-3" style="margin-bottom:16px;">
    <?php foreach ($totaux as $total): ?>
        <div class="card kpi">
            <div class="k-label"><?= esc($total['operateur']) ?></div>
            <div class="k-value gold"><?= number_format($total['montantAReverser'], 2, ',', ' ') ?> Ar</div>
            <div class="k-foot"><?= $total['nombre'] ?> transfert(s) · commission <?= number_format($total['commission'], 2, ',', ' ') ?> Ar</div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-head"><h2>Détail par destinataire</h2><div class="hint"><?= count($transferts) ?> ligne(s)</div></div>
    <div class="table-wrap"><table class="tbl">
        <thead><tr><th>Opérateur</th><th>Envoyeur</th><th>Destinataire</th><th>Montant</th><th>Commission</th><th>Net à envoyer</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($transferts as $transfert): ?><tr>
            <td><?= esc($transfert['operateur']) ?></td><td class="td-mono"><?= esc($transfert['envoyeur']) ?></td><td class="td-mono"><?= esc($transfert['numeroDestinataire']) ?></td>
            <td class="td-mono"><?= number_format((float) $transfert['montantEnvoye'], 2, ',', ' ') ?> Ar</td><td class="td-mono"><?= number_format((float) $transfert['commission'], 2, ',', ' ') ?> Ar</td>
            <td class="td-mono"><?= number_format((float) $transfert['montantAReverser'], 2, ',', ' ') ?> Ar</td><td class="td-muted"><?= esc($transfert['date']) ?></td>
        </tr><?php endforeach; ?>
        <?php if ($transferts === []): ?><tr><td colspan="7"><div class="empty-state">Aucun transfert externe pour ces filtres.</div></td></tr><?php endif; ?>
        </tbody>
    </table></div>
</div>

<?= $this->endSection() ?>
