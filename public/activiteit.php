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

//join reserveringen en tel deelnemers per activiteit tijd

$sql = "SELECT 
    a.id AS activiteit_id,
//join reserveringen en tel deelnemers per activiteit tijd

$sql = "SELECT 
    a.id AS activiteit_id,
    a.naam,
    a.titel,
    a.titel,
    a.beschrijving,
    a.banner,
    a.prijs,
    a.tag,
    a.banner,
    a.prijs,
    a.tag,
    a.max_deelnemers,
    t.id AS tijd_id,
    t.deadline_inschrijven,
    t.starttijd,
    t.eindtijd,
    t.status,
    COALESCE(SUM(r.aantal_personen), 0) AS plaatsen_ingenomen,
    CASE 
        WHEN t.id IS NULL THEN a.max_deelnemers 
        ELSE (a.max_deelnemers - COALESCE(SUM(r.aantal_personen), 0)) 
    END AS plaatsen_over
FROM Activiteiten a
LEFT JOIN ActiviteitTijden t ON a.id = t.activiteit_id
LEFT JOIN Reserveringen r ON r.activiteittijd_id = t.id AND r.status = 'bevestigd'
WHERE a.id = ?
GROUP BY 
    a.id, a.naam, a.titel, a.beschrijving, a.banner, a.prijs, a.tag, a.max_deelnemers,
    t.id, t.deadline_inschrijven, t.starttijd, t.eindtijd, t.status
ORDER BY COALESCE(t.starttijd, '9999-12-31 23:59:59');
    t.status,
    COALESCE(SUM(r.aantal_personen), 0) AS plaatsen_ingenomen,
    CASE 
        WHEN t.id IS NULL THEN a.max_deelnemers 
        ELSE (a.max_deelnemers - COALESCE(SUM(r.aantal_personen), 0)) 
    END AS plaatsen_over
FROM Activiteiten a
LEFT JOIN ActiviteitTijden t ON a.id = t.activiteit_id
LEFT JOIN Reserveringen r ON r.activiteittijd_id = t.id AND r.status = 'bevestigd'
WHERE a.id = ?
GROUP BY 
    a.id, a.naam, a.titel, a.beschrijving, a.banner, a.prijs, a.tag, a.max_deelnemers,
    t.id, t.deadline_inschrijven, t.starttijd, t.eindtijd, t.status
ORDER BY COALESCE(t.starttijd, '9999-12-31 23:59:59');
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultaat = $stmt->get_result();

?>
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Activiteiten - Boer Bert Boerencamping</title>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Activiteiten - Boer Bert Boerencamping</title>

</head>



