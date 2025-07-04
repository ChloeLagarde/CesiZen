<?php
session_start();

// Vérifie que l'utilisateur est connecté et est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';
$db = (new Database())->getConnection();

// Récupération des utilisateurs
$stmt = $db->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<div class="container">
    <?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
        <?php if ($_GET['type'] === 'add'): ?>
            <div class="alert alert-success">
                Utilisateur ajouté avec succès
            </div>
        <?php elseif ($_GET['type'] === 'update'): ?>
            <div class="alert alert-success">
                Utilisateur modifié avec succès
            </div>
        <?php elseif ($_GET['type'] === 'delete'): ?>
            <div class="alert alert-success">
                Suppression effectuée
            </div>
        <?php elseif ($_GET['type'] === 'disable'): ?>
            <div class="alert alert-success">
                Compte utilisateur désactivé avec succès
            </div>
        <?php elseif ($_GET['type'] === 'enable'): ?>
            <div class="alert alert-success">
                Compte utilisateur réactivé avec succès
            </div>
        <?php endif; ?>
    <?php elseif (isset($_GET['message']) && $_GET['message'] === 'error'): ?>
        <div class="alert alert-danger">
            <?php if ($_GET['type'] === 'missing_info'): ?>
                Informations manquantes. Veuillez remplir tous les champs obligatoires.
            <?php else: ?>
                Une erreur est survenue. Veuillez réessayer.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <h1>Liste des utilisateurs :</h1>
    <a href="ajoutUser.php" class="btn btn-primary">AJOUTER</a>
    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Role</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?= htmlspecialchars($user['nom'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['prenom'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['role'] ?? '') ?></td>
                    <td>
                        <?php if (isset($user['est_actif']) && $user['est_actif'] == 0): ?>
                            <span class="badge badge-danger">Désactivé</span>
                        <?php else: ?>
                            <span class="badge badge-success">Actif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="modifierUser.php?id=<?= $user['id'] ?>" class="btn btn-warning">MODIFIER</a>
                        
                        <?php if (!isset($user['est_actif']) || $user['est_actif'] == 1): ?>
                            <form action="../controleur/gestionUser.php" method="post" style="display:inline;" onsubmit="return confirm('Confirmer la désactivation de ce compte ?');">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <input type="hidden" name="action" value="desactiver">
                                <button type="submit" class="btn btn-secondary">DÉSACTIVER</button>
                            </form>
                        <?php else: ?>
                            <form action="../controleur/gestionUser.php" method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <input type="hidden" name="action" value="activer">
                                <button type="submit" class="btn btn-info">RÉACTIVER</button>
                            </form>
                        <?php endif; ?>
                        
                        <form action="../controleur/gestionUser.php" method="post" style="display:inline;" onsubmit="return confirm('Confirmer la suppression de cet utilisateur ?');">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
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