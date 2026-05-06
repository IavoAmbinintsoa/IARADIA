<?php

/**
 * Utility fuction for best management
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/images.php';

function sanitize(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** message 
 */
function flash_set(string $key, string $message): void
{
    start_session_once();
    $_SESSION['flash'][$key] = $message;
}

function flash_get(string $key): ?string
{
    start_session_once();
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }
    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $message;
}

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function formatPrice(float $p): string
{
    return number_format($p, 0, ',', ' ') . ' Ar';
}

function formatDate(string $d): string
{
    $ts = strtotime($d);
    if (!$ts) return $d;
    $months = ['jan','fév','mar','avr','mai','jun','jul','aoû','sep','oct','nov','déc'];
    return date('d', $ts) . ' ' . $months[(int)date('m', $ts) - 1] . '. ' . date('Y', $ts);
}