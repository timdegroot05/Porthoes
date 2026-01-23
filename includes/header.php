<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Boer Bert Camping</title>

  <!-- Google Fonts (alternatief voor Tanker & Bebas Neue SemiRounded) -->
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

  <style>
    :root {
      --celadon: #8EC694;
      --sky: #81C9F8;
      --coffee: #453022;
      --ivory: #FFF8E6;
      --green: #658C6E
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    .header-body {
      font-family: 'Bebas Neue', sans-serif;
      background-color: var(--ivory);
      color: var(--coffee);
    }

    header {
      background: linear-gradient(90deg, var(--green), var(--celadon));
      padding: 20px 40px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .logo {
      font-size: 2.2rem;
      letter-spacing: 2px;
    }

    .logo span {
      font-size: 1.2rem;
      display: block;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 30px;
    }

    nav a {
      text-decoration: none;
      color: var(--coffee);
      font-size: 1.3rem;
      transition: color 0.2s ease;
    }

    nav a:hover {
      color: #2f1f18;
    }

    .cta {
      background-color: var(--coffee);
      color: var(--ivory);
      padding: 10px 20px;
      border-radius: 30px;
      text-decoration: none;
      font-size: 1.2rem;
      margin-left: 20px;
    }

    .cta:hover {
      opacity: 0.9;
    }

    @media (max-width: 768px) {
      header {
        flex-direction: column;
        gap: 20px;
      }

      nav ul {
        flex-direction: column;
        align-items: center;
      }
    }
  </style>
</head>

<body class="header-body">

  <header>
    <div class="logo">
      Boer Bert
      <span>Boerencamping</span>
    </div>

    <nav>
      <ul>
        <li><a href="activiteitenkaart.php">Home</a></li>
        <li><a href="overzicht.php">Camping</a></li>
        <li><a href="over_ons.php">Over ons</a></li>
      </ul>
    </nav>

    <a href="inschrijven2.php" class="cta">Reserveer je activiteit</a>
  </header>

</body>

</html>