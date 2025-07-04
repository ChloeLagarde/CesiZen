<?php
session_start();

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';
$db = (new Database())->getConnection();

// Récupération de l'ID de l'utilisateur connecté
$id = $_SESSION['user']['id'];

// Récupération des informations de l'utilisateur
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si aucun utilisateur trouvé avec cet ID (ce qui ne devrait pas arriver)
if (!$user) {
    header('Location: index.php?message=error&type=not_found');
    exit();
}

// Récupération des valeurs
$username = $user['username'] ?? '';
$prenom = $user['prenom'] ?? '';
$nom = $user['nom'] ?? '';
$email = $user['email'] ?? '';
$tel = $user['tel'] ?? '';
$adresse = $user['adresse'] ?? '';
$role = $user['role'] ?? 'utilisateur';

// Récupération des messages d'erreur éventuels
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>

<?php include 'header.php'; ?>

<div class="container">
    <h1>Mon compte</h1>
    
    <?php if (isset($success) && $success === 'updated'): ?>
        <div class="alert alert-success">
            Vos informations ont été mises à jour avec succès.
        </div>
    <?php endif; ?>
    
    <?php if (isset($error) && $error === 'email_exists'): ?>
        <div class="alert alert-danger">
            Cet email est déjà utilisé par un autre compte.
        </div>
    <?php endif; ?>
    
    <form action="../controleur/gestionCompte.php" method="post">
        <input type="hidden" name="action" value="modifier_compte">
        
        <div class="form-group mb-3">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>
            <?php if ($error === 'missing_info' && empty($username)): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez remplir ce champ.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group mb-3">
            <label for="prenom">Prénom :</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>
            <?php if ($error === 'missing_info' && empty($prenom)): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez remplir ce champ.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group mb-3">
            <label for="nom">Nom :</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
            <?php if ($error === 'missing_info' && empty($nom)): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez remplir ce champ.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group mb-3">
            <label for="email">Email :</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            <?php if ($error === 'missing_info' && empty($email)): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez remplir ce champ.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group mb-3">
            <label for="tel">Téléphone :</label>
            <input type="text" class="form-control" id="tel" name="tel" value="<?= htmlspecialchars($tel) ?>">
        </div>
        
        <div class="form-group mb-3">
            <label for="adresse">Adresse :</label>
            <input type="text" class="form-control" id="adresse" name="adresse" value="<?= htmlspecialchars($adresse) ?>">
        </div>
        
        <!-- Le rôle n'est pas modifiable par l'utilisateur -->
        <div class="form-group mb-3">
            <label>Rôle :</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($role) ?>" readonly>
            <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">
        </div>
        
        <div class="form-group mb-3">
            <p class="text-muted">
                Note : Si vous souhaitez modifier votre mot de passe, utilisez le formulaire ci-dessous.
            </p>
        </div>
        
        <button type="submit" class="btn btn-primary">ENREGISTRER LES MODIFICATIONS</button>
        <a href="index.php" class="btn btn-secondary">ANNULER</a>
    </form>
    
    <hr class="my-4">
    
    <h2>Changer mon mot de passe</h2>
    
    <?php if (isset($success) && $success === 'password_updated'): ?>
        <div class="alert alert-success">
            Votre mot de passe a été mis à jour avec succès.
        </div>
    <?php endif; ?>
    
    <?php if (isset($error) && $error === 'wrong_password'): ?>
        <div class="alert alert-danger">
            Votre mot de passe actuel est incorrect.
        </div>
    <?php endif; ?>
    
    <?php if (isset($error) && $error === 'password_mismatch'): ?>
        <div class="alert alert-danger">
            Les nouveaux mots de passe ne correspondent pas.
        </div>
    <?php endif; ?>
    
    <form action="../controleur/gestionCompte.php" method="post">
        <input type="hidden" name="action" value="changer_mdp">
        
        <div class="form-group mb-3">
            <label for="current_password">Mot de passe actuel :</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        
        <div class="form-group mb-3">
            <label for="new_password">Nouveau mot de passe :</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        
        <div class="form-group mb-3">
            <label for="confirm_password">Confirmer le nouveau mot de passe :</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit" class="btn btn-warning">CHANGER MON MOT DE PASSE</button>
    </form>
</div>

<?php include 'composants/footer.php'; ?>