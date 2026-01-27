<?php
$host = "localhost";
$dbname = "ActiviteitenDB";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database fout: " . $e->getMessage());
}


// Haal alle activiteiten uit de database
$activiteiten = $pdo->query("SELECT naam FROM Activiteiten ORDER BY naam")->fetchAll(PDO::FETCH_ASSOC);

// Check of er een filter is gekozen
$filter = $_GET['activiteit'] ?? '';



if ($filter) {
    $stmt = $pdo->prepare("SELECT * FROM inschrijvingen WHERE activity = ? ORDER BY aangemeld_op DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = $pdo->query("SELECT * FROM inschrijvingen ORDER BY aangemeld_op DESC");
}

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Overzicht inschrijvingen</title>
</head>
<body>

<a href="admin/admin_dashboard.php" class="btn back-btn">← Dashboard</a>


<form method="GET">
    <label for="activiteit">Selecteer activiteit:</label>
    <select name="activiteit" id="activiteit" onchange="this.form.submit()">
        <option value="">-- Alle activiteiten --</option>
        <?php foreach ($activiteiten as $act): ?>
            <option value="<?= htmlspecialchars($act['naam']) ?>" <?= ($act['naam'] == $filter) ? 'selected' : '' ?>>
                <?= htmlspecialchars(ucfirst($act['naam'])) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>


<h2>Inschrijvingen overzicht</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Activiteit</th>
        <th>Naam aanmelder</th>
        <th>Email</th>
        <th>Kampeerplek</th>
        <th>Aantal personen</th>
        <th>Personen + Leeftijden</th>
        <th>Aangemeld op</th>
    </tr>

    <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['activity']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= $row['kampeerplek'] ?></td>
            <td><?= $row['aantal_personen'] ?></td>
            <td>
                <?php
                $personen = json_decode($row['personen_json'], true);

                if ($personen) {
                    foreach ($personen as $p) {
                        echo htmlspecialchars($p['naam']) . " (" . htmlspecialchars($p['leeftijd']) . " jaar)<br>";
                    }
                } else {
                    echo "Geen personen gevonden";
                }
                ?>
            </td>
            <td><?= $row['aangemeld_op'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

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
}

*{
  box-sizing:border-box;
  font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
}

body{
  margin:0;
  background:
    radial-gradient(900px 400px at 10% 0%, rgba(133,168,152,.35), transparent 60%),
    radial-gradient(800px 380px at 90% 0%, rgba(223,205,128,.35), transparent 55%),
    linear-gradient(180deg, var(--bg), #fff 70%);
  color:var(--ink);
  min-height:100vh;
  padding:28px 16px 44px;
}


/* TITEL */

h2{
  text-align:center;
  font-size:26px;
  font-weight:800;
  margin-bottom:24px;
  color:var(--ink);
}


/* FILTER FORM */

form{
  max-width:500px;
  margin:0 auto 24px;
  padding:16px;
  background:rgba(255,255,255,.85);
  border:1px solid var(--border);
  border-radius:var(--radius);
  box-shadow:var(--shadow);
  display:flex;
  flex-direction:column;
  gap:10px;
}

label{
  font-weight:700;
  font-size:14px;
}

select{
  padding:10px 12px;
  border-radius:12px;
  border:1.5px solid var(--border);
  background:var(--surface);
  font-size:14px;
  cursor:pointer;
  transition:.15s;
}

select:hover{
  background:rgba(239,249,232,.9);
}

select:focus{
  outline:none;
  border-color:var(--green-dark);
}


/* TABEL */

table{
  width:100%;
  border-collapse:collapse;
  background:var(--surface);
  border-radius:var(--radius);
  overflow:hidden;
  box-shadow:var(--shadow);
  border:1px solid var(--border);
}

th, td{
  padding:12px 14px;
  border-bottom:1px solid var(--border);
  text-align:left;
  vertical-align:top;
  font-size:14px;
}

th{
  background:rgba(101,140,110,.15);
  font-weight:800;
  color:var(--ink);
}

tr:last-child td{
  border-bottom:none;
}

tr:hover{
  background:rgba(239,249,232,.7);
}


/* MOBIEL */

@media (max-width:768px){

  body{
    padding:18px 12px 32px;
  }

  table{
    font-size:13px;
  }

  th, td{
    padding:10px;
  }
}

/* TERUGKNOP */

.back-btn{
  position:fixed;
  top:20px;
  left:20px;

  display:inline-flex;
  align-items:center;
  gap:6px;

  padding:10px 14px;

  font-size:14px;
  font-weight:800;

  border-radius:12px;
  border:1.5px solid var(--border);

  background:var(--surface);
  color:var(--ink);

  text-decoration:none;

  box-shadow:0 2px 0 rgba(69,62,62,.1);

  transition:.15s;

  z-index:1000;
}

.back-btn:hover{
  background:rgba(239,249,232,.9);
  transform:translateY(-1px);
}

</style>



</body>
</html>

