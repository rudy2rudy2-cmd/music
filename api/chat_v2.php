<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

// Ensure we are working with a discussion
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? 'fetch';

// Discussion ID logic
$admin_discussion_id = $_REQUEST['admin_discussion_id'] ?? null;
if ($admin_discussion_id && !isAdmin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// For users, ensure they have a discussion ID in session
if (!isAdmin() && !isset($_SESSION['chat_discussion_id'])) {
    $user_id = $_SESSION['user_id'] ?? null;
    $visitor_token = $_SESSION['visitor_token'] ?? bin2hex(random_bytes(16));
    $_SESSION['visitor_token'] = $visitor_token;

    $stmt = $pdo->prepare("INSERT INTO chat_discussions (user_id, visitor_token) VALUES (?, ?)");
    $stmt->execute([$user_id, $visitor_token]);
    $_SESSION['chat_discussion_id'] = $pdo->lastInsertId();
}

$discussion_id = $admin_discussion_id ?: ($_SESSION['chat_discussion_id'] ?? null);

if ($action === 'send') {
    $message = trim($_POST['message'] ?? '');
    if (empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Empty message']);
        exit;
    }

    $sender = isAdmin() ? 'admin' : 'user';

    try {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (discussion_id, sender, message) VALUES (?, ?, ?)");
        $stmt->execute([$discussion_id, $sender, $message]);

        // Update last activity
        $stmt = $pdo->prepare("UPDATE chat_discussions SET last_activity = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$discussion_id]);

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} elseif ($action === 'fetch') {
    if (!$discussion_id) {
        echo json_encode([]);
        exit;
    }
    try {
        $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE discussion_id = ? ORDER BY created_at ASC");
        $stmt->execute([$discussion_id]);
        echo json_encode($stmt->fetchAll());
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
