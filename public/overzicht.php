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
    body {
        font-family: "Segoe UI", Tahoma, sans-serif;
        background-color: #EFF9E8;
        color: #2f3e34;
        padding: 2rem;
    }

    h2 {
        text-align: center;
        color: #658C6E;
        margin-bottom: 1.5rem;
        font-size: 2rem;
    }

    form {
        max-width: 500px;
        margin: 0 auto 2rem;
        padding: 1rem 1.2rem;
        background: #F5E2B0;
        border-radius: 8px;
        border: 1px solid #85A898;
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    label {
        font-weight: bold;
        font-size: 1rem;
    }

    select {
        font-size: 1rem;
        padding: 0.6rem;
        border-radius: 6px;
        border: 1px solid #85A898;
        background: #EFF9E8;
        cursor: pointer;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: #F5E2B0;
        border-radius: 8px;
        overflow: hidden;
    }

    th, td {
        border: 1px solid #85A898;
        padding: 0.8rem 1rem;
        text-align: left;
        vertical-align: top;
        font-size: 0.95rem;
    }

    th {
        background-color: #658C6E;
        color: #EFF9E8;
        font-size: 1rem;
    }

    tr:nth-child(even) {
        background-color: #EFF9E8;
    }

    tr:hover {
        background-color: #DFCD80;
    }

    option {
        font-size: 1rem;
    }

</style>


</body>
</html>

