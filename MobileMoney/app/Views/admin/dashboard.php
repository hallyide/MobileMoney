<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Opérateur</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="ink">

  <header class="topbar">
    <div class="topbar-inner">
      <a class="brand" href="admin/dashboard"><span class="brand-mark">MM</span>Mobile Money<small>Espace opérateur</small></a>
      <nav class="nav-flow">
        <a href="/admin/dashboard" class="active">Dashboard</a>
        <a href="/api/bareme">Barème des frais</a>
        <a href="/api/historique/(:segment)">Historique gains</a>
        <a href="comptes.html">Comptes clients</a>
        <span class="nav-sep"></span>
        <a href="../index.html" class="btn-logout">Déconnexion</a>
      </nav>
    </div>
  </header>

  <main class="app-shell app-shell--wide" id="page-admin-dashboard">

    <div class="page-head">
      <div>
        <p class="eyebrow">Vue d'ensemble</p>
        <h1>Tableau de bord</h1>
        <p>Suivi des gains, des clients et de l'activité du réseau.</p>
      </div>
    </div>

    <div class="grid grid-3" style="margin-bottom:16px;">
      <div class="card kpi">
        <div class="k-label">Valeur des gains</div>
        <div class="k-value gold" id="kpiGains">0 Ar</div>
        <div class="k-foot">Cumul des frais perçus (caisseOp)</div>
      </div>
      <div class="card kpi">
        <div class="k-label">Nombre de clients</div>
        <div class="k-value" id="kpiClients">0</div>
        <div class="k-foot">Comptes actifs sur la plateforme</div>
      </div>
      <div class="card kpi">
        <div class="k-label">Préfixes disponibles</div>
        <div class="k-value" id="kpiPrefixes">0</div>
        <div class="k-foot">Préfixes autorisés à l'ouverture de compte</div>
      </div>
    </div>

    <div class="card" style="margin-bottom:16px;">
      <div class="card-head">
        <div>
          <h2>Statistiques des transactions</h2>
          <div class="hint">Volume total mouvementé sur la période</div>
        </div>
        <div class="chart-toolbar seg" id="chartToolbar">
          <button data-period="jour" class="active">Jour</button>
          <button data-period="semaine">Semaine</button>
          <button data-period="mois">Mois</button>
          <button data-period="annee">Année</button>
        </div>
      </div>
      <div class="chart-legend"><span><i></i> Volume des transactions</span></div>
      <div class="chart-box">
        <canvas id="statsChart" style="width:100%; height:220px;"></canvas>
      </div>
    </div>

    <div class="card">
      <div class="card-head">
        <div>
          <h2>Préfixes disponibles</h2>
          <div class="hint">Préfixes de numéro acceptés pour la création de comptes</div>
        </div>
        <button class="btn btn-primary btn-sm" data-open-modal="modalAddPrefix">+ Ajouter</button>
      </div>
      <div class="table-wrap">
        <table class="tbl">
          <thead><tr><th>Préfixe</th><th style="text-align:right;">Actions</th></tr></thead>
          <tbody id="prefixTableBody"></tbody>
        </table>
      </div>
    </div>

  </main>

  <p class="footer-note">Mobile Money · Espace opérateur</p>

  <!-- Modale ajout préfixe -->
  <div class="modal-overlay" id="modalAddPrefix">
    <div class="modal">
      <div class="modal-head"><h3>Ajouter un préfixe</h3><button class="modal-close" data-close-modal>×</button></div>
      <form id="formAddPrefix">
        <div class="modal-body">
          <div class="field">
            <label for="addPrefixValue">Préfixe</label>
            <input class="input input-mono" id="addPrefixValue" name="prefixe" placeholder="ex : 034" maxlength="4" required>
          </div>
        </div>
        <div class="modal-foot">
          <button type="button" class="btn" data-close-modal>Annuler</button>
          <button type="submit" class="btn btn-primary">Ajouter</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modale modification préfixe -->
  <div class="modal-overlay" id="modalEditPrefix">
    <div class="modal">
      <div class="modal-head"><h3>Modifier le préfixe</h3><button class="modal-close" data-close-modal>×</button></div>
      <form id="formEditPrefix">
        <input type="hidden" id="editPrefixId">
        <div class="modal-body">
          <div class="field">
            <label for="editPrefixValue">Préfixe</label>
            <input class="input input-mono" id="editPrefixValue" maxlength="4" required>
          </div>
        </div>
        <div class="modal-foot">
          <button type="button" class="btn" data-close-modal>Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <script src="../js/script.js"></script>
</body>
</html>
