/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE IF NOT EXISTS PrgUtenti (
    IDUtente INT PRIMARY KEY AUTO_INCREMENT,
    Email VARCHAR(255) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Nome VARCHAR(255) NOT NULL,
    Cognome VARCHAR(255) NOT NULL,
    DataOraUtente DATETIME NOT NULL DEFAULT NOW(),
    Bio TEXT
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgNotifiche (
    IDNotifica INT PRIMARY KEY AUTO_INCREMENT,
    Titolo VARCHAR(255) NOT NULL,
    Descrizione TEXT,
    ActionLink VARCHAR(255),
    IDUtente INT NOT NULL,
    DataOraNotifica DATETIME NOT NULL DEFAULT NOW(),
    FOREIGN KEY (IDUtente) REFERENCES PrgUtenti(IDUtente)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgProgetti (
    IDProgetto INT PRIMARY KEY AUTO_INCREMENT,
    Titolo VARCHAR(255) NOT NULL,
    Abstract TEXT,
    DataOraProgetto DATETIME NOT NULL DEFAULT NOW(),
    IDUtente INT NOT NULL,
    FOREIGN KEY (IDUtente) REFERENCES PrgUtenti(IDUtente)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

-- I permessi sono conservati in una stringa binaria organizzata come segue
-- Il bit meno significativo è il permesso in lettura
-- A seguire è permesso di aggiungere commenti
-- Modificare il contenuto della revisione
-- Valutare la revisione del progetto come membro interno
-- Eliminare progetti

CREATE TABLE IF NOT EXISTS PrgPermessi (
    IDPermessi TINYINT UNSIGNED PRIMARY KEY,
    NomePermessi VARCHAR(255) NOT NULL UNIQUE,
    DescrizionePermessi TEXT
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

INSERT INTO PrgPermessi 
    (IDPermessi, NomePermessi, DescrizionePermessi)
VALUES 
    (0b00000, "Nascosto", "Gli utenti con questi permessi non hanno alcun accesso alla revisione"),
    (0b00001, "Visualizzatore", "Gli utenti con questi permessi possono visualizzare la revisione"),
    (0b00011, "Commentatore", "Gli utenti con questi permessi possono commentare la revisione"),
    (0b00111, "Editor", "Gli utenti con questi permessi possono modificare la revisione"),
    (0b01011, "Valutatore", "Gli utenti con questi permessi possono valutare la revisione secondo le caratteristiche valutate dal progetto"),
    (0b01111, "Manutentore", "Gli utenti con questi permessi possono modificare e valutare la revisione secondo le caratteristiche valutate dal progetto"),
    (0b11111, "Admin", "Gli utenti con questi permessi possono effettuare qualsiasi operazione sulla revision, compresa l'eliminazione")
ON DUPLICATE KEY UPDATE
    NomePermessi = VALUES(NomePermessi),
    DescrizionePermessi = VALUES(DescrizionePermessi);

CREATE TABLE IF NOT EXISTS PrgRevisioni (
    IDRevisione INT PRIMARY KEY AUTO_INCREMENT,
    Numero INT NOT NULL,
    Motivazioni TEXT,
    DataOraRevisione DATETIME NOT NULL DEFAULT NOW(),
    DataInizio DATE NOT NULL DEFAULT CURRENT_DATE(),
    DataFine DATE,
    IDProgetto INT NOT NULL,
    IDPermessi TINYINT UNSIGNED NOT NULL DEFAULT 0b00001,
    FOREIGN KEY (IDProgetto) REFERENCES PrgProgetti(IDProgetto)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (IDPermessi) REFERENCES PrgPermessi(IDPermessi)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgCollaboratori (
    IDUtente INT,
    IDRevisione INT,
    IDPermessi TINYINT UNSIGNED, 
    FOREIGN KEY (IDUtente) REFERENCES PrgUtenti(IDUtente)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (IDRevisione) REFERENCES PrgRevisioni(IDRevisione)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (IDPermessi) REFERENCES PrgPermessi(IDPermessi)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgCategorie (
    IDCategoria INT PRIMARY KEY AUTO_INCREMENT,
    Nome VARCHAR(255) NOT NULL UNIQUE,
    Descrizione TEXT,
    IDProgetto INT NOT NULL,
    FOREIGN KEY (IDProgetto) REFERENCES PrgProgetti(IDProgetto)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgFile (
    IDFile INT PRIMARY KEY AUTO_INCREMENT,
    NomeFile VARCHAR(255) NOT NULL,
    DataOraFile DATETIME NOT NULL DEFAULT NOW(),
    Descrizione Text,
    IDRevisione INT NOT NULL,
    FOREIGN KEY (IDRevisione) REFERENCES PrgRevisioni(IDRevisione)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgValutazioni (
    IDValutazione INT PRIMARY KEY AUTO_INCREMENT,
    DataOraValutazione DATETIME NOT NULL DEFAULT NOW(),
    Voto INT CHECK (Voto BETWEEN 1 AND 5),
    IDUtente INT,
    IDCategoria INT NOT NULL,
    IDRevisione INT NOT NULL,
    FOREIGN KEY (IDUtente) REFERENCES PrgUtenti(IDUtente)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    FOREIGN KEY (IDCategoria) references PrgCategorie(IDCategoria)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgProblemi (
    IDProblema INT PRIMARY KEY AUTO_INCREMENT,
    Titolo VARCHAR(255) NOT NULL,
    Descrizione TEXT,
    DataOraProblema DATETIME NOT NULL DEFAULT NOW(),
    Aperto BOOLEAN NOT NULL DEFAULT 1,
    IDRevisione INT NOT NULL,
    FOREIGN KEY (IDRevisione) REFERENCES PrgRevisioni(IDRevisione)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgPost (
    IDPost INT PRIMARY KEY AUTO_INCREMENT,
    Titolo VARCHAR(255) NOT NULL,
    Corpo TEXT,
    DataOraPost DATETIME NOT NULL DEFAULT NOW(),
    Allegato VARCHAR(255),
    IDProblema INT NOT NULL,
    IDUtente INT,
    FOREIGN KEY (IDProblema) REFERENCES PrgProblemi(IDProblema)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (IDUtente) REFERENCES PrgUtenti(IDUtente)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;