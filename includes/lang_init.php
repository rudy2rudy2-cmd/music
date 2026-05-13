<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ro';
}

$lang_code = $_SESSION['lang'] ?? 'ro';
$translations = require __DIR__ . "/../lang/{$lang_code}.php";

function __($key) {
    global $translations;
    return $translations[$key] ?? $key;
}
