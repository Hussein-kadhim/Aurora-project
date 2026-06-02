/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Aurora database aanmaken en selecteren.
  Opmerkingen  : Zorg ervoor dat MySQL actief is voordat dit script wordt uitgevoerd.
*/
DROP DATABASE IF EXISTS Aurora;
CREATE DATABASE Aurora;

USE Aurora;



/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Tabel voor de gebruikers aangemaakt. Deze tabel bevat informatie
                 over de gebruikers van het systeem, zoals hun naam, gebruikersnaam,
                 wachtwoord en inlogstatus.
  Opmerkingen  : Wachtwoord wordt versleuteld opgeslagen (bcrypt).
*/
CREATE TABLE Gebruiker (
    Id INT NOT NULL AUTO_INCREMENT
    ,Voornaam VARCHAR(50) NOT NULL
    ,Tussenvoegsel VARCHAR(10) NULL
    ,Achternaam VARCHAR(50) NOT NULL
    ,Gebruikersnaam VARCHAR(100) NOT NULL UNIQUE
    ,Wachtwoord VARCHAR(255) NOT NULL
    ,IsIngelogd BIT NOT NULL DEFAULT 0
    ,Ingelogd DATETIME NULL
    ,Uitgelogd DATETIME NULL
    ,IsActief BIT NOT NULL DEFAULT 1
    ,Opmerking VARCHAR(250) NULL
    ,DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
    ,DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
    ,PRIMARY KEY (Id)
) ENGINE=InnoDB;

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Tabel voor de rollen van gebruikers. Elke gebruiker krijgt een
                 rol toegewezen: Bezoeker, Medewerker of Administrator.
  Opmerkingen  : Foreign key verwijst naar Gebruiker. Bij verwijdering wordt de
                 rol ook verwijderd (CASCADE).
*/
CREATE TABLE Rol (
    Id INT NOT NULL AUTO_INCREMENT
    ,GebruikerId INT NOT NULL
    ,Naam VARCHAR(100) NOT NULL -- Bezoeker, Medewerker, Administrator
    ,IsActief BIT NOT NULL DEFAULT 1
    ,Opmerking VARCHAR(250) NULL
    ,DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
    ,DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
    ,PRIMARY KEY (Id)
    ,FOREIGN KEY (GebruikerId) REFERENCES Gebruiker(Id) ON DELETE CASCADE
) ENGINE=InnoDB;

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Tabel voor de contactgegevens van gebruikers. Bevat e-mailadres
                 en mobiel telefoonnummer per gebruiker.
  Opmerkingen  : E-mailadres moet uniek zijn in het systeem.
*/
CREATE TABLE Contact (
    Id INT NOT NULL AUTO_INCREMENT
    ,GebruikerId INT NOT NULL
    ,Email VARCHAR(100) NOT NULL UNIQUE
    ,Mobiel VARCHAR(20) NOT NULL
    ,IsActief BIT NOT NULL DEFAULT 1
    ,Opmerking VARCHAR(250) NULL
    ,DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
    ,DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
    ,PRIMARY KEY (Id)
    ,FOREIGN KEY (GebruikerId) REFERENCES Gebruiker(Id) ON DELETE CASCADE
) ENGINE=InnoDB;

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Tabel voor medewerkers van Aurora. Bevat het unieke
                 medewerkersnummer en het soort functie van de medewerker.
  Opmerkingen  : Medewerkersoort kan zijn: Beheerder, Ticketcontroleur of Planner.
*/
CREATE TABLE Medewerker (
    Id INT NOT NULL AUTO_INCREMENT
    ,GebruikerId INT NOT NULL
    ,Nummer MEDIUMINT NOT NULL UNIQUE -- Uniek medewerkersnummer
    ,Medewerkersoort VARCHAR(20) NOT NULL -- Bijvoorbeeld: Beheerder, Ticketcontroleur
    ,IsActief BIT NOT NULL DEFAULT 1
    ,Opmerking VARCHAR(250) NULL
    ,DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
    ,DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
    ,PRIMARY KEY (Id)
    ,FOREIGN KEY (GebruikerId) REFERENCES Gebruiker(Id) ON DELETE CASCADE
) ENGINE=InnoDB;

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Tabel voor bezoekers van Aurora. Elk bezoekeracccount krijgt
                 een uniek relatienummer toegewezen.
  Opmerkingen  : Relatienummer is uniek per bezoeker en wordt gebruikt voor
                 ticketreserveringen.
