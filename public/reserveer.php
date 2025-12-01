<?php
// /c:/wamp64/www/Porthoes/public/reserveer.php
session_start();

// Simple CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$errors = [];
$values = [
    'activeittijd_id' => '',
    'email' => '',
    'aantal_personen' => '1',
    'status' => ''
];

$storageFile = __DIR__ . '/reservations.csv';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(400);
        exit('Invalid request');
    }

    // Collect and sanitize
    // Accept activeittijd_id from POST or GET (URL) and validate as positive int
    $rawId = trim((string)($_POST['activeittijd_id'] ?? $_GET['activeittijd_id'] ?? ''));
    $id = $rawId === '' ? false : filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $print_r($id);
    $values['activeittijd_id'] = $id === false ? '' : (string)$id;
    $values['email'] = trim((string)($_POST['email'] ?? ''));
    $values['aantal_personen'] = trim((string)($_POST['aantal_personen'] ?? '1'));
    $values['status'] = trim((string)($_POST['status'] ?? ''));

    // Validate
    if ($values['email'] === '' || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geldig e-mailadres is verplicht.';
    }
    $guestsInt = filter_var($values['aantal_personen'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 50]]);
    if ($guestsInt === false) {
        $errors[] = 'Aantal gasten moet een getal tussen 1 en 50 zijn.';
    } else {
        $values['guests'] = (string)$guestsInt;
    }

    if (empty($errors)) {
        // Prepare record
        $record = [
            (new DateTime())->format(DateTime::ATOM),
            $values['name'],
            $values['email'],
            $values['date'],
            $values['time'],
            $values['guests'],
            $values['status']
        ];

        // Ensure directory/writable and write CSV with exclusive lock
        $fp = @fopen($storageFile, 'a');
        if ($fp === false) {
            $errors[] = 'Kan reservering niet opslaan (bestand niet schrijfbaar).';
        } else {
            if (flock($fp, LOCK_EX)) {
                // If file was empty, optionally write header
                if (ftell($fp) === 0) {
                    fputcsv($fp, ['created_at', 'name', 'email', 'date', 'time', 'guests', 'comments']);
                }
                fputcsv($fp, $record);
                fflush($fp);
                flock($fp, LOCK_UN);
                $success = true;
                // Reset values to defaults after success
                $values = ['name' => '', 'email' => '', 'date' => '', 'time' => '', 'guests' => '1', 'comments' => ''];
                // regenerate CSRF token to avoid double-posts
                $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
            } else {
                $errors[] = 'Kan bestand niet vergrendelen voor schrijven.';
            }
            fclose($fp);
        }
    }
}

// Helper for safe output
function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
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

        <label for="name">Naam *</label>
        <input id="name" name="name" type="text" required value="<?php echo e($values['name']); ?>">

        <label for="email">E-mail *</label>
        <input id="email" name="email" type="email" required value="<?php echo e($values['email']); ?>">

        <div class="row">
            <div>
                <label for="date">Datum (YYYY-MM-DD) *</label>
                <input id="date" name="date" type="date" required value="<?php echo e($values['date']); ?>">
            </div>
            <div>
                <label for="time">Tijd (HH:MM) *</label>
                <input id="time" name="time" type="time" required value="<?php echo e($values['time']); ?>">
            </div>
        </div>

        <label for="guests">Aantal gasten *</label>
        <input id="guests" name="guests" type="number" min="1" max="50" required value="<?php echo e($values['guests']); ?>">

        <label for="comments">Opmerkingen</label>
        <textarea id="comments" name="comments" rows="4"><?php echo e($values['comments']); ?></textarea>

        <button type="submit">Verstuur reservering</button>
    </form>

    <?php
    // Optional: show last 10 reservations (read-only)
    if (file_exists($storageFile)):
        $rows = [];
        $fp = @fopen($storageFile, 'r');
        if ($fp) {
            while (($data = fgetcsv($fp)) !== false) {
                $rows[] = $data;
            }
            fclose($fp);
        }
        // Remove header if present and show last 10
        if (count($rows) > 1) {
            $header = $rows[0];
            $entries = array_slice($rows, 1);
            $entries = array_reverse($entries);
            $entries = array_slice($entries, 0, 10);
            ?>
            <h2>Recente reserveringen</h2>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead>
                    <tr>
                        <?php foreach ($header as $h): ?><th><?php echo e($h); ?></th><?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $row): ?>
                        <tr>
                            <?php foreach ($row as $cell): ?>
                                <td><?php echo e($cell); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php
        }
    endif;
    ?>

</body>
</html>