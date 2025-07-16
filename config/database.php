<?php
// config/database.php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        // Configuration pour les tests ou l'environnement
        $this->host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'cesizentest';
        $this->username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root';
        $this->password = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '';
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            // Affiche les erreurs SQL
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // En mode test, ne pas mourir brutalement
            if (defined('PHPUNIT_RUNNING') || getenv('PHPUNIT_RUNNING')) {
                throw new Exception("Erreur de connexion : " . $exception->getMessage());
            }
            die("Erreur de connexion : " . $exception->getMessage());
        }

        return $this->conn;
    }
    
    // Méthode statique pour la rétrocompatibilité
    public static function connect() {
        $database = new self();
        return $database->getConnection();
    }
    
    // Méthode pour fermer la connexion
    public static function disconnect() {
        // Méthode vide pour la compatibilité
        return true;
    }
}
?>