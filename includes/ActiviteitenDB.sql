-- Maak de database aan
DROP DATABASE IF EXISTS ActiviteitenDB;
CREATE DATABASE IF NOT EXISTS ActiviteitenDB;
USE ActiviteitenDB;

-- Tabel voor activiteiten
CREATE TABLE Activiteiten (
    id INT PRIMARY KEY AUTO_INCREMENT,
    naam VARCHAR(100) NOT NULL,
    beschrijving TEXT,
    max_deelnemers INT,
    
    

    prijs DECIMAL(10,2)
);

-- Tabel voor activiteitstijden
CREATE TABLE ActiviteitTijden (
    id INT PRIMARY KEY AUTO_INCREMENT,
    activiteit_id INT NOT NULL,
    deadline_inschrijven DATETIME NOT NULL,
    starttijd DATETIME NOT NULL,
    eindtijd DATETIME,
    status VARCHAR(20) DEFAULT 'beschikbaar',
    CONSTRAINT fk_activiteit
        FOREIGN KEY (activiteit_id)
        REFERENCES Activiteiten(id)
        ON DELETE CASCADE,
    CONSTRAINT chk_status CHECK (status IN ('beschikbaar', 'vol', 'geannuleerd'))
);

-- Tabel voor reserveringen
CREATE TABLE Reserveringen (
    id INT PRIMARY KEY AUTO_INCREMENT,
    activiteittijd_id INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    aantal_personen INT DEFAULT 1,
    status VARCHAR(20) DEFAULT 'bevestigd',
    CONSTRAINT fk_activiteittijd
        FOREIGN KEY (activiteittijd_id)
        REFERENCES ActiviteitTijden(id)
        ON DELETE CASCADE,
    CONSTRAINT chk_reservering_status CHECK (status IN ('bevestigd', 'geannuleerd')),
    CONSTRAINT chk_aantal_personen CHECK (aantal_personen > 0)
);

-- Tabel voor deelnemers
CREATE TABLE Deelnemers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reservering_id INT NOT NULL,
    naam VARCHAR(100) NOT NULL,
    leeftijd INT,
    CONSTRAINT fk_reservering
        FOREIGN KEY (reservering_id)
        REFERENCES Reserveringen(id)
        ON DELETE CASCADE,
    CONSTRAINT chk_leeftijd CHECK (leeftijd IS NULL OR leeftijd >= 0)
);

USE ActiviteitenDB;

-- ============================
-- 1. TESTDATA VOOR ACTIVITEITEN
-- ============================
INSERT INTO Activiteiten (naam, beschrijving, max_deelnemers, prijs)
VALUES
('zwembad', 'Workshop boogschieten voor beginners', 12, 25.00),
('kampvuur', 'Buitenklimwand activiteit', 8, 40.00),
('bingo', 'Creatieve schilderworkshop met begeleiding', 15, 30.00);
('geitenyoga', 'Creatieve schilderworkshop met begeleiding', 15, 30.00);
('koe melken met nepkoe', 'Creatieve schilderworkshop met begeleiding', 15, 30.00);
('koeien knuffelen', 'Creatieve schilderworkshop met begeleiding', 15, 30.00);
('eieren rapen', 'Creatieve schilderworkshop met begeleiding', 15, 30.00);
('tafeltennis toernooi', 'Creatieve schilderworkshop met begeleiding', 15, 30.00);
('tienkamp', 'Creatieve schilderworkshop met begeleiding', 15, 30.00);
('rondleiding', 'Creatieve schilderworkshop met begeleiding', 15, 30.00);
('ijssalon', 'Creatieve schilderworkshop met begeleiding', 15, 30.00);

-- Bekijk activiteiten
SELECT * FROM Activiteiten;


-- ====================================
-- 2. TESTDATA VOOR ACTIVITEITTIJDEN
-- ====================================
INSERT INTO ActiviteitTijden (activiteit_id, deadline_inschrijven, starttijd, eindtijd, status)
VALUES
(1, '2025-06-01 12:00:00', '2025-06-05 14:00:00', '2025-06-05 16:00:00', 'beschikbaar'),
(1, '2025-06-10 12:00:00', '2025-06-15 14:00:00', '2025-06-15 16:00:00', 'beschikbaar'),
(2, '2025-07-01 10:00:00', '2025-07-03 09:00:00', '2025-07-03 12:00:00', 'beschikbaar'),
(3, '2025-06-20 18:00:00', '2025-06-25 19:00:00', '2025-06-25 21:30:00', 'beschikbaar');

-- Bekijk tijden
SELECT * FROM ActiviteitTijden;


-- ====================================
-- 3. TESTDATA VOOR RESERVERINGEN
-- ====================================
INSERT INTO Reserveringen (activiteittijd_id, email, aantal_personen, status)
VALUES
(1, 'jan@example.com', 2, 'bevestigd'),
(1, 'piet@example.com', 1, 'bevestigd'),
(2, 'klant@test.com', 4, 'bevestigd'),
(3, 'anna@mail.com', 2, 'bevestigd'),
(4, 'sophie@web.nl', 3, 'bevestigd');

-- Bekijk reserveringen
SELECT * FROM Reserveringen;


-- 
-- 4. TESTDATA VOOR DEELNEMERS
-- ==============================
INSERT INTO Deelnemers (reservering_id, naam, leeftijd)
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

SELECT id, activiteittijd_id, email, aantal_personen, status FROM reserveringen ORDER BY id DESC LIMIT 10

