<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

function getVoyageStats(array $voyage): array {
    $cap   = 32;
    $id    = (int)($voyage['id_Voyage'] ?? 0);
    $seats = getSeatsForVoyage($id, "libre");
    $libre = is_array($seats) ? count($seats) : 0;
    $taken = $cap - $libre;
    return ['libre' => $libre, 'total' => $cap, 'taken' => $taken];
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
            WHERE v1.nom_Ville = :from AND v2.nom_Ville = :to';

    $params = ['from' => $from, 'to' => $to];

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

function getSeatsForVoyage(int $voyageId, string $coms): array {
    $pdo = getPDO();
    $stmt = $pdo->prepare(
        'SELECT id_Siege, statut_Siege, expirer_dans_Siege
         FROM Siege 
         WHERE id_Voyage = :voyageId 
         AND statut_Siege = :coms 
         ORDER BY id_Siege'
    );
    $stmt->execute(['voyageId' => $voyageId, 'coms' => $coms]);
    return $stmt->fetchAll() ?: [];
}

