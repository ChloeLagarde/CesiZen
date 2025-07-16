<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

$db = (new Database())->getConnection();

// Vérifie si l'utilisateur est admin pour toutes les actions ci-dessous
function verifierAdmin() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header("Location: ../vues/login.php");
        exit();
    }
}

// Ajouter un utilisateur
function ajouterUtilisateur($db) {
    verifierAdmin();

    $username = $_POST['username'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'utilisateur';

    if ($username && $email && $password) {
        // Vérifier si l'email existe déjà
        $checkEmail = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $checkEmail->execute([$email]);
        $emailExists = $checkEmail->fetchColumn();

        if ($emailExists > 0) {
            // Utiliser le nom de fichier correct ici
            header("Location: ../vues/ajoutUser.php?error=email_exists&username=" . urlencode($username) . 
                "&prenom=" . urlencode($prenom) . "&nom=" . urlencode($nom) . 
                "&tel=" . urlencode($tel) . "&adresse=" . urlencode($adresse) . 
                "&role=" . urlencode($role));
            exit();
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (username, prenom, nom, email, tel, adresse, password, role, est_actif) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $result = $stmt->execute([$username, $prenom, $nom, $email, $tel, $adresse, $hashedPassword, $role]);
        
        if ($result) {
            header("Location: ../vues/vueGestionUser.php?message=success&type=add");
        } else {
            header("Location: ../vues/ajoutUser.php?error=db_error&username=" . urlencode($username) . 
                "&prenom=" . urlencode($prenom) . "&nom=" . urlencode($nom) . 
                "&email=" . urlencode($email) . "&tel=" . urlencode($tel) . 
                "&adresse=" . urlencode($adresse) . "&role=" . urlencode($role));
        }
        exit();
    }
    
    header("Location: ../vues/ajoutUser.php?error=missing_info");
    exit();
}

// Modifier un utilisateur
function modifierUtilisateur($db) {
    verifierAdmin();

    $id = $_POST['id'] ?? null;
    $username = $_POST['username'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $role = $_POST['role'] ?? 'utilisateur';

    if ($id) {
        $stmt = $db->prepare("UPDATE users SET username = ?, prenom = ?, nom = ?, email = ?, tel = ?, adresse = ?, role = ? WHERE id = ?");
        $result = $stmt->execute([$username, $prenom, $nom, $email, $tel, $adresse, $role, $id]);
        
        if ($result) {
            header("Location: ../vues/vueGestionUser.php?message=success&type=update");
        } else {
            header("Location: ../vues/vueGestionUser.php?message=error");
        }
        exit();
    }
    
    header("Location: ../vues/vueGestionUser.php?message=error&type=missing_info");
    exit();
}

function supprimerUtilisateur($db) {
    verifierAdmin();

    $id = $_POST['id'] ?? null;

    if ($id) {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            header("Location: ../vues/vueGestionUser.php?message=success&type=delete");
        } else {
            header("Location: ../vues/vueGestionUser.php?message=error");
        }
        exit();
    }
    
    header("Location: ../vues/vueGestionUser.php?message=error&type=missing_info");
    exit();
}

// Désactiver un utilisateur
function desactiverUtilisateur($db) {
    verifierAdmin();

    $id = $_POST['id'] ?? null;

    if ($id) {
        // Vérifier qu'on ne désactive pas le dernier admin
        if (isLastActiveAdmin($db, $id)) {
            header("Location: ../vues/vueGestionUser.php?message=error&type=last_admin");
            exit();
        }

        $stmt = $db->prepare("UPDATE users SET est_actif = 0 WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            header("Location: ../vues/vueGestionUser.php?message=success&type=disable");
        } else {
            header("Location: ../vues/vueGestionUser.php?message=error");
        }
        exit();
    }
    
    header("Location: ../vues/vueGestionUser.php?message=error&type=missing_info");
    exit();
}

// Activer un utilisateur
function activerUtilisateur($db) {
    verifierAdmin();

    $id = $_POST['id'] ?? null;

    if ($id) {
        $stmt = $db->prepare("UPDATE users SET est_actif = 1 WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            header("Location: ../vues/vueGestionUser.php?message=success&type=enable");
        } else {
            header("Location: ../vues/vueGestionUser.php?message=error");
        }
        exit();
    }
    
    header("Location: ../vues/vueGestionUser.php?message=error&type=missing_info");
    exit();
}

// Fonction utilitaire pour vérifier si c'est le dernier admin actif
function isLastActiveAdmin($db, $id) {
    // Vérifie si l'utilisateur est un admin
    $checkAdmin = $db->prepare("SELECT role FROM users WHERE id = ?");
    $checkAdmin->execute([$id]);
    $role = $checkAdmin->fetchColumn();
    
    if ($role !== 'admin') {
        // Si l'utilisateur n'est pas admin, aucun problème à le désactiver
        return false;
    }
    
    // Compter le nombre d'admins actifs
    $countAdmins = $db->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND est_actif = 1");
    $countAdmins->execute();
    $nbAdmins = $countAdmins->fetchColumn();
    
    // Si c'est le dernier admin actif, renvoyer true
    return $nbAdmins <= 1;
}

// Dispatcher des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'ajouter':
            ajouterUtilisateur($db);
            break;
        case 'modifier':
            modifierUtilisateur($db);
            break;
        case 'supprimer':
            supprimerUtilisateur($db);
            break;
        case 'desactiver':
            desactiverUtilisateur($db);
            break;
        case 'activer':
            activerUtilisateur($db);
            break;
    }
}