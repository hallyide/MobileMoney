<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dépôt — Mobile Money</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="theme-client">

  <header class="topbar">
    <div class="topbar-inner">
      <a class="brand" href="dashboard.html"><span class="brand-mark">MM</span>Mobile Money<small>Espace client</small></a>
      <nav class="nav-flow">
        <a href="depot.html" class="active">Dépôt</a>
        <a href="retrait.html">Retrait</a>
        <a href="transfert.html">Transfert</a>
        <span class="nav-sep"></span>
        <button class="nav-link btn-logout" data-logout-client>Déconnexion</button>
      </nav>
    </div>
  </header>

  <main class="app-shell" id="page-depot">

    <div class="page-head">
      <div>
        <p class="eyebrow">Alimenter mon compte</p>
        <h1>Effectuer un dépôt</h1>
        <p>Solde actuel : <strong class="td-mono" id="formSoldeActuel">0 Ar</strong></p>
      </div>
      <a class="btn" href="dashboard.html">← Retour</a>
    </div>

    <div class="card card-pad form-page">
      <div class="form-error" id="opError"></div>
      <form id="formOp">
        <div class="field">
          <label for="montantOp">Montant à déposer</label>
          <input class="input input-mono" type="number" min="1" id="montantOp" placeholder="0" required>
        </div>
        <div class="summary-box">
          <div class="s-row"><span>Frais estimés</span><strong id="previewFrais">0 Ar</strong></div>
          <div class="s-row"><span>Total crédité</span><strong id="previewTotal">0 Ar</strong></div>
        </div>
        <button type="submit" class="btn btn-primary btn-block" style="margin-top:18px;">Valider le dépôt</button>
      </form>
    </div>

  </main>

  <p class="footer-note">Mobile Money · Espace client</p>

  <script src="../js/script.js"></script>
</body>
</html>
