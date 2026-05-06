<?php
/**
 * inc/images.php — Local asset path helper
 * Maps city names and keys to /assets/img/ local images
 */

define('IMG_BASE', '/assets/img/');

/**
 * Returns the web path to a city image, with a fallback.
 */
function cityImg(string $name): string {
    static $map = [
        'antananarivo' => 'antananarivo.jpg',
        'toamasina'    => 'toamasina.jpg',
        'mahajanga'    => 'mahajanga.jpg',
        'antsirabe'    => 'antsirabe.jpg',
        'toliara'      => 'toliara.jpg',
        'tullear'      => 'toliara.jpg',
        'soavinandriana' => 'soavinandriana.jpg',
        'tsingy'       => 'tsingy.jpeg',
    ];

    $key = strtolower(trim($name));
    $file = $map[$key] ?? null;

    if ($file && file_exists(__DIR__ . '/../assets/img/' . $file)) {
        return IMG_BASE . $file;
    }
    // default city is antananarivo
    return IMG_BASE . 'antananarivo.jpg';
}

function main_image(): string {
    return IMG_BASE . 'tsingy.jpeg';
}

