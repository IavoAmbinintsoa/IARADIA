<?php

define('IMG_BASE', '/assets/img/');

function cityImg(string $name): string {
    $name = strtolower($name);
    return IMG_BASE . $name . '.jpg';
}

function main_image(): string {
    return IMG_BASE . 'tsingy.jpeg';
}

