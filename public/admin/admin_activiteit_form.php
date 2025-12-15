<?php
session_start();

if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: admin_login.php');
    exit;
}

require_once __DIR__ . '/../../includes/db.php';

$errors = [];
$id = (int)($_GET['id'] ?? 0);

$naam = '';
$beschrijving = '';
$max_deelnemers = '';
$prijs = '';

$isEdit = $id > 0;

// Bij bewerken: huidige data ophalen
if ($isEdit && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT naam, beschrijving, max_deelnemers, prijs FROM Activiteiten WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();

    if (!$data) {
        die("Activiteit niet gevonden.");
    }

    $naam = $data['naam'] ?? '';
    $beschrijving = $data['beschrijving'] ?? '';
    $max_deelnemers = $data['max_deelnemers'] ?? '';
    $prijs = $data['prijs'] ?? '';
}

// Opslaan (toevoegen of updaten)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = trim($_POST['naam'] ?? '');
    $beschrijving = trim($_POST['beschrijving'] ?? '');
    $max_deelnemers = trim($_POST['max_deelnemers'] ?? '');
    $prijs = trim($_POST['prijs'] ?? '');

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
                SET naam = ?, beschrijving = ?, max_deelnemers = ?, prijs = ?
                WHERE id = ?
            ");
            // i en d kunnen niet null zonder extra werk; dus zetten we lege velden naar 0 of NULL via workaround:
            // Simpel: als je NULL wilt toestaan, maak max_deelnemers/prijs in DB NULLable.
            $max_db = $max_int ?? 0;
            $prijs_db = $prijs_val ?? 0.00;

            $stmt->bind_param("ssidi", $naam, $beschrijving, $max_db, $prijs_db, $id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("
                INSERT INTO Activiteiten (naam, beschrijving, max_deelnemers, prijs)
                VALUES (?, ?, ?, ?)
            ");
            $max_db = $max_int ?? 0;
            $prijs_db = $prijs_val ?? 0.00;

            $stmt->bind_param("ssid", $naam, $beschrijving, $max_db, $prijs_db);
            $stmt->execute();
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
  <title><?= $isEdit ? 'Activiteit bewerken' : 'Activiteit toevoegen' ?></title>
</head>
<body>
  <h1><?= $isEdit ? 'Activiteit bewerken' : 'Activiteit toevoegen' ?></h1>

  <p><a href="admin_activiteiten.php">← Terug naar overzicht</a></p>

  <?php if (!empty($errors)): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post">
    <label>
      Naam*:<br>
      <input type="text" name="naam" value="<?= htmlspecialchars($naam) ?>" required>
    </label>
    <br><br>

    <label>
      Beschrijving:<br>
      <textarea name="beschrijving" rows="5" cols="40"><?= htmlspecialchars($beschrijving) ?></textarea>
    </label>
    <br><br>

    <label>
      Max deelnemers:<br>
      <input type="number" name="max_deelnemers" min="0" value="<?= htmlspecialchars((string)$max_deelnemers) ?>">
    </label>
    <br><br>

    <label>
      Prijs (bijv 0.00):<br>
      <input type="text" name="prijs" value="<?= htmlspecialchars((string)$prijs) ?>">
    </label>
    <br><br>

    <button type="submit"><?= $isEdit ? 'Opslaan' : 'Toevoegen' ?></button>
  </form>
</body>
</html>
