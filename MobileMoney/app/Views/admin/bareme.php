<?= $this->extend('layouts/admin') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div>
        <p class="eyebrow">Tarification</p>
        <h1>Barème des frais</h1>
        <p>Frais appliqués selon l’opération et la tranche de montant.</p>
    </div>
    <button class="btn btn-primary" type="button" data-open-modal="modalAddBareme">+ Ajouter une tranche</button>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Opération</th><th>Montant min</th><th>Montant max</th><th>Frais</th><th style="text-align:right;">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($baremes as $bareme): ?>
                <tr>
                    <td><span class="badge <?= esc($bareme['operation']) ?>"><?= esc(ucfirst($bareme['operation'])) ?></span></td>
                    <td class="td-mono"><?= number_format((float) $bareme['montant_min'], 0, ',', ' ') ?> Ar</td>
                    <td class="td-mono"><?= number_format((float) $bareme['montant_max'], 0, ',', ' ') ?> Ar</td>
                    <td class="td-mono"><?= number_format((float) $bareme['prix'], 0, ',', ' ') ?> Ar</td>
                    <td><div class="row-actions">
                        <button class="btn btn-sm btn-ghost" type="button" data-open-modal="modalBareme<?= $bareme['id'] ?>">Modifier</button>
                        <form method="post" action="<?= site_url('admin/baremes/' . $bareme['id'] . '/supprimer') ?>" onsubmit="return confirm('Supprimer cette tranche ?')">
                            <button class="btn btn-sm btn-danger" type="submit">Supprimer</button>
                        </form>
                    </div></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($baremes === []): ?>
                <tr><td colspan="5"><div class="empty-state">Aucun barème défini.</div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalAddBareme">
    <div class="modal" role="dialog" aria-modal="true">
        <div class="modal-head"><h3>Ajouter une tranche</h3><button class="modal-close" type="button" data-close-modal>×</button></div>
        <form method="post" action="<?= site_url('admin/baremes') ?>">
            <div class="modal-body">
                <?= $this->include('components/form_bareme') ?>
            </div>
            <div class="modal-foot"><button class="btn" type="button" data-close-modal>Annuler</button><button class="btn btn-primary" type="submit">Ajouter</button></div>
        </form>
    </div>
</div>

<?php foreach ($baremes as $bareme): ?>
    <div class="modal-overlay" id="modalBareme<?= $bareme['id'] ?>">
        <div class="modal" role="dialog" aria-modal="true">
            <div class="modal-head"><h3>Modifier la tranche</h3><button class="modal-close" type="button" data-close-modal>×</button></div>
            <form method="post" action="<?= site_url('admin/baremes/' . $bareme['id']) ?>">
                <div class="modal-body">
                    <div class="field"><label>Type d’opération</label><select class="input" name="idtypeOp" required>
                        <?php foreach ($types as $type): ?><option value="<?= $type['id'] ?>" <?= (int) $type['id'] === (int) $bareme['idtypeOp'] ? 'selected' : '' ?>><?= esc(ucfirst($type['type'])) ?></option><?php endforeach; ?>
                    </select></div>
                    <div class="field-row">
                        <div class="field"><label>Montant minimum</label><input class="input input-mono" type="number" min="0" name="montant_min" value="<?= esc($bareme['montant_min']) ?>" required></div>
                        <div class="field"><label>Montant maximum</label><input class="input input-mono" type="number" min="0" name="montant_max" value="<?= esc($bareme['montant_max']) ?>" required></div>
                    </div>
                    <div class="field"><label>Frais</label><input class="input input-mono" type="number" min="0" name="prix" value="<?= esc($bareme['prix']) ?>" required></div>
                </div>
                <div class="modal-foot"><button class="btn" type="button" data-close-modal>Annuler</button><button class="btn btn-primary" type="submit">Enregistrer</button></div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<?= $this->endSection() ?>
