<?php
// vue/register.php
require_once '../controleur/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $auth = new Auth();
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas';
    } else {
        if ($auth->register($username, $email, $password)) {
            $success = 'Inscription réussie. Vous pouvez maintenant vous connecter.';
        } else {
            $error = 'Erreur lors de l\'inscription. Cet email est peut-être déjà utilisé.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Emotion Tracker</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        main {
            height: 100vh;
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
                <h2 class="mb-4 text-center">Inscription</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success text-center"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                </form>

                <p class="mt-3 text-center">
                    Déjà un compte ? <a href="login.php">Connectez-vous</a>
                </p>
            </div>
        </div>
    </main>

    <script src="../assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
