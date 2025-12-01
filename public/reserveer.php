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
        $success = true;

        // Reset values after success
        $values = [
            'actviteittijd_id' => '',
            'email' => '',
            'aantal_personen' => '1',
            'status' => ''
        ];
        // regenerate CSRF token to avoid double-posts
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
}

// Helper for safe output
function e($str) {
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
        body { font-family: Arial, sans-serif; max-width:720px; margin:20px auto; padding:0 10px; }
        label { display:block; margin-top:10px; }
        input, textarea, select { width:100%; padding:8px; box-sizing:border-box; }
        .row { display:flex; gap:10px; }
        .row > * { flex:1; }
        .errors { background:#fdecea; color:#611; padding:10px; border:1px solid #f5c6cb; margin-bottom:10px; }
        .success { background:#e6ffed; color:#083; padding:10px; border:1px solid #c3f0d1; margin-bottom:10px; }
        button { margin-top:10px; padding:10px 14px; }
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
        <input id="actviteittijd_id" name="actviteittijd_id" type="number" min="1" required value="<?php echo e($values['actviteittijd_id']); ?>">

        <label for="email">E-mail *</label>
        <input id="email" name="email" type="email" required value="<?php echo e($values['email']); ?>">

        <label for="aantal_personen">Aantal personen *</label>
        <input id="aantal_personen" name="aantal_personen" type="number" min="1" max="50" required value="<?php echo e($values['aantal_personen']); ?>">

        <label for="status">Status</label>
        <input id="status" name="status" type="text" value="<?php echo e($values['status']); ?>">

        <button type="submit">Verstuur reservering</button>
    </form>

    <?php if (!empty($recent)): ?>
        <h2>Recente reserveringen</h2>
        <table border="1" cellpadding="6" cellspacing="0">
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
    <?php endif; ?>

</body>
</html>
