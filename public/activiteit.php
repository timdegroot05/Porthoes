<!-- bingo, kampvuur, geitenyoga, zwembad, boogschieten, koe melken met nepkoe, boerengolf -->
<<?php  // Database verbinding
  $server = "localhost";
  $gebruiker = "root";
  $wachtwoord = "";
  $database = "ActiviteitenDB";

  $conn = new mysqli($server, $gebruiker, $wachtwoord, $database);

  // Foutencontrole
  if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
  }

  //hier haal je het id uit de url
  $id = (int)($_GET['id'] ?? 0);

  $sql = "zet hier je query neer met $id";

  $resultaat = $conn->query($sql);

  ?>


  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Paardrijden op de camping</title>


    <style>
      body {
        background-image: url(images/paardrijdenn.png);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        margin: 0;
        font-family: 'Georgia', serif;
        height: 100vh;
        display: flex;
        align-items: center;
        color: white;
        justify-content: center;
        background-color: #453E3E;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
      }

      .container {
        text-align: center;
        max-width: 1000px;
      }

      h1 {
        font-size: 3rem;
        margin-bottom: 0.2rem;
      }

      .subtitle {
        font-size: 1.4rem;
        margin-bottom: 2rem;
      }

      .info-wrapper {
        display: flex;
        justify-content: space-between;
        gap: 2rem;
        margin-top: 2rem;
      }

      .info-box {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 12px;
        padding: 1.5rem;
        flex: 1;
        color: #000;
        text-shadow: none;
      }

      .info-box h2 {
        margin-top: 0;
      }

      .btn {
        display: inline-block;
        margin-top: 1.5rem;
        background: #4a8f64;
        color: #fff;
        padding: 0.8rem 2rem;
        border-radius: 8px;
        font-size: 1.2rem;
        text-decoration: none;
        transition: 0.2s;
      }

      .btn:hover {
        background: #3a724f;
      }
    </style>
  </head>

  <body>
    <img src="/images/paardrijden.png" alt="">
    <div class="container">
      <h1>Paardrijden op de camping van boer Bert</h1>
      <div class="subtitle">avontuurlijke paardrijdtochten voor jong en oud!</div>

      <div class="info-wrapper">
        <div class="info-box">
          <h2>Waar en wanneer?</h2>
          <p>Locatie: op de camping<br>Tijd: 10:00 – 13:00</p>
        </div>

        <div class="info-box">
          <h2>Praktische informatie</h2>
          <p>Lunch inbegrepen<br>kinderen 10–14 jaar</p>
        </div>
      </div>

      <a href="reserveer.php?id=1" class="btn">Nu inschrijven</a>
    </div>
  </body>

  </html>