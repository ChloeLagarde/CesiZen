<?php
session_start();

// Vérifie que l'utilisateur est connecté et est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';
$db = (new Database())->getConnection();

// Vérifier qu'un ID est bien fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: vueGestionUser.php?message=error&type=missing_id');
    exit();
}

$id = $_GET['id'];

// Récupération des informations de l'utilisateur
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si aucun utilisateur trouvé avec cet ID
if (!$user) {
    header('Location: vueGestionUser.php?message=error&type=not_found');
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
?>

<?php include 'header.php'; ?>

<div class="container">
    <h1>Modifier un utilisateur</h1>
    
    <?php if (isset($error) && $error === 'email_exists'): ?>
        <div class="alert alert-danger">
            Cet email est déjà utilisé par un autre compte.
        </div>
    <?php endif; ?>
    
    <form action="../controleur/gestionUser.php" method="post">
        <input type="hidden" name="action" value="modifier">
        <input type="hidden" name="id" value="<?= $id ?>">
        
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
            <label for="prenom">Prénom :</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>
            <?php if ($error === 'missing_info' && empty($prenom)): ?>
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
            <label for="email">Email :</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            <?php if ($error === 'missing_info' && empty($email)): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez remplir ce champ.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="tel">Téléphone :</label>
            <input type="text" class="form-control" id="tel" name="tel" value="<?= htmlspecialchars($tel) ?>">
        </div>
        
        <div class="form-group">
            <label for="adresse">Adresse :</label>
            <input type="text" class="form-control" id="adresse" name="adresse" value="<?= htmlspecialchars($adresse) ?>">
        </div>
        
        <div class="form-group">
            <label for="role">Rôle :</label>
            <select class="form-control" id="role" name="role" required>
                <option value="utilisateur" <?= $role === 'utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Administrateur</option>
            </select>
        </div>
        
        <div class="form-group">
            <p class="text-muted">
                Note : Si vous souhaitez modifier le mot de passe, veuillez créer une nouvelle fonctionnalité dédiée.
            </p>
        </div>
        
        <button type="submit" class="btn btn-primary">ENREGISTRER LES MODIFICATIONS</button>
        <a href="vueGestionUser.php" class="btn btn-secondary">ANNULER</a>
    </form>
</div>

<?php include 'composants/footer.php'; ?>