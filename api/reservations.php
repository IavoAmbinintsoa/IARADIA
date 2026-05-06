<?php
require_once __DIR__ . '/../inc/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$user = current_user();
if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $reservations = getReservationsByUser((int)$user['id']);
    echo json_encode($reservations);
    exit;
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? $_POST['action'] ?? '';
    $id     = (int)($body['id'] ?? $_POST['id'] ?? 0);

    if ($action !== 'cancel' || !$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Action ou ID invalide']);
        exit;
    }

    $pdo  = getPDO();
    $check = $pdo->prepare(
        "SELECT id_Reservation FROM Reservation
         WHERE id_Reservation = :id AND QR_code_Reservation LIKE :pattern"
    );
    $check->execute(['id' => $id, 'pattern' => 'IARADIA-' . $user['id'] . '-%']);
    if (!$check->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Accès refusé']);
        exit;
    }

    $stmt = $pdo->prepare(
        "UPDATE Reservation SET statut_Reservation = 'annule' WHERE id_Reservation = :id"
    );
    $stmt->execute(['id' => $id]);
    echo json_encode(['success' => true]);
}