<?php
require_once '../config/database.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', trim($uri, '/'));

// /api/users/{id}
$id = null;
if (count($uri) >= 3 && is_numeric($uri[2])) {
    $id = (int) $uri[2];
}

$conn = Database::connect();

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($user ?: ["message" => "Utilisateur introuvable"]);
        } else {
            $stmt = $conn->query("SELECT id, username, email, role FROM users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['username'], $input['email'], $input['password'])) {
            http_response_code(400);
            echo json_encode(["message" => "Champs requis: username, email, password"]);
            exit;
        }

        // Check si utilisateur existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$input['email']]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(["message" => "Email déjà utilisé"]);
            exit;
        }

        // Hash du mot de passe
        $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

        // Création de l'utilisateur
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'utilisateur')");
        $stmt->execute([$input['username'], $input['email'], $hashedPassword]);

        echo json_encode(["message" => "Utilisateur enregistré"]);
        break;

    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["message" => "ID requis"]);
            exit;
        }
        $input = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$input['username'], $input['email'], $input['role'], $id]);
        echo json_encode(["message" => "Utilisateur mis à jour"]);
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["message" => "ID requis"]);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["message" => "Utilisateur supprimé"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}

Database::disconnect();