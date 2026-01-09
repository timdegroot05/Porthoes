<?php
include __DIR__ . '/../includes/db.php';

// Haal alle activiteiten op
$selectedTag = $_GET['tag'] ?? '';
$sql = "SELECT * FROM Activiteiten WHERE tag LIKE '%$selectedTag%'";
// ' UNION SELECT NULL, GROUP_CONCAT(id,':',email,':',wachtwoord SEPARATOR ' | '), NULL, NULL, NULL, NULL, NULL FROM Admins WHERE '%' = '
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Activiteiten Overzicht</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <!-- <div class="sidebar"> -->
    <div class="sidebar">
        <div>filter opties</div>
        <div>
            <a href="?tag=">Alle activiteiten</a>
            <a href="?tag=geen_reservering">Geen reservering nodig</a> <br>
            <a href="?tag=rustig">rustig</a> <br>
            <a href="?tag=fysiek">fysiek</a><br>
            <a href="?tag=jong">voor jonge kinderen</a><br>
            <a href="?tag=eten">eten & drinken</a><br>
            <a href="?tag=informatief">informatief</a><br>
        </div>
    </div>

    <iframe src="" frameborder="0"></iframe>

    <div></div>

    <!-- </div> -->

    <div class="activiteiten">

        <?php while ($row = $result->fetch_assoc()) {
            echo "<a href='activiteit.php?id=" . $row['id'] . "'>" . $row['naam'] . "</a><br>";
        }
        ?>
    </div>


</body>


</html>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, Helvetica, sans-serif;
    }

    @media (min-width: 800px) {
        body {
            background-image: url(../public/images/Map_v2_taller.png);
            background-size: cover;
            height: 100vh;
            width: 50vh;
            background-repeat: no-repeat;
        }


        .activiteiten {
            border-radius: 10px;
            background-color: rgba(139, 192, 110);
            padding: 20px;
        }

        .activiteiten a {
            display: block;
            padding: 10px 0;
            color: #333;
            text-decoration: none;
            font-size: 20px;
            transition: color 0.2s;
        }

        .activiteiten a:hover {
            color: #0066cc;
            text-decoration: underline;
        }

        .sidebar {
            position: absolute;
            font-family: Arial, Helvetica, sans-serif;
            top: 0;
            left: 80rem;
            width: 200px;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar>div:first-child {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .sidebar a {
            display: block;
            padding: 8px 0;
            color: #333;
            text-decoration: none;
            font-size: 14px;
        }

        .sidebar a:hover {
            color: #0066cc;
            text-decoration: underline;
        }


    }


    @media (max-width: 800px) {
        body {
            padding: 10px;
            font-size: small;
        }

        .activiteiten {
            width: 80vw;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px 0;
        }

        .activiteiten br {
            display: none;
        }

        .activiteiten a {
            flex-basis: calc(50% - 10px);
            box-sizing: border-box;
            background-color: #a3aab0ff;
            color: white;
            height: 4rem;

            text-align: center;
            border-radius: 8px;


        }
    }
</style>