<?php
session_start();

// Vérifie que l'utilisateur est connecté et est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

// Récupération des valeurs soumises en cas d'erreur pour pré-remplir le formulaire
$titre = $_GET['titre'] ?? '';
$description = $_GET['description'] ?? '';
$categories = $_GET['categories'] ?? '';
$error = $_GET['error'] ?? '';
?>

<?php include 'header.php'; ?>

<div class="container">
    <h1>Ajouter une ressource</h1>
    
    <form action="../controleur/gestionRessources.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="ajouter">
        
        <div class="form-group">
            <label for="titre">Titre :</label>
            <input type="text" class="form-control" id="titre" name="titre" value="<?= htmlspecialchars($titre) ?>" required>
            <?php if ($error === 'missing_info' && empty($titre)): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez remplir ce champ.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="description">Description :</label>
            <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($description) ?></textarea>
            <?php if ($error === 'missing_info' && empty($description)): ?>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez remplir ce champ.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="categories">Catégories (séparées par des virgules) :</label>
            <input type="text" class="form-control" id="categories" name="categories" value="<?= htmlspecialchars($categories) ?>">
        </div>
        
        <div class="form-group">
            <label for="auteur">Auteur :</label>
            <input type="text" class="form-control" id="auteur" name="auteur" value="<?= htmlspecialchars($_SESSION['user']['username']) ?>">
        </div>
        
        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="est_visible" name="est_visible" value="1" checked>
                <label class="form-check-label" for="est_visible">
                    Visible pour tous les utilisateurs
                </label>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">ENREGISTRER</button>
        <a href="vueGestionRessources.php" class="btn btn-secondary">ANNULER</a>
    </form>
</div>

<?php include 'composants/footer.php'; ?>