<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>

  <?php while ($row = $resultaat->fetch_assoc()) { ?>

    <section class="hero" style="background-image: url('images/activiteiten_banners/<?= $row['banner'] ?>');">
      <div class="hero-content">
        <h1><?= $row['naam']; ?></h1>
        <p><?= $row['titel']; ?></p>
      </div>
    </section>


    <section class="hero" style="background-image: url('images/activiteiten_banners/<?= $row['banner'] ?>');">
      <div class="hero-content">
        <h1><?= $row['naam']; ?></h1>
        <p><?= $row['titel']; ?></p>
      </div>
    </section>

    <div class="container">
      <div class="content-card">
        <div class="breadcrumb">
          <?= $row['naam']; ?>
      <div class="content-card">
        <div class="breadcrumb">
          <?= $row['naam']; ?>
        </div>

        <div class="content-grid">
          <div class="workshop-info">
            <h2>Over de activiteit</h2>
            <p><?= $row['beschrijving']; ?></p>
          </div>


          <aside class="booking-card">
            <h3>Details & Status</h3>

            <div class="availability">
              <h4>Nog slechts <?= $row['plaatsen_over']; ?> plekken beschikbaar</h4>
              <div class="spots"><?= $row['plaatsen_ingenomen']; ?> / <?= $row['max_deelnemers']; ?></div>
              <div class="progress-bar">
                <div class="progress-fill"></div>
              </div>
              <small style="color: #666;">plekken gevuld</small>
            </div>

            <div class="date-info">
              <span class="date-icon">📅</span>
              <div class="date-text">
                <?php $date = date_create("$row[starttijd]");
                ?>
                <span class="date-label">Datum:</span>
                <span class="date-value"><?= date_format($date, "d/m H:i")  ?></span>
              </div>
            </div>

            <div class="price">€<?= $row['prijs']; ?></div>

            <a href="reserveer.php?id=<?= $row['activiteit_id'] ?>">
              <button class="register-btn">Nu Inschrijven</button></a>
          </aside>
        </div>
      </div>
    </div>

  <?php  }; ?>

  <?php  }; ?>

  <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #333;
  }

  /* Header */


  nav {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  /* Hero Section */
  .hero {
    /* background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 400"><rect fill="%237c9473" width="1200" height="400"/></svg>'); */
    /* background-image: url('images/activiteiten_banners/bingo.png'); */
    background-size: cover;
    background-position: center;
    color: white;
    padding: 2rem;
    /* 4rem om te compenseren voor de container */
    padding-bottom: calc(2rem + 4rem);
    text-align: center;
    position: relative;
  }

  .hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y="50" font-size="60" opacity="0.1">🎨</text></svg>') repeat;
    opacity: 0.3;
  }

  .hero-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
    margin: 0 auto;
  }

  .hero h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  }

  .hero p {
    font-size: 1.2rem;
    opacity: 0.95;
  }

  /* Main Content */
  .container {
    max-width: 1200px;
    margin: -4rem auto 3rem;
    padding: 0 2rem;
    position: relative;
    z-index: 10;
  }

  .content-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
  }

  .breadcrumb {
    background: #f8f9fa;
    padding: 1rem 2rem;
    font-size: 0.9rem;
    color: #666;
    border-bottom: 1px solid #e9ecef;
  }

  .content-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 3rem;
    padding: 3rem;
  }

  /* Workshop Info */
  .workshop-info h2 {
    color: #4a3428;
    font-size: 2rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid #7c9473;
  }

  .workshop-info p {
    color: #555;
    line-height: 1.8;
    margin-bottom: 1rem;
  }

  /* Booking Card */
  .booking-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 2rem;
    border-radius: 15px;
    position: sticky;
    top: 100px;
    border: 2px solid #7c9473;
  }

  .booking-card h3 {
    color: #4a3428;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    text-align: center;
  }

  .availability {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    text-align: center;
  }

  .availability h4 {
    color: #4a3428;
    font-size: 1.1rem;
    margin-bottom: 1rem;
  }

  .spots {
    font-size: 2rem;
    color: #7c9473;
    font-weight: bold;
    margin-bottom: 0.5rem;
  }

  .progress-bar {
    background: #e9ecef;
    height: 12px;
    border-radius: 10px;
    overflow: hidden;
    margin: 1rem 0;
  }

  .progress-fill {
    background: linear-gradient(90deg, #7c9473, #a4b89a);
    height: 100%;
    width: 73.3%;
    border-radius: 10px;
    transition: width 0.3s;
  }

  .date-info {
    background: white;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .date-icon {
    font-size: 2rem;
  }

  .date-text {
    flex: 1;
  }

  .date-label {
    font-size: 0.85rem;
    color: #666;
    display: block;
  }

  .date-value {
    font-size: 1.1rem;
    color: #4a3428;
    font-weight: 600;
  }

  .price {
    background: #7c9473;
    color: white;
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
  }

  .register-btn {
    width: 100%;
    background: #4a3428;
    color: white;
    padding: 1.2rem;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .register-btn:hover {
    background: #362619;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
  }




  /* Responsive */
  @media (max-width: 968px) {
    .content-grid {
      grid-template-columns: 1fr;
    }

    .booking-card {
      position: static;
    }

    .hero h1 {
      font-size: 2rem;
    }

  }
</style>

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #333;
  }

  /* Header */


  nav {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  /* Hero Section */
  .hero {
    /* background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 400"><rect fill="%237c9473" width="1200" height="400"/></svg>'); */
    /* background-image: url('images/activiteiten_banners/bingo.png'); */
    background-size: cover;
    background-position: center;
    color: white;
    padding: 2rem;
    /* 4rem om te compenseren voor de container */
    padding-bottom: calc(2rem + 4rem);
    text-align: center;
    position: relative;
  }

  .hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y="50" font-size="60" opacity="0.1">🎨</text></svg>') repeat;
    opacity: 0.3;
  }

  .hero-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
    margin: 0 auto;
  }

  .hero h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  }

  .hero p {
    font-size: 1.2rem;
    opacity: 0.95;
  }

  /* Main Content */
  .container {
    max-width: 1200px;
    margin: -4rem auto 3rem;
    padding: 0 2rem;
    position: relative;
    z-index: 10;
  }

  .content-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
  }

  .breadcrumb {
    background: #f8f9fa;
    padding: 1rem 2rem;
    font-size: 0.9rem;
    color: #666;
    border-bottom: 1px solid #e9ecef;
  }

  .content-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 3rem;
    padding: 3rem;
  }

  /* Workshop Info */
  .workshop-info h2 {
    color: #4a3428;
    font-size: 2rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid #7c9473;
  }

  .workshop-info p {
    color: #555;
    line-height: 1.8;
    margin-bottom: 1rem;
  }

  /* Booking Card */
  .booking-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 2rem;
    border-radius: 15px;
    position: sticky;
    top: 100px;
    border: 2px solid #7c9473;
  }

  .booking-card h3 {
    color: #4a3428;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    text-align: center;
  }

  .availability {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    text-align: center;
  }

  .availability h4 {
    color: #4a3428;
    font-size: 1.1rem;
    margin-bottom: 1rem;
  }

  .spots {
    font-size: 2rem;
    color: #7c9473;
    font-weight: bold;
    margin-bottom: 0.5rem;
  }

  .progress-bar {
    background: #e9ecef;
    height: 12px;
    border-radius: 10px;
    overflow: hidden;
    margin: 1rem 0;
  }

  .progress-fill {
    background: linear-gradient(90deg, #7c9473, #a4b89a);
    height: 100%;
    width: 73.3%;
    border-radius: 10px;
    transition: width 0.3s;
  }

  .date-info {
    background: white;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .date-icon {
    font-size: 2rem;
  }

  .date-text {
    flex: 1;
  }

  .date-label {
    font-size: 0.85rem;
    color: #666;
    display: block;
  }

  .date-value {
    font-size: 1.1rem;
    color: #4a3428;
    font-weight: 600;
  }

  .price {
    background: #7c9473;
    color: white;
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
  }

  .register-btn {
    width: 100%;
    background: #4a3428;
    color: white;
    padding: 1.2rem;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .register-btn:hover {
    background: #362619;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
  }




  /* Responsive */
  @media (max-width: 968px) {
    .content-grid {
      grid-template-columns: 1fr;
    }

    .booking-card {
      position: static;
    }

    .hero h1 {
      font-size: 2rem;
    }

  }
</style>