<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Global settings fetch to set timezone
$stmt_settings = $pdo->query("SELECT * FROM settings");
$site_settings = [];
while ($row = $stmt_settings->fetch()) {
    $site_settings[$row['setting_key']] = $row['setting_value'];
}
date_default_timezone_set($site_settings['timezone'] ?? 'Europe/Bucharest');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $defect_id = $_POST['defect_id'];
    $subtask_index = $_POST['subtask_index'];

    $stmt = $pdo->prepare("SELECT description, resolved_subtasks FROM defects WHERE id = ?");
    $stmt->execute([$defect_id]);
    $defect = $stmt->fetch();

    if ($defect) {
        $resolved = array_filter(explode(',', $defect['resolved_subtasks'] ?? ''));

        if (in_array($subtask_index, $resolved)) {
            $resolved = array_diff($resolved, [$subtask_index]);
        } else {
            $resolved[] = $subtask_index;
        }

        $resolved_str = implode(',', $resolved);

        // Check if all are resolved
        $subtasks = array_filter(array_map('trim', explode('.', $defect['description'] ?? '')));
        $new_status = (count($resolved) >= count($subtasks)) ? 'rezolvat' : 'activ';
        $resolved_at = ($new_status == 'rezolvat') ? date('Y-m-d H:i:s') : ($defect['resolved_at'] ?? null);

        $update = $pdo->prepare("UPDATE defects SET resolved_subtasks = ?, status = ?, resolved_at = ? WHERE id = ?");
        $update->execute([$resolved_str, $new_status, $resolved_at, $defect_id]);

        echo json_encode(['status' => 'success', 'new_status' => $new_status]);
        exit;
    }
}
echo json_encode(['status' => 'error']);
?>
