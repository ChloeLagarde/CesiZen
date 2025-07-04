<?php
// vue/login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
};
require_once '../controleur/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $auth = new Auth();
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $auth->login($email, $password);
    
    if (is_array($result)) {
        // Si le résultat est un tableau avec une indication de succès
        if ($result['success']) {
            $_SESSION['user'] = $result['user'];
            header('Location: index.php');
            exit();
        } else {
            // Afficher le message d'erreur
            $error = $result['message'];
        }
    } else if ($result) {
        // Rétrocompatibilité avec l'ancien système
        $_SESSION['user'] = $result;
        header('Location: index.php');
        exit();
    } else {
        $error = 'Email ou mot de passe incorrect';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Emotion Tracker</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        main {
            height: 70vh;
        }
        .form-container {
            max-width: 95%;
            width: 100%;
        }

        @media (min-width: 768px) {
            .form-container {
                max-width: 50%;
            }
        }
    </style>
</head>
<body>
    <main class="container d-flex justify-content-center align-items-center">
        <div class="form-container">
            <div class="card p-4 shadow-sm w-100">
                <h2 class="mb-4 text-center">Connexion</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
                <p class="mt-3 text-center">
                    Pas encore de compte ? <a href="register.php">Créer un compte</a>
                </p>

            </div>
        </div>
    </main>
</body>
</html>
