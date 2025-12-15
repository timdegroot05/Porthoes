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

$stmt = $pdo->query("SELECT * FROM inschrijvingen ORDER BY aangemeld_op DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Overzicht inschrijvingen</title>
</head>
<body>

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

</body>
</html>

