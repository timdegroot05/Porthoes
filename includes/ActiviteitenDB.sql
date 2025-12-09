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
-- ============================
-- 1. TESTDATA VOOR ACTIVITEITEN
-- ============================
INSERT INTO Activiteiten (naam, beschrijving, max_deelnemers, prijs)
VALUES
('zwembad', 'Zwemmen met je familie en vrienden', 25, 0.00),
('kampvuur', 'in de avond rond het vuurtje zitten', 8, 5.00),
('bingo', 'gokken met de stichting!', 30, 4.00),
('geitenyoga', 'lekker rekken en strekken met geiten op je rug', 10, 5.00),
('koe melken met nepkoe', 'jij freaky mf', 10, 0.00),
('koeien knuffelen', 'jij cutie lekker koeien knuffelen', 10, 0.00),
('eieren rapen', 'ballen oppakken van de grond #lekker', 8, 0.00),
('tafeltennis toernooi', 'tryharden tegen kinderen die denken dat ze goed zijn', 16, 0.00),
('tienkamp', 'spelletjes avond', 20, 0.00),
('rondleiding', 'bekijk de hele camping onder leiding van boer bert', 5, 5.00),
('ijssalon', 'als het te warm is lekker afkoelen met ijsjes', 20, 5.00);

-- ====================================
-- 2. TESTDATA VOOR ACTIVITEITTIJDEN
-- ====================================
INSERT INTO ActiviteitTijden (activiteit_id, deadline_inschrijven, starttijd, eindtijd, status)
VALUES
(1, '2025-06-01 12:00:00', '2025-06-05 14:00:00', '2025-06-05 16:00:00', 'beschikbaar'),
(1, '2025-06-10 12:00:00', '2025-06-15 14:00:00', '2025-06-15 16:00:00', 'beschikbaar'),
(2, '2025-07-01 10:00:00', '2025-07-03 09:00:00', '2025-07-03 12:00:00', 'beschikbaar'),
(3, '2025-06-20 18:00:00', '2025-06-25 19:00:00', '2025-06-25 21:30:00', 'beschikbaar');


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

CREATE TABLE IF NOT EXISTS Admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    wachtwoord VARCHAR(255) NOT NULL
);