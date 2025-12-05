<?php
include __DIR__ . '/../includes/db.php';

// Haal alle activiteiten op
$sql = "SELECT * FROM Activiteiten";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Activiteiten Overzicht</title>
</head>

<body>
    <div id="kaart">

    </div>

    <div>filter opties</div>
    <div>
        voor ouderen <br>
        <label><input type="checkbox" name="doelgroep[]" value="ouderen"> Voor ouderen</label><br>
        voor jonge kinderen -3 jaar <br>
        voor 12+ jaar <br>
        ochtend activiteiten<br>
        middag activiteiten<br>
        avond activiteiten<br>
    </div>

    <?php while ($row = $result->fetch_assoc()) {
        echo "<pre>";
        print_r($row['naam']);
        print_r($row['id']);
        echo "</pre>";
    }
    ?>

    <div class="btn-wrap">
        <a class="btn" href="activiteit.php">Bekijk Activiteiten</a>
    </div>




</body>


</html>