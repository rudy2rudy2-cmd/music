<?php
session_start();
header('Content-Type: application/json');

require_once 'config.php';

// --- Authorization ---
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(['success' => false, 'message' => 'Trebuie să fii autentificat pentru a genera muzică.']);
    exit;
}
$user_id = $_SESSION['id'];

// --- Check Coin Balance ---
$user_coins = 0;
$sql_coins = "SELECT coins FROM users WHERE id = ?";
if($stmt_coins = mysqli_prepare($link, $sql_coins)){
    mysqli_stmt_bind_param($stmt_coins, "i", $user_id);
    mysqli_stmt_execute($stmt_coins);
    mysqli_stmt_bind_result($stmt_coins, $user_coins);
    mysqli_stmt_fetch($stmt_coins);
    mysqli_stmt_close($stmt_coins);
}

$song_cost = 5;
if ($user_coins < $song_cost) {
    echo json_encode(['success' => false, 'message' => 'Monede insuficiente! O melodie costă ' . $song_cost . ' monede.']);
    exit;
}

// --- Input Validation ---
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['success' => false, 'message' => 'Metodă de request invalidă.']);
    exit;
}
$prompt = $_POST['prompt'] ?? 'Fără prompt';
$duration = (int)($_POST['duration'] ?? 30);
$genre = $_POST['genre'] ?? 'Pop';
// ... Add validation for all other inputs if needed

// --- Helper Functions ---
function createSilentWav($duration, $filename) {
    $sampleRate = 44100;
    $numChannels = 1;
    $bitsPerSample = 16;
    $numSamples = $sampleRate * $duration;

    $byteRate = $sampleRate * $numChannels * $bitsPerSample / 8;
    $blockAlign = $numChannels * $bitsPerSample / 8;
    $subchunk2Size = $numSamples * $numChannels * $bitsPerSample / 8;
    $chunkSize = 36 + $subchunk2Size;

    $header = pack('N', 0x52494646); // "RIFF"
    $header .= pack('V', $chunkSize);
    $header .= pack('N', 0x57415645); // "WAVE"
    $header .= pack('N', 0x666d7420); // "fmt "
    $header .= pack('V', 16); // Subchunk1Size
    $header .= pack('v', 1); // AudioFormat (PCM)
    $header .= pack('v', $numChannels);
    $header .= pack('V', $sampleRate);
    $header .= pack('V', $byteRate);
    $header .= pack('v', $blockAlign);
    $header .= pack('v', $bitsPerSample);
    $header .= pack('N', 0x64617461); // "data"
    $header .= pack('V', $subchunk2Size);

    $data = str_repeat(pack('s', 0), $numSamples); // Silent audio data

    if (file_put_contents($filename, $header . $data)) {
        return true;
    }
    return false;
}

function createCoverArt($genre, $mood) {
    // Simplified cover art simulation without GD library
    $background_dir = 'covers/backgrounds/';
    $background_file = strtolower($genre) . '.jpg';

    if (file_exists($background_dir . $background_file)) {
        return $background_dir . $background_file;
    }

    return $background_dir . 'default.jpg';
}

// --- Main Logic ---
$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
$audio_filename = $upload_dir . 'music_' . time() . '_' . uniqid() . '.wav';

if (createSilentWav($duration, $audio_filename)) {
    // 1. Deduct coins
    $sql_update_coins = "UPDATE users SET coins = coins - ? WHERE id = ?";
    if ($stmt_update = mysqli_prepare($link, $sql_update_coins)) {
        mysqli_stmt_bind_param($stmt_update, "ii", $song_cost, $user_id);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);
    }

    // 2. Generate cover art
    $cover_art_path = createCoverArt($genre, $_POST['mood'] ?? 'Happy');

    // 3. Save song to database
    $sql_insert_song = "INSERT INTO songs (user_id, prompt, genre, duration, file_path, cover_art_path) VALUES (?, ?, ?, ?, ?, ?)";
    if ($stmt_insert = mysqli_prepare($link, $sql_insert_song)) {
        mysqli_stmt_bind_param($stmt_insert, "ississ", $user_id, $prompt, $genre, $duration, $audio_filename, $cover_art_path);
        mysqli_stmt_execute($stmt_insert);
        mysqli_stmt_close($stmt_insert);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Melodie generată cu succes!',
        'file_path' => $audio_filename
    ]);

} else {
    echo json_encode(['success' => false, 'message' => 'Eroare la generarea fișierului audio.']);
}
?>
