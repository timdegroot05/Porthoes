<?php
// Database verbinding
$server = "localhost";
$gebruiker = "root";
$wachtwoord = "";
$database = "ActiviteitenDB";

$conn = new mysqli($server, $gebruiker, $wachtwoord, $database);

// Foutencontrole
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Activiteit ID (kan ook dynamisch worden meegestuurd via GET)
$activiteit_id = 1;

// SQL-query voorbereiden
$sql = "
SELECT 
    A.id AS activiteit_id,
    A.naam AS activiteit_naam,
    D.naam AS deelnemer_naam,
    D.leeftijd
FROM Activiteiten A
JOIN ActiviteitTijden AT ON A.id = AT.activiteit_id
JOIN Reserveringen R ON AT.id = R.activiteittijd_id
JOIN Deelnemers D ON R.id = D.reservering_id
ORDER BY A.id, D.naam
";

$resultaat = $conn->query($sql);

?>


<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Deelnemerslijst</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 350px;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background: #f3f3f3;
        }
    </style>
</head>
<body>

<h2>Deelnemers per activiteit</h2>

<?php
$huidige_activiteit = null;

while ($rij = $resultaat->fetch_assoc()) {

    // Check of we een nieuwe activiteit beginnen
    if ($huidige_activiteit !== $rij['activiteit_id']) {

        // Sluit vorige tabel af (behalve bij de eerste)
        if ($huidige_activiteit !== null) {
            echo "</table><br>";
        }

        // Nieuwe activiteit start
        $huidige_activiteit = $rij['activiteit_id'];

        echo "<h3>" . htmlspecialchars($rij['activiteit_naam']) . "</h3>";
        echo "
        <table>
            <tr>
                <th>Naam</th>
                <th>Leeftijd</th>
            </tr>
        ";
    }

    // Voeg deelnemer toe
    echo "
    <tr>
        <td>" . htmlspecialchars($rij['deelnemer_naam']) . "</td>
        <td>" . htmlspecialchars($rij['leeftijd']) . "</td>
    </tr>
    ";
}

// Sluit laatste tabel af
echo "</table>";
?>

</body>

</html>

<?php
$conn->close();
?>
