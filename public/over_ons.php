<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Over Ons - Boer Bert Boerencamping</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f1de;
        }

        main.about-us {
            padding: 40px 0;
            background-color: #fff;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        h1 {
            font-size: 2.5em;
            color: #8b4513;
            margin-bottom: 20px;
            text-align: center;
        }

        h2 {
            font-size: 1.8em;
            color: #8b4513;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .about-content {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e8dcc8;
        }

        .about-content:last-child {
            border-bottom: none;
        }

        ul {
            list-style-position: inside;
            margin-left: 20px;
        }

        li {
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        p {
            font-size: 1.1em;
            line-height: 1.8;
            color: #555;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="about-us">
        <section class="container">
            <h1>Over Ons</h1>
            <p>Welkom bij Boer Bert Boerencamping, uw ideale bestemming voor een authentieke plattelandservaring.</p>

            <section class="about-content">
                <h2>Onze Verhaal</h2>
                <p>Met meer dan 20 jaar ervaring bieden wij gasten een unieke mogelijkheid om het boerenleven te ontdekken in een gastvrije en ontspannen omgeving.</p>
            </section>

            <section class="about-content">
                <h2>Onze Faciliteiten</h2>
                <ul>
                    <li>Ruime kampeerterreinen</li>
                    <li>Boerderij dieren</li>
                    <li>Traditionele boerenkost</li>
                    <li>Familie-vriendelijke activiteiten</li>
                </ul>
            </section>

            <section class="about-content">
                <h2>Contact</h2>
                <p>Heeft u vragen? Neem gerust contact met ons op voor meer informatie.</p>
            </section>
        </section>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>