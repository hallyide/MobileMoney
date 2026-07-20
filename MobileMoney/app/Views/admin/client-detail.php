<?= $this->extend('layouts/admin') ?>
<?= $this->section('contenu') ?>

<div style="padding-top:28px;">
    <a class="back-link" href="<?= site_url('admin/comptes') ?>">← Retour aux comptes</a>
    <div class="client-head">
        <div class="client-avatar"><?= esc(substr($compte['numero'], -2)) ?></div>
        <div>
            <h1 style="font-family:var(--font-display);font-size:22px;margin:0 0 4px;">Compte client</h1>
            <div class="client-id"><?= esc($compte['numero']) ?></div>
        </div>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom:16px;">
    <div class="card kpi">
        <div class="k-label">Solde actuel <span class="pulse-dot" style="margin-left:4px;"></span></div>
        <div class="k-value mint"><?= number_format((float) $compte['soldeActuel'], 0, ',', ' ') ?> Ar</div>
        <div class="k-foot">Valeur enregistrée dans le compte</div>
    </div>
    <div class="card kpi">
        <div class="k-label">Client depuis</div>
        <div class="k-value" style="font-size:20px;"><?= esc($compte['creation']) ?></div>
    </div>
    <div class="card kpi">
        <div class="k-label">Mouvements enregistrés</div>
        <div class="k-value"><?= count($mouvements) ?></div>
    </div>
</div>

<div class="card">
    <div class="card-head"><h2>Historique des mouvements</h2><div class="hint">Table mvmtCompte</div></div>
    <?= $this->include('components/mouvements') ?>
</div>

<?= $this->endSection() ?>
