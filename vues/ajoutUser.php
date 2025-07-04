<?php
session_start();

// Vérifie que l'utilisateur est connecté et est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

// Récupération des valeurs soumises en cas d'erreur pour pré-remplir le formulaire
$username = $_GET['username'] ?? '';
$prenom = $_GET['prenom'] ?? '';
$nom = $_GET['nom'] ?? '';
$email = $_GET['email'] ?? '';
$tel = $_GET['tel'] ?? '';
$adresse = $_GET['adresse'] ?? '';
$role = $_GET['role'] ?? 'utilisateur';
$error = $_GET['error'] ?? '';
?>

<?php include 'header.php'; ?>

<div class="container">
    <h1>Ajouter un utilisateur</h1>
    
    <form action="../controleur/gestionUser.php" method="post">
        <input type="hidden" name="action" value="ajouter">
        
        <div class="form-group">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>
            <?php if ($error === 'missing_info' && empty($username)): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez remplir ce champ.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
            <?php if ($error === 'missing_info' && empty($nom)): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez remplir ce champ.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>
            <?php if ($error === 'missing_info' && empty($prenom)): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez remplir ce champ.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            <?php if ($error === 'email_exists'): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Adresse mail déjà utilisée.
                </div>
            <?php elseif ($error === 'missing_info' && empty($email)): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez remplir ce champ.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="tel">Téléphone :</label>
            <input type="tel" class="form-control" id="tel" name="tel" value="<?= htmlspecialchars($tel) ?>">
        </div>
        
        <div class="form-group">
            <label for="adresse">Adresse :</label>
            <input type="text" class="form-control" id="adresse" name="adresse" value="<?= htmlspecialchars($adresse) ?>">
        </div>
        
        <div class="form-group">
            <label for="password">Mot de passe :</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <?php if ($error === 'missing_info' && empty($_POST['password'] ?? '')): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez remplir ce champ.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="role">Rôle :</label>
            <select class="form-control" id="role" name="role">
                <option value="utilisateur" <?= $role === 'utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Administrateur</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">ENREGISTRER</button>
        <a href="vueGestionUser.php" class="btn btn-secondary">ANNULER</a>
    </form>
</div>

<?php include 'composants/footer.php'; ?>