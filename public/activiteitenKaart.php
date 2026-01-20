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

<body>


    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="hero">
        <img src="images/tent.png" alt="Tent" class="hero-img">
        <div class="hero-overlay">
            <h1 class="hero-title">Camping Boer Bert</h1>
        </div>
    </div>

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
        //hier tabel voor maken
        $pins = [
            ['image' => 'Zwembad.png', 'x' => 13, 'y' => 50, 'activity_id' => 1],
            ['image' => 'Kampvuur.png', 'x' => 50, 'y' => 42, 'activity_id' => 2],
            ['image' => 'Bingo.png', 'x' => 80, 'y' => 84, 'activity_id' => 3],
            ['image' => 'Geiten_yoga.png', 'x' => 48, 'y' => 15, 'activity_id' => 4],
            ['image' => 'Melkeennnn.png', 'x' => 17, 'y' => 15, 'activity_id' => 5],
            ['image' => 'Koeien_knuffelen.png', 'x' => 12, 'y' => 15, 'activity_id' => 6],
            ['image' => 'Eieren_rapen.png', 'x' => 32, 'y' => 15, 'activity_id' => 7],
            ['image' => 'Taffeltennis_toernooi.png', 'x' => 75, 'y' => 84, 'activity_id' => 8],
            ['image' => 'Tienkamp.png', 'x' => 95, 'y' => 90, 'activity_id' => 9],
            ['image' => 'Rondleiding.png', 'x' => 17, 'y' => 80, 'activity_id' => 10],
            ['image' => 'Ijssalon.png', 'x' => 27, 'y' => 36, 'activity_id' => 11],
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

        <div class="map-wrapper" id="map-wrapper">
            <div id="map">
                <img id="map-image" src="<?= htmlspecialchars($mapImage) ?>" alt="Map">
                <button id="fullscreen-toggle" class="fullscreen-btn" aria-label="Toggle fullscreen">⤢</button>
                <div id="pins" aria-hidden="false"></div>
            </div>
        </div>

        <dih></dih>

        <noscript>
            <!-- Fallback list for users without JS -->
            <div class="activiteiten">
                <?php foreach ($activities as $a) : ?>
                    <a href="activiteit2.php?id=<?= $a['id'] ?>"><?= htmlspecialchars($a['naam']) ?></a><br>
                    <style>
                        div:hover {
                            background: #985353;
                            color: #0066cc;
                            text-decoration: underline;
                        }
                    </style>
                <?php endforeach; ?>
            </div>
        </noscript>

        <script>
            const activities = <?php echo json_encode($activities, JSON_HEX_TAG); ?>;
            const pins = <?php echo json_encode($pins, JSON_HEX_TAG); ?>;

            document.addEventListener('DOMContentLoaded', () => {
                const mapWrapper = document.getElementById('map-wrapper');
                const mapEl = document.getElementById('map');
                const mapImg = document.getElementById('map-image');
                const pinsContainer = document.getElementById('pins');
                const fsBtn = document.getElementById('fullscreen-toggle');
                const activityById = {};
                activities.forEach(a => activityById[a.id] = a);

                function clearPins() {
                    pinsContainer.innerHTML = '';
                }

                function createPinElement(pin) {
                    if (typeof pin.x === 'undefined' || typeof pin.y === 'undefined') return null;
                    const el = document.createElement('button');
                    el.type = 'button';
                    el.className = 'pin';
                    // store percent coords; we will convert to pixel positions later
                    el.dataset.x = pin.x;
                    el.dataset.y = pin.y;
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

                function positionPins() {
                    if (!mapImg || !pinsContainer) return;
                    const imgRect = mapImg.getBoundingClientRect();
                    const mapRect = mapEl.getBoundingClientRect();
                    const offsetLeft = imgRect.left - mapRect.left;
                    const offsetTop = imgRect.top - mapRect.top;
                    const imgW = imgRect.width;
                    const imgH = imgRect.height;
                    if (imgW === 0 || imgH === 0) return;

                    Array.from(pinsContainer.children).forEach(el => {
                        const x = parseFloat(el.dataset.x);
                        const y = parseFloat(el.dataset.y);
                        if (isNaN(x) || isNaN(y)) return;
                        const leftPx = offsetLeft + (x / 100) * imgW;
                        const topPx = offsetTop + (y / 100) * imgH;
                        el.style.left = leftPx + 'px';
                        el.style.top = topPx + 'px';
                    });
                }

                function renderPins(filterTag = '') {
                    clearPins();
                    pins.forEach(pin => {
                        let pinTag = pin.tag || (pin.activity_id && activityById[pin.activity_id] ? activityById[pin.activity_id].tag : '');
                        if (filterTag && filterTag !== '') {
                            if (!pinTag || pinTag.indexOf(filterTag) === -1) return;
                        }
                        const pEl = createPinElement(pin);
                        if (pEl) pinsContainer.appendChild(pEl);
                    });

                    if (mapImg.complete && mapImg.naturalWidth !== 0) {
                        positionPins();
                    } else {
                        mapImg.addEventListener('load', positionPins, {
                            once: true
                        });
                        setTimeout(positionPins, 50);
                    }
                }

                // fullscreen toggle
                fsBtn.addEventListener('click', () => {
                    if (!document.fullscreenElement) {
                        mapWrapper.requestFullscreen().catch(() => {});
                    } else {
                        document.exitFullscreen().catch(() => {});
                    }
                });

                document.addEventListener('fullscreenchange', () => {
                    positionPins();
                    if (document.fullscreenElement) fsBtn.textContent = '⤡';
                    else fsBtn.textContent = '⤢';
                });

                window.addEventListener('resize', positionPins);
                window.addEventListener('orientationchange', positionPins);

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
</body>

</html>

<style>
    .hero {
        position: relative;
        width: 100%;
        overflow: hidden;
        margin: 0 auto 1.5rem;
        box-sizing: border-box;
    }

    .hero-img {
        display: block;
        width: 100%;
        height: clamp(160px, 30vw, 320px);
        /* responsive height */
        object-fit: cover;
        filter: brightness(60%);
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        /* allow clicks through */
    }

    .hero-title {
        color: #fff;
        font-weight: 700;
        font-size: clamp(22px, 3.5vw, 44px);
        margin: 0;
        padding: 0.2rem 0.6rem;
        background: transparent;
        /* removed dark background per request */
        border-radius: 0;
        text-align: center;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
    }

    .map-wrapper {
        max-width: 1200px;
        width: calc(100% - 4rem);
        margin: 2rem auto;
        box-sizing: border-box;
        order: 1;
        flex: 1 1 auto;
    }

    #map {
        position: relative;
        width: 100%;
        border-radius: 6px;
        background-color: #f8f8f8;
        overflow: hidden;
    }

    #map img {
        display: block;
        width: 100%;
        height: auto;
        max-width: 100%;
    }

    #pins {
        position: absolute;
        inset: 0;
        pointer-events: none;
        /* allow pointer events on pin children */
    }

    .fullscreen-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 3;
        background: rgba(255, 255, 255, 0.85);
        border: none;
        border-radius: 4px;
        padding: 6px 8px;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }

    .map-wrapper:fullscreen,
    .map-wrapper:-webkit-full-screen {
        width: 100vw !important;
        height: 100vh !important;
        margin: 0 !important;
        max-width: none;
    }

    .map-wrapper:fullscreen #map img,
    .map-wrapper:-webkit-full-screen #map img {
        width: 100%;
        height: 100%;
        object-fit: contain;
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

        pointer-events: auto;
        border: none;
        padding: 0;
        background-color: transparent;
        /* remove white box */
        transition: transform 150ms ease, box-shadow 150ms ease;
        -webkit-appearance: none;
        appearance: none;
        outline: none;
    }

    .pin:focus {
        box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.12);
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
        min-height: calc(100vh - 200px);
        width: 100%;
        overflow-x: hidden;
        background-repeat: no-repeat;
        align-items: flex-start;
        gap: 1.5rem;
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

    /* Sidebar (right column) */
    .sidebar {
        box-sizing: border-box;
        font-family: Arial, Helvetica, sans-serif;
        order: 2;
        flex: 0 0 260px;
        /* fixed column width */
        width: 260px;
        height: calc(100vh - 140px);
        position: sticky;
        top: 30px;
        background: linear-gradient(180deg, rgba(159, 195, 166, 0.95), rgba(141, 175, 147, 0.95));
        color: #3b2b1b;
        /* warm brown text */
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        overflow: auto;
    }

    .sidebar>div:first-child {
        font-weight: 800;
        margin-bottom: 12px;
        font-size: 16px;
        color: #2f5a33;
        letter-spacing: 0.4px;
    }

    .sidebar a {
        display: block;
        padding: 10px 8px;
        color: #3b2b1b;
        text-decoration: none;
        font-size: 15px;
        border-radius: 6px;
        transition: background 120ms ease, color 120ms ease;
    }

    .sidebar a:hover {
        color: white;
        background: rgba(75, 98, 61, 0.9);
    }

    /* Mobile: stack map and sidebar */
    @media (max-width: 900px) {
        .activiteiten-body {
            flex-direction: column;
            gap: 0.5rem;
        }

        .sidebar {
            position: relative;
            order: 2;
            width: 100%;
            flex: 0 0 auto;
            height: auto;
            top: auto;
            margin: 0 1rem;
        }

        .map-wrapper {
            order: 1;
            width: 100%;
        }

        .fullscreen-btn {
            top: 10px;
            right: 10px;
        }
    }
</style>