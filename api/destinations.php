<?php
require_once __DIR__ . '/../inc/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$pdo    = getPDO();
$from   = trim($_GET['from'] ?? '');
$to     = trim($_GET['to']   ?? '');

if ($from && $to) {
    $stmt = $pdo->prepare(
        'SELECT t.id_Trajet, t.distance_km_Trajet,
                v1.nom_Ville as from_city, v2.nom_Ville as to_city,
                MIN(ts.prix_Tarif_segment) as min_price
         FROM Trajet t
         JOIN Ville v1 ON t.id_Ville_depart = v1.id_Ville
         JOIN Ville v2 ON t.id_Ville_arrivee = v2.id_Ville
         LEFT JOIN Tarif_segment ts ON ts.id_Trajet = t.id_Trajet
         WHERE v1.nom_Ville = :from AND v2.nom_Ville = :to
         GROUP BY t.id_Trajet LIMIT 1'
    );
    $stmt->execute(['from' => $from, 'to' => $to]);
    $result = $stmt->fetch();
    echo json_encode($result ?: null);
} else {
    $stmt = $pdo->query(
        'SELECT t.id_Trajet, t.distance_km_Trajet,
                v1.nom_Ville as from_city, v2.nom_Ville as to_city,
                MIN(ts.prix_Tarif_segment) as min_price,
                COUNT(DISTINCT v.id_Voyage) as voyage_count
         FROM Trajet t
         JOIN Ville v1 ON t.id_Ville_depart = v1.id_Ville
         JOIN Ville v2 ON t.id_Ville_arrivee = v2.id_Ville
         LEFT JOIN Tarif_segment ts ON ts.id_Trajet = t.id_Trajet
         LEFT JOIN Voyage v ON v.id_Trajet = t.id_Trajet
         GROUP BY t.id_Trajet'
    );
    echo json_encode($stmt->fetchAll());
}