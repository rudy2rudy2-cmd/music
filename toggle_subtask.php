<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Global settings fetch to set timezone
$stmt_settings = $pdo->query("SELECT * FROM settings");
$site_settings = [];
while ($row = $stmt_settings->fetch()) {
    $site_settings[$row['setting_key']] = $row['setting_value'];
}
date_default_timezone_set($site_settings['timezone'] ?? 'Europe/Bucharest');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $defect_id = $_POST['defect_id'] ?? null;
    $subtask_index = isset($_POST['subtask_index']) ? (string)$_POST['subtask_index'] : null;

    if ($defect_id === null || $subtask_index === null) {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT description, resolved_subtasks, resolved_at FROM defects WHERE id = ?");
    $stmt->execute([$defect_id]);
    $defect = $stmt->fetch();

    if ($defect) {
        $resolved_data = json_decode((string)$defect['resolved_subtasks'], true) ?: [];

        if (isset($resolved_data[$subtask_index])) {
            unset($resolved_data[$subtask_index]);
        } else {
            $resolved_data[$subtask_index] = $_SESSION['username'];
        }

        $resolved_str = json_encode($resolved_data);

        // Subtasks count logic
        $subtasks = array_filter(array_map('trim', explode('.', (string)$defect['description'])), 'strlen');
        $total_subtasks = count($subtasks);
        $resolved_count = count($resolved_data);

        $new_status = ($resolved_count >= $total_subtasks && $total_subtasks > 0) ? 'rezolvat' : 'activ';

        // Manage resolved_at timestamp
        $resolved_at = $defect['resolved_at'];
        if ($new_status == 'rezolvat') {
            if (!$resolved_at) $resolved_at = gmdate('Y-m-d H:i:s');
        } else {
            // If at least one subtask is resolved, we might want a timestamp of "partial resolution"
            // but the user wants it to appear in "Rezolvate" filter.
            // For now, only set resolved_at if fully resolved.
            // But my index.php filter uses resolved_subtasks != '' too.
        }

        $update = $pdo->prepare("UPDATE defects SET resolved_subtasks = ?, status = ?, resolved_at = ? WHERE id = ?");
        $update->execute([$resolved_str, $new_status, $resolved_at, $defect_id]);

        echo json_encode(['status' => 'success', 'new_status' => $new_status, 'resolved_count' => $resolved_count]);
        exit;
    }
}
echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
?>
