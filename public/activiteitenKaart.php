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

        <p>activiteiten</p>
    </div>

    <aside>
        <input type="radio" placeholder="Filter activiteiten">Voor ouderen
    </aside>

    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #FFF4D7;
            display: flex;
            justify-content: space-between;
        }

        #kaart {
            background-image: url(images/boerderij.png);
            height: 100vh;
            width: 100vw;
            padding: 10px;
        }
`
        aside {
            height: 100vh;
            width: 15rem;
            position: fixed;
            border-right: 10px solid #85A898;
            padding: 10px;
            background-color: #FFF4D7;
        }
    </style>


</body>

</html>