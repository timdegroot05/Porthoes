<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: admin_login.php');
    exit;
}

require_once __DIR__ . '/../../includes/db.php';

$result = $conn->query("
    SELECT id, naam, max_deelnemers, prijs 
    FROM Activiteiten 
    ORDER BY id DESC
");
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Activiteiten beheren</title>

<style>
/* =========================
   KLEURENPALET (FIGMA)
   ========================= */
:root{
  --green-dark:#658C6E;
  --green:#85A898;
  --green-light:#EFF9E8;
  --sand:#F5E2B0;
  --yellow:#DFCD80;
  --ink:#453E3E;

  --bg: var(--green-light);
  --surface:#ffffff;
  --border: rgba(69,62,62,.18);
  --shadow: 0 10px 24px rgba(69,62,62,.12);
  --radius:16px;

  /* toegankelijkheid */
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
}

/* =========================
   LAYOUT
   ========================= */
.container{
  max-width:1100px;
  margin:0 auto;
}

.page-intro{
  display:flex;
  justify-content:space-between;
  gap:16px;
  padding:18px;
  background:rgba(255,255,255,.8);
  border:1px solid var(--border);
  border-radius:var(--radius);
  box-shadow:var(--shadow);
}

.page-title{
  margin:0;
  font-size:22px;
}

.page-subtitle{
  margin-top:6px;
  font-size:13px;
  color:rgba(69,62,62,.75);
  max-width:70ch;
}

.actions{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}

/* =========================
   BUTTONS (niet alleen kleur)
   ========================= */
.btn{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:10px 14px;
  font-size:13px;
  font-weight:700;
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

.btn-primary{
  background:linear-gradient(
    180deg,
    rgba(101,140,110,.25),
    rgba(133,168,152,.25)
  );
  border-color:rgba(101,140,110,.5);
}

.btn-danger{
  background:rgba(122,46,46,.08);
  border-color:rgba(122,46,46,.45);
}

.btn-danger:hover{
  background:rgba(122,46,46,.14);
}

/* keyboard focus */
a:focus-visible,
button:focus-visible{
  outline:3px solid var(--focus);
  outline-offset:2px;
}

/* =========================
   CARD + TABLE
   ========================= */
.card{
  margin-top:18px;
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  box-shadow:var(--shadow);
  overflow:hidden;
}

.table-wrap{ overflow:auto; }

table{
  width:100%;
  border-collapse:separate;
  border-spacing:0;
  min-width:760px;
}

thead th{
  position:sticky;
  top:0;
  background:linear-gradient(
    180deg,
    rgba(245,226,176,.8),
    rgba(223,205,128,.5)
  );
  text-transform:uppercase;
  font-size:11px;
  letter-spacing:.08em;
  padding:14px;
  border-bottom:1px solid var(--border);
  text-align:left;
}

tbody td{
  padding:14px;
  border-bottom:1px solid rgba(69,62,62,.12);
  font-size:14px;
}

tbody tr:nth-child(even){
  background:rgba(239,249,232,.55);
}

tbody tr:hover{
  background:rgba(133,168,152,.2);
  box-shadow:inset 0 0 0 2px rgba(101,140,110,.25);
}

.col-actions{
  white-space:nowrap;
}

/* =========================
   UI ELEMENTEN
   ========================= */
.badge{
  display:inline-block;
  padding:6px 10px;
  border-radius:999px;
  font-size:12px;
  font-weight:800;
  background:rgba(245,226,176,.45);
  border:1px solid rgba(69,62,62,.2);
}

.price{
  font-weight:800;
  font-variant-numeric:tabular-nums;
}

.stack{
  display:inline-flex;
  gap:10px;
  align-items:center;
}

form.inline{ display:inline; }

/* =========================
   RESPONSIVE
   ========================= */
@media (max-width:720px){
  .page-intro{
    flex-direction:column;
  }
  table{
    min-width:640px;
  }
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
      <h1 class="page-title">Activiteiten beheren</h1>
      <p class="page-subtitle">
        Beheer activiteiten. De interface gebruikt hoog contrast en duidelijke focus-staten
        zodat hij goed werkt bij kleurenblindheid en toetsenbordgebruik.
      </p>
    </div>

    <div class="actions">
      <a href="admin_dashboard.php" class="btn">← Dashboard</a>
      <a href="admin_activiteit_form.php" class="btn btn-primary">
        + Nieuwe activiteit
      </a>
    </div>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>Max. deelnemers</th>
            <th>Prijs</th>
            <th class="col-actions">Acties</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= (int)$row['id'] ?></td>
            <td><?= htmlspecialchars($row['naam']) ?></td>
            <td><span class="badge"><?= (int)$row['max_deelnemers'] ?></span></td>
            <td class="price">€ <?= number_format($row['prijs'], 2, ',', '.') ?></td>
            <td class="col-actions">
              <div class="stack">
                <a class="btn"
                   href="admin_activiteit_form.php?id=<?= (int)$row['id'] ?>">
                   Bewerken
                </a>

                <form class="inline"
                      method="post"
                      action="admin_activiteit_delete.php"
                      onsubmit="return confirm('Weet je zeker dat je deze activiteit wilt verwijderen?');">
                  <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                  <input type="hidden" name="csrf_token"
                         value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                  <button class="btn btn-danger" type="submit">
                    Verwijderen
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</body>
</html>
