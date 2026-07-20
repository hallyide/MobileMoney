<?= $this->extend('layouts/simple') ?>
<?= $this->section('contenu') ?>

<h1>Configuration des prefixes</h1>

<section class="carte">
    <h2>Ajouter un prefixe</h2>
    <form method="post" action="<?= site_url('admin/prefixes') ?>">
        <div class="champ">
            <label for="nouveau-prefixe">Prefixe telephonique</label>
            <input id="nouveau-prefixe" name="prefixe" value="<?= esc(old('prefixe')) ?>"
                   maxlength="3" pattern="[0-9]{3}" placeholder="033" required>
        </div>
        <button type="submit">Ajouter</button>
    </form>
</section>

<section class="carte">
    <h2>Prefixes disponibles</h2>
    <div class="tableau">
        <table>
            <thead><tr><th>Prefixe</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($prefixes as $prefixe): ?>
                <tr>
                    <td><?= esc($prefixe['prefixe']) ?></td>
                    <td>
                        <div class="actions">
                            <form method="post" action="<?= site_url('admin/prefixes/' . $prefixe['id']) ?>">
                                <input name="prefixe" value="<?= esc($prefixe['prefixe']) ?>"
                                       maxlength="3" pattern="[0-9]{3}" required aria-label="Modifier le prefixe">
                                <button type="submit">Modifier</button>
                            </form>
                            <form method="post" action="<?= site_url('admin/prefixes/' . $prefixe['id'] . '/supprimer') ?>"
                                  onsubmit="return confirm('Supprimer ce prefixe ?')">
                                <button class="danger" type="submit">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($prefixes === []): ?>
                <tr><td colspan="2">Aucun prefixe configure.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?= $this->endSection() ?>
