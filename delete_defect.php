<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Acces neautorizat.");
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM defects WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit();
?>
