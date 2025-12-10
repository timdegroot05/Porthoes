<!-- bingo, kampvuur, geitenyoga, zwembad, boogschieten, koe melken met nepkoe, boerengolf -->
<?php  // Database verbinding
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

  $sql = "SELECT * FROM activiteiten WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $resultaat = $stmt->get_result();

  ?>
  <?php
  // while ($row = $resultaat->fetch_assoc()) {
  //   var_dump($row);  // Hier krijg je de echte rijen als array
  // }

  // print_r($id);
  ?>



  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Paardrijden op de camping</title>

    <style>
      body {
        background-color: #edf4e8;
        margin: 0;
        padding: 0;
        font-family: 'Arial', serif;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
      }

      .container {
        background-color: #668668;
        width: 85%;
        margin: 40px auto;
        padding: 40px;
        border-radius: 30px;
        display: flex;
        gap: 40px;
        align-items:flex-start;
        flex-direction: column-reverse;
        justify-content: space-between;
      }

      .wanneer {
        width: 35%;
        color: black;
      }

      .wanneer h2 {
        font-size: 18px;
        margin-bottom: 8px;
      }

      h1 {
        text-align: center;
        margin-top: 30px;
        font-size: 48px;
        font-family: "Georgia", serif;
      }

      .inputveld {
        background-color: #f7e493;
        border-radius: 20px;
        padding: 12px;
        margin-bottom: 20px;
        width: 100%;
        border: none;
        font-size: 16px;
      }

      .info-box {
       background-color: #f7e493;
       border-radius: 20px;
       padding: 12px;
       width: 300px;
       height: 200px;
       border: none;
       font-size: 16px;
       resize: none;
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

      .image-section {
       width: 60%;
       position: relative;
      }

      .image-section img {
       width: 100%;
       border-radius: 30px;
      }

      .top-text {
        text-align: center;
       top: -10px;
       right: 0;
       font-size: 14px;
       text-shadow: 0 2px 4px rgba(0,0,0,0.6);
      }

      .top-texta {
        position: absolute;
        top: -10px;
        right: 0px;
        font-size: 14px;
      }
    </style>
  </head>
 <body>
    

      <?php while ($row = $resultaat->fetch_assoc()) { ?> 
        <div class="top-text"><h1><?= $row['naam']; ?> </h1>
        <h3 class="subtitle"><?= $row['beschrijving']; ?> </h3></div>
        <div class="container">
        <div class="info-box"><b>Praktische informatie<b><p><?= $row[ 'max_deelnemers']; ?> max deelnemers<p>
          <p class="prijs">Prijs: €<?= $row['prijs']; ?></p>
        </div>
      <?php }; ?>


        <div class="wanneer">  
          <h2>Waar en wanneer?</h2>
          </div>
          <div class="inputveld">Wanneer</div>
        

        <div class="image-section">
         <div class="top-texta">Aantal aanmeldingen:</div>
         <img src="/Porthoes/public/images/geitenyoga.png" alt="Activiteit Afbeelding" width="500px" height="200px">
        </div>

      <a href="reserveer.php?id=1" class="btn">Nu inschrijven</a>
    </div>
  </body>

  </html>