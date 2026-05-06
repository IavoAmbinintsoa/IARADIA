<?php
require_once __DIR__ . '/../inc/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $body['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        $email = trim($body['email'] ?? $_POST['email'] ?? '');
        $pass  = $body['password'] ?? $_POST['password'] ?? '';

        if (!$email || !$pass) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Champs requis']);
            exit;
        }

        $user = authenticate_user($email, $pass);
        if (!$user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Identifiants invalides']);
            exit;
        }

        login_user($user);
        $token = base64_encode($user['id'] . ':' . hash_hmac('sha256', $user['email'], SESSION_NAME));
        echo json_encode(['success' => true, 'user' => $user, 'token' => $token]);
        break;

    case 'register':
        $name  = trim($body['name']     ?? $_POST['name']     ?? '');
        $email = trim($body['email']    ?? $_POST['email']    ?? '');
        $pass  = $body['password']      ?? $_POST['password'] ?? '';
        $role  = 'client';

        if (!$name || !$email || !$pass) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Tous les champs sont requis']);
            exit;
        }

        $user = register_user($name, $email, $pass, $role);
        if (!$user) {
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => 'Email déjà utilisé']);
            exit;
        }

        login_user($user);
        $token = base64_encode($user['id'] . ':' . hash_hmac('sha256', $user['email'], SESSION_NAME));
        echo json_encode(['success' => true, 'user' => $user, 'token' => $token]);
        break;

    case 'logout':
        logout_user();
        echo json_encode(['success' => true]);
        break;
}