*/
CREATE TABLE Bezoeker (
    Id INT NOT NULL AUTO_INCREMENT
    ,GebruikerId INT NOT NULL
    ,Relatienummer MEDIUMINT NOT NULL UNIQUE -- Uniek bezoekersnummer
    ,IsActief BIT NOT NULL DEFAULT 1
    ,Opmerking VARCHAR(250) NULL
    ,DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
    ,DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
    ,PRIMARY KEY (Id)
    ,FOREIGN KEY (GebruikerId) REFERENCES Gebruiker(Id) ON DELETE CASCADE
) ENGINE=InnoDB;

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Tabel voor de ticketprijzen. Bevat het tarief dat gehanteerd
                 wordt bij de verkoop van tickets voor voorstellingen.
  Opmerkingen  : Tarief is opgeslagen als DECIMAL(5,2) voor nauwkeurige
                 geldbedragen (max. 999.99).
*/
CREATE TABLE Prijs (
    Id INT NOT NULL AUTO_INCREMENT
    ,Tarief DECIMAL(5,2) NOT NULL
    ,IsActief BIT NOT NULL DEFAULT 1
    ,Opmerking VARCHAR(250) NULL
    ,DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
    ,DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
    ,PRIMARY KEY (Id)
) ENGINE=InnoDB;

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Tabel voor voorstellingen die worden geprogrammeerd door
                 medewerkers. Bevat datum, tijd, capaciteit en beschikbaarheid.
  Opmerkingen  : Beschikbaarheid kan zijn: Ingepland, Uitverkocht of Geannuleerd.
*/
CREATE TABLE Voorstelling (
    Id INT NOT NULL AUTO_INCREMENT
    ,MedewerkerId INT NOT NULL
    ,Naam VARCHAR(100) NOT NULL
    ,Beschrijving TEXT NULL
    ,Datum DATE NOT NULL
    ,Tijd TIME NOT NULL
    ,MaxAantalTickets INT NOT NULL
    ,Beschikbaarheid VARCHAR(50) NOT NULL -- Ingepland, Uitverkocht, Geannuleerd
    ,IsActief BIT NOT NULL DEFAULT 1
    ,Opmerking VARCHAR(250) NULL
    ,DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
    ,DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
    ,PRIMARY KEY (Id)
    ,FOREIGN KEY (MedewerkerId) REFERENCES Medewerker(Id)
) ENGINE=InnoDB;

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Tabel voor tickets die door bezoekers worden gereserveerd voor
                 een specifieke voorstelling. Elk ticket heeft een unieke barcode
                 en reserveringsnummer.
  Opmerkingen  : Status kan zijn: Vrij, Bezet, Gereserveerd of Geannuleerd.
*/
CREATE TABLE Ticket (
    Id INT NOT NULL AUTO_INCREMENT
    ,BezoekerId INT NOT NULL
    ,VoorstellingId INT NOT NULL
    ,PrijsId INT NOT NULL
    ,Nummer MEDIUMINT NOT NULL UNIQUE -- Uniek reserveringsnummer
    ,Barcode VARCHAR(20) NOT NULL UNIQUE
    ,Datum DATE NOT NULL -- Datum van reservering
    ,Tijd TIME NOT NULL -- Tijdstip van reservering
    ,Status VARCHAR(20) NOT NULL -- Vrij, Bezet, Gereserveerd, Geannuleerd
    ,IsActief BIT NOT NULL DEFAULT 1
    ,Opmerking VARCHAR(250) NULL
    ,DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
    ,DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
    ,PRIMARY KEY (Id)
    ,FOREIGN KEY (BezoekerId) REFERENCES Bezoeker(Id)
    ,FOREIGN KEY (VoorstellingId) REFERENCES Voorstelling(Id)
    ,FOREIGN KEY (PrijsId) REFERENCES Prijs(Id)
) ENGINE=InnoDB;

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Tabel voor meldingen vanuit bezoekers of medewerkers. Een
                 melding kan een notificatie, klacht of review zijn.
  Opmerkingen  : Zowel BezoekerId als MedewerkerId zijn nullable. Een melding
                 kan van een bezoeker of medewerker komen, of systeemgegenereerd zijn.
*/
CREATE TABLE Melding (
    Id INT NOT NULL AUTO_INCREMENT
    ,BezoekerId INT NULL
    ,MedewerkerId INT NULL
    ,Nummer MEDIUMINT NOT NULL UNIQUE -- Uniek meldingsnummer
    ,Type VARCHAR(20) NOT NULL -- Notificatie, Klacht of Review
    ,Bericht VARCHAR(250) NOT NULL
    ,IsActief BIT NOT NULL DEFAULT 1
    ,Opmerking VARCHAR(250) NULL
    ,DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
    ,DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
    ,PRIMARY KEY (Id)
    ,FOREIGN KEY (BezoekerId) REFERENCES Bezoeker(Id) ON DELETE SET NULL
    ,FOREIGN KEY (MedewerkerId) REFERENCES Medewerker(Id) ON DELETE SET NULL
) ENGINE=InnoDB;




