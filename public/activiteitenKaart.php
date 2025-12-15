<?php
include __DIR__ . '/../includes/db.php';

// Haal alle activiteiten op
$selectedTag = $_GET['tag'] ?? 'geen_reservering';
$sql = "SELECT * FROM Activiteiten WHERE tag LIKE '%$selectedTag%'";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Activiteiten Overzicht</title>
</head>

<body>
    <div class="sidebar">
        <div>filter opties</div>
        <div>

            <?php
            // Default value if no tag is set
            ?>
            <a href="?tag=geen_reservering">Geen reservering nodig</a> <br>
            <a href="?tag=niet_fysiek">niet fysiek</a> <br>
            <a href="?tag=fysiek">fysiek</a> <br>
            <a href="?tag=jong">voor jonge kinderen</a> <br>
            <a href="?tag=eten">eten & drinken</a> <br>
            <a href="?tag=informatief">informatief</a> <br>
            niet fysiek <br>
            fysiek <br>
            voor jonge kinderen <br>
            eten & drinken <br>
            informatief <br>
        </div>

        <?php while ($row = $result->fetch_assoc()) {
            echo "<pre>";
            print_r($row['naam']);
            echo "</pre>";
        }
        ?>

    </div>


</body>


</html>

<style>
    body {
        background-image: url(images/Map_v2_taller.png);
        background-size: cover;
        height: 100vh;
        width: 50vh;
        background-repeat: no-repeat;

    }
</style>