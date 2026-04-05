<?php
require_once 'includes/db.php';

$channel_id = $_GET['channel'] ?? 0;

if (!$channel_id) {
    die("Canalul nu a fost specificat.");
}

$stmt_media = $pdo->prepare("SELECT * FROM media WHERE channel_id = ? ORDER BY display_order ASC, id ASC");
$stmt_media->execute([$channel_id]);
$medias = $stmt_media->fetchAll();

header('Content-Type: application/json');
echo json_encode($medias);
?>