/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Testdata invoegen in de tabel Gebruiker. Bevat 3 medewerkers
                 en 3 bezoekers als voorbeeldgebruikers.
  Opmerkingen  : Wachtwoorden zijn bcrypt-gehasht opgeslagen.
*/
INSERT INTO Gebruiker (Id, Voornaam, Tussenvoegsel, Achternaam, Gebruikersnaam, Wachtwoord, IsIngelogd, Ingelogd, Uitgelogd, IsActief, Opmerking) VALUES
(1, 'Jan', 'van', 'Dam', 'jan.vandam@aurora.nl', '$2y$10$3e5z.r1uuAYhlzTPph1xw.qwajnaE3NxbzpyzyyBJWYLx4F3iRwEG', b'0', NULL, NULL, b'1', 'Hoofdbeheerder systeem'),
(2, 'Sarah', NULL, 'Koning', 'sarah.koning@aurora.nl', '$2y$10$3e5z.r1uuAYhlzTPph1xw.qwajnaE3NxbzpyzyyBJWYLx4F3iRwEG', b'0', NULL, NULL, b'1', 'Ticketcontroleur'),
(3, 'Mark', 'de', 'Vries', 'mark.devries@aurora.nl', '$2y$10$3e5z.r1uuAYhlzTPph1xw.qwajnaE3NxbzpyzyyBJWYLx4F3iRwEG', b'0', NULL, NULL, b'1', 'Evenementen beheerder'),
(4, 'Lisa', NULL, 'Jansen', 'lisa.jansen@gmail.com', '$2y$10$3e5z.r1uuAYhlzTPph1xw.qwajnaE3NxbzpyzyyBJWYLx4F3iRwEG', b'0', NULL, NULL, b'1', 'Reguliere bezoeker'),
(5, 'Ahmed', NULL, 'El Mansouri', 'ahmed.elmansouri@outlook.com', '$2y$10$3e5z.r1uuAYhlzTPph1xw.qwajnaE3NxbzpyzyyBJWYLx4F3iRwEG', b'0', NULL, NULL, b'1', 'Premium bezoeker'),
(6, 'Emma', 'van der', 'Berg', 'emma.vdberg@hotmail.com', '$2y$10$3e5z.r1uuAYhlzTPph1xw.qwajnaE3NxbzpyzyyBJWYLx4F3iRwEG', b'0', NULL, NULL, b'1', 'Nieuwe bezoeker');

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Testdata invoegen in de tabel Rol. Elke gebruiker krijgt
                 de bijbehorende systeemrol toegewezen.
  Opmerkingen  : Mogelijke rollen: Administrator, Medewerker, Bezoeker.
*/
INSERT INTO Rol (Id, GebruikerId, Naam, IsActief, Opmerking) VALUES
(1, 1, 'Administrator', b'1', 'Volledige systeemrechten'),
(2, 2, 'Medewerker', b'1', 'Ticketcontroleur rechten'),
(3, 3, 'Medewerker', b'1', 'Voorstellingen planner'),
(4, 4, 'Bezoeker', b'1', 'Standaard bezoekerstoegang'),
(5, 5, 'Bezoeker', b'1', 'Standaard bezoekerstoegang'),
(6, 6, 'Bezoeker', b'1', 'Standaard bezoekerstoegang');

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Testdata invoegen in de tabel Contact. Bevat e-mailadres
                 en mobiel nummer per gebruiker.
  Opmerkingen  : E-mailadressen zijn uniek per gebruiker.
*/
INSERT INTO Contact (Id, GebruikerId, Email, Mobiel, IsActief, Opmerking) VALUES
(1, 1, 'jan.vandam@aurora.nl', '0612345678', b'1', 'Werktelefoon Jan'),
(2, 2, 'sarah.koning@aurora.nl', '0687654321', b'1', 'Werktelefoon Sarah'),
(3, 3, 'mark.devries@aurora.nl', '0623456789', b'1', 'Werktelefoon Mark'),
(4, 4, 'lisa.jansen@gmail.com', '0634567890', b'1', 'Persoonlijk Lisa'),
(5, 5, 'ahmed.elmansouri@outlook.com', '0645678901', b'1', 'Persoonlijk Ahmed'),
(6, 6, 'emma.vdberg@hotmail.com', '0656789012', b'1', 'Persoonlijk Emma');

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Testdata invoegen in de tabel Medewerker. Bevat 3 medewerkers
                 met hun unieke nummers en functies.
  Opmerkingen  : Medewerkersnummers starten vanaf 10001.
