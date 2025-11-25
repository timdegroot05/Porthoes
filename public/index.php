<?php
    include __DIR__ . '/../includes/db.php';

    // Haal alle activiteiten op
    $sql = "SELECT * FROM Activiteiten";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Activiteiten Overzicht</title>
</head>

<body>

    <h1>Activiteiten</h1>

    <?php if ($result->num_rows > 0): ?>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <strong><?= $row['naam'] ?></strong><br>
                    <?= $row['beschrijving'] ?><br>
                    Max deelnemers: <?= $row['max_deelnemers'] ?><br>
                    Prijs: €<?= $row['prijs'] ?><br>
                    <hr>
                </li>
            <?php endwhile; ?>
        </ul>

    <?php else: ?>
        <p>Geen activiteiten gevonden.</p>
    <?php endif; ?>

</body>

</html>