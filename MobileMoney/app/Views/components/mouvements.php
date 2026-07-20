<div class="tableau">
    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Operation</th>
            <th>Sens</th>
            <th>Montant</th>
            <th>Frais</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($mouvements as $mouvement): ?>
            <?php $estEntree = $mouvement['sens'] === 'debit'; ?>
            <tr>
                <td><?= esc($mouvement['date']) ?></td>
                <td><?= esc(ucfirst($mouvement['operation'])) ?></td>
                <td class="<?= $estEntree ? 'entree' : 'sortie' ?>">
                    <?= $estEntree ? 'Entree' : 'Sortie' ?>
                </td>
                <td><?= number_format((float) $mouvement['valeur'], 0, ',', ' ') ?> Ar</td>
                <td><?= number_format((float) $mouvement['frais'], 0, ',', ' ') ?> Ar</td>
            </tr>
        <?php endforeach; ?>
        <?php if ($mouvements === []): ?>
            <tr><td colspan="5">Aucun mouvement enregistre.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