*/
INSERT INTO Medewerker (Id, GebruikerId, Nummer, Medewerkersoort, IsActief, Opmerking) VALUES
(1, 1, 10001, 'Beheerder', b'1', 'Systeembeheerder'),
(2, 2, 10002, 'Ticketcontroleur', b'1', 'Toegangscontrole'),
(3, 3, 10003, 'Planner', b'1', 'Voorstellingen en programmering');

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Testdata invoegen in de tabel Bezoeker. Bevat 3 bezoekers
                 met unieke relatienummers.
  Opmerkingen  : Relatienummers starten vanaf 50001.
*/
INSERT INTO Bezoeker (Id, GebruikerId, Relatienummer, IsActief, Opmerking) VALUES
(1, 4, 50001, b'1', 'Reguliere bezoeker'),
(2, 5, 50002, b'1', 'Vaste bezoeker'),
(3, 6, 50003, b'1', 'Nieuw geregistreerd');

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Testdata invoegen in de tabel Prijs. Bevat 3 tarieven:
                 standaard, korting en VIP/premium.
  Opmerkingen  : Tarieven zijn exclusief eventuele servicekosten.
*/
INSERT INTO Prijs (Id, Tarief, IsActief, Opmerking) VALUES
(1, 15.50, b'1', 'Standaard ticketprijs'),
(2, 10.00, b'1', 'Kortingstarief voor studenten en senioren'),
(3, 25.00, b'1', 'VIP en Premium zitplaatsen');

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Testdata invoegen in de tabel Voorstelling. Bevat 3 voorstellingen
                 waarvan 1 geannuleerd is.
  Opmerkingen  : Voorstellingen worden aangemaakt door medewerker met Id 3 (Planner).
*/
INSERT INTO Voorstelling (Id, MedewerkerId, Naam, Beschrijving, Datum, Tijd, MaxAantalTickets, Beschikbaarheid, IsActief, Opmerking) VALUES
(1, 3, 'The Sound of Music', 'Klassieke familiemusical met bekende liedjes.', '2026-06-15', '20:00:00', 150, 'Ingepland', b'1', 'Avondvoorstelling'),
(2, 3, 'Cinderella (Ballet)', 'Een prachtig balletstuk uitgevoerd door het Nationaal Ballet.', '2026-07-20', '19:30:00', 200, 'Ingepland', b'1', 'Topvoorstelling'),
(3, 3, 'Romeo en Julia', 'Het bekende toneelstuk van Shakespeare in een moderne jas.', '2026-08-05', '14:30:00', 100, 'Geannuleerd', b'1', 'Middagvoorstelling geannuleerd');

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Testdata invoegen in de tabel Ticket. Bevat 3 tickets:
                 2 gereserveerd en 1 geannuleerd.
  Opmerkingen  : Elk ticket heeft een unieke barcode en reserveringsnummer.
*/
INSERT INTO Ticket (Id, BezoekerId, VoorstellingId, PrijsId, Nummer, Barcode, Datum, Tijd, Status, IsActief, Opmerking) VALUES
(1, 1, 1, 1, 80001, 'T-100020003001', '2026-05-20', '10:00:00', 'Gereserveerd', b'1', 'Standaard ticket'),
(2, 2, 1, 3, 80002, 'T-100020003002', '2026-05-21', '11:30:00', 'Gereserveerd', b'1', 'VIP ticket met hapjes'),
(3, 3, 2, 2, 80003, 'T-100020003003', '2026-05-22', '09:15:00', 'Geannuleerd', b'1', 'Geannuleerd door klant');

/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Testdata invoegen in de tabel Melding. Bevat 3 meldingen:
                 een review, een klacht en een systeemnotificatie.
  Opmerkingen  : Melding 3 is verstuurd door zowel een medewerker als gekoppeld
                 aan een bezoeker (geannuleerde voorstelling).
*/
INSERT INTO Melding (Id, BezoekerId, MedewerkerId, Nummer, Type, Bericht, IsActief, Opmerking) VALUES
(1, 1, NULL, 90001, 'Review', 'Geweldige service en erg makkelijk om te boeken!', b'1', 'Positieve recensie'),
(2, 2, NULL, 90002, 'Klacht', 'Mijn ticket-barcode laadde niet direct op mijn mobiel.', b'1', 'In behandeling door IT support'),
(3, 3, 2, 90003, 'Notificatie', 'Beste bezoeker, uw voorstelling van Romeo en Julia is geannuleerd.', b'1', 'Systeemnotificatie verzonden');