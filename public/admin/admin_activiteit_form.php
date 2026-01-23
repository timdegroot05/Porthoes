<?php
session_start();

if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

require_once __DIR__ . '/../../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;

$errors = [];
$naam = '';
$beschrijving = '';
$max_deelnemers = '';
$prijs = '';
$tag = ''; // blijft bestaan: we slaan uiteindelijk weer op als string

/* =========================
   NIEUW: haal alle filter-opties uit DB (uniek)
========================= */
$allFilters = [];
$res = $conn->query("SELECT tag FROM Activiteiten WHERE tag IS NOT NULL AND tag <> ''");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $parts = preg_split('/[,;]/', (string)$r['tag']);
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p !== '') $allFilters[$p] = true;
        }
    }
}
$allFilters = array_keys($allFilters);

/* =========================
   NIEUW: voeg extra filters toe
========================= */
$extraFilters = ['NonGast', '18+', 'Binnen', 'Buiten', 'Workshop', 'Gratis'];
$allFilters = array_merge($allFilters, $extraFilters);
$allFilters = array_unique($allFilters);
sort($allFilters, SORT_NATURAL | SORT_FLAG_CASE);

/* =========================
   BEWERKEN: bestaande data ophalen
========================= */
if ($isEdit) {
    // tag meenemen in SELECT
    $stmt = $conn->prepare("SELECT naam, beschrijving, max_deelnemers, prijs, tag FROM Activiteiten WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($naam, $beschrijving, $max_deelnemers, $prijs, $tag);

    if (!$stmt->fetch()) {
        $stmt->close();
        die("Activiteit niet gevonden.");
    }
    $stmt->close();
}

/* =========================
   NIEUW: geselecteerde filters als array voor de UI
========================= */
$selectedFilters = array_filter(array_map('trim', preg_split('/[,;]/', (string)$tag)));
$selectedFilters = array_values(array_unique($selectedFilters));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = trim($_POST['naam'] ?? '');
    $beschrijving = trim($_POST['beschrijving'] ?? '');
    $max_deelnemers = trim($_POST['max_deelnemers'] ?? '');
    $prijs = trim($_POST['prijs'] ?? '');

    /* =========================
       NIEUW: tags komen nu uit checkboxes
    ========================= */
    $postedFilters = $_POST['tags'] ?? [];
    if (!is_array($postedFilters)) $postedFilters = [];

    // alleen toestaan wat in de DB-opties zit
    $allowed = array_flip($allFilters);
    $postedFilters = array_values(array_unique(array_filter(array_map('trim', $postedFilters))));
    $postedFilters = array_values(array_filter($postedFilters, fn($t) => isset($allowed[$t])));

    // opslaan als "tag" string (zoals jouw DB nu werkt)
    $tag = implode(', ', $postedFilters);

    // zodat form opnieuw correct checked wordt als er errors zijn
    $selectedFilters = $postedFilters;

    if ($naam === '') {
        $errors[] = "Naam is verplicht.";
    }

    if ($max_deelnemers !== '' && (!ctype_digit($max_deelnemers) || (int)$max_deelnemers < 0)) {
        $errors[] = "Max deelnemers moet een positief getal zijn (of leeg).";
    }

    // prijs mag 0.00 zijn; maak komma → punt
    $prijs_norm = str_replace(',', '.', $prijs);
    if ($prijs_norm !== '' && (!is_numeric($prijs_norm) || (float)$prijs_norm < 0)) {
        $errors[] = "Prijs moet een geldig positief bedrag zijn (bijv 0.00).";
    }

    if (empty($errors)) {
        $max_int = ($max_deelnemers === '') ? null : (int)$max_deelnemers;
        $prijs_val = ($prijs_norm === '') ? null : (float)$prijs_norm;

        if ($isEdit) {
            $stmt = $conn->prepare("
                UPDATE Activiteiten
                SET naam = ?, beschrijving = ?, max_deelnemers = ?, prijs = ?, tag = ?
                WHERE id = ?
            ");
            $max_db = $max_int ?? 0;
            $prijs_db = $prijs_val ?? 0.00;

            $stmt->bind_param("ssidsi", $naam, $beschrijving, $max_db, $prijs_db, $tag, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("
                INSERT INTO Activiteiten (naam, beschrijving, max_deelnemers, prijs, tag)
                VALUES (?, ?, ?, ?, ?)
            ");
            $max_db = $max_int ?? 0;
            $prijs_db = $prijs_val ?? 0.00;

            $stmt->bind_param("ssids", $naam, $beschrijving, $max_db, $prijs_db, $tag);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: admin_activiteiten.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $isEdit ? 'Activiteit bewerken' : 'Activiteit toevoegen' ?></title>

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
    html, body{ height:100%; }
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

    .container{ max-width:900px; margin:0 auto; }

    .page-intro{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:16px;
      padding:18px;
      background:rgba(255,255,255,.82);
      border:1px solid var(--border);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
    }

    .page-title{
      margin:0;
      font-size:26px;
      font-weight:800;
      letter-spacing:.2px;
    }

    .page-subtitle{
      margin:8px 0 0;
      font-size:15px;
      font-weight:500;
      color:rgba(69,62,62,.80);
      max-width:70ch;
    }

    .actions{ display:flex; gap:10px; flex-wrap:wrap; }

    .btn{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:12px 16px;
      font-size:14px;
      font-weight:800;
      border-radius:12px;
      border:1.5px solid var(--border);
      background:var(--surface);
      color:var(--ink);
      text-decoration:none;
      cursor:pointer;
      box-shadow:0 2px 0 rgba(69,62,62,.10);
      transition:.15s;
    }
    .btn:hover{ background:rgba(239,249,232,.9); }
    .btn:active{ transform:translateY(1px); }

    .btn-primary{
      background:linear-gradient(180deg, rgba(101,140,110,.25), rgba(133,168,152,.25));
      border-color:rgba(101,140,110,.50);
    }

    a:focus-visible, button:focus-visible, input:focus-visible, textarea:focus-visible{
      outline:3px solid var(--focus);
      outline-offset:2px;
      border-radius:12px;
    }

    .card{
      margin-top:18px;
      background:var(--surface);
      border:1px solid var(--border);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
      overflow:hidden;
    }

    .card-header{
      padding:16px 18px;
      background:linear-gradient(180deg, rgba(245,226,176,.80), rgba(223,205,128,.50));
      border-bottom:1px solid var(--border);
    }
    .card-header strong{
      font-size:14px;
      letter-spacing:.08em;
      text-transform:uppercase;
    }

    .card-body{ padding:18px; }

    .form-grid{
      display:grid;
      grid-template-columns:1fr;
      gap:14px;
    }

    label{ display:block; font-weight:800; }
    .hint{
      display:block;
      margin-top:6px;
      font-weight:600;
      font-size:13px;
      color:rgba(69,62,62,.75);
    }

    input[type="text"], input[type="number"], textarea{
      width:100%;
      margin-top:8px;
      padding:12px 12px;
      border-radius:12px;
      border:1.5px solid rgba(69,62,62,.22);
      background:#fff;
      color:var(--ink);
      font-size:16px;
      font-weight:600;
    }

    textarea{ resize:vertical; min-height:140px; }

    .row-2{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:14px;
    }

    .required{
      display:inline-flex;
      align-items:center;
      gap:8px;
    }
    .required .dot{
      width:10px; height:10px; border-radius:999px;
      background: var(--yellow);
      border:1px solid rgba(69,62,62,.25);
      flex:0 0 auto;
    }

    .errors{
      margin:16px 0 0;
      padding:14px 14px;
      border-radius:14px;
      border:1px solid rgba(122,46,46,.35);
      background: rgba(122,46,46,.06);
    }
    .errors-title{
      display:flex;
      align-items:center;
      gap:10px;
      font-weight:900;
      margin:0 0 10px;
    }
    .errors-title .icon{
      width:22px; height:22px;
      display:inline-grid;
      place-items:center;
      border-radius:999px;
      border:1px solid rgba(122,46,46,.35);
      background: rgba(122,46,46,.10);
      font-size:13px;
      line-height:1;
    }
    .errors ul{ margin:0; padding-left:18px; }
    .errors li{ margin:6px 0; font-weight:650; }

    .footer-actions{
      margin-top:16px;
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      justify-content:flex-end;
      padding:0 18px 18px;
    }

    /* ===== NIEUW: checkbox filter UI ===== */
    .filters-grid{
      display:grid;
      grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
      gap:10px;
      margin-top:10px;
    }
    .filter-item{
      display:flex;
      align-items:center;
      gap:10px;
      padding:10px 12px;
      border:1.5px solid rgba(69,62,62,.18);
      border-radius:12px;
      background:rgba(239,249,232,.55);
    }
    .filter-item input{
      width:18px;
      height:18px;
      margin:0;
    }

    @media (max-width:720px){
      body{ padding:18px 12px 34px; }
      .page-intro{ flex-direction:column; }
      .row-2{ grid-template-columns:1fr; }
      .footer-actions{ justify-content:flex-start; }
    }

    textarea[name="beschrijving"]{
      font-size: 18px;
      font-weight: 400;
      line-height: 1.6;
      letter-spacing: 0.3px;
      font-family: Arial, Helvetica, sans-serif;
    }
  </style>
</head>
<body>
<div class="container">

  <div class="page-intro">
    <div>
      <h1 class="page-title"><?= $isEdit ? 'Activiteit bewerken' : 'Activiteit toevoegen' ?></h1>
      <p class="page-subtitle">
        Vul de velden in en sla op. Velden met een markering zijn verplicht.
      </p>
    </div>
    <div class="actions">
      <a class="btn" href="admin_activiteiten.php">← Terug</a>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <strong>Gegevens</strong>
    </div>
    <div class="card-body">

      <?php if (!empty($errors)): ?>
        <div class="errors" role="alert" aria-live="polite">
          <p class="errors-title"><span class="icon">!</span>Controleer de volgende punten</p>
          <ul>
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post">
        <div class="form-grid">
          <div>
            <label class="required">
              <span class="dot" aria-hidden="true"></span>
              Naam <span aria-hidden="true">*</span>
            </label>
            <span class="hint">Korte, duidelijke titel van de activiteit.</span>
            <input type="text" name="naam" value="<?= htmlspecialchars($naam) ?>" required>
          </div>

          <div>
            <label>Beschrijving</label>
            <span class="hint">Optioneel: wat houdt de activiteit in?</span>
            <textarea name="beschrijving"><?= htmlspecialchars($beschrijving) ?></textarea>
          </div>

          <!-- ✅ NIEUW: Filters kiezen uit DB-opties -->
          <div>
            <label>Filters</label>
            <span class="hint">Je kunt alleen kiezen uit bestaande filters die al in de database voorkomen.</span>

            <?php if (empty($allFilters)): ?>
              <div class="filter-item" style="background: rgba(245,226,176,.35);">
                Er zijn nog geen filters in de database. Voeg eerst een activiteit toe met een tag, of maak een aparte filter-tabel.
              </div>
            <?php else: ?>
              <div class="filters-grid">
                <?php foreach ($allFilters as $f): ?>
                  <?php $checked = in_array($f, $selectedFilters, true); ?>
                  <label class="filter-item">
                    <input type="checkbox" name="tags[]" value="<?= htmlspecialchars($f) ?>" <?= $checked ? 'checked' : '' ?>>
                    <span><?= htmlspecialchars($f) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="row-2">
            <div>
              <label>Max deelnemers</label>
              <span class="hint">Laat leeg als dit niet van toepassing is.</span>
              <input type="number" name="max_deelnemers" min="0" value="<?= htmlspecialchars((string)$max_deelnemers) ?>">
            </div>

            <div>
              <label>Prijs</label>
              <span class="hint">Gebruik bijvoorbeeld 0.00 (of 0,00).</span>
              <input type="text" name="prijs" value="<?= htmlspecialchars((string)$prijs) ?>">
            </div>
          </div>
        </div>

        <div class="footer-actions">
          <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Opslaan' : 'Toevoegen' ?></button>
        </div>
      </form>

    </div>
  </div>

</div>
</body>
</html>
