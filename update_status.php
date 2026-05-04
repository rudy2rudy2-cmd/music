<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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
