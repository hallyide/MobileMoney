(function(){
  "use strict";

  var DB_KEY     = "flux_db_v1";
  var SESSION_KEY= "flux_client_session";
  var RECENT_KEY = "flux_recent_clients";
  var API_BASE   = ""; // ex: "http://localhost:8080" si le front n'est pas servi par CI

  /* =======================================================
     1. BASE DE DONNÉES SIMULÉE (fallback / pages non migrées)
     ======================================================= */

  function seedDB(){
    var db = {
      prefixeDispo: [
        { id:1, prefixe:"034" },
        { id:2, prefixe:"038" },
        { id:3, prefixe:"032" },
        { id:4, prefixe:"033" }
      ],
      typeOperation: [
        { id:1, type:"depot" },
        { id:2, type:"retrait" },
        { id:3, type:"transfert" }
      ],
      typeMvmtComp: [
        { id:1, type:"debit" },  // entrée
        { id:2, type:"credit" } // sortie
      ],
      baremeFrais: [
        { id:1, idtypeOp:1, montant_min:0,      montant_max:50000,   prix:0 },
        { id:2, idtypeOp:1, montant_min:50001,  montant_max:500000,  prix:500 },
        { id:3, idtypeOp:1, montant_min:500001, montant_max:9999999, prix:1000 },
        { id:4, idtypeOp:2, montant_min:0,      montant_max:50000,   prix:500 },
        { id:5, idtypeOp:2, montant_min:50001,  montant_max:500000,  prix:1500 },
        { id:6, idtypeOp:2, montant_min:500001, montant_max:9999999, prix:3000 },
        { id:7, idtypeOp:3, montant_min:0,      montant_max:100000,  prix:300 },
        { id:8, idtypeOp:3, montant_min:100001, montant_max:9999999, prix:800 }
      ],
      compte: [
        { id:1, numero:"0341234567", nom:"Rakoto Jean",      soldeActuel:245000,  creation:"2024-02-10" },
        { id:2, numero:"0389876543", nom:"Rasoa Marie",      soldeActuel:1280000, creation:"2023-11-05" },
        { id:3, numero:"0331122334", nom:"Andry Tojo",       soldeActuel:53500,   creation:"2025-01-20" },
        { id:4, numero:"0325566778", nom:"Voahangy Nirina",  soldeActuel:670000,  creation:"2024-06-18" }
      ],
      mvmtCompte: [],
      fraisMvmt: [],
      caisseOp: [ { id:1, gains:0 } ]
    };

    var opCycle = [1,2,3];
    var mvmtId = 1, fraisId = 1;
    var now = new Date();
    for(var d=44; d>=0; d--){
      var date = new Date(now); date.setDate(now.getDate()-d);
      var dateStr = date.toISOString().slice(0,10);
      var nbOps = (d % 3 === 0) ? 2 : 1;
      if(d % 5 === 0) nbOps = 3;
      for(var n=0; n<nbOps; n++){
        var compteIdx = (d + n) % db.compte.length;
        var compte = db.compte[compteIdx];
        var idTypeOp = opCycle[(d + n) % 3];
        var montant = 10000 + ((d*137 + n*911) % 40) * 10000;
        var idType = (idTypeOp === 1) ? 1 : 2;

        db.mvmtCompte.push({
          id: mvmtId, idCompte: compte.id, valeur: montant,
          date: dateStr, idType: idType, indTypeOp: idTypeOp
        });

        var frais = computeFrais(db, idTypeOp, montant);
        db.fraisMvmt.push({
          id: fraisId, idMvmtCompt: mvmtId, valeur: frais,
          typeOp: idTypeOp, date: dateStr
        });
        db.caisseOp[0].gains += frais;

        mvmtId++; fraisId++;
      }
    }
    return db;
  }

  function loadDB(){
    var raw = localStorage.getItem(DB_KEY);
    if(!raw){
      var db = seedDB();
      saveDB(db);
      return db;
    }
    try{ return JSON.parse(raw); }
    catch(e){ var db2 = seedDB(); saveDB(db2); return db2; }
  }

  function saveDB(db){ localStorage.setItem(DB_KEY, JSON.stringify(db)); }
  function nextId(arr){ return arr.length ? Math.max.apply(null, arr.map(function(x){return x.id;})) + 1 : 1; }

  function computeFrais(db, idtypeOp, montant){
    var lignes = db.baremeFrais.filter(function(b){ return b.idtypeOp === idtypeOp; });
    for(var i=0;i<lignes.length;i++){
      var b = lignes[i];
      if(montant >= b.montant_min && montant <= b.montant_max) return b.prix;
    }
    return lignes.length ? lignes[lignes.length-1].prix : 0;
  }

  window.FLUX = { loadDB: loadDB, saveDB: saveDB, nextId: nextId, computeFrais: computeFrais };

  /* =======================================================
     2. UTILITAIRES
     ======================================================= */

  function qs(sel, ctx){ return (ctx||document).querySelector(sel); }
  function qsa(sel, ctx){ return Array.prototype.slice.call((ctx||document).querySelectorAll(sel)); }

  function formatAr(n){
    n = Math.round(Number(n) || 0);
    return n.toLocaleString("fr-FR") + " Ar";
  }
  function formatDate(iso){
    var d = new Date(iso + "T00:00:00");
    if(isNaN(d)) return iso;
    return d.toLocaleDateString("fr-FR", { day:"2-digit", month:"2-digit", year:"numeric" });
  }
  function todayISO(){ return new Date().toISOString().slice(0,10); }
  function opTypeLabel(t){
    return { depot:"Dépôt", retrait:"Retrait", transfert:"Transfert" }[t] || t;
  }
  function opTypeById(db, id){
    var t = db.typeOperation.find(function(o){ return o.id === id; });
    return t ? t.type : "";
  }
  function mvmtTypeLabel(t){ return t === "debit" ? "Entrée" : "Sortie"; }
  function clientLabel(c){ return c ? (c.nom + " · " + c.numero) : "—"; }
  function initials(name){
    return (name||"").split(" ").filter(Boolean).slice(0,2).map(function(w){return w[0];}).join("").toUpperCase();
  }
  window.U = {
    qs:qs, qsa:qsa, formatAr:formatAr, formatDate:formatDate, todayISO:todayISO,
    opTypeLabel:opTypeLabel, opTypeById:opTypeById, mvmtTypeLabel:mvmtTypeLabel,
    clientLabel:clientLabel, initials:initials
  };

  /* =======================================================
     2bis. HELPER FETCH (API CodeIgniter)
     ======================================================= */
  function apiGet(path){
    return fetch(API_BASE + path, { credentials: "same-origin" })
      .then(function(r){ if(!r.ok) return r.json().then(function(e){ throw e; }); return r.json(); });
  }
  function apiSend(path, method, body){
    return fetch(API_BASE + path, {
      method: method,
      credentials: "same-origin",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(body || {})
    }).then(function(r){
      return r.json().then(function(data){
        if(!r.ok){ throw data; }
        return data;
      });
    });
  }
  function apiDelete(path){
    return fetch(API_BASE + path, { method: "DELETE", credentials: "same-origin" })
      .then(function(r){ return r.json().then(function(data){ if(!r.ok) throw data; return data; }); });
  }
  window.API = { get: apiGet, send: apiSend, del: apiDelete };

  /* =======================================================
     3. MODALES
     ======================================================= */
  function openModal(id){
    var el = document.getElementById(id);
    if(el) el.classList.add("open");
  }
  function closeModal(id){
    var el = document.getElementById(id);
    if(el) el.classList.remove("open");
  }
  window.openModal = openModal;
  window.closeModal = closeModal;

  document.addEventListener("click", function(e){
    if(e.target.classList && e.target.classList.contains("modal-overlay")){
      e.target.classList.remove("open");
    }
    var closer = e.target.closest && e.target.closest("[data-close-modal]");
    if(closer){
      var overlay = closer.closest(".modal-overlay");
      if(overlay) overlay.classList.remove("open");
    }
    var opener = e.target.closest && e.target.closest("[data-open-modal]");
    if(opener){
      openModal(opener.getAttribute("data-open-modal"));
    }
  });
  document.addEventListener("keydown", function(e){
    if(e.key === "Escape"){
      qsa(".modal-overlay.open").forEach(function(m){ m.classList.remove("open"); });
    }
  });

  /* =======================================================
     4. TOASTS
     ======================================================= */
  function toast(msg, type){
    var stack = qs(".toast-stack");
    if(!stack){
      stack = document.createElement("div");
      stack.className = "toast-stack";
      document.body.appendChild(stack);
    }
    var el = document.createElement("div");
    el.className = "toast " + (type === "error" ? "error" : "success");
    el.innerHTML = '<span class="dot"></span><span>' + msg + "</span>";
    stack.appendChild(el);
    setTimeout(function(){
      el.style.transition = "opacity .2s ease, transform .2s ease";
      el.style.opacity = "0";
      el.style.transform = "translateX(12px)";
      setTimeout(function(){ el.remove(); }, 200);
    }, 3200);
  }
  window.toast = toast;

  /* =======================================================
     5. GRAPHIQUE (canvas natif, sans dépendance) — inchangé
     ======================================================= */
  function aggregate(db, period){
    var now = new Date();
    var buckets = [];

    function sumForRange(start, end){
      var s = 0;
      db.mvmtCompte.forEach(function(m){
        var d = new Date(m.date + "T00:00:00");
        if(d >= start && d < end) s += m.valeur;
      });
      return s;
    }

    if(period === "jour"){
      for(var i=13;i>=0;i--){
        var d0 = new Date(now); d0.setDate(now.getDate()-i); d0.setHours(0,0,0,0);
        var d1 = new Date(d0); d1.setDate(d0.getDate()+1);
        buckets.push({ label: d0.toLocaleDateString("fr-FR",{day:"2-digit",month:"2-digit"}), value: sumForRange(d0,d1) });
      }
    } else if(period === "semaine"){
      for(var w=7;w>=0;w--){
        var w1 = new Date(now); w1.setDate(now.getDate()-(w*7)); w1.setHours(0,0,0,0);
        var w0 = new Date(w1); w0.setDate(w1.getDate()-6);
        buckets.push({ label: "S-"+w, value: sumForRange(w0, new Date(w1.getTime()+86400000)) });
      }
    } else if(period === "mois"){
      for(var mo=11;mo>=0;mo--){
        var m0 = new Date(now.getFullYear(), now.getMonth()-mo, 1);
        var m1 = new Date(now.getFullYear(), now.getMonth()-mo+1, 1);
        buckets.push({ label: m0.toLocaleDateString("fr-FR",{month:"short"}), value: sumForRange(m0,m1) });
      }
    } else {
      for(var y=4;y>=0;y--){
        var y0 = new Date(now.getFullYear()-y, 0, 1);
        var y1 = new Date(now.getFullYear()-y+1, 0, 1);
        buckets.push({ label: String(now.getFullYear()-y), value: sumForRange(y0,y1) });
      }
    }
    return buckets;
  }

  function drawChart(canvas, buckets){
    var ctx = canvas.getContext("2d");
    var dpr = window.devicePixelRatio || 1;
    var cssW = canvas.clientWidth || 600;
    var cssH = canvas.clientHeight || 220;
    canvas.width = cssW * dpr;
    canvas.height = cssH * dpr;
    ctx.setTransform(dpr,0,0,dpr,0,0);
    ctx.clearRect(0,0,cssW,cssH);

    var padL = 44, padR = 14, padT = 16, padB = 26;
    var w = cssW - padL - padR, h = cssH - padT - padB;
    var max = Math.max.apply(null, buckets.map(function(b){return b.value;}).concat([1]));
    max = max * 1.15;

    ctx.strokeStyle = "rgba(143,160,181,.18)";
    ctx.lineWidth = 1;
    ctx.font = "11px Inter, sans-serif";
    ctx.fillStyle = "rgba(143,160,181,.85)";
    var steps = 4;
    for(var s=0;s<=steps;s++){
      var y = padT + h - (h*s/steps);
      ctx.beginPath();
      ctx.moveTo(padL, y); ctx.lineTo(padL+w, y);
      ctx.stroke();
      var val = Math.round(max*s/steps);
      ctx.fillText(val >= 1000 ? Math.round(val/1000)+"k" : String(val), 4, y+4);
    }

    if(buckets.length < 2) return;
    var stepX = w / (buckets.length - 1);
    function px(i){ return padL + stepX*i; }
    function py(v){ return padT + h - (h * v/max); }

    var grad = ctx.createLinearGradient(0,padT,0,padT+h);
    grad.addColorStop(0, "rgba(212,169,71,.35)");
    grad.addColorStop(1, "rgba(212,169,71,0)");
    ctx.beginPath();
    ctx.moveTo(px(0), padT+h);
    buckets.forEach(function(b,i){ ctx.lineTo(px(i), py(b.value)); });
    ctx.lineTo(px(buckets.length-1), padT+h);
    ctx.closePath();
    ctx.fillStyle = grad;
    ctx.fill();

    ctx.beginPath();
    buckets.forEach(function(b,i){
      var x=px(i), y=py(b.value);
      if(i===0) ctx.moveTo(x,y); else ctx.lineTo(x,y);
    });
    ctx.strokeStyle = "#D4A947";
    ctx.lineWidth = 2.2;
    ctx.lineJoin = "round";
    ctx.stroke();

    buckets.forEach(function(b,i){
      var x=px(i), y=py(b.value);
      ctx.beginPath();
      ctx.arc(x,y,3,0,Math.PI*2);
      ctx.fillStyle = "#0F1B2B";
      ctx.fill();
      ctx.lineWidth = 1.6;
      ctx.strokeStyle = "#D4A947";
      ctx.stroke();
    });

    ctx.fillStyle = "rgba(143,160,181,.85)";
    ctx.font = "10.5px Inter, sans-serif";
    ctx.textAlign = "center";
    var skip = buckets.length > 10 ? 2 : 1;
    buckets.forEach(function(b,i){
      if(i % skip === 0) ctx.fillText(b.label, px(i), cssH-8);
    });
    ctx.textAlign = "left";
  }

  function initChartWidget(canvasId, toolbarId){
    var canvas = document.getElementById(canvasId);
    var toolbar = document.getElementById(toolbarId);
    if(!canvas || !toolbar) return;
    var db = loadDB();
    function render(period){
      qsa("button", toolbar).forEach(function(b){ b.classList.toggle("active", b.dataset.period === period); });
      drawChart(canvas, aggregate(db, period));
    }
    toolbar.addEventListener("click", function(e){
      var btn = e.target.closest("button[data-period]");
      if(!btn) return;
      render(btn.dataset.period);
    });
    render("jour");
    window.addEventListener("resize", function(){
      var active = qs("button.active", toolbar);
      render(active ? active.dataset.period : "jour");
    });
  }

  /* =======================================================
     6. CLIENTS RÉCEMMENT CONSULTÉS (admin, localStorage)
     ======================================================= */
  function getRecent(){
    try{ return JSON.parse(localStorage.getItem(RECENT_KEY)) || []; }
    catch(e){ return []; }
  }
  function recordRecent(id){
    var list = getRecent().filter(function(x){ return x !== id; });
    list.unshift(id);
    if(list.length > 8) list = list.slice(0,8);
    localStorage.setItem(RECENT_KEY, JSON.stringify(list));
  }

  /* =======================================================
     7. SESSION CLIENT (miroir local, en plus de la session PHP)
     ======================================================= */
  function getClientSession(){ return sessionStorage.getItem(SESSION_KEY); }
  function setClientSession(numero){ sessionStorage.setItem(SESSION_KEY, numero); }
  function clearClientSession(){ sessionStorage.removeItem(SESSION_KEY); }
  function currentCompte(db){
    var numero = getClientSession();
    if(!numero) return null;
    return db.compte.find(function(c){ return c.numero === numero; }) || null;
  }

  /* =======================================================
     8. INITIALISATION PAR PAGE
     ======================================================= */
  document.addEventListener("DOMContentLoaded", function(){

    qsa("[data-logout-client]").forEach(function(b){
      b.addEventListener("click", function(){ clearClientSession(); window.location.href = "login.html"; });
    });

    if(qs("#page-admin-dashboard"))   initAdminDashboard();
    if(qs("#page-bareme"))            initBareme();
    if(qs("#page-historique-gains"))  initHistoriqueGains();
    if(qs("#page-comptes"))           initComptes();
    if(qs("#page-client-detail"))     initClientDetail();

    if(qs("#page-client-login"))      initClientLogin();
    if(qs("#page-client-dashboard"))  initClientDashboard();
    if(qs("#page-client-historique")) initClientHistorique();
    if(qs("#page-depot"))             initOpForm("depot");
    if(qs("#page-retrait"))           initOpForm("retrait");
    if(qs("#page-transfert"))         initTransfert();
  });

  /* ---------- 8.1 Dashboard opérateur (localStorage, ETU1944) ---------- */
  function initAdminDashboard(){
    var db = loadDB();

    qs("#kpiGains").textContent = formatAr(db.caisseOp[0].gains);
    qs("#kpiClients").textContent = db.compte.length;
    qs("#kpiPrefixes").textContent = db.prefixeDispo.length;

    renderPrefixTable();
    initChartWidget("statsChart", "chartToolbar");

    function renderPrefixTable(){
      var body = qs("#prefixTableBody");
      body.innerHTML = "";
      if(!db.prefixeDispo.length){
        body.innerHTML = '<tr><td colspan="2"><div class="empty-state">Aucun préfixe enregistré.</div></td></tr>';
        return;
      }
      db.prefixeDispo.forEach(function(p){
        var tr = document.createElement("tr");
        tr.innerHTML =
          '<td class="td-mono">'+p.prefixe+'</td>'+
          '<td><div class="row-actions">'+
            '<button class="btn btn-sm btn-ghost" data-edit="'+p.id+'">Modifier</button>'+
            '<button class="btn btn-sm btn-danger" data-del="'+p.id+'">Supprimer</button>'+
          '</div></td>';
        body.appendChild(tr);
      });
    }

    qs("#prefixTableBody").addEventListener("click", function(e){
      var editBtn = e.target.closest("[data-edit]");
      var delBtn  = e.target.closest("[data-del]");
      if(editBtn){
        var p = db.prefixeDispo.find(function(x){ return x.id === Number(editBtn.dataset.edit); });
        qs("#editPrefixId").value = p.id;
        qs("#editPrefixValue").value = p.prefixe;
        openModal("modalEditPrefix");
      }
      if(delBtn){
        if(!confirm("Supprimer ce préfixe ?")) return;
        db.prefixeDispo = db.prefixeDispo.filter(function(x){ return x.id !== Number(delBtn.dataset.del); });
        saveDB(db);
        renderPrefixTable();
        qs("#kpiPrefixes").textContent = db.prefixeDispo.length;
        toast("Préfixe supprimé.");
      }
    });

    qs("#formAddPrefix").addEventListener("submit", function(e){
      e.preventDefault();
      var val = qs("#addPrefixValue").value.trim();
      if(!val){ return; }
      db.prefixeDispo.push({ id: nextId(db.prefixeDispo), prefixe: val });
      saveDB(db);
      renderPrefixTable();
      qs("#kpiPrefixes").textContent = db.prefixeDispo.length;
      e.target.reset();
      closeModal("modalAddPrefix");
      toast("Préfixe ajouté.");
    });

    qs("#formEditPrefix").addEventListener("submit", function(e){
      e.preventDefault();
      var id = Number(qs("#editPrefixId").value);
      var p = db.prefixeDispo.find(function(x){ return x.id === id; });
      p.prefixe = qs("#editPrefixValue").value.trim();
      saveDB(db);
      renderPrefixTable();
      closeModal("modalEditPrefix");
      toast("Préfixe modifié.");
    });
  }

  /* ---------- 8.2 Barème des frais (API CI4, ETU4269) ---------- */
  function initBareme(){
    var typeLabels = { depot:"Dépôt", retrait:"Retrait", transfert:"Transfert" };
    var typesCache = [];

    function fillTypeSelects(){
      return apiGet("/api/typeoperation").catch(function(){ return []; }).then(function(types){
        // fallback si la route /api/typeoperation n'existe pas encore côté serveur :
        if(!types || !types.length){
          types = [{id:1,type:"depot"},{id:2,type:"retrait"},{id:3,type:"transfert"}];
        }
        typesCache = types;
        qsa(".op-type-select").forEach(function(sel){
          sel.innerHTML = types.map(function(t){
            return '<option value="'+t.id+'">'+(typeLabels[t.type]||t.type)+'</option>';
          }).join("");
        });
      });
    }

    function render(){
      apiGet("/api/bareme").then(function(rows){
        var body = qs("#baremeTableBody");
        body.innerHTML = "";
        if(!rows.length){
          body.innerHTML = '<tr><td colspan="5"><div class="empty-state">Aucun barème défini.</div></td></tr>';
          return;
        }
        rows
          .slice()
          .sort(function(a,b){ return a.idtypeOp - b.idtypeOp || a.montant_min - b.montant_min; })
          .forEach(function(b){
            var typeStr = b.type_label || "";
            var tr = document.createElement("tr");
            tr.innerHTML =
              '<td><span class="badge '+typeStr+'">'+(typeLabels[typeStr]||typeStr)+'</span></td>'+
              '<td class="td-mono">'+formatAr(b.montant_min)+'</td>'+
              '<td class="td-mono">'+formatAr(b.montant_max)+'</td>'+
              '<td class="td-mono">'+formatAr(b.prix)+'</td>'+
              '<td><div class="row-actions">'+
                '<button class="btn btn-sm btn-ghost" data-edit="'+b.id+'" data-json=\''+JSON.stringify(b).replace(/'/g,"&#39;")+'\'>Modifier</button>'+
                '<button class="btn btn-sm btn-danger" data-del="'+b.id+'">Supprimer</button>'+
              '</div></td>';
            body.appendChild(tr);
          });
      }).catch(function(err){
        toast("Erreur de chargement du barème.", "error");
        console.error(err);
      });
    }

    fillTypeSelects().then(render);

    qs("#baremeTableBody").addEventListener("click", function(e){
      var editBtn = e.target.closest("[data-edit]");
      var delBtn  = e.target.closest("[data-del]");

      if(editBtn){
        var b = JSON.parse(editBtn.dataset.json);
        qs("#editBaremeId").value = b.id;
        qs("#editBaremeType").value = b.idtypeOp;
        qs("#editBaremeMin").value = b.montant_min;
        qs("#editBaremeMax").value = b.montant_max;
        qs("#editBaremePrix").value = b.prix;
        openModal("modalEditBareme");
      }

      if(delBtn){
        if(!confirm("Supprimer cette ligne de barème ?")) return;
        apiDelete("/api/bareme/" + delBtn.dataset.del)
          .then(function(){ render(); toast("Barème supprimé."); })
          .catch(function(err){ toast("Erreur lors de la suppression.", "error"); console.error(err); });
      }
    });

    qs("#formAddBareme").addEventListener("submit", function(e){
      e.preventDefault();
      var payload = {
        idtypeOp: Number(qs("#addBaremeType").value),
        montant_min: Number(qs("#addBaremeMin").value),
        montant_max: Number(qs("#addBaremeMax").value),
        prix: Number(qs("#addBaremePrix").value)
      };
      apiSend("/api/bareme", "POST", payload)
        .then(function(){
          render();
          e.target.reset();
          closeModal("modalAddBareme");
          toast("Barème ajouté.");
        })
        .catch(function(err){ toast("Erreur lors de l'ajout.", "error"); console.error(err); });
    });

    qs("#formEditBareme").addEventListener("submit", function(e){
      e.preventDefault();
      var id = qs("#editBaremeId").value;
      var payload = {
        idtypeOp: Number(qs("#editBaremeType").value),
        montant_min: Number(qs("#editBaremeMin").value),
        montant_max: Number(qs("#editBaremeMax").value),
        prix: Number(qs("#editBaremePrix").value)
      };
      apiSend("/api/bareme/" + id, "POST", payload)
        .then(function(){
          render();
          closeModal("modalEditBareme");
          toast("Barème modifié.");
        })
        .catch(function(err){ toast("Erreur lors de la modification.", "error"); console.error(err); });
    });
  }

  /* ---------- 8.3 Historique des gains (API CI4, ETU4269) ---------- */
  function initHistoriqueGains(){
    var typeLabels = { depot:"Dépôt", retrait:"Retrait", transfert:"Transfert" };

    function fillSelects(){
      var pTypes = apiGet("/api/typeoperation").catch(function(){
        return [{id:1,type:"depot"},{id:2,type:"retrait"},{id:3,type:"transfert"}];
      });
      var pClients = apiGet("/api/comptes").catch(function(){ return []; });

      return Promise.all([pTypes, pClients]).then(function(res){
        var types = res[0], clients = res[1];
        qsa(".op-type-select").forEach(function(sel){
          sel.innerHTML = types.map(function(t){
            return '<option value="'+t.id+'">'+(typeLabels[t.type]||t.type)+'</option>';
          }).join("");
        });
        qsa(".client-select").forEach(function(sel){
          sel.innerHTML = clients.map(function(c){
            return '<option value="'+c.id+'">'+c.nom+' · '+c.numero+'</option>';
          }).join("");
        });
      });
    }

    function render(){
      apiGet("/api/gains").then(function(rows){
        var body = qs("#gainsTableBody");
        body.innerHTML = "";
        if(!rows.length){
          body.innerHTML = '<tr><td colspan="6"><div class="empty-state">Aucun frais enregistré.</div></td></tr>';
          return;
        }
        rows.forEach(function(f){
          var typeStr = f.type_label || "";
          var tr = document.createElement("tr");
          tr.innerHTML =
            '<td class="td-mono td-muted">#'+f.id+'</td>'+
            '<td>'+(f.nom || "—")+' · '+(f.numero || "")+'</td>'+
            '<td><span class="badge '+typeStr+'">'+(typeLabels[typeStr]||typeStr)+'</span></td>'+
            '<td class="td-mono">'+formatAr(f.valeur)+'</td>'+
            '<td class="td-muted">'+formatDate(f.date)+'</td>'+
            '<td><div class="row-actions">'+
              '<button class="btn btn-sm btn-ghost" data-edit="'+f.id+'" data-json=\''+JSON.stringify(f).replace(/'/g,"&#39;")+'\'>Modifier</button>'+
              '<button class="btn btn-sm btn-danger" data-del="'+f.id+'">Supprimer</button>'+
            '</div></td>';
          body.appendChild(tr);
        });
      }).catch(function(err){
        toast("Erreur de chargement des gains.", "error");
        console.error(err);
      });
    }

    fillSelects().then(render);

    qs("#gainsTableBody").addEventListener("click", function(e){
      var editBtn = e.target.closest("[data-edit]");
      var delBtn  = e.target.closest("[data-del]");

      if(editBtn){
        var f = JSON.parse(editBtn.dataset.json);
        qs("#editGainId").value = f.id;
        qs("#editGainType").value = f.typeOp;
        qs("#editGainMontant").value = f.valeur;
        qs("#editGainDate").value = f.date;
        openModal("modalEditGain");
      }

      if(delBtn){
        if(!confirm("Supprimer ce frais ?")) return;
        apiDelete("/api/gains/" + delBtn.dataset.del)
          .then(function(){ render(); toast("Frais supprimé."); })
          .catch(function(err){ toast("Erreur lors de la suppression.", "error"); console.error(err); });
      }
    });

    qs("#formAddGain").addEventListener("submit", function(e){
      e.preventDefault();
      var payload = {
        idCompte: Number(qs("#addGainClient").value),
        idtypeOp: Number(qs("#addGainType").value),
        montant: Number(qs("#addGainMontantMvmt").value),
        date: qs("#addGainDate").value || todayISO()
      };
      apiSend("/api/gains", "POST", payload)
        .then(function(res){
          render();
          e.target.reset();
          closeModal("modalAddGain");
          toast("Frais ajouté (calculé selon le barème : " + formatAr(res.frais) + ").");
        })
        .catch(function(err){ toast("Erreur lors de l'ajout.", "error"); console.error(err); });
    });

    qs("#formEditGain").addEventListener("submit", function(e){
      e.preventDefault();
      var id = qs("#editGainId").value;
      var payload = {
        typeOp: Number(qs("#editGainType").value),
        valeur: Number(qs("#editGainMontant").value),
        date: qs("#editGainDate").value
      };
      apiSend("/api/gains/" + id, "POST", payload)
        .then(function(){
          render();
          closeModal("modalEditGain");
          toast("Frais modifié.");
        })
        .catch(function(err){ toast("Erreur lors de la modification.", "error"); console.error(err); });
    });
  }

  /* ---------- 8.4 Situation des comptes (localStorage, ETU1944) ---------- */
  function initComptes(){
    var db = loadDB();

    function renderRecent(){
      var wrap = qs("#recentClients");
      var ids = getRecent();
      var body = qs("#allClientsBody");

      if(!ids.length){
        wrap.innerHTML = '<div class="empty-state">Aucun client consulté récemment. Cliquez sur un client dans le tableau ci-dessous.</div>';
      } else {
        wrap.innerHTML = "";
        ids.forEach(function(id){
          var c = db.compte.find(function(x){ return x.id === id; });
          if(!c) return;
          var item = document.createElement("div");
          item.className = "recent-item clickable";
          item.innerHTML =
            '<div class="recent-left">'+
              '<div class="recent-avatar">'+initials(c.nom)+'</div>'+
              '<div><div class="recent-name">'+c.nom+'</div><div class="recent-sub">'+c.numero+'</div></div>'+
            '</div>'+
            '<div class="ledger"><span class="amt td-mono">'+formatAr(c.soldeActuel)+'</span></div>';
          item.addEventListener("click", function(){ goToClient(c.id); });
          wrap.appendChild(item);
        });
      }

      body.innerHTML = "";
      db.compte.forEach(function(c){
        var tr = document.createElement("tr");
        tr.className = "clickable";
        tr.innerHTML =
          '<td>'+
            '<div style="display:flex;align-items:center;gap:10px;">'+
              '<div class="recent-avatar">'+initials(c.nom)+'</div>'+
              '<div><div class="recent-name">'+c.nom+'</div><div class="recent-sub td-mono">'+c.numero+'</div></div>'+
            '</div>'+
          '</td>'+
          '<td class="td-mono">'+formatAr(c.soldeActuel)+'</td>'+
          '<td class="td-muted">'+formatDate(c.creation)+'</td>'+
          '<td class="row-actions"><button class="btn btn-sm btn-ghost">Voir le détail →</button></td>';
        tr.addEventListener("click", function(){ goToClient(c.id); });
        body.appendChild(tr);
      });
    }

    function goToClient(id){
      recordRecent(id);
      window.location.href = "client-detail.html?id=" + id;
    }

    qs("#searchClient").addEventListener("input", function(e){
      var q = e.target.value.trim().toLowerCase();
      qsa("#allClientsBody tr").forEach(function(tr){
        tr.style.display = tr.textContent.toLowerCase().indexOf(q) > -1 ? "" : "none";
      });
    });

    renderRecent();
  }

  /* ---------- 8.5 Détail client admin (localStorage, ETU1944) ---------- */
  function initClientDetail(){
    var db = loadDB();
    var id = Number(new URLSearchParams(window.location.search).get("id"));
    var c = db.compte.find(function(x){ return x.id === id; });

    if(!c){
      qs("#page-client-detail").innerHTML = '<div class="empty-state">Client introuvable. <a href="comptes.html" style="color:var(--gold-soft)">Retour à la liste</a></div>';
      return;
    }
    recordRecent(id);

    qs("#clientNom").textContent = c.nom;
    qs("#clientNumero").textContent = c.numero;
    qs("#clientAvatar").textContent = initials(c.nom);
    qs("#clientSolde").textContent = formatAr(c.soldeActuel);
    qs("#clientCreation").textContent = formatDate(c.creation);

    var mouvements = db.mvmtCompte
      .filter(function(m){ return m.idCompte === c.id; })
      .sort(function(a,b){ return b.id - a.id; });

    qs("#clientNbMvmt").textContent = mouvements.length;

    var body = qs("#mvmtTableBody");
    if(!mouvements.length){
      body.innerHTML = '<tr><td colspan="4"><div class="empty-state">Aucun mouvement pour ce compte.</div></td></tr>';
    } else {
      body.innerHTML = "";
      mouvements.forEach(function(m){
        var typeMv = db.typeMvmtComp.find(function(t){ return t.id === m.idType; });
        var isIn = typeMv && typeMv.type === "debit";
        var opStr = opTypeById(db, m.indTypeOp);
        var tr = document.createElement("tr");
        tr.innerHTML =
          '<td class="td-mono td-muted">#'+m.id+'</td>'+
          '<td><span class="badge '+opStr+'">'+opTypeLabel(opStr)+'</span></td>'+
          '<td><div class="ledger '+(isIn?"in":"out")+'"><span class="amt">'+formatAr(m.valeur)+'</span></div></td>'+
          '<td class="td-muted">'+formatDate(m.date)+'</td>';
        body.appendChild(tr);
      });
    }
  }

  /* ---------- 8.6 Login client (API CI4, ETU4269) ---------- */
  function initClientLogin(){
    var form = qs("#formClientLogin");
    form.addEventListener("submit", function(e){
      e.preventDefault();
      var numero = qs("#loginNumero").value.trim();
      var err = qs("#loginError");
      err.classList.remove("show");

      apiSend("/api/auth/login", "POST", { numero: numero })
        .then(function(res){
          // Session serveur créée. On garde un miroir local pour les
          // pages pas encore migrées (dashboard, historique local).
          setClientSession(numero);

          // On synchronise aussi le compte dans le localStorage local
          // s'il n'existe pas encore (pour compat avec initClientDashboard).
          var db = loadDB();
          var exists = db.compte.find(function(c){ return c.numero === numero; });
          if(!exists && res.compte){
            db.compte.push({
              id: res.compte.id,
              numero: res.compte.numero,
              nom: res.compte.nom,
              soldeActuel: Number(res.compte.soldeActuel),
              creation: res.compte.creation
            });
            saveDB(db);
          } else if(exists && res.compte){
            exists.soldeActuel = Number(res.compte.soldeActuel);
            saveDB(db);
          }

          window.location.href = "dashboard.html";
        })
        .catch(function(errRes){
          err.textContent = (errRes && errRes.messages && errRes.messages.error)
            ? errRes.messages.error
            : "Aucun compte associé à ce numéro. Vérifiez et réessayez.";
          err.classList.add("show");
        });
    });
  }

  /* ---------- 8.7 Dashboard client (localStorage, ETU1944) ---------- */
  function initClientDashboard(){
    var db = loadDB();
    var c = requireClient(db);
    if(!c) return;

    qs("#soldeActuel").textContent = formatAr(c.soldeActuel);
    qs("#clientNomHeader").textContent = c.nom;
    qs("#clientNumeroHeader").textContent = c.numero;
    qs("#clientDepuis").textContent = formatDate(c.creation);

    var mouvements = db.mvmtCompte
      .filter(function(m){ return m.idCompte === c.id; })
      .sort(function(a,b){ return b.id - a.id; });

    qs("#nbTransactions").textContent = mouvements.length;

    renderMvmtTable(qs("#last10Body"), mouvements.slice(0,10), db);
  }

  /* ---------- 8.8 Historique client (API CI4, ETU4269) ---------- */
  function initClientHistorique(){
    var db = loadDB();
    var c = requireClient(db);
    if(!c) return;

    apiGet("/api/historique/" + encodeURIComponent(c.numero))
      .then(function(rows){
        var body = qs("#fullHistoBody");
        body.innerHTML = "";
        if(!rows.length){
          body.innerHTML = '<tr><td colspan="4"><div class="empty-state">Aucune transaction pour le moment.</div></td></tr>';
          return;
        }
        rows.forEach(function(m){
          var isIn = m.type_mvmt === "debit";
          var tr = document.createElement("tr");
          tr.innerHTML =
            '<td><span class="badge '+m.type_op+'">'+opTypeLabel(m.type_op)+'</span></td>'+
            '<td><div class="ledger '+(isIn?"in":"out")+'"><span class="amt">'+formatAr(m.valeur)+'</span></div></td>'+
            '<td class="td-muted">'+formatDate(m.date)+'</td>'+
            '<td class="td-muted">'+mvmtTypeLabel(m.type_mvmt)+'</td>';
          body.appendChild(tr);
        });
      })
      .catch(function(err){
        // Fallback localStorage si l'API n'est pas encore prête
        var mouvements = db.mvmtCompte
          .filter(function(m){ return m.idCompte === c.id; })
          .sort(function(a,b){ return b.id - a.id; });
        renderMvmtTable(qs("#fullHistoBody"), mouvements, db);
        console.warn("API historique indisponible, fallback localStorage.", err);
      });
  }

  function renderMvmtTable(body, mouvements, db){
    if(!body) return;
    body.innerHTML = "";
    if(!mouvements.length){
      body.innerHTML = '<tr><td colspan="4"><div class="empty-state">Aucune transaction.</div></td></tr>';
      return;
    }
    mouvements.forEach(function(m){
      var typeMv = db.typeMvmtComp.find(function(t){ return t.id === m.idType; });
      var isIn = typeMv && typeMv.type === "debit";
      var opStr = opTypeById(db, m.indTypeOp);
      var tr = document.createElement("tr");
      tr.innerHTML =
        '<td><span class="badge '+opStr+'">'+opTypeLabel(opStr)+'</span></td>'+
        '<td><div class="ledger '+(isIn?"in":"out")+'"><span class="amt">'+formatAr(m.valeur)+'</span></div></td>'+
        '<td class="td-muted">'+formatDate(m.date)+'</td>'+
        '<td class="td-muted">'+mvmtTypeLabel(typeMv ? typeMv.type : "")+'</td>';
      body.appendChild(tr);
    });
  }

  /* ---------- 8.9 Dépôt (localStorage, ETU1944) / Retrait (API CI4, ETU4269) ---------- */
  function initOpForm(kind){
    var db = loadDB();
    var c = requireClient(db);
    if(!c) return;

    var idtypeOp = kind === "depot" ? 1 : 2;
    qs("#formSoldeActuel").textContent = formatAr(c.soldeActuel);

    var form = qs("#formOp");
    var input = qs("#montantOp");
    var errBox = qs("#opError");
    var previewFrais = qs("#previewFrais");
    var previewTotal = qs("#previewTotal");

    input.addEventListener("input", function(){
      var montant = Number(input.value) || 0;
      var frais = computeFrais(db, idtypeOp, montant);
      previewFrais.textContent = formatAr(frais);
      previewTotal.textContent = formatAr(kind === "depot" ? montant : montant + frais);
    });

    if(kind === "depot"){
      // ----- Dépôt : reste en localStorage (module ETU1944) -----
      form.addEventListener("submit", function(e){
        e.preventDefault();
        var montant = Number(input.value);
        errBox.classList.remove("show");

        if(!montant || montant <= 0){
          errBox.textContent = "Indiquez un montant valide.";
          errBox.classList.add("show");
          return;
        }
        var frais = computeFrais(db, idtypeOp, montant);
        var date = todayISO();
        var mvmtId = nextId(db.mvmtCompte);
        db.mvmtCompte.push({ id:mvmtId, idCompte:c.id, valeur:montant, date:date, idType:1, indTypeOp:idtypeOp });
        db.fraisMvmt.push({ id: nextId(db.fraisMvmt), idMvmtCompt: mvmtId, valeur: frais, typeOp: idtypeOp, date: date });
        db.caisseOp[0].gains += frais;
        c.soldeActuel += montant;

        saveDB(db);
        toast("Dépôt effectué avec succès.");
        form.reset();
        previewFrais.textContent = formatAr(0);
        previewTotal.textContent = formatAr(0);
        qs("#formSoldeActuel").textContent = formatAr(c.soldeActuel);
      });
      return;
    }

    // ----- Retrait : appel API CI4 (module ETU4269) -----
    form.addEventListener("submit", function(e){
      e.preventDefault();
      var montant = Number(input.value);
      errBox.classList.remove("show");

      if(!montant || montant <= 0){
        errBox.textContent = "Indiquez un montant valide.";
        errBox.classList.add("show");
        return;
      }

      apiSend("/api/retrait", "POST", { montant: montant })
        .then(function(res){
          // Synchronisation du solde local pour les pages non migrées.
          c.soldeActuel = res.nouveauSolde;
          saveDB(db);

          toast("Retrait effectué avec succès (frais : " + formatAr(res.frais) + ").");
          form.reset();
          previewFrais.textContent = formatAr(0);
          previewTotal.textContent = formatAr(0);
          qs("#formSoldeActuel").textContent = formatAr(c.soldeActuel);
        })
        .catch(function(errRes){
          errBox.textContent = (errRes && errRes.messages && errRes.messages.error)
            ? errRes.messages.error
            : "Erreur lors du retrait. Vérifiez votre solde.";
          errBox.classList.add("show");
        });
    });
  }

  /* ---------- 8.10 Transfert (localStorage, ETU1944) ---------- */
  function initTransfert(){
    var db = loadDB();
    var c = requireClient(db);
    if(!c) return;

    var idtypeOp = 3;
    qs("#formSoldeActuel").textContent = formatAr(c.soldeActuel);

    var form = qs("#formTransfert");
    var input = qs("#montantTransfert");
    var destInput = qs("#destTransfert");
    var errBox = qs("#opError");
    var previewFrais = qs("#previewFrais");
    var previewTotal = qs("#previewTotal");

    input.addEventListener("input", function(){
      var montant = Number(input.value) || 0;
      var frais = computeFrais(db, idtypeOp, montant);
      previewFrais.textContent = formatAr(frais);
      previewTotal.textContent = formatAr(montant + frais);
    });

    form.addEventListener("submit", function(e){
      e.preventDefault();
      errBox.classList.remove("show");
      var montant = Number(input.value);
      var dest = destInput.value.trim();

      if(!dest){ errBox.textContent = "Indiquez le numéro du destinataire."; errBox.classList.add("show"); return; }
      if(dest === c.numero){ errBox.textContent = "Vous ne pouvez pas transférer vers votre propre compte."; errBox.classList.add("show"); return; }
      if(!montant || montant <= 0){ errBox.textContent = "Indiquez un montant valide."; errBox.classList.add("show"); return; }

      var frais = computeFrais(db, idtypeOp, montant);
      if((montant + frais) > c.soldeActuel){
        errBox.textContent = "Solde insuffisant pour ce transfert (frais inclus : " + formatAr(frais) + ").";
        errBox.classList.add("show");
        return;
      }

      var destCompte = db.compte.find(function(x){ return x.numero === dest; });
      if(!destCompte){
        errBox.textContent = "Aucun compte trouvé pour ce numéro destinataire.";
        errBox.classList.add("show");
        return;
      }

      var date = todayISO();
      var mvmtId = nextId(db.mvmtCompte);
      db.mvmtCompte.push({ id:mvmtId, idCompte:c.id, valeur:montant, date:date, idType:2, indTypeOp:idtypeOp });
      db.fraisMvmt.push({ id: nextId(db.fraisMvmt), idMvmtCompt: mvmtId, valeur: frais, typeOp: idtypeOp, date: date });
      db.caisseOp[0].gains += frais;
      c.soldeActuel -= (montant + frais);

      var mvmtId2 = nextId(db.mvmtCompte);
      db.mvmtCompte.push({ id:mvmtId2, idCompte:destCompte.id, valeur:montant, date:date, idType:1, indTypeOp:idtypeOp });
      destCompte.soldeActuel += montant;

      saveDB(db);
      toast("Transfert de " + formatAr(montant) + " envoyé à " + destCompte.nom + ".");
      form.reset();
      previewFrais.textContent = formatAr(0);
      previewTotal.textContent = formatAr(0);
      qs("#formSoldeActuel").textContent = formatAr(c.soldeActuel);
    });
  }

  /* ---------- helper session client ---------- */
  function requireClient(db){
    var c = currentCompte(db);
    if(!c){ window.location.href = "login.html"; return null; }
    return c;
  }

})();