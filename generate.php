<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in (optional, but good practice)
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to generate music.']);
    exit;
}

// Check user's remaining generations
require_once 'config.php';
$user_id = $_SESSION['id'];
$generations_left = 0;
$sql = "SELECT generations_left FROM users WHERE id = ?";
if($stmt = mysqli_prepare($link, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $generations_left);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

if ($generations_left <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Nu mai aveți generări disponibile. <a href="pricing.php">Fă un upgrade acum</a> pentru a continua să creezi.'
    ]);
    exit;
}


// Function to generate a silent WAV file
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prompt = $_POST['prompt'] ?? 'No prompt provided';
    $duration = (int)($_POST['duration'] ?? 30);
    $voice = $_POST['voice'] ?? 'Instrumental';
    $genre = $_POST['genre'] ?? 'Pop';
    $mood = $_POST['mood'] ?? 'Happy';

    // Basic validation
    if (empty($prompt)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a prompt.']);
        exit;
    }
    if ($duration <= 0 || $duration > 300) { // Max 5 minutes
        echo json_encode(['success' => false, 'message' => 'Duration must be between 1 and 300 seconds.']);
        exit;
    }

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $filename = $upload_dir . 'music_' . time() . '_' . uniqid() . '.wav';

    // Simulate music generation
    if (createSilentWav($duration, $filename)) {
        // Decrement generations_left count
        $sql_update = "UPDATE users SET generations_left = generations_left - 1 WHERE id = ?";
        if ($stmt_update = mysqli_prepare($link, $sql_update)) {
            mysqli_stmt_bind_param($stmt_update, "i", $user_id);
            mysqli_stmt_execute($stmt_update);
            mysqli_stmt_close($stmt_update);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Music generated successfully!',
            'file_path' => $filename
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to generate the audio file.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
