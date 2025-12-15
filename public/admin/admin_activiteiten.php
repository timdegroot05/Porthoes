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
