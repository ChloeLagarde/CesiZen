<?php
/**
 * Configuration de base de données pour Docker
 * Ce fichier sera utilisé en environnement containerisé
 */
class Database {
    // Configuration pour Docker
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        // Récupération des variables d'environnement Docker
        $this->host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'db';
        $this->db_name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'cesizentest';
        $this->username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? 'cesizen_password';
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            // Log de connexion réussie (à désactiver en production)
            if (getenv('APP_ENV') === 'development') {
                error_log("Connexion BDD réussie vers {$this->host}:{$this->db_name}");
            }
            
        } catch (PDOException $exception) {
            $error_message = "Erreur de connexion BDD: " . $exception->getMessage();
            error_log($error_message);
            
            // En développement, afficher l'erreur détaillée
            if (getenv('APP_ENV') === 'development') {
                die($error_message);
            } else {
                // En production, message générique
                die("Erreur de connexion à la base de données. Veuillez contacter l'administrateur.");
            }
        }

        return $this->conn;
    }

    public function connect() {
        return $this->getConnection();
    }

    public function disconnect() {
        $this->conn = null;
    }

    /**
     * Test de connexion
     */
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            $stmt = $conn->query("SELECT 1");
            return $stmt !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Informations de connexion (pour debug)
     */
    public function getConnectionInfo() {
        return [
            'host' => $this->host,
            'database' => $this->db_name,
            'username' => $this->username,
            'connected' => $this->testConnection()
        ];
    }
}