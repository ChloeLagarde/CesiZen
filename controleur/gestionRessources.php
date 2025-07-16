<?php
// controleur/gestionRessources.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

class GestionRessources
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    function verifierAdmin()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header("Location: ../vues/login.php");
            exit();
        }
    }

    public function ajouterRessource()
    {
        $this->verifierAdmin();  // Vérifie que c'est un admin

        // Récupération des données POST
        $titre = $_POST['titre'] ?? '';
        $description = $_POST['description'] ?? '';
        $categories = $_POST['catégorie'] ?? '';
        $auteur = $_POST['auteur'] ?? '';
        $uploadé_par = $_POST['uploadé_par'] ?? null;

        // Validation rapide
        if (empty($titre) || empty($auteur)) {
            header("Location: ../vues/ajoutRessource.php?error=missing_info");
            exit();
        }

        // Correction : bonne table et bon nombre de ?
        $stmt = $this->conn->prepare(
            "INSERT INTO ressources (titre, description, categories, uploadé_par) 
             VALUES (?, ?, ?, ?)"
        );

        $result = $stmt->execute([$titre, $description, $categories, $uploadé_par]);

        if ($result) {
            header("Location: ../vues/vueGestionRessources.php?message=success&type=add");
        } else {
            header("Location: ../vues/ajoutRessource.php?error=db_error&titre=" . urlencode($titre) .
                "&description=" . urlencode($description) .
                "&categories=" . urlencode($categories));
        }
        exit();
    }           

        

    function supprimerRessource($ressourceId) {
        $this->verifierAdmin();
    
        if ($ressourceId) {
            // Supprimer l'entrée en base de données
            $stmtDelete = $this->conn->prepare("DELETE FROM ressources WHERE id = :ressource_id");
            $stmtDelete->bindParam(':ressource_id', $ressourceId);
            $result = $stmtDelete->execute();
            
            if ($result) {
                header("Location: ../vues/vueGestionRessources.php?message=success&type=delete");
            } else {
                header("Location: ../vues/vueGestionRessources.php?message=error");
            }
            exit();
        }
    
        header("Location: ../vues/vueGestionRessources.php?message=error&type=missing_info");
        exit();
    }

    function modifierRessource($ressourceId, $titre, $description, $categories) {
        $this->verifierAdmin();
    
        if ($ressourceId) {
            $stmtUpdate = $this->conn->prepare("UPDATE ressources SET titre = :titre, description = :description, categories = :categories WHERE id = :ressource_id");
            $stmtUpdate->bindParam(':titre', $titre);
            $stmtUpdate->bindParam(':description', $description);
            $stmtUpdate->bindParam(':categories', $categories);
            $stmtUpdate->bindParam(':ressource_id', $ressourceId);
            $result = $stmtUpdate->execute();
            
            if ($result) {
                header("Location: ../vues/vueGestionRessources.php?message=success&type=update");
            } else {
                header("Location: ../vues/vueGestionRessources.php?message=error");
            }
            exit();
        }
        
        header("Location: ../vues/vueGestionRessources.php?message=error&type=missing_info");
        exit();
    }
    
    public function recupererRessources($user_id, $role)
    {
        // Modification: permettre à tous les utilisateurs connectés de voir les ressources
        // Les administrateurs voient toutes les ressources
        // Les utilisateurs connectés voient les ressources visibles
        if ($role === 'admin') {
            // Les admins voient tout
            $query = "SELECT * FROM ressources ORDER BY date_upload DESC";
            $stmt = $this->conn->prepare($query);
        } else if (isset($_SESSION['user'])) {
            // Les utilisateurs connectés voient les ressources visibles
            // Suppression de la condition sur acces_ressources qui limitait l'accès
            $query = "SELECT * FROM ressources 
                      WHERE est_visible = TRUE 
                      ORDER BY date_upload DESC";
            $stmt = $this->conn->prepare($query);
        } else {
            // Utilisateurs non connectés ne voient rien
            return [];
        }

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur de récupération des ressources : " . $e->getMessage());
            return [];
        }
    }
}
// Dispatcher des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $gestionRessources = new GestionRessources();
    
    switch ($_POST['action']) {
        case 'ajouter':
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $categories = $_POST['categories'] ?? '';
            $auteur = $_POST['auteur'] ?? '';
            $uploadé_par = $_SESSION['user']['id'] ?? null;
            
            $gestionRessources->ajouterRessource($titre, $description, $categories, $auteur, $uploadé_par);
            break;
            
        case 'modifier':
            $ressourceId = $_POST['ressource_id'] ?? null;
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $categories = $_POST['categories'] ?? '';
            
            $gestionRessources->modifierRessource($ressourceId, $titre, $description, $categories);
            break;
            
            case 'supprimer':
                $ressourceId = $_POST['ressource_id'] ?? null;
                $gestionRessources->supprimerRessource($ressourceId);
                break;
    }
}
?>