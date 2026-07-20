<?= $this->extend('layouts/admin') ?>
<?= $this->section('contenu') ?>

<div class="page-head">
    <div>
        <p class="eyebrow">Portefeuille clients</p>
        <h1>Situation des comptes</h1>
        <p>Consultez le solde et les mouvements de chaque client.</p>
    </div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-head">
        <div>
            <h2>Récemment consultés</h2>
            <div class="hint">Les huit derniers comptes ouverts</div>
        </div>
    </div>
    <div class="recent-list">
        <?php foreach ($recents as $compte): ?>
            <a class="recent-item" href="<?= site_url('admin/comptes/' . $compte['id']) ?>">
                <div class="recent-left">
                    <div class="recent-avatar"><?= esc(substr($compte['numero'], -2)) ?></div>
                    <div>
                        <div class="recent-name">Compte <?= esc($compte['numero']) ?></div>
                        <div class="recent-sub">Créé le <?= esc($compte['creation']) ?></div>
                    </div>
                </div>
                <div class="ledger in"><span class="amt"><?= number_format((float) $compte['soldeActuel'], 0, ',', ' ') ?> Ar</span></div>
            </a>
        <?php endforeach; ?>
        <?php if ($recents === []): ?>
            <div class="empty-state">Aucun compte consulté récemment.</div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <div>
            <h2>Tous les clients</h2>
            <div class="hint"><?= count($comptes) ?> compte(s) enregistré(s)</div>
        </div>
        <input class="input" id="searchClient" placeholder="Rechercher un numéro…" style="max-width:260px;">
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Compte</th><th>Solde actuel</th><th>Client depuis</th><th></th></tr></thead>
            <tbody id="backendClientsBody">
            <?php foreach ($comptes as $compte): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="recent-avatar"><?= esc(substr($compte['numero'], -2)) ?></div>
                            <div><div class="recent-name">Client</div><div class="recent-sub td-mono"><?= esc($compte['numero']) ?></div></div>
                        </div>
                    </td>
                    <td class="td-mono"><?= number_format((float) $compte['soldeActuel'], 0, ',', ' ') ?> Ar</td>
                    <td class="td-muted"><?= esc($compte['creation']) ?></td>
                    <td><div class="row-actions"><a class="btn btn-sm btn-ghost" href="<?= site_url('admin/comptes/' . $compte['id']) ?>">Voir le détail →</a></div></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($comptes === []): ?>
                <tr><td colspan="4"><div class="empty-state">Aucun compte client.</div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.getElementById('searchClient').addEventListener('input', function () {
    const recherche = this.value.trim().toLowerCase();
    document.querySelectorAll('#backendClientsBody tr').forEach(function (ligne) {
        ligne.style.display = ligne.textContent.toLowerCase().includes(recherche) ? '' : 'none';
    });
});
</script>
<?= $this->endSection() ?>
