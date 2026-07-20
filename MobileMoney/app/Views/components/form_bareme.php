<div class="field">
    <label for="typeBareme">Type d’opération</label>
    <select class="input" id="typeBareme" name="idtypeOp" required>
        <?php foreach ($types as $type): ?>
            <option value="<?= $type['id'] ?>"><?= esc(ucfirst($type['type'])) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="field-row">
    <div class="field"><label for="minimumBareme">Montant minimum</label><input class="input input-mono" id="minimumBareme" type="number" min="0" name="montant_min" required></div>
    <div class="field"><label for="maximumBareme">Montant maximum</label><input class="input input-mono" id="maximumBareme" type="number" min="0" name="montant_max" required></div>
</div>
<div class="field"><label for="prixBareme">Frais</label><input class="input input-mono" id="prixBareme" type="number" min="0" name="prix" required></div>
