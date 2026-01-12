<?php
session_start();

// check of admin is ingelogd
if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // niet ingelogd als admin → terug naar login
    header('Location: admin_login.php');
    exit;
}

// hier kun je eventueel db.php inladen als je data wilt tonen
// require 'db.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Admin dashboard</title>
</head>
<body>
    <h1>Welkom in het admin panel</h1>
    <p>Ingelogd als: <?= htmlspecialchars($_SESSION['admin_email'] ?? 'onbekend') ?></p>

    <p><a href="admin_logout.php">Uitloggen</a></p>

    <hr>

    <h2>Admin functies</h2>
    <ul>
        <li><a href="../reserveringen.php">Reserveringen beheren</a></li>
        <li><a href="../deelnemers.php">Deelnemers bekijken</a></li>
        <li><a href="../activiteitenKaart.php">Activiteiten kaart</a></li>
        <li><a href="admin_activiteiten.php">Activiteiten beheren</a></li>
        <li><a href="../overzicht.php">Alle inschrijvingen</a></li>
        <!-- hier kun je meer admin-linkjes zetten -->
    </ul>
</body>
</html>
