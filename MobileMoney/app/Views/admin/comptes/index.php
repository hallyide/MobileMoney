<?= $this->extend('layouts/simple') ?>
<?= $this->section('contenu') ?>

<h1>Situation des comptes clients</h1>
<p><?= count($comptes) ?> compte(s) client(s).</p>

<section class="carte tableau">
    <table>
        <thead><tr><th>Numero</th><th>Solde actuel</th><th>Creation</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($comptes as $compte): ?>
            <tr>
                <td><?= esc($compte['numero']) ?></td>
                <td><?= number_format((float) $compte['soldeActuel'], 0, ',', ' ') ?> Ar</td>
                <td><?= esc($compte['creation']) ?></td>
                <td class="actions">
                    <a class="bouton" href="<?= site_url('admin/comptes/' . $compte['id']) ?>">Details</a>
                    <a class="bouton" href="<?= site_url('client/' . $compte['id']) ?>">Espace client</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($comptes === []): ?>
            <tr><td colspan="4">Aucun compte client.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>

<?= $this->endSection() ?>
