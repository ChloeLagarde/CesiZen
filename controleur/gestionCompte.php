<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: ../vues/login.php');
    exit();
}

$db = (new Database())->getConnection();

// Fonction pour modifier les informations du compte
function modifierCompte($db) {
    // L'utilisateur ne peut modifier que son propre compte
    $id = $_SESSION['user']['id'];
    
    $username = $_POST['username'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $role = $_SESSION['user']['role']; // On utilise le rôle existant, il ne peut pas être modifié

    if ($username && $email) {
        // Vérifier si l'email existe déjà (pour un autre utilisateur)
        $checkEmail = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
        $checkEmail->execute([$email, $id]);
        $emailExists = $checkEmail->fetchColumn();

        if ($emailExists > 0) {
            header("Location: ../vues/monCompte.php?error=email_exists");
            exit();
        }

        $stmt = $db->prepare("UPDATE users SET username = ?, prenom = ?, nom = ?, email = ?, tel = ?, adresse = ? WHERE id = ?");
        $result = $stmt->execute([$username, $prenom, $nom, $email, $tel, $adresse, $id]);
        
        if ($result) {
            // Mettre à jour les informations de session
            $_SESSION['user']['username'] = $username;
            $_SESSION['user']['nom'] = $nom;
            $_SESSION['user']['email'] = $email;
            
            header("Location: ../vues/monCompte.php?success=updated");
        } else {
            header("Location: ../vues/monCompte.php?error=db_error");
        }
        exit();
    }
    
    header("Location: ../vues/monCompte.php?error=missing_info");
    exit();
}

// Fonction pour changer le mot de passe
function changerMotDePasse($db) {
    $id = $_SESSION['user']['id'];
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Vérifier si le mot de passe actuel est correct
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($currentPassword, $user['password'])) {
        header("Location: ../vues/monCompte.php?error=wrong_password");
        exit();
    }
    
    // Vérifier si les nouveaux mots de passe correspondent
    if ($newPassword !== $confirmPassword) {
        header("Location: ../vues/monCompte.php?error=password_mismatch");
        exit();
    }
    
    // Mettre à jour le mot de passe
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $result = $stmt->execute([$hashedPassword, $id]);
    
    if ($result) {
        header("Location: ../vues/monCompte.php?success=password_updated");
    } else {
        header("Location: ../vues/monCompte.php?error=db_error");
    }
    exit();
}

// Dispatcher des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'modifier_compte':
            modifierCompte($db);
            break;
        case 'changer_mdp':
            changerMotDePasse($db);
            break;
        default:
            header("Location: ../vues/monCompte.php");
            exit();
    }
}