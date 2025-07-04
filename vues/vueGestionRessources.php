<?php
session_start();

// Vérifie que l'utilisateur est connecté et est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';
$db = (new Database())->getConnection();

// Récupération des ressources
$stmt = $db->query("SELECT * FROM ressources");
$ressources = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<div class="container">
    <h1>Liste des ressources :</h1>
    <a href="ajoutRessource.php" class="btn btn-primary">AJOUTER</a>
    <table class="table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Description</th>
                <th>Catégories</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ressources as $ressource) : ?>
                <tr>
                <td><?= htmlspecialchars($ressource['titre'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ressource['description'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ressource['categorie'] ?? '') ?></td>
                    <td>
                        <a href="modifierRessource.php?id=<?= $ressource['id'] ?>" class="btn btn-warning">MODIFIER</a>
                        <form action="../controleur/gestionRessources.php" method="post" style="display:inline;" onsubmit="return confirm('Confirmer la suppression de cet utilisateur ?');">
                            <input type="hidden" name="ressource_id" value="<?= $ressource['id'] ?>">
                            <input type="hidden" name="action" value="supprimer">
                            <button type="submit" class="btn btn-danger">SUPPRIMER</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'composants/footer.php'; ?>

