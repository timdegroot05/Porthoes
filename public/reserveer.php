<?php
// /c:/wamp64/www/Porthoes/public/reserveer.php
session_start();

// Simple CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$errors = [];
$values = [
    'actviteittijd_id' => '',
    'email' => '',
    'aantal_personen' => '1',
    'status' => ''
];

$success = false;

// Database settings - pas deze aan naar jouw omgeving
$dbHost = '127.0.0.1';
$dbName = 'activiteitenDB';
$dbUser = 'root';
$dbPass = ''; // of je wachtwoord
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

$pdo = new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)$_POST['csrf_token'])) {
        http_response_code(400);
        exit('Ongeldig verzoek');
    }

    // Collect and sanitize
    $rawId = trim((string)($_POST['actviteittijd_id'] ?? $_GET['actviteittijd_id'] ?? ''));
    $id = $rawId === '' ? false : filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $values['actviteittijd_id'] = $id === false ? '' : (string)$id;

    $values['email'] = trim((string)($_POST['email'] ?? ''));
    $values['aantal_personen'] = trim((string)($_POST['aantal_personen'] ?? '1'));
    $values['status'] = trim((string)($_POST['status'] ?? ''));

    // Validate
    if ($values['actviteittijd_id'] === '') {
        $errors[] = 'actviteittijd_id is verplicht en moet een positief geheel getal zijn.';
    }
    if ($values['email'] === '' || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geldig e-mailadres is verplicht.';
    }
    $guestsInt = filter_var($values['aantal_personen'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 50]]);
    if ($guestsInt === false) {
        $errors[] = 'Aantal personen moet een geheel getal tussen 1 en 50 zijn.';
    } else {
        $values['aantal_personen'] = (string)$guestsInt;
    }

    if ($values['status'] === '') {
        // optioneel: default status
        $values['status'] = 'open';
    }

    // Insert into DB
    if (empty($errors)) {
        $sql = "INSERT INTO reserveringen (activiteittijd_id, email, aantal_personen, status) VALUES (:actviteittijd_id, :email, :aantal_personen, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':actviteittijd_id' => $values['actviteittijd_id'],
            ':email' => $values['email'],
            ':aantal_personen' => $values['aantal_personen'],
            ':status' => $values['status'],
        ]);

        // regenerate CSRF token to avoid double-posts
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));

        // Redirect to reserveringen.php after successful insert
        header('Location: reserveringen.php', true, 303);
        exit;
    }
}

// Helper for safe output
function e($str)
{
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Fetch last 10 reservations for display (if DB available)
$recent = [];
$stmt = $pdo->query("SELECT id, activiteittijd_id AS actviteittijd_id, email, aantal_personen, status FROM reserveringen ORDER BY id DESC");
$recent = $stmt->fetchAll();
?>
<!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <title>Reserveer</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 720px;
            margin: 20px auto;
            padding: 0 10px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .row {
            display: flex;
            gap: 10px;
        }

        .row>* {
            flex: 1;
        }

        .errors {
            background: #fdecea;
            color: #611;
            padding: 10px;
            border: 1px solid #f5c6cb;
            margin-bottom: 10px;
        }

        .success {
            background: #e6ffed;
            color: #083;
            padding: 10px;
            border: 1px solid #c3f0d1;
            margin-bottom: 10px;
        }

        button {
            margin-top: 10px;
            padding: 10px 14px;
        }
    </style>
</head>

<body>
    <h1>Reserveer</h1>

    <?php if ($success): ?>
        <div class="success">Reservering ontvangen. Bedankt!</div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
        <label for="actviteittijd_id">actviteittijd_id *</label>
        <input id="actviteittijd_id" name="actviteittijd_id" type="hidden" min="1" required value="1">

        <label for="email">E-mail *</label>
        <input id="email" name="email" type="email" required value="<?php echo e($values['email']); ?>">

        <label for="aantal_personen">Aantal personen *</label>
        <input id="aantal_personen" name="aantal_personen" type="number" min="1" max="50" required value="<?php echo e($values['aantal_personen']); ?>">

        <!-- <label for="status">Status</label> -->
        <input id="status" name="status" type="hidden" value="bevestigd">

        <button type="submit">Verstuur reservering</button>
    </form>

    <!-- <?php if (!empty($recent)): ?>
        <h2>Recente reserveringen</h2>
        <table cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>id</th>
                    <th>actviteittijd_id</th>
                    <th>email</th>
                    <th>aantal_personen</th>
                    <th>status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $row): ?>
                    <tr>
                        <td><?php echo e($row['id']); ?></td>
                        <td><?php echo e($row['actviteittijd_id']); ?></td>
                        <td><?php echo e($row['email']); ?></td>
                        <td><?php echo e($row['aantal_personen']); ?></td>
                        <td><?php echo e($row['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?> -->

</body>

</html>

<style>
    body {
        background-image: url(images/paardrijdenn.png);
        background-size: cover;
        /* background-position: center; */
        height: 100vh;
        background-repeat: no-repeat;
        background-color: #453E3E;
        color: black;
    }

    form {
        background: rgba(0, 0, 0, .5);
        border-radius: 12px;
        padding: 1.5rem;
        flex: 1;
        color: #ffffffff;
        text-shadow: none;
    }

    table {
        width: 100%;
        max-width: 100%;
        border-collapse: collapse;
        background-color: rgba(255,255,255,0.95);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        border-radius: 8px;
        overflow: hidden;
        display: block; /* allows rounded corners + horizontal scroll on small screens */
    }

    table thead {
        background: linear-gradient(#f7f7f7, #efefef);
    }

    table th,
    table td {
        padding: 10px 12px;
        text-align: left;
        border-bottom: 1px solid #e9e9e9;
        color: #222;
        white-space: nowrap;
    }

    table th {
        font-weight: 700;
        font-size: 0.95rem;
    }

    table tbody tr:nth-child(even) td {
        background: rgba(0,0,0,0.02);
    }

    table tbody tr:hover td {
        background: rgba(0,0,0,0.04);
    }

    /* make table horizontally scrollable on small screens */
    @media (max-width: 720px) {
        table {
            overflow-x: auto;
            display: block;
        }
        table th,
        table td {
            padding: 8px;
        }
    }
</style>
<style>
/* Minimal, unobtrusive tweaks */
h1 { color: #fff; text-align: center; margin-bottom: 1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.6); }
form { max-width: 520px; margin: 0 auto 1.5rem; }
label { font-size: 0.95rem; color: #fff; }
input[type="number"],
input[type="email"],
button {
    border-radius: 6px;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.04);
    color: #fff;
    padding: 8px;
    box-sizing: border-box;
}
button {
    display: inline-block;
    padding: 10px 16px;
    margin-top: 0.5rem;
    cursor: pointer;
}
button:hover { opacity: 0.95; transform: translateY(-1px); transition: .12s ease; }
.success, .errors { max-width: 520px; margin: 0.5rem auto; border-radius: 6px; padding: 10px; }
table { max-width: 720px; margin: 1rem auto; }
@media (max-width: 480px) {
    body { padding: 12px; }
    input, button { font-size: 14px; }
}
</style>