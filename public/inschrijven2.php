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

// Haal alle activiteiten op
$activiteiten = $pdo->query("SELECT naam FROM Activiteiten ORDER BY naam")->fetchAll(PDO::FETCH_ASSOC);

// --- Success message ophalen als redirect is geweest ---
$message = '';
if (isset($_GET['success'])) {
    $message = "Je bent ingeschreven voor " . htmlspecialchars($_GET['success']) . "!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activity = $_POST['activity'] ?? "onbekend";
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $kampeerplek  = intval($_POST['kampeerplek']);
    $aantal = intval($_POST['aantal']);

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

    $personen_json = json_encode($personen);

    if ($email !== "" && $activity !== "") {
        $stmt = $pdo->prepare("
            INSERT INTO inschrijvingen 
            (activity, name, email, kampeerplek, aantal_personen, personen_json)
            VALUES 
            (:activity, :name, :email, :kampeerplek, :aantal_personen, :personen_json)
        ");
        $stmt->execute([
            ':activity' => $activity,
            ':name' => $name,
            ':email' => $email,
            ':kampeerplek' => $kampeerplek,
            ':aantal_personen' => $aantal,
            ':personen_json' => $personen_json
        ]);

        
        header("Location: activiteitenKaart.php?success=" . urlencode($activity));
exit;
 
    } else {
        $message = " Vul alle verplichte velden in!";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inschrijven</title>
<style>
    /* Kleurenpalet boers warm */
    :root {
        --groen-donker: #658C6E;
        --groen-licht: #85A898;
        --crème-licht: #EFF9E8;
        --crème-medium: #F5E2B0;
        --crème-donker: #DFCD80;
    }

/* Verwijder pijltjes bij number inputs */

/* Chrome, Safari, Edge */
  input[type=number]::-webkit-inner-spin-button,
  input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

/* Firefox */
  input[type=number] {
      -moz-appearance: textfield;
  }


    * {
        box-sizing: border-box;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body {
        margin: 0;
        padding: 1rem;
        background: var(--crème-licht);
        color: #2f3e34;
    }

    h2 {
        text-align: center;
        color: var(--groen-donker);
        margin-bottom: 1.5rem;
        font-weight: 700;
    }

    form {
        max-width: 520px;
        margin: auto;
        background: var(--crème-medium);
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    label {
        font-weight: 600;
        margin-bottom: 0.3rem;
    }

    select, input {
        width: 100%;
        padding: 0.7rem;
        margin-bottom: 1rem;
        border-radius: 6px;
        border: 1px solid var(--groen-licht);
        background: var(--crème-licht);
        font-size: 1rem;
    }

    select:focus, input:focus {
        outline: none;
        border-color: var(--groen-donker);
        background: #ffffff;
    }

    button {
        width: 100%;
        padding: 0.8rem;
        background: var(--groen-donker);
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1.05rem;
        transition: background 0.3s;
    }

    button:hover {
        background: var(--groen-licht);
    }

    #personenvelden h4 {
        margin: 0.6rem 0 0.4rem;
        font-size: 0.95rem;
        color: #2f3e34;
    }

    #personenvelden input {
        margin-bottom: 0.6rem;
    }

    .message {
        text-align: center;
        margin-bottom: 1rem;
        font-weight: bold;
        padding: 0.5rem;
        border-radius: 6px;
        background: var(--crème-donker);
        color: #2f3e34;
    }

    @media (min-width: 768px) {
        form {
            padding: 2rem;
        }
    }

    /* Terug naar home knop */
.back-button {
    position: fixed;
    top: 1rem;
    left: 1rem;
    background: var(--groen-donker);
    color: white;
    text-decoration: none;
    padding: 0.6rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.95rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    transition: background 0.3s, transform 0.2s;
    z-index: 1000;
}

.back-button:hover {
    background: var(--groen-licht);
    transform: translateY(-2px);
}

</style>
</head>

<body>

<h2>Inschrijven</h2>

<a href="activiteitenKaart.php" class="back-button">Terug naar home</a>


<?php if($message): ?>
    <div class="message"><?= $message ?></div>
<?php endif; ?>

<form method="POST">
    <label for="activity">Activiteit</label>
    <select name="activity" id="activity" required>
        <option value="">-- Kies een activiteit --</option>
        <?php foreach ($activiteiten as $act): ?>
            <option value="<?= htmlspecialchars($act['naam']) ?>">
                <?= htmlspecialchars(ucfirst($act['naam'])) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="name">Naam</label>
    <input type="text" id="name" name="name" required>

    <label for="email">E-mail</label>
    <input type="email" id="email" name="email" required>

    <label for="kampeerplek">Kampeerplek</label>
    <select name="kampeerplek" id="kampeerplek" required>
      <option value="0">Geen campinggast</option>
        <?php for ($i = 1; $i <= 60; $i++): ?>
          <option value="<?= $i ?>">Plek <?= $i ?></option>
        <?php endfor; ?>
    </select>


    <label for="aantal">Hoeveel personen wil je aanmelden?</label>
    <select name="aantal" id="aantal" onchange="updatePersonFields()" required>
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
    </select>

        <div id="personenvelden"></div>

        <button type="submit">Inschrijven</button>
    </form>

<script>
function updatePersonFields() {
    let aantal = document.getElementById("aantal").value;
    let container = document.getElementById("personenvelden");
    container.innerHTML = "";
    for (let i = 1; i <= aantal; i++) {
        container.innerHTML += `
            <h4>Persoon ${i}</h4>
            <input type="text" name="persoon_naam_${i}" placeholder="Naam persoon ${i}" required>
            <input type="number" name="persoon_leeftijd_${i}" placeholder="Leeftijd persoon ${i}" required>
        `;
    }
}
updatePersonFields();
</script>

</body>

</html>