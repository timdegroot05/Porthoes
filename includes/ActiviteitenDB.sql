-- Maak de database aan
DROP DATABASE IF EXISTS ActiviteitenDB;

CREATE DATABASE IF NOT EXISTS ActiviteitenDB;

USE ActiviteitenDB;

-- Tabel voor activiteiten
CREATE TABLE Activiteiten (
    id INT PRIMARY KEY AUTO_INCREMENT,
    naam VARCHAR(100) NOT NULL,
    titel TEXT,
    beschrijving VARCHAR(1000),
    max_deelnemers INT,
    prijs DECIMAL(10, 2),
    tag VARCHAR(100),
    banner VARCHAR(100)
);


-- Tabel voor activiteitstijden
CREATE TABLE ActiviteitTijden (
    id INT PRIMARY KEY AUTO_INCREMENT,
    activiteit_id INT NOT NULL,
    deadline_inschrijven DATETIME NOT NULL,
    starttijd DATETIME NOT NULL,
    eindtijd DATETIME,
    status VARCHAR(20) DEFAULT 'beschikbaar',
    CONSTRAINT fk_activiteit FOREIGN KEY (activiteit_id) REFERENCES Activiteiten(id) ON DELETE CASCADE,
    CONSTRAINT chk_status CHECK (status IN ('beschikbaar', 'vol', 'geannuleerd'))
);

-- Tabel voor reserveringen
CREATE TABLE Reserveringen (
    id INT PRIMARY KEY AUTO_INCREMENT,
    activiteittijd_id INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    aantal_personen INT DEFAULT 1,
    status VARCHAR(20) DEFAULT 'bevestigd',
    CONSTRAINT fk_activiteittijd FOREIGN KEY (activiteittijd_id) REFERENCES ActiviteitTijden(id) ON DELETE CASCADE,
    CONSTRAINT chk_reservering_status CHECK (status IN ('bevestigd', 'geannuleerd')),
    CONSTRAINT chk_aantal_personen CHECK (aantal_personen > 0)
);

-- Tabel voor deelnemers
CREATE TABLE Deelnemers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reservering_id INT NOT NULL,
    naam VARCHAR(100) NOT NULL,
    leeftijd INT,
    CONSTRAINT fk_reservering FOREIGN KEY (reservering_id) REFERENCES Reserveringen(id) ON DELETE CASCADE,
    CONSTRAINT chk_leeftijd CHECK (
        leeftijd IS NULL
        OR leeftijd >= 0
    )
);

