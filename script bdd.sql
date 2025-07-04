-- Création de la base de données
CREATE DATABASE IF NOT EXISTS cesizentest;
USE cesizentest;

-- Table Utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    prenom VARCHAR(50),
    nom VARCHAR(50),
    email VARCHAR(100) NOT NULL UNIQUE,
    tel VARCHAR(20),
    adresse VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    est_actif int (2),
    role ENUM('admin', 'utilisateur') NOT NULL DEFAULT 'utilisateur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table Ressources
CREATE TABLE IF NOT EXISTS ressources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    fichier_path VARCHAR(255) NOT NULL,
    categories VARCHAR(255),
    uploadé_par INT,
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    est_visible BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (uploadé_par) REFERENCES users(id) ON DELETE SET NULL
);

-- Exemple de procédure pour insérer un utilisateur avec hachage de mot de passe
DELIMITER //
CREATE PROCEDURE InsertUtilisateur(
    IN p_username VARCHAR(50),
    IN p_prenom VARCHAR(50),
    IN p_nom VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_tel VARCHAR(20),
    IN p_adresse TEXT,
    IN p_password VARCHAR(255),
    IN p_role ENUM('admin', 'utilisateur')
)
BEGIN
    INSERT INTO users (
        username, 
        prenom, 
        nom, 
        email, 
        tel, 
        adresse, 
        password, 
        role
    ) VALUES (
        p_username,
        p_prenom,
        p_nom,
        p_email,
        p_tel,
        p_adresse,
        p_password,
        p_role
    );
END //
DELIMITER ;
