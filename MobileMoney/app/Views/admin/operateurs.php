<?= $this->extend('layouts/admin') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div>
        <p class="eyebrow">Réseaux partenaires</p>
        <h1>Autres opérateurs</h1>
        <p>Configurez leurs préfixes et la commission retenue sur les envois.</p>
    </div>
    <button class="btn btn-primary" type="button" data-open-modal="modalNouvelOperateur">+ Ajouter un opérateur</button>
</div>

<?php foreach ($operateurs as $operateur): ?>
    <div class="card card-pad" style="margin-bottom:16px;">
        <div class="card-head">
            <div>
                <h2><?= esc($operateur['nom']) ?></h2>
                <div class="hint"><?= count($operateur['prefixes']) ?> préfixe(s)</div>
            </div>
            <div class="row-actions">
                <button class="btn btn-sm btn-ghost" type="button" data-open-modal="modalOperateur<?= $operateur['id'] ?>">Renommer</button>
                <form method="post" action="<?= site_url('admin/operateurs/' . $operateur['id'] . '/supprimer') ?>" onsubmit="return confirm('Supprimer cet opérateur ?')">
                    <button class="btn btn-sm btn-danger" type="submit">Supprimer</button>
                </form>
            </div>
        </div>

        <div class="grid grid-2">
            <div>
                <h3 style="font-size:14px;">Préfixes</h3>
                <div class="table-wrap">
                    <table class="tbl">
                        <tbody>
                        <?php foreach ($operateur['prefixes'] as $prefixe): ?>
                            <tr>
                                <td class="td-mono"><?= esc($prefixe['prefixe']) ?></td>
                                <td><div class="row-actions">
                                    <button class="btn btn-sm btn-ghost" type="button" data-open-modal="modalPrefixe<?= $prefixe['id'] ?>">Modifier</button>
                                    <form method="post" action="<?= site_url('admin/prefixes-externes/' . $prefixe['id'] . '/supprimer') ?>">
                                        <button class="btn btn-sm btn-danger" type="submit">Supprimer</button>
                                    </form>
                                </div></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($operateur['prefixes'] === []): ?>
                            <tr><td><div class="empty-state">Aucun préfixe.</div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <form class="field-row" method="post" action="<?= site_url('admin/operateurs/' . $operateur['id'] . '/prefixes') ?>" style="margin-top:12px;">
                    <input class="input input-mono" name="prefixe" maxlength="3" pattern="[0-9]{3}" placeholder="031" required>
                    <button class="btn" type="submit">Ajouter le préfixe</button>
                </form>
            </div>

            <div>
                <h3 style="font-size:14px;">Commission</h3>
                <form method="post" action="<?= site_url('admin/operateurs/' . $operateur['id'] . '/commission') ?>">
                    <div class="field">
                        <label for="commission<?= $operateur['id'] ?>">Pourcentage retenu sur le montant envoyé</label>
                        <input class="input input-mono" id="commission<?= $operateur['id'] ?>" name="pourcentage"
                               type="number" min="0" max="100" step="0.01"
                               value="<?= esc($operateur['pourcentage']) ?>" required>
                    </div>
                    <button class="btn btn-primary" type="submit">Enregistrer la commission</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modalOperateur<?= $operateur['id'] ?>">
        <div class="modal"><div class="modal-head"><h3>Renommer l’opérateur</h3><button class="modal-close" type="button" data-close-modal>×</button></div>
            <form method="post" action="<?= site_url('admin/operateurs/' . $operateur['id']) ?>">
                <div class="modal-body"><div class="field"><label>Nom</label><input class="input" name="nom" value="<?= esc($operateur['nom']) ?>" required></div></div>
                <div class="modal-foot"><button class="btn" type="button" data-close-modal>Annuler</button><button class="btn btn-primary" type="submit">Enregistrer</button></div>
            </form>
        </div>
    </div>

    <?php foreach ($operateur['prefixes'] as $prefixe): ?>
        <div class="modal-overlay" id="modalPrefixe<?= $prefixe['id'] ?>">
            <div class="modal"><div class="modal-head"><h3>Modifier le préfixe</h3><button class="modal-close" type="button" data-close-modal>×</button></div>
                <form method="post" action="<?= site_url('admin/prefixes-externes/' . $prefixe['id']) ?>">
                    <div class="modal-body"><div class="field"><label>Préfixe</label><input class="input input-mono" name="prefixe" maxlength="3" pattern="[0-9]{3}" value="<?= esc($prefixe['prefixe']) ?>" required></div></div>
                    <div class="modal-foot"><button class="btn" type="button" data-close-modal>Annuler</button><button class="btn btn-primary" type="submit">Enregistrer</button></div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>

<?php if ($operateurs === []): ?>
    <div class="card"><div class="empty-state">Aucun autre opérateur configuré.</div></div>
<?php endif; ?>

<div class="modal-overlay" id="modalNouvelOperateur">
    <div class="modal"><div class="modal-head"><h3>Ajouter un opérateur</h3><button class="modal-close" type="button" data-close-modal>×</button></div>
        <form method="post" action="<?= site_url('admin/operateurs') ?>">
            <div class="modal-body"><div class="field"><label for="nomOperateur">Nom</label><input class="input" id="nomOperateur" name="nom" required></div></div>
            <div class="modal-foot"><button class="btn" type="button" data-close-modal>Annuler</button><button class="btn btn-primary" type="submit">Ajouter</button></div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
