<?php
session_start();

if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: admin_login.php');
    exit;
}

require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_activiteiten.php');
    exit;
}

// CSRF check
$token = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    die("Ongeldige aanvraag (CSRF).");
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: admin_activiteiten.php');
    exit;
}

$stmt = $conn->prepare("DELETE FROM Activiteiten WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header('Location: admin_activiteiten.php');
exit;
