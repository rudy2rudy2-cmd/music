<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
$users = $stmt->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=users_export_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['ID', 'Username', 'Role', 'Created At']);

foreach ($users as $row) {
    fputcsv($output, [
        $row['id'],
        $row['username'],
        $row['role'],
        $row['created_at']
    ]);
}

fclose($output);
exit();
