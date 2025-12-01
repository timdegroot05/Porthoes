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

    <div class="btn-wrap">
        <a class="btn" href="activiteit.php">Bekijk Activiteiten</a>
    </div>

    <style>
        .btn-wrap { padding: 1rem; }

        .btn {
            display: inline-block;
            text-decoration: none;
            background: linear-gradient(135deg,#85A898 0%, #5C8F74 100%);
            color: #ffffff;
            padding: 0.6rem 1.1rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
            transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.16);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: 0 6px 12px rgba(0,0,0,0.10);
            opacity: 0.98;
        }

        .btn:focus {
            outline: 3px solid rgba(133,168,152,0.22);
            outline-offset: 2px;
        }
    </style>

    <aside>
        <!-- <input type="radio" placeholder="Filter activiteiten">Voor ouderen -->
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
            background-image: url(images/Map_resized.png);
            background-size: cover;
            height: 100vh;
            width: 100vw;
            padding: 10px;
        }

         /* aside {
            height: 100vh;
            width: 15rem;
            position: fixed;
            border-right: 10px solid #85A898;
            padding: 10px;
            background-color: #FFF4D7;
        } */
    </style>


</body>

</html>