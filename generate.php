<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prompt = $_POST['prompt'] ?? 'No prompt provided.';
    $duration = (int)($_POST['duration'] ?? 60);

    // Sanitize prompt to create a safe filename
    $filename = preg_replace("/[^a-zA-Z0-9\-\_]/", "", substr($prompt, 0, 50));
    if (empty($filename)) {
        $filename = 'song_' . time();
    }

    $uploads_dir = "uploads/";
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0755, true);
    }

    $generated_file_path = $uploads_dir . $filename . ".wav";

    // Generate a silent WAV file with the specified duration
    $sampleRate = 44100;
    $numSamples = $sampleRate * $duration;
    $bitsPerSample = 16;
    $numChannels = 1;
    $byteRate = $sampleRate * $numChannels * ($bitsPerSample / 8);
    $blockAlign = $numChannels * ($bitsPerSample / 8);
    $subchunk2Size = $numSamples * $numChannels * ($bitsPerSample / 8);
    $chunkSize = 36 + $subchunk2Size;

    $file = fopen($generated_file_path, 'w');
    // RIFF header
    fwrite($file, 'RIFF');
    fwrite($file, pack('V', $chunkSize));
    fwrite($file, 'WAVE');
    // fmt sub-chunk
    fwrite($file, 'fmt ');
    fwrite($file, pack('V', 16));
    fwrite($file, pack('v', 1));
    fwrite($file, pack('v', $numChannels));
    fwrite($file, pack('V', $sampleRate));
    fwrite($file, pack('V', $byteRate));
    fwrite($file, pack('v', $blockAlign));
    fwrite($file, pack('v', $bitsPerSample));
    // data sub-chunk
    fwrite($file, 'data');
    fwrite($file, pack('V', $subchunk2Size));
    // Audio data (silence)
    for ($i = 0; $i < $numSamples; $i++) {
        fwrite($file, pack('s', 0));
    }
    fclose($file);

    echo json_encode([
        'success' => true,
        'file_path' => $generated_file_path
    ]);

} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => "Error: Invalid request method."]);
}
?>
