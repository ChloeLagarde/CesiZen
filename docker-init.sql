-- Script d'initialisation Docker pour CesiZen
-- Ce fichier sera exécuté automatiquement au premier démarrage du conteneur MySQL

-- Création de la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS cesizentest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cesizentest;

-- Création de l'utilisateur dédié avec permissions limitées
CREATE USER IF NOT EXISTS 'cesizen_user'@'%' IDENTIFIED BY 'cesizen_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON cesizentest.* TO 'cesizen_user'@'%';
FLUSH PRIVILEGES;

-- Table Utilisateurs avec colonnes supplémentaires pour Docker
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    prenom VARCHAR(50),
    nom VARCHAR(50),
    email VARCHAR(100) NOT NULL UNIQUE,
    tel VARCHAR(20),
    adresse VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    est_actif TINYINT(1) DEFAULT 1,
    role ENUM('admin', 'utilisateur') NOT NULL DEFAULT 'utilisateur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    
    -- Index pour optimiser les performances
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_est_actif (est_actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table Ressources avec améliorations pour Docker
DROP TABLE IF EXISTS ressources;
CREATE TABLE ressources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    fichier_path VARCHAR(500),
    categories VARCHAR(255),
    auteur VARCHAR(100),
    uploadé_par INT,
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    est_visible BOOLEAN DEFAULT TRUE,
    taille_fichier BIGINT,
    type_mime VARCHAR(100),
    downloads_count INT DEFAULT 0,
    
    -- Clé étrangère
    FOREIGN KEY (uploadé_par) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Index pour optimiser les performances
    INDEX idx_titre (titre),
    INDEX idx_categories (categories),
    INDEX idx_est_visible (est_visible),
    INDEX idx_date_upload (date_upload),
    INDEX idx_uploade_par (uploadé_par)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table Sessions pour gérer les sessions utilisateur
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table Logs pour traçabilité
CREATE TABLE IF NOT EXISTS logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_table_name (table_name),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des données par défaut
-- Mot de passe hashé pour "admin123" : $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO users (username, prenom, nom, email, password, role, est_actif) VALUES 
('admin', 'Administrateur', 'CesiZen', 'admin@cesizen.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1),
('user_demo', 'Utilisateur', 'Demo', 'user@cesizen.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'utilisateur', 1);

-- Insertion de ressources de démonstration
INSERT INTO ressources (titre, description, categories, auteur, uploadé_par, est_visible) VALUES 
('Guide de respiration 4-7-8', 'Technique de respiration pour réduire l\'anxiété et favoriser l\'endormissement', 'Respiration,Relaxation', 'Dr. Andrew Weil', 1, TRUE),
('Méditation guidée de 10 minutes', 'Séance de méditation pour débutants axée sur la respiration consciente', 'Méditation,Bien-être', 'Équipe CesiZen', 1, TRUE),
('Exercices de cohérence cardiaque', 'Programme d\'exercices pour améliorer la variabilité cardiaque', 'Cohérence cardiaque,Santé', 'Dr. David Servan-Schreiber', 1, TRUE);

-- Procédure pour nettoyer les sessions expirées
DELIMITER //
CREATE PROCEDURE CleanExpiredSessions()
BEGIN
    DELETE FROM user_sessions WHERE expires_at < NOW();
END //
DELIMITER ;

-- Événement pour nettoyer automatiquement les sessions expirées (toutes les heures)
SET GLOBAL event_scheduler = ON;
CREATE EVENT IF NOT EXISTS clean_sessions
ON SCHEDULE EVERY 1 HOUR
DO
    CALL CleanExpiredSessions();

-- Vue pour statistiques utilisateurs
CREATE VIEW user_stats AS
SELECT 
    role,
    COUNT(*) as total_users,
    SUM(CASE WHEN est_actif = 1 THEN 1 ELSE 0 END) as active_users,
    SUM(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as recent_users
FROM users 
GROUP BY role;

-- Vue pour statistiques ressources
CREATE VIEW resource_stats AS
SELECT 
    categories,
    COUNT(*) as total_resources,
    SUM(CASE WHEN est_visible = 1 THEN 1 ELSE 0 END) as visible_resources,
    SUM(downloads_count) as total_downloads
FROM ressources 
GROUP BY categories;

-- Configuration MySQL optimisée pour Docker
SET @@global.sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- Message de fin d'initialisation
SELECT 'Base de données CesiZen initialisée avec succès pour Docker!' as message;