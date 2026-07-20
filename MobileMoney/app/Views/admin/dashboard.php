<?= $this->extend('layouts/admin') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div>
        <p class="eyebrow">Vue d’ensemble</p>
        <h1>Tableau de bord</h1>
        <p>Suivez les clients, les gains et les préfixes autorisés.</p>
    </div>
    <button class="btn btn-primary" type="button" data-open-modal="modalAddPrefix">+ Ajouter un préfixe</button>
</div>

<div class="grid grid-3" style="margin-bottom:16px;">
    <div class="card kpi">
        <div class="k-label">Gains de l’opérateur</div>
        <div class="k-value gold"><?= number_format($gains, 0, ',', ' ') ?> Ar</div>
        <div class="k-foot">Frais de retrait et de transfert encaissés</div>
    </div>
    <div class="card kpi">
        <div class="k-label">Nombre de clients</div>
        <div class="k-value"><?= $nombreClients ?></div>
        <div class="k-foot">Comptes enregistrés</div>
    </div>
    <div class="card kpi">
        <div class="k-label">Préfixes disponibles</div>
        <div class="k-value gold"><?= count($prefixes) ?></div>
        <div class="k-foot">Préfixes actuellement autorisés</div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <div>
            <h2>Préfixes disponibles</h2>
            <div class="hint">Format attendu : trois chiffres, par exemple 033</div>
        </div>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Préfixe</th><th style="text-align:right;">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($prefixes as $prefixe): ?>
                <tr>
                    <td class="td-mono"><?= esc($prefixe['prefixe']) ?></td>
                    <td>
                        <div class="row-actions">
                            <button class="btn btn-sm btn-ghost" type="button"
                                    data-open-modal="modalEditPrefix<?= $prefixe['id'] ?>">Modifier</button>
                            <form method="post" action="<?= site_url('admin/prefixes/' . $prefixe['id'] . '/supprimer') ?>"
                                  onsubmit="return confirm('Supprimer ce préfixe ?')">
                                <button class="btn btn-sm btn-danger" type="submit">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($prefixes === []): ?>
                <tr><td colspan="2"><div class="empty-state">Aucun préfixe configuré.</div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalAddPrefix">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="titre-ajout-prefixe">
        <div class="modal-head">
            <h3 id="titre-ajout-prefixe">Ajouter un préfixe</h3>
            <button class="modal-close" type="button" data-close-modal aria-label="Fermer">×</button>
        </div>
        <form method="post" action="<?= site_url('admin/prefixes') ?>">
            <div class="modal-body">
                <div class="field">
                    <label for="addPrefixValue">Préfixe</label>
                    <input class="input input-mono" id="addPrefixValue" name="prefixe"
                           value="<?= esc(old('prefixe')) ?>" placeholder="ex : 033"
                           maxlength="3" pattern="[0-9]{3}" required>
                    <span class="hint-sm">Le préfixe doit contenir exactement trois chiffres.</span>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" data-close-modal>Annuler</button>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<?php foreach ($prefixes as $prefixe): ?>
    <div class="modal-overlay" id="modalEditPrefix<?= $prefixe['id'] ?>">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="titre-modif-prefixe-<?= $prefixe['id'] ?>">
            <div class="modal-head">
                <h3 id="titre-modif-prefixe-<?= $prefixe['id'] ?>">Modifier le préfixe</h3>
                <button class="modal-close" type="button" data-close-modal aria-label="Fermer">×</button>
            </div>
            <form method="post" action="<?= site_url('admin/prefixes/' . $prefixe['id']) ?>">
                <div class="modal-body">
                    <div class="field">
                        <label for="prefixe-<?= $prefixe['id'] ?>">Préfixe</label>
                        <input class="input input-mono" id="prefixe-<?= $prefixe['id'] ?>" name="prefixe"
                               value="<?= esc($prefixe['prefixe']) ?>" maxlength="3" pattern="[0-9]{3}" required>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn" data-close-modal>Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<?= $this->endSection() ?>
