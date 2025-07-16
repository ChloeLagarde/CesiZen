<?php
// Vérifier si la classe Database n'existe pas déjà
if (!class_exists('Database')) {
    class Database {
        private $host = 'localhost';
        private $db_name = 'cesizentest'; // remplace par le nom de ta base
        private $username = 'root';
        private $password = '';
        private $conn;

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