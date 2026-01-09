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


<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="activiteiten-body">

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

    <iframe src="../public/images/Map_resized.png" frameborder="0">

    </iframe>

    <!-- </div> -->

    <!-- <div class="activiteiten"> -->

    <?php while ($row = $result->fetch_assoc()) {
        echo "<a href='activiteit.php?id=" . $row['id'] . "'>" . $row['naam'] . "</a><br>";
    }
    ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</div>


</html>

<style>
    iframe {
        /* position: absolute; */
        width: 100%;
        height: 75%;
        border: none;
        margin: 5rem;
    }

    .activiteiten-body {
        margin: 0;
        padding: 0;
        font-family: Arial, Helvetica, sans-serif;
    }


    .activiteiten-body {
        display: flex;
        /* background-image: url(../public/images/Map_v2_taller.png); */
        background-size: cover;
        height: 100vh;
        width: 100vw;
        background-repeat: no-repeat;
    }


    .activiteiten {
        border-radius: 10px;
        background-color: white;
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
</style>