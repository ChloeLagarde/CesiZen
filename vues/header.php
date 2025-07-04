<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CesiZen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Styles personnalisés -->
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nom = isset($_SESSION['user']['nom']) ? htmlspecialchars($_SESSION['user']['nom']) : 'Utilisateur';
$role = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="Exercice.php">CesiZen</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="Exercice.php">Exercices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ressources.php">Information</a>
                </li>

                <?php if (isset($_SESSION['user'])): ?>
                    <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="vueGestionRessources.php">Gestion des ressources</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="vueGestionUser.php">Gestion des utilisateurs</a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="confirmerDeconnexion()">Déconnexion</a>
                    </li>
                    <!-- Remplacer la ligne avec "Bonjour, <?= $nom ?>" par: -->
                    <li class="nav-item">
                        <a class="nav-link" href="monCompte.php">
                            Bonjour, <?= $nom ?> (<?= $role ?>)
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Connexion</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script>
function confirmerDeconnexion() {
    if (confirm("Voulez-vous vraiment vous déconnecter ?")) {
        window.location.href = "logout.php";
    }
}
</script>
