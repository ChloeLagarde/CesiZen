<?php
// Vérifier si la classe Database n'existe pas déjà
if (!class_exists('Database')) {
    class Database {
        private $host;
        private $db_name;
        private $username;
        private $password;
        private $conn;

        public function __construct() {
            // Configuration pour Docker
            $this->host = getenv('DB_HOST') ?: 'database'; // nom du service Docker
            $this->db_name = getenv('DB_NAME') ?: 'cesizentest'; // VOTRE BASE DE DONNÉES
            $this->username = getenv('DB_USER') ?: 'root';
            $this->password = getenv('DB_PASS') ?: '';
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
                die("Erreur de connexion : " . $exception->getMessage());
            }

            return $this->conn;
        }
    }
}
?>