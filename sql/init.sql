/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE IF NOT EXISTS PrgUsers (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    user_datetime DATETIME NOT NULL DEFAULT NOW(),
    bio TEXT
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgNotifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    action_link VARCHAR(255),
    user_id INT NOT NULL,
    notification_datetime DATETIME NOT NULL DEFAULT NOW(),
    FOREIGN KEY (user_id) REFERENCES PrgUsers(user_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgProjects (
    project_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    abstract TEXT,
    project_datetime DATETIME NOT NULL DEFAULT NOW(),
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES PrgUsers(user_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

-- I permessi sono conservati in una stringa binaria organizzata come segue
-- Il bit meno significativo è il permesso in lettura
-- A seguire è permesso di aggiungere commenti
-- Modificare il contenuto della revisione
-- Valutare la revisione del progetto come membro interno
-- Eliminare progetti

CREATE TABLE IF NOT EXISTS PrgPermissions (
    permissions_id TINYINT UNSIGNED PRIMARY KEY,
    permissions_name VARCHAR(255) NOT NULL UNIQUE,
    permissions_description TEXT
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

INSERT INTO PrgPermissions 
    (permissions_id, permissions_name, permissions_description)
VALUES 
    (0b00000, "Nascosto", "Gli utenti con questi permessi non hanno alcun accesso alla revisione"),
    (0b00001, "Visualizzatore", "Gli utenti con questi permessi possono visualizzare la revisione"),
    (0b00011, "Commentatore", "Gli utenti con questi permessi possono commentare la revisione"),
    (0b00111, "Editor", "Gli utenti con questi permessi possono modificare la revisione"),
    (0b01011, "Valutatore", "Gli utenti con questi permessi possono valutare la revisione secondo le caratteristiche valutate dal progetto"),
    (0b01111, "Manutentore", "Gli utenti con questi permessi possono modificare e valutare la revisione secondo le caratteristiche valutate dal progetto"),
    (0b11111, "Admin", "Gli utenti con questi permessi possono effettuare qualsiasi operazione sulla revision, compresa l'eliminazione")
ON DUPLICATE KEY UPDATE
    permissions_name = VALUES(permissions_name),
    permissions_description = VALUES(permissions_description);

CREATE TABLE IF NOT EXISTS PrgRevisions (
    revision_id INT PRIMARY KEY AUTO_INCREMENT,
    revision_number INT NOT NULL,
    motivations TEXT,
    revision_datetime DATETIME NOT NULL DEFAULT NOW(),
    start_date DATE NOT NULL DEFAULT CURRENT_DATE(),
    end_date DATE,
    project_id INT NOT NULL,
    permissions_id TINYINT UNSIGNED NOT NULL DEFAULT 0b00001,
    FOREIGN KEY (project_id) REFERENCES PrgProjects(project_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (permissions_id) REFERENCES PrgPermissions(permissions_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgCollaborators (
    user_id INT,
    revision_id INT,
    permissions_id TINYINT UNSIGNED, 
    FOREIGN KEY (user_id) REFERENCES PrgUsers(user_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (revision_id) REFERENCES PrgRevisions(revision_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (permissions_id) REFERENCES PrgPermissions(permissions_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgCategories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    project_id INT NOT NULL,
    FOREIGN KEY (project_id) REFERENCES PrgProjects(project_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgFiles (
    file_id INT PRIMARY KEY AUTO_INCREMENT,
    file_name VARCHAR(255) NOT NULL,
    file_datetime DATETIME NOT NULL DEFAULT NOW(),
    file_description Text,
    revision_id INT NOT NULL,
    FOREIGN KEY (revision_id) REFERENCES PrgRevisions(revision_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgEvaluations (
    evaluation_id INT PRIMARY KEY AUTO_INCREMENT,
    evaluation_datetime DATETIME NOT NULL DEFAULT NOW(),
    grade INT CHECK (grade BETWEEN 1 AND 5),
    user_id INT,
    category_id INT NOT NULL,
    revision_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES PrgUsers(user_id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    FOREIGN KEY (category_id) references PrgCategories(category_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (revision_id) references PrgRevisions(revision_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgIssues (
    issue_id INT PRIMARY KEY AUTO_INCREMENT,
    issue_title VARCHAR(255) NOT NULL,
    issue_description TEXT,
    issue_datetime DATETIME NOT NULL DEFAULT NOW(),
    is_open BOOLEAN NOT NULL DEFAULT 1,
    revision_id INT NOT NULL,
    FOREIGN KEY (revision_id) REFERENCES PrgRevisions(revision_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

CREATE TABLE IF NOT EXISTS PrgPosts (
    post_id INT PRIMARY KEY AUTO_INCREMENT,
    post_title VARCHAR(255) NOT NULL,
    body TEXT,
    post_datetime DATETIME NOT NULL DEFAULT NOW(),
    attachment VARCHAR(255),
    issue_id INT NOT NULL,
    user_id INT,
    FOREIGN KEY (issue_id) REFERENCES PrgIssues(issue_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES PrgUsers(user_id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT charset=utf8mb4;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;