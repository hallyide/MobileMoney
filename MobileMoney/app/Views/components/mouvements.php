<div class="table-wrap">
    <table class="tbl">
        <thead><tr><th>Réf.</th><th>Opération</th><th>Destinataire</th><th>Montant</th><th>Frais inclus</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($mouvements as $mouvement): ?>
            <?php $estEntree = $mouvement['sens'] === 'debit'; ?>
            <tr>
                <td class="td-mono td-muted">#<?= esc($mouvement['id']) ?></td>
                <td><span class="badge <?= esc($mouvement['operation']) ?>"><?= esc(ucfirst($mouvement['operation'])) ?></span></td>
                <td class="td-mono"><?= $mouvement['numeroDestinataire'] === null ? '—' : esc($mouvement['numeroDestinataire']) ?></td>
                <td><div class="ledger <?= $estEntree ? 'in' : 'out' ?>"><span class="amt"><?= number_format((float) $mouvement['valeur'], 0, ',', ' ') ?> Ar</span></div></td>
                <td class="td-mono"><?= number_format((float) $mouvement['frais'], 0, ',', ' ') ?> Ar</td>
                <td class="td-muted"><?= esc($mouvement['date']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($mouvements === []): ?>
            <tr><td colspan="6"><div class="empty-state">Aucun mouvement enregistré.</div></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
