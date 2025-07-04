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

// Recherche de l'identifiant (ex: /api/ressources/3)
$id = null;
if (count($uri) >= 3 && is_numeric($uri[2])) {
    $id = (int) $uri[2];
}

$conn = Database::connect();

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM ressources WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($data ?: ["message" => "Introuvable"]);
        } else {
            $stmt = $conn->query("SELECT * FROM ressources");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['titre'], $input['description'], $input['lien'])) {
            http_response_code(400);
            echo json_encode(["message" => "Champs manquants"]);
            exit;
        }
        $stmt = $conn->prepare("INSERT INTO ressources (titre, description, lien) VALUES (?, ?, ?)");
        $stmt->execute([$input['titre'], $input['description'], $input['lien']]);
        echo json_encode(["message" => "Ressource créée"]);
        break;

    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["message" => "ID requis"]);
            exit;
        }
        $input = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("UPDATE ressources SET titre = ?, description = ?, lien = ? WHERE id = ?");
        $stmt->execute([$input['titre'], $input['description'], $input['lien'], $id]);
        echo json_encode(["message" => "Ressource mise à jour"]);
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["message" => "ID requis"]);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM ressources WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["message" => "Ressource supprimée"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}

Database::disconnect();