-- ============================
-- 1. TESTDATA VOOR ACTIVITEITEN
-- ============================
INSERT INTO Activiteiten (naam, titel, beschrijving, max_deelnemers, prijs, tag, banner)
VALUES
    (
        'zwembad',
        'Lekker zwemmen op de camping!',
        'Kom gezellig zwemmen op de camping! Ons verwarmde buitenzwembad biedt verkoeling en plezier voor jong en oud. Trek een paar baantje om actief bezig te zijn of dobber ontspannen op een luchtbed in de zon. Voor de kleintjes is er een ondiep peuterbad en ligstoelen staan klaar voor ouders die willen relaxen. Neem je handdoek en zonnebrand mee, en maak er een heerlijke zomerochtend of middag van – reserveren is niet nodig, dus duik er gewoon in!',
        12,
        5.00,
        'geen reservering nodig',
        'zwembad.png'
    ),
    (
        'kampvuur',
        'Buitenklimwand activiteit',
        'Geniet van een sfeervolle avond rond het kampvuur! Terwijl het hout knispert en de vonken omhoog dansen, kun je marshmallows roosteren, liedjes zingen of gewoon gezellig kletsen met andere campinggasten. Het kampvuur is dé plek om verhalen te delen en te ontspannen onder de sterrenhemel. Er is geen reservering nodig — gewoon aanschuiven, een plekje zoeken op een boomstam of dekentje, en genieten van de warmte, geur en gezelligheid van het echte buitenleven.',
        8,
        0,
        'geen reservering nodig',
        'kampvuur.webp'
    ),
    (
        'bingo',
        'Een gezelig spel bingo, win je volgende jackpot!',
        'Doe mee aan onze gezellige bingomiddag en maak kans op leuke prijzen of misschien zelfs de jackpot! Jong en oud kunnen meespelen in een ontspannen sfeer vol humor en spanning. Onze spelleider zorgt voor leuke rondes en verrassingen, dus ook als je geen prijs wint, beleef je gegarandeerd een plezierige tijd. Ideaal voor een rustige avond op de camping met vrienden of familie. Vergeet je bingokaart niet – wie weet heb jij straks alle vakjes vol!',
        15,
        5.00,
        'rustig',
        'bingo.png'
    ),
    (
        'geitenyoga',
        'Een begeleide yoga sessie met geiten en een echte Yogi!',
        'Ontdek de perfecte balans tussen ontspanning en speelsheid met onze unieke geitenyoga-sessie. Onder begeleiding van een ervaren yogi leer je eenvoudige houdingen terwijl lieve geiten om je heen scharrelen. Hun nieuwsgierige aanwezigheid zorgt voor glimlachen, ontspanning en verrassende interacties. De sessie is geschikt voor beginners én gevorderden, dus iedereen kan meedoen. Na afloop kun je nog even knuffelen of een foto maken met je nieuwe harige yogavrienden. Een heerlijke activiteit om lichaam en geest op te laden!',
        15,
        10.00,
        'fysiek',
        'geitenyoga.jpg'
    ),
    (
        'koe melken met nepkoe',
        'Creatieve schilderworkshop met begeleiding',
        'Altijd al willen weten hoe het is om een koe te melken? Probeer het nu veilig en droog met onze realistische nepkoe! Een begeleider legt uit hoe het melken werkt en vertelt leuke weetjes over het boerenleven. Kinderen vinden het geweldig om te oefenen en ervaren even hoe een echte boer te werk gaat. Het is leerzaam, grappig en een perfecte activiteit voor gezinnen met jonge kinderen die meer willen leren over het leven op de boerderij.',
        15,
        5.00,
        'voor jonge kinderen',
        'nepkoemelken.jpg'
    ),
    (
        'koeien knuffelen',
        'Kom knuffelen met ons zachte koeien!',
        'Kom tot rust tussen onze lieve, rustige koeien! Onder begeleiding mag je kennismaken met deze zachtaardige dieren, ze borstelen en knuffelen. Koeienknuffelen is niet alleen schattig maar ook verrassend ontspannend: hun kalme ademhaling en warme vacht helpen stress direct te verminderen. Het is een bijzondere ervaring voor jong en oud, ideaal voor dierenvrienden en gezinnen. Trek oude kleren aan en ontdek hoe fijn het is om even écht dicht bij de natuur te zijn.',
        15,
        4.00,
        'voor jonge kinderen',
        'koeienknuffelen.webp'
    ),
    (
        'eieren rapen',
        'Kom eieren verzamelen wie weet kan je een lekker ommeletje maken!',
        'Word boer voor een ochtend en help mee met het rapen van verse eieren bij onze kippen! De kinderen leren op speelse wijze waar hun ontbijt vandaan komt en mogen de eieren natuurlijk zelf verzamelen. Soms scharrelt er een kip naast je of ontdek je een ei op een onverwachte plek. De activiteit is gratis, leerzaam en vooral erg leuk voor jonge gezinnen. Wie weet kun je later in de keuken nog een heerlijk omeletje maken met je eigen vondst!',
        15,
        0,
        'voor jonge kinderen',
        'eierenrapen'
    ),
    (
        'tafeltennis toernooi',
        'Ben jij ons volgende tafeltennis kampioen?',
        'Ben jij de ster van het pingpongbatje? Schrijf je in voor ons spannende tafeltennistoernooi en laat je skills zien! Iedereen kan meedoen — van beginners tot fanatieke spelers. We spelen in een gezellige sfeer met korte wedstrijden, zodat iedereen meerdere keren aan de beurt komt. Het draait niet alleen om winnen maar vooral om plezier en sportiviteit. Kom meedoen, moedig je vrienden aan, en strijd om de titel van campingkampioen tafeltennis!',
        15,
        10.00,
        'fysiek',
        'tafeltennis.jpg'
    ),
    (
        'tienkamp',
        'Een atletiekonderdeel voor mannen waarbij atleten in twee dagen tijd tien verschillende disciplines afleggen!',
        'Test je uithoudingsvermogen met onze uitdagende tienkamp! Verspreid over twee dagen nemen deelnemers het tegen elkaar op in tien sportieve disciplines, variërend van sprint tot kogelstoten. Zowel kracht, snelheid als behendigheid worden op de proef gesteld, maar ook doorzettingsvermogen en teamgeest zijn belangrijk. Met begeleiding en aanmoediging van andere kampeerders is het altijd een sportief feest. Een geweldige manier om actief bezig te zijn, nieuwe mensen te ontmoeten en je grenzen te verleggen!',
        15,
        10.00,
        'fysiek',
        'tienkamp.png'
    ),
    (
        'rondleiding',
        'Leer de camping van boer Bert goed kennen!',
        'Ga op ontdekking met boer Bert en leer alles over onze camping en het boerenleven! Tijdens deze informatieve rondleiding kom je op plekken waar je normaal niet komt en hoor je leuke verhalen over de dieren, de velden en het werk op de boerderij. Voor kinderen is het spannend en leerzaam, voor ouders interessant en ontspannend. Ideaal om een keer mee te maken tijdens je verblijf. Trek stevige schoenen aan en loop gezellig mee!',
        15,
        0,
        'informatief',
        'rondleiding.jpg'
    ),
    (
        'ijssalon',
        'Kom een lekker ijsje eten gemaakt met eigen room!',
        'Trakteer jezelf op een heerlijk ijsje in onze eigen ijssalon! Het roomijs wordt gemaakt met melk en room van onze boerderijdieren, vers en vol van smaak. Kies uit diverse smaken, van klassiek vanille tot verrassende seizoensspecials. Ideaal voor een warme zomerdag of als afsluiter na een leuke activiteit. Kinderen kunnen hun ijsje versieren en volwassenen genieten van een rustig terrasje met uitzicht over de weide. Een smakelijke stop die je zeker niet wilt missen!',
		15,
		2.50,
		'eten & drinken',
        'ijssalon.png'
   );


-- ====================================
-- 2. TESTDATA VOOR ACTIVITEITTIJDEN
-- ====================================
INSERT INTO
    ActiviteitTijden (
        activiteit_id,
        deadline_inschrijven,
        starttijd,
        eindtijd,
        status
    )
VALUES
(1, '2025-06-01 12:00:00', '2025-06-05 14:00:00', '2025-06-05 16:00:00', 'beschikbaar'),
(2, '2025-06-10 12:00:00', '2025-06-15 14:00:00', '2025-06-15 16:00:00', 'beschikbaar'),
(3, '2025-07-01 10:00:00', '2025-07-03 09:00:00', '2025-07-03 12:00:00', 'beschikbaar'),
(4, '2025-06-20 18:00:00', '2025-06-25 19:00:00', '2025-06-25 21:30:00', 'beschikbaar');

-- Bekijk tijden
-- ====================================
-- 3. TESTDATA VOOR RESERVERINGEN
-- ====================================
INSERT INTO
    Reserveringen (
        activiteittijd_id,
        email,
        aantal_personen,
        status
    )
VALUES
    (1, 'jan@example.com', 2, 'bevestigd'),
    (1, 'piet@example.com', 1, 'bevestigd'),
    (2, 'klant@test.com', 4, 'bevestigd'),
    (3, 'anna@mail.com', 2, 'bevestigd'),
    (4, 'sophie@web.nl', 3, 'bevestigd');

-- Bekijk reserveringen
-- 
-- 4. TESTDATA VOOR DEELNEMERS
-- ==============================
INSERT INTO
    Deelnemers (reservering_id, naam, leeftijd)
VALUES
    (1, 'Jan Jansen', 34),
    (1, 'Kees Vermeer', 29),
    (2, 'Piet de Groot', 41),
    (3, 'Laura Visser', 25),
    (3, 'Tom Visser', 27),
    (4, 'Anna de Vries', 30),
    (4, 'Mila de Vries', 5),
    (5, 'Sophie Bakker', 33),
    (5, 'Joris Bakker', 35),
    (5, 'Nina Bakker', 7);


CREATE TABLE IF NOT EXISTS Admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    wachtwoord VARCHAR(255) NOT NULL
);


CREATE TABLE IF NOT EXISTS inschrijvingen (
    id INT PRIMARY KEY AUTO_INCREMENT,
    activity VARCHAR(100) NOT NULL,
    name VARCHAR(100),                 -- aanmelder (optioneel)
    email VARCHAR(150) NOT NULL,       -- email is verplicht
    kampeerplek INT NOT NULL,          -- 1 - 60
    aantal_personen INT NOT NULL,      -- aantal personen dat wordt aangemeld
    personen_json TEXT NOT NULL,       
    aangemeld_op TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

select * from Activiteiten;

