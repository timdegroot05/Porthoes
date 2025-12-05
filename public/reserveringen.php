<?php
$dbHost = '127.0.0.1';
$dbName = 'activiteitenDB';
$dbUser = 'root';
$dbPass = ''; // of je wachtwoord
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

$pdo = new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// simple escaping helper used in the template below
if (!function_exists('e')) {
    function e($str)
    {
        return htmlspecialchars((string) $str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$stmt = $pdo->query("SELECT id, activiteittijd_id AS actviteittijd_id, email, aantal_personen, status FROM reserveringen ORDER BY id DESC");
$recent = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <title>Recente reserveringen</title>
    <style>
        /* Centered container */
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f8;
            color: #222;
            margin: 0;
            padding: 20px;


            background-image: url(images/paardrijdenn.png);
            background-size: cover;
            /* background-position: center; */
            height: 100vh;
            background-repeat: no-repeat;
            background-color: #453E3E;
            color: black;

        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 18px;
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            text-align: center;
        }

        h2 {
            margin: 0 0 16px;
            font-weight: 600;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            text-align: left;
        }

        th,
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e6eef3;
        }

        th {
            background: #f0f6fb;
            font-weight: 700;
            color: #0b4a6f;
        }

        tbody tr:nth-child(odd) {
            background: #fcfeff;
        }

        tbody tr:hover {
            background: #eef8ff;
        }

        .empty {
            padding: 24px 0;
            color: #555;
        }

        @media (max-width: 600px) {

            th,
            td {
                padding: 8px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (!empty($recent)): ?>
            <h2>Recente reserveringen</h2>
            <div class="table-wrap">
                <table cellpadding="6" cellspacing="0" role="table" aria-label="Recente reserveringen">
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
            </div>
        <?php else: ?>
            <p class="empty">Geen recente reserveringen gevonden.</p>
        <?php endif; ?>
    </div>

    <a href="deelnemers.php" role="button" tabindex="0" style="display:inline-block;padding:10px 16px;background:#0b4a6f;color:#fff;border-radius:6px;text-decoration:none;font-weight:600;box-shadow:0 2px 6px rgba(11,74,111,0.25);">deelnemers</a>

</body>

</html>