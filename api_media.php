<?php
require_once 'includes/db.php';

$channel_id = $_GET['channel'] ?? 0;

if (!$channel_id) {
    die("Canalul nu a fost specificat.");
}

$stmt_media = $pdo->prepare("SELECT * FROM media WHERE channel_id = ? ORDER BY display_order ASC, id ASC");
$stmt_media->execute([$channel_id]);
$medias = $stmt_media->fetchAll();

$stmt_chan = $pdo->prepare("SELECT * FROM channels WHERE id = ?");
$stmt_chan->execute([$channel_id]);
$channel = $stmt_chan->fetch();

header('Content-Type: application/json');
echo json_encode([
    'media' => $medias,
    'config' => [
        'name' => $channel['name'],
        'logo' => $channel['logo_path'],
        'ticker' => $channel['ticker_text']
    ]
]);
?>
