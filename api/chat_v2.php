<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'send';

// Handle Admin Context
$admin_discussion_id = $_REQUEST['admin_discussion_id'] ?? null;
if ($admin_discussion_id && !isAdmin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$discussion_id = $admin_discussion_id ?: ($_SESSION['chat_discussion_id'] ?? null);

if ($action === 'send') {
    $message = $_POST['message'] ?? '';
    $sender = isAdmin() ? 'admin' : 'user';

    if (!$discussion_id && !isAdmin()) {
        $user_id = $_SESSION['user_id'] ?? null;
        $stmt = $pdo->prepare("INSERT INTO chat_discussions (user_id) VALUES (?)");
        $stmt->execute([$user_id]);
        $discussion_id = $pdo->lastInsertId();
        $_SESSION['chat_discussion_id'] = $discussion_id;
    }

    if ($discussion_id && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (discussion_id, sender, message) VALUES (?, ?, ?)");
        $stmt->execute([$discussion_id, $sender, $message]);
        echo json_encode(['status' => 'success']);
    }
} elseif ($action === 'fetch') {
    if (!$discussion_id) {
        echo json_encode([]);
        exit;
    }
    $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE discussion_id = ? ORDER BY created_at ASC");
    $stmt->execute([$discussion_id]);
    echo json_encode($stmt->fetchAll());
}
?>
