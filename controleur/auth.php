<?php
// controleur/auth.php

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../config/database.php');

if (!class_exists('Auth')) {
    class Auth {
        private $conn;

        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
            
            if ($this->conn === null) {
                die('La connexion à la base de données a échoué.');
            }
        }

        public function register($username, $email, $password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            
            try {
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                
                return $stmt->execute();
            } catch(PDOException $e) {
                echo "Erreur d'inscription : " . $e->getMessage();
                return false;
            }
        }

        public function login($email, $password) {
            $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
            
            try {
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    // Vérifier si le compte est actif
                    if (isset($user['est_actif']) && $user['est_actif'] == 0) {
                        return ['success' => false, 'message' => 'Votre compte a été désactivé. Veuillez contacter un administrateur.'];
                    }
                    
                    // Stocker les informations utilisateur en session
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'role' => $user['role'] ?? 'user'
                    ];
                    
                    // Compte actif, autoriser la connexion
                    return [
                        'success' => true,
                        'user' => $_SESSION['user']
                    ];
                }
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
            } catch(PDOException $e) {
                echo "Erreur de connexion : " . $e->getMessage();
                return ['success' => false, 'message' => 'Erreur de connexion à la base de données.'];
            }
        }
        
        // Ajouter une méthode de déconnexion pour faciliter les tests
        public function logout() {
            session_unset(); // Supprimer toutes les variables de session
            session_destroy(); // Détruire la session
            return true;
        }
    }
}

// Code pour traiter la déconnexion via une requête GET
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $auth = new Auth();
    $auth->logout();
    header("Location: ../vues/login.php");
    exit();
}