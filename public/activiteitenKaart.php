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

    <?php
    $mapImage = 'images/map.jpg';
    $pins = [
        ['image' => 'Zwembad.png', 'x' => 19, 'y' => 50, 'activity_id' => 1],
        ['image' => 'Kampvuur.png', 'x' => 50, 'y' => 42, 'activity_id' => 2],
        ['image' => 'Bingo.png', 'x' => 75, 'y' => 84, 'activity_id' => 3],
        ['image' => 'Geiten_yoga.png', 'x' => 48, 'y' => 15, 'activity_id' => 4],
        ['image' => 'Melkeennnn.png', 'x' => 22, 'y' => 15, 'activity_id' => 5],
        ['image' => 'Koeien_knuffelen.png', 'x' => 12, 'y' => 15, 'activity_id' => 6],
        ['image' => 'Eieren_rapen.png', 'x' => 35, 'y' => 15, 'activity_id' => 7],
        ['image' => 'Taffeltennis_toernooi.png', 'x' => 70, 'y' => 84, 'activity_id' => 8],
        ['image' => 'Tienkamp.png', 'x' => 87, 'y' => 90, 'activity_id' => 9],
        ['image' => 'Rondleiding.png', 'x' => 20, 'y' => 73, 'activity_id' => 10],
        ['image' => 'Ijssalon.png', 'x' => 30, 'y' => 36, 'activity_id' => 11],
    ];

    // Build activities list for frontend (id, naam, tag, banner)
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'id' => (int)$row['id'],
            'naam' => $row['naam'],
            'tag' => $row['tag'] ?? '',
            'banner' => $row['banner'] ?? ''
        ];
    }
    ?>

    <div class="map-wrapper">
        <div id="map" style="background-image: url('<?= htmlspecialchars($mapImage) ?>');"></div>
        <div id="pins" aria-hidden="false"></div>
    </div>

    <noscript>
        <!-- Fallback list for users without JS -->
        <div class="activiteiten">
            <?php foreach ($activities as $a) : ?>
                <a href="activiteit2.php?id=<?= $a['id'] ?>"><?= htmlspecialchars($a['naam']) ?></a><br>
            <?php endforeach; ?>
        </div>
    </noscript>

    <script>
        const activities = <?php echo json_encode($activities, JSON_HEX_TAG); ?>;
        const pins = <?php echo json_encode($pins, JSON_HEX_TAG); ?>;

        document.addEventListener('DOMContentLoaded', () => {
            const pinsContainer = document.getElementById('pins');
            const activityById = {};
            activities.forEach(a => activityById[a.id] = a);

            function clearPins() { pinsContainer.innerHTML = ''; }

            function createPinElement(pin) {
                if (typeof pin.x === 'undefined' || typeof pin.y === 'undefined') return null;
                const el = document.createElement('button');
                el.type = 'button';
                el.className = 'pin';
                el.style.left = pin.x + '%';
                el.style.top = pin.y + '%';
                const activity = pin.activity_id ? activityById[pin.activity_id] : null;
                const title = activity ? activity.naam : (pin.label || pin.image);
                el.title = title;
                el.setAttribute('aria-label', title);
                el.style.backgroundColor = 'transparent';

                if (pin.image) {
                    el.style.backgroundImage = "url('images/pins/" + pin.image + "')";
                }

                el.addEventListener('click', () => {
                    if (pin.activity_id) {
                        window.location.href = 'activiteit2.php?id=' + pin.activity_id;
                    } else if (pin.url) {
                        window.location.href = pin.url;
                    }
                });

                return el;
            }

            function renderPins(filterTag = '') {
                clearPins();
                pins.forEach(pin => {
                    // determine pin tag: explicit tag, or activity tag if linked
                    let pinTag = pin.tag || (pin.activity_id && activityById[pin.activity_id] ? activityById[pin.activity_id].tag : '');
                    if (filterTag && filterTag !== '') {
                        if (!pinTag || pinTag.indexOf(filterTag) === -1) return;
                    }
                    const pEl = createPinElement(pin);
                    if (pEl) pinsContainer.appendChild(pEl);
                });
            }

            // apply initial filter based on ?tag= in URL if present
            const params = new URLSearchParams(window.location.search);
            const initialTag = params.get('tag') || '';
            renderPins(initialTag);

            // intercept sidebar filter clicks to re-render without reload
            document.querySelectorAll('.sidebar a').forEach(a => {
                a.addEventListener('click', e => {
                    e.preventDefault();
                    const url = new URL(a.href);
                    const tag = url.searchParams.get('tag') || '';
                    renderPins(tag);
                    // update URL
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('tag', tag);
                    window.history.pushState({}, '', newUrl);
                    // set active class
                    document.querySelectorAll('.sidebar a').forEach(x => x.classList.remove('active'));
                    a.classList.add('active');
                });
            });

            window.addEventListener('popstate', () => {
                const t = new URLSearchParams(window.location.search).get('tag') || '';
                renderPins(t);
            });
        });
    </script>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</div>


</html>

<style>
    .map-wrapper {
        position: relative;
        width: 100%;
        height: 75%;
        margin: 5rem;
        box-sizing: border-box;
    }

    #map {
        width: 100%;
        height: 100%;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 6px;
        background-color: #f8f8f8;
    }

    #pins {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none; /* allow pointer events on pin children */
    }

    .pin {
        position: absolute;
        width: 56px;
        height: 56px;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        transform: translate(-50%, -100%);
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
        pointer-events: auto;
        border: none;
        padding: 0;
        background-color: transparent; /* remove white box */
        transition: transform 150ms ease, box-shadow 150ms ease;
        -webkit-appearance: none;
        appearance: none;
        outline: none;
    }

    .pin:focus {
        box-shadow: 0 0 0 4px rgba(0,123,255,0.12);
    }

    .pin:hover {
        transform: translate(-50%, -100%) scale(1.15);
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