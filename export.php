<?php
require_once 'includes/db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all defects
$stmt = $pdo->query("SELECT d.*, u.username as reported_by_name FROM defects d LEFT JOIN users u ON d.reported_by = u.id ORDER BY d.reported_at DESC");
$defects = $stmt->fetchAll();

// Set headers for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=rapoarte_defectiuni_' . date('Y-m-d') . '.csv');

// Create file pointer connected to PHP output stream
$output = fopen('php://output', 'w');

// UTF-8 BOM for Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Column headers
fputcsv($output, ['ID', 'Cameră', 'Tip Problemă', 'Descriere', 'Status', 'Prioritate', 'Raportat De', 'Data Raportării', 'Data Rezolvării', 'Subtask-uri (JSON)']);

foreach ($defects as $row) {
    fputcsv($output, [
        $row['id'],
        $row['room_number'],
        $row['issue_type'],
        $row['description'],
        $row['status'],
        $row['priority'],
        $row['reported_by_name'],
        $row['reported_at'],
        $row['resolved_at'],
        $row['resolved_subtasks']
    ]);
}

fclose($output);
exit();
