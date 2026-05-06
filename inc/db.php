<?php

require_once __DIR__ . '/config.php';


/** Mi-creer connexion a BD */
function getPDO(): PDO 
{
    static $pdo = null; 

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Erreur BD: ' . $e->getMessage());
        }
    }

    return $pdo;
}

function getChauffeur($cooperative = null) {
    $pdo = getPDO();
    $sql = "SELECT * FROM Chauffeur";
    $params = [];

    if ($cooperative) {
        $sql .= " WHERE cooperative_Chauffeur = ?";
        $params[] = $cooperative;
    }

    $sql .= " ORDER BY nom_Chauffeur ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getVoyages($from = null, $to = null): array {
    $pdo = getPDO();
    $sql = 'SELECT v.id_Voyage, v.date_depart_Voyage, v.status_Voyage, 
                   v.immatriculation_Vehicule, t.distance_km_Trajet,
                   v1.nom_Ville as from_city, v2.nom_Ville as to_city
            FROM Voyage v
            JOIN Trajet t ON v.id_Trajet = t.id_Trajet
            JOIN Ville v1 ON t.id_Ville_depart = v1.id_Ville
            JOIN Ville v2 ON t.id_Ville_arrivee = v2.id_Ville
            WHERE 1=1';
    
    $params = [];
    
    if ($from && $to) { 
        $sql .= ' AND v1.nom_Ville = :from'; 
        $sql .= ' AND v2.nom_Ville = :to'; 
        $params['from'] = $from;
        $params['to'] = $to;
    }
    
    $sql .= ' ORDER BY v.date_depart_Voyage DESC';
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll() ?: [];
}

function getReservationsByUser(int $userId): array {
    $pdo = getPDO();

    $stmt = $pdo->prepare(
        'SELECT r.id_Reservation, r.date_reservation_Reservation, r.total_prix_Reservation,
                r.statut_Reservation, r.QR_code_Reservation,
                v.id_Voyage, v.date_depart_Voyage, v.status_Voyage,
                v1.nom_Ville as from_city, v2.nom_Ville as to_city
         FROM Reservation r
         JOIN Tarif_segment ts ON r.id_Tarif_segment = ts.id_Tarif_segment
         JOIN Trajet t          ON ts.id_Trajet = t.id_Trajet
         JOIN Siege s           ON s.id_Reservation = r.id_Reservation
         JOIN Voyage v          ON s.id_Voyage = v.id_Voyage
         JOIN Ville v1          ON t.id_Ville_depart = v1.id_Ville
         JOIN Ville v2          ON t.id_Ville_arrivee = v2.id_Ville
         WHERE r.id_User = :userId
         ORDER BY r.date_reservation_Reservation DESC'
    );
    $stmt->execute(['userId' => $userId]);
    return $stmt->fetchAll() ?: [];
}

function getCities(): array {
    $pdo = getPDO();
    $stmt = $pdo->query('SELECT nom_Ville FROM Ville ORDER BY nom_Ville');
    return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
}

function getVehicule(): array
{
    $pdo = getPDO();

    $stmt = $pdo->query('
        SELECT
            v.immatriculation_Vehicule AS plate,
            v.modele_Vehicule          AS model,
            v.capacite_Vehicule        AS seats,
            c.nom_Cooperative          AS cooperative
        FROM Vehicule v
        LEFT JOIN Cooperative c ON c.id_Cooperative = v.id_Cooperative
        LIMIT 10
    ');

    return $stmt->fetchAll() ?: [];
}

function getDestinations(): array {
    $pdo = getPDO();
    $stmt = $pdo->query(
        'SELECT t.id_Trajet, t.distance_km_Trajet,
                v1.nom_Ville as from_city, v2.nom_Ville as to_city
         FROM Trajet t
         JOIN Ville v1 ON t.id_Ville_depart = v1.id_Ville
         JOIN Ville v2 ON t.id_Ville_arrivee = v2.id_Ville
         ORDER BY t.distance_km_Trajet DESC
         LIMIT 10'
    );
    return $stmt->fetchAll() ?: [];
}

function searchVoyages(string $from, string $to, ?string $date = null): array {
    $pdo = getPDO();
    $sql = 'SELECT v.id_Voyage, v.date_depart_Voyage, v.status_Voyage,
                   v.immatriculation_Vehicule, t.distance_km_Trajet,
                   v1.nom_Ville as from_city, v2.nom_Ville as to_city
            FROM Voyage v
            JOIN Trajet t ON v.id_Trajet = t.id_Trajet
            JOIN Ville v1 ON t.id_Ville_depart = v1.id_Ville
            JOIN Ville v2 ON t.id_Ville_arrivee = v2.id_Ville
            WHERE v1.nom_Ville = :from AND v2.nom_Ville = :to
            AND v.status_Voyage = "planifie"';

    $params = ['from' => $from, 'to' => $to];

    if ($date) {
        $sql .= ' AND DATE(v.date_depart_Voyage) = :date';
        $params['date'] = $date;
    }

    $sql .= ' ORDER BY v.date_depart_Voyage ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll() ?: [];
}

function getTarifForVoyage(int $voyageId): ?array {
    $pdo = getPDO();
    $stmt = $pdo->prepare(
        'SELECT ts.id_Tarif_segment, ts.prix_Tarif_segment
         FROM Tarif_segment ts
         JOIN Voyage v ON v.id_Trajet = ts.id_Trajet
         WHERE v.id_Voyage = :voyageId
         LIMIT 1'
    );
    $stmt->execute(['voyageId' => $voyageId]);
    return $stmt->fetch() ?: null;
}

function getSeatsForVoyage(int $voyageId): array {
    $pdo = getPDO();
    $stmt = $pdo->prepare(
        'SELECT id_Siege, statut_Siege, expirer_dans_Siege
         FROM Siege WHERE id_Voyage = :voyageId ORDER BY id_Siege'
    );
    $stmt->execute(['voyageId' => $voyageId]);
    return $stmt->fetchAll() ?: [];
}

function createReservation(int $userId, int $voyageId, array $seatNums, float $totalPrice): int {
    $pdo = getPDO();
    $pdo->beginTransaction();

    $tarif = getTarifForVoyage($voyageId);
    if (!$tarif) {
        throw new Exception('Cette voyage n\'est pas encore disponible');
    }

    $qrCode = 'IARADIA-' . $userId . '-' . $voyageId . '-' . uniqid();

    $stmt = $pdo->prepare(
        'INSERT INTO Reservation
            (date_reservation_Reservation, total_prix_Reservation, statut_Reservation,
            QR_code_Reservation, id_Tarif_segment)
            VALUES (CURDATE(), :total, "en_attente", :qr, :tarif)'
    );
    $stmt->execute([
        'total' => $totalPrice,
        'qr'    => $qrCode,
        'tarif' => $tarif['id_Tarif_segment'],
    ]);
    $reservationId = (int) $pdo->lastInsertId();

    $ins = $pdo->prepare(
        'INSERT INTO Siege (statut_Siege, id_Voyage, id_Reservation,numero)
            VALUES ("reserve", :voyage, :resa,:numero)'
    );
    foreach ($seatNums as $num) {
        $ins->execute(['voyage' => $voyageId, 'resa' => $reservationId, 'numero' => $num]);
    }

    $pdo->commit();
    return $reservationId;
}

