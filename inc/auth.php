<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function start_session_once(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start([
            'cookie_lifetime' => SESSION_LIFETIME,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
        ]);
    }
}

function authenticate_user(string $email, string $password): ?array {
    
    $pdo = getPDO();
    $stmt = $pdo->prepare(
        'SELECT id_User, nom_User, prenom_User, email_User, mdp_User, role_User 
         FROM User WHERE email_User = :email LIMIT 1'
    );
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && $password === $user['mdp_User']) {
        return [
            'id' => $user['id_User'],
            'name' => trim($user['nom_User'] . ' ' . $user['prenom_User']),
            'email' => $user['email_User'],
            'role' => strtoupper($user['role_User']),
        ];
    }

    return null;
}

function register_user(string $name, string $email, string $password, string $role): ?array {
    $pdo = getPDO();
    
    $stmt = $pdo->prepare('SELECT id_User FROM User WHERE email_User = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        return null; // efa misy
    }

    $parts = explode(' ', trim($name), 2);
    $nom = $parts[0] ?? '';
    $prenom = '';
    for ($i = 1; $i < count($parts); $i++) {
        $prenom .= $parts[$i];
        $prenom .= ' ';
    }
    
    $stmt = $pdo->prepare(
        'INSERT INTO User (nom_User, prenom_User, email_User, mdp_User, role_User)
         VALUES (:nom, :prenom, :email, :mdp, :role)'
    );
    $stmt->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'mdp' => $password,
        'role' => strtoupper($role),
    ]);

    return authenticate_user($email, $password);
}

function login_user(array $user): void {
    start_session_once();
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
}

function logout_user(): void {
    start_session_once();
    $_SESSION = [];
    session_destroy();
}

function current_user(): ?array {
    start_session_once();
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool {
    return current_user() !== null;
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }
}


?>