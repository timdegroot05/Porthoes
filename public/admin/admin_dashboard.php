<?php
session_start();

if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: admin_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin dashboard</title>

<style>
:root{
  --green-dark:#658C6E;
  --green:#85A898;
  --green-light:#EFF9E8;
  --yellow:#DFCD80;
  --sand:#F5E2B0;
  --ink:#453E3E;

  --bg: var(--green-light);
  --surface:#ffffff;
  --border: rgba(69,62,62,.18);
  --shadow: 0 10px 24px rgba(69,62,62,.12);
  --radius:16px;

  --focus:#1d4ed8;
  --danger:#7a2e2e;
}

*{ box-sizing:border-box; }

body{
  margin:0;
  font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
  color:var(--ink);
  background:
    radial-gradient(900px 400px at 10% 0%, rgba(133,168,152,.35), transparent 60%),
    radial-gradient(800px 380px at 90% 0%, rgba(223,205,128,.35), transparent 55%),
    linear-gradient(180deg, var(--bg), #fff 70%);
  min-height:100vh;
  padding:28px 16px 44px;

  font-size:16px;
  line-height:1.6;
  font-weight:500;
}

.container{
  max-width:1000px;
  margin:0 auto;
}

/* =========================
   PAGE INTRO
   ========================= */
.page-intro{
  display:flex;
  justify-content:space-between;
  gap:16px;
  padding:18px;
  background:rgba(255,255,255,.85);
  border:1px solid var(--border);
  border-radius:var(--radius);
  box-shadow:var(--shadow);
}

.page-title{
  margin:0;
  font-size:26px;
  font-weight:800;
}

.page-subtitle{
  margin-top:8px;
  font-size:15px;
  color:rgba(69,62,62,.8);
  max-width:65ch;
}

.actions{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}

/* =========================
   BUTTONS
   ========================= */
.btn{
  display:inline-flex;
  align-items:center;
  padding:12px 16px;
  font-size:14px;
  font-weight:800;
  border-radius:12px;
  border:1.5px solid var(--border);
  background:var(--surface);
  color:var(--ink);
  text-decoration:none;
  cursor:pointer;
  box-shadow:0 2px 0 rgba(69,62,62,.1);
  transition:.15s;
}

.btn:hover{
  background:rgba(239,249,232,.9);
}

.btn-danger{
  background:rgba(122,46,46,.08);
  border-color:rgba(122,46,46,.45);
}

.btn-danger:hover{
  background:rgba(122,46,46,.14);
}

a:focus-visible{
  outline:3px solid var(--focus);
  outline-offset:2px;
  border-radius:12px;
}

/* =========================
   DASHBOARD CARDS
   ========================= */
.section{
  margin-top:22px;
}

.section-title{
  font-size:18px;
  font-weight:800;
  margin:0 0 12px;
}

.card-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));
  gap:16px;
}

.card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  box-shadow:var(--shadow);
  padding:18px;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
}

.card h3{
  margin:0 0 8px;
  font-size:17px;
  font-weight:800;
}

.card p{
  margin:0 0 16px;
  font-size:14px;
  color:rgba(69,62,62,.8);
}

.card a{
  align-self:flex-start;
}

/* =========================
   RESPONSIVE
   ========================= */
@media (max-width:720px){
  body{ padding:18px 12px 34px; }
  .page-intro{ flex-direction:column; }
}

/* =========================
   REDUCED MOTION
   ========================= */
@media (prefers-reduced-motion:reduce){
  *{ transition:none !important; }
}
</style>
</head>

<body>
<div class="container">

  <div class="page-intro">
    <div>
      <h1 class="page-title">Admin dashboard</h1>
      <p class="page-subtitle">
        Welkom in het beheerpaneel. Kies een functie om activiteiten te beheren
        of gegevens te bekijken.
      </p>
    </div>
    <div class="actions">
      <a class="btn btn-danger" href="admin_logout.php">Uitloggen</a>
    </div>
  </div>

  <div class="section">
    <h2 class="section-title">Admin functies</h2>

    <div class="card-grid">
      <div class="card">
        <h3>Activiteiten beheren</h3>
        <p>Bekijk, bewerk en verwijder activiteiten.</p>
        <a class="btn" href="admin_activiteiten.php">Open</a>
      </div>

      <div class="card">
        <h3>Activiteiten kaart</h3>
        <p>Bekijk activiteiten op de kaart.</p>
        <a class="btn" href="../activiteitenKaart.php">Open</a>
      </div>

      <div class="card">
        <h3>Alle inschrijvingen</h3>
        <p>Overzicht van alle deelnemers en inschrijvingen.</p>
        <a class="btn" href="../overzicht.php">Open</a>
      </div>

      <div class="card">
        <h3>Inschrijven</h3>
        <p>Test de inschrijfpagina als admin.</p>
        <a class="btn" href="../inschrijven.php">Open</a>
      </div>
    </div>
  </div>

</div>
</body>
</html>
