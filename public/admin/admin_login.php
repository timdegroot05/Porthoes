<?php
session_start();

$errors = [];

// vaste admin-gegevens (alleen voor lokaal gebruik!)
$adminEmail = 'admin@example.com';
$adminWachtwoord = 'admin123';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $wachtwoord = $_POST['wachtwoord'] ?? '';

    if ($email === $adminEmail && $wachtwoord === $adminWachtwoord) {
        // inloggen geslaagd
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_email'] = $email;

        header('Location: admin_dashboard.php');
        exit;
    } else {
        $errors[] = "Onjuiste e-mail of wachtwoord.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Admin login</title>
</head>
<body>
    <h1>Admin login</h1>

    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="">
        <label>
            E-mail:
            <input type="email" name="email" required>
        </label>
        <br><br>
        <label>
            Wachtwoord:
            <input type="password" name="wachtwoord" required>
        </label>
        <br><br>
        <button type="submit">Inloggen</button>
    </form>
</body>
</html>
