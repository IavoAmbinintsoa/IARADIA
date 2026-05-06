<?php
require_once __DIR__ . '/../inc/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $from = trim($_GET['from'] ?? '');
    $to   = trim($_GET['to']   ?? '');
    $date = trim($_GET['date'] ?? '');

    if ($from && $to) {
        $voyages = searchVoyages($from, $to, $date ?: null);
    } else {
        $voyages = getVoyages();
    }

    foreach ($voyages as &$v) {
        $t = getTarifForVoyage((int)$v['id_Voyage']);
        $v['prix'] = $t ? (float)$t['prix_Tarif_segment'] : null;
    }

    echo json_encode($voyages);
    exit;
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? $_POST['action'] ?? '';

    if ($action === 'cancel') {
        $id = (int)($body['id'] ?? $_POST['id'] ?? 0);
        $pdo = getPDO();
        $pdo->prepare("UPDATE Voyage SET status_Voyage = 'annule' WHERE id_Voyage = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'create') {
        $fromCity  = $body['from']      ?? $_POST['from']      ?? '';
        $toCity    = $body['to']        ?? $_POST['to']        ?? '';
        $departure = $body['departure'] ?? $_POST['departure'] ?? '';
        $vehicle   = $body['vehicle']   ?? $_POST['vehicle']   ?? '';

        $pdo = getPDO();
        $st = $pdo->prepare(
            'SELECT id_Trajet FROM Trajet t
             JOIN Ville v1 ON t.id_Ville_depart = v1.id_Ville
             JOIN Ville v2 ON t.id_Ville_arrivee = v2.id_Ville
             WHERE v1.nom_Ville = :f AND v2.nom_Ville = :t LIMIT 1'
        );
        $st->execute(['f' => $fromCity, 't' => $toCity]);
        $trajet = $st->fetch();

        if (!$trajet) {
            foreach ([$fromCity, $toCity] as $city) {
                $c = $pdo->prepare('SELECT id_Ville FROM Ville WHERE nom_Ville = :n LIMIT 1');
                $c->execute(['n' => $city]);
                if (!$c->fetch()) {
                    $pdo->prepare('INSERT INTO Ville (nom_Ville) VALUES (:n)')->execute(['n' => $city]);
                }
            }
            $vFrom = $pdo->prepare('SELECT id_Ville FROM Ville WHERE nom_Ville = :n'); $vFrom->execute(['n' => $fromCity]); $idFrom = $vFrom->fetchColumn();
            $vTo   = $pdo->prepare('SELECT id_Ville FROM Ville WHERE nom_Ville = :n'); $vTo->execute(['n' => $toCity]);   $idTo   = $vTo->fetchColumn();
            $pdo->prepare('INSERT INTO Trajet (distance_km_Trajet, id_Ville_depart, id_Ville_arrivee) VALUES (0, :f, :t)')->execute(['f' => $idFrom, 't' => $idTo]);
            $idTrajet = (int)$pdo->lastInsertId();
        } else {
            $idTrajet = (int)$trajet['id_Trajet'];
        }

        $ins = $pdo->prepare(
            "INSERT INTO Voyage (date_depart_Voyage, status_Voyage, id_Trajet, immatriculation_Vehicule)
             VALUES (:dep, 'planifie', :trajet, :veh)"
        );
        $ins->execute(['dep' => $departure, 'trajet' => $idTrajet, 'veh' => $vehicle ?: null]);
        
        header('Location: /dashboard-admin.php');
        exit;
    }
}