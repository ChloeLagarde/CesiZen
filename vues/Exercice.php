<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("header.php"); ?>

<div class="container text-center mt-5">
    <h1 class="fw-bold">Bienvenue aux exercises de respiration</h1>
    <p class="lead">Veuillez choisir l'exercise de respiration de votre choix</p>

    <div class="row justify-content-center mt-5">
        <!-- 748 Pattern Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm p-3 h-100">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-wind fs-1 text-primary"></i>
                    </div>
                    <h5 class="card-title fw-bold">Avancé</h5>
                    <p class="card-text">Exercice de respiration avancé</p>
                    <div class="text-muted small">
                        <p>Inspiration: 7s</p>
                        <p>Apnee: 4s</p>
                        <p>Expiration: 8s</p>
                    </div>
                </div>
                <a href="respiration.php?pattern=748" class="stretched-link"></a>
            </div>
        </div>

        <!-- 55 Pattern Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm p-3 h-100">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-wind fs-1 text-primary"></i>
                    </div>
                    <h5 class="card-title fw-bold">Moyen</h5>
                    <p class="card-text">Exercice de respiration moyen</p>
                    <div class="text-muted small">
                        <p>Inspiration: 5s</p>
                        <p>Apnee: 0s</p>
                        <p>Expiration: 5s</p>
                    </div>
                </div>
                <a href="respiration.php?pattern=55" class="stretched-link"></a>
            </div>
        </div>

        <!-- 46 Pattern Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm p-3 h-100">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-wind fs-1 text-primary"></i>
                    </div>
                    <h5 class="card-title fw-bold">Simple</h5>
                    <p class="card-text">Exercice de respiration calme</p>
                    <div class="text-muted small">
                        <p>Inspiration: 4s</p>
                        <p>Apnee: 0s</p>
                        <p>Expiration: 6s</p>
                    </div>
                </div>
                <a href="respiration.php?pattern=46" class="stretched-link"></a>
            </div>
        </div>
    </div>
</div>

<?php include("composants/footer.php"); ?>
