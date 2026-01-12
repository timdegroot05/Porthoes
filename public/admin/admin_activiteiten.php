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

$result = $conn->query("SELECT id, naam, max_deelnemers, prijs FROM Activiteiten ORDER BY id DESC");
if (!$result) {
    die("Query fout: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
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

  /* 🔹 GROTER & LEESBAARDER */
  font-size:16px;
  line-height:1.6;
  font-weight:500;
}

/* =========================
   PAGE INTRO
   ========================= */
.page-title{
  margin:0;
  font-size:26px;        /* was 22px */
  font-weight:800;
}

.page-subtitle{
  margin-top:8px;
  font-size:15px;        /* was 13px */
  font-weight:500;
  color:rgba(69,62,62,.8);
  max-width:70ch;
}

/* =========================
   BUTTONS
   ========================= */
.btn{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:12px 16px;     /* iets groter */
  font-size:14px;
  font-weight:800;       /* dikker */
  border-radius:12px;
  border:1.5px solid var(--border);
  background:var(--surface);
  color:var(--ink);
  text-decoration:none;
  cursor:pointer;
  box-shadow:0 2px 0 rgba(69,62,62,.1);
  transition:.15s;
}

/* =========================
   TABLE
   ========================= */
thead th{
  position:sticky;
  top:0;
  background:linear-gradient(
    180deg,
    rgba(245,226,176,.8),
    rgba(223,205,128,.5)
  );
  text-transform:uppercase;
  font-size:12px;        /* was 11px */
  font-weight:800;
  letter-spacing:.1em;
  padding:16px;
  border-bottom:1px solid var(--border);
  text-align:left;
}

tbody td{
  padding:16px;
  border-bottom:1px solid rgba(69,62,62,.12);
  font-size:15px;        /* was 14px */
  font-weight:500;
}

/* =========================
   BADGE & PRIJS
   ========================= */
.badge{
  display:inline-block;
  padding:7px 12px;
  border-radius:999px;
  font-size:13px;
  font-weight:900;       /* extra duidelijk */
  background:rgba(245,226,176,.45);
  border:1px solid rgba(69,62,62,.2);
}

.price{
  font-weight:900;
  font-size:15px;
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
  <h1>Activiteiten beheren</h1>

  <p>
    <a href="admin_dashboard.php">← Terug naar dashboard</a> |
    <a href="admin_activiteit_form.php">+ Nieuwe activiteit</a>
  </p>

  <table border="1" cellpadding="8" cellspacing="0">
    <thead>
      <tr>
        <th>ID</th>
        <th>Naam</th>
        <th>Max deelnemers</th>
        <th>Prijs</th>
        <th>Acties</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= (int)$row['id'] ?></td>
          <td><?= htmlspecialchars($row['naam']) ?></td>
          <td><?= htmlspecialchars((string)$row['max_deelnemers']) ?></td>
          <td>€ <?= htmlspecialchars((string)$row['prijs']) ?></td>
          <td>
            <a href="admin_activiteit_form.php?id=<?= (int)$row['id'] ?>">Bewerken</a>
            
                <form method="post" action="admin_activiteit_delete.php" style="display:inline;"
                    onsubmit="return confirm('Weet je zeker dat je deze activiteit wilt verwijderen?');">
                <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <button type="submit">Verwijderen</button>
                </form>

          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
