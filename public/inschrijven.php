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

$activity = isset($_GET['act']) ? $_GET['act'] : "onbekend";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $kampeerplek  = intval($_POST['kampeerplek']);
    $aantal = intval($_POST['aantal']);

    // Personen opslaan in array
    $personen = [];
    for ($i = 1; $i <= $aantal; $i++) {
        $pnaam = $_POST["persoon_naam_$i"] ?? "";
        $pleeftijd = $_POST["persoon_leeftijd_$i"] ?? "";
        if ($pnaam !== "" && $pleeftijd !== "") {
            $personen[] = [
                "naam" => $pnaam,
                "leeftijd" => $pleeftijd
            ];
        }
    }

    // Convert naar JSON voor database
    $personen_json = json_encode($personen);

    if ($email !== "") {
        $stmt = $pdo->prepare("
    INSERT INTO inschrijvingen 
    (activity, name, email, kampeerplek, aantal_personen, personen_json)
    VALUES 
    (:activity, :name, :email, :kampeerplek, :aantal_personen, :personen_json)
");

        $stmt->execute([
            ':activity'         => $activity,
            ':name'             => $name,
            ':email'            => $email,
            ':kampeerplek'      => $kampeerplek,
            ':aantal_personen'  => $aantal,
            ':personen_json'    => $personen_json
        ]);


        $message = "Je bent ingeschreven voor <strong>" . htmlspecialchars($activity) . "</strong>!";
    } else {
        $message = "Vul een emailadres in!";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Inschrijven</title>

    <script>
        function updatePersonFields() {
            let aantal = document.getElementById("aantal").value;
            let container = document.getElementById("personenvelden");
            container.innerHTML = "";

            for (let i = 1; i <= aantal; i++) {
                container.innerHTML += `
                    <h4>Persoon ${i}</h4>
                    <input type="text" name="persoon_naam_${i}" placeholder="Naam persoon ${i}" required><br><br>
                    <input type="number" name="persoon_leeftijd_${i}" placeholder="Leeftijd persoon ${i}" required><br><br>
                `;
            }
        }
    </script>
</head>

<body>

    <h2>Inschrijven voor: <?= htmlspecialchars($activity) ?></h2>

    <?= $message ?>

    <form method="POST">

        <input type="text" name="name" placeholder="Jouw naam"><br><br>

        <input type="email" name="email" placeholder="Email (verplicht)" required><br><br>

        <label>Kampeerplek</label><br>
        <select name="kampeerplek" required>
            <?php for ($i = 1; $i <= 60; $i++): ?>
                <option value="<?= $i ?>">Plek <?= $i ?></option>
            <?php endfor; ?>
        </select>
        <br><br>

        <label>Hoeveel personen wil je aanmelden?</label><br>
        <select name="aantal" id="aantal" onchange="updatePersonFields()" required>
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>
        <br><br>

        <div id="personenvelden"></div>

        <button type="submit">Inschrijven</button>
    </form>

    <script>
        updatePersonFields(); // laat direct 1 persoon zien
    </script>

</body>

</html>