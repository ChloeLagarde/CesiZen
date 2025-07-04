<?php
// vues/ressources.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controleur/gestionRessources.php';

$gestionRessources = new GestionRessources();
$userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
$userRole = isset($_SESSION['user']) ? $_SESSION['user']['role'] : null;

// Passer les informations correctes à la méthode recupererRessources
$ressources = $gestionRessources->recupererRessources($userId, $userRole);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ressources - Suivi Bien-être</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <!-- Menu -->
    <?php include("header.php"); ?>

    <main class="container">
        <section class="ressources-container">
            <h2 class="mb-4 text-center">Nos Ressources</h2>
            
            <?php if (empty($ressources)): ?>
                <div class="alert alert-info text-center">
                    <?php if (!isset($_SESSION['user'])): ?>
                        Veuillez vous connecter pour accéder aux ressources.
                    <?php else: ?>
                        Aucune ressource disponible pour le moment.
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($ressources as $ressource) : ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($ressource['titre']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($ressource['description']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include("composants/footer.php"); ?>