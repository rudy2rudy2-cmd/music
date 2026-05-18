<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($first_name) || empty($last_name) || empty($email) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Toate câmpurile sunt obligatorii.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO messages (first_name, last_name, email, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $message]);
        echo json_encode(['status' => 'success', 'message' => 'Mesajul a fost trimis!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Eroare la trimitere: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Metodă nepermisă.']);
}
