<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ro';
}

$lang_code = $_SESSION['lang'] ?? 'ro';

// Fetch translations from DB
try {
    $stmt = $pdo->prepare("SELECT translation_key, translation_value FROM translations WHERE lang = ?");
    $stmt->execute([$lang_code]);
    $db_translations = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    $db_translations = [];
}

// Fallback to file-based if DB is empty or fails
$file_translations = file_exists(__DIR__ . "/../lang/{$lang_code}.php") ? require __DIR__ . "/../lang/{$lang_code}.php" : [];
$translations = array_merge($file_translations, $db_translations);

function __($key) {
    global $translations;
    return $translations[$key] ?? $key;
}
