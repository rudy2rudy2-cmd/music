<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Global settings fetch for timezone
$stmt_settings = $pdo->query("SELECT * FROM settings");
$site_settings = [];
while ($row = $stmt_settings->fetch()) {
    $site_settings[$row['setting_key']] = $row['setting_value'];
}
date_default_timezone_set($site_settings['timezone'] ?? 'Europe/Bucharest');

$id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? 'rezolvat';

if ($id) {
    $resolved_at = ($status == 'rezolvat') ? date('Y-m-d H:i:s') : null;
    $stmt = $pdo->prepare("UPDATE defects SET status = ?, resolved_at = ? WHERE id = ?");
    $stmt->execute([$status, $resolved_at, $id]);
}

header("Location: index.php");
exit();
?>
