<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titre) ?> — Mobile Money</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body class="theme-auth">
<div class="auth-card">
    <div class="auth-mark">MM</div>
    <h1>Espace client</h1>
    <p class="sub">Entrez votre numéro : le compte est créé automatiquement si nécessaire.</p>

    <?php if (session()->getFlashdata('erreur')): ?>
        <div class="form-error show"><?= esc(session()->getFlashdata('erreur')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('succes')): ?>
        <div class="toast success" style="margin-bottom:14px;"><span class="dot"></span><span><?= esc(session()->getFlashdata('succes')) ?></span></div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('client/login') ?>">
        <div class="field">
            <label for="loginNumero">Numéro de téléphone</label>
            <input class="input input-mono" id="loginNumero" name="numero"
                   value="<?= esc(old('numero')) ?>" placeholder="ex : 0341234567"
                   maxlength="10" pattern="[0-9]{10}" required autofocus>
            <span class="hint-sm">Le numéro doit commencer par un préfixe autorisé.</span>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
    </form>
    <p class="auth-foot"><a href="<?= site_url('/') ?>" style="color:var(--gold-soft)">← Retour au portail</a></p>
</div>
</body>
</html>
