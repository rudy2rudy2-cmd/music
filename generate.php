<?php
session_start();
require_once "src/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lyrics = $_POST['lyrics'] ?? 'No lyrics provided.';
    $style = $_POST['style'] ?? 'No style provided.';
    $title = $_POST['title'] ?? 'Untitled';
    $voice_id = $_POST['voice_id'] ?? 1; // Default to 1 if not provided

    // Sanitize title to create a safe filename
    $filename = preg_replace("/[^a-zA-Z0-9\-\_]/", "", $title);
    if (empty($filename)) {
        $filename = 'song_' . time();
    }

    $placeholder_file = "assets/placeholder.mp3";
    $generated_file_path = "uploads/" . $filename . ".mp3";

    if (file_exists($placeholder_file)) {
        copy($placeholder_file, $generated_file_path);

        // If user is logged in, save the song to the database
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            $user_id = $_SESSION["id"];

            $sql = "INSERT INTO songs (user_id, voice_id, lyrics, file_path, title) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("iisss", $user_id, $voice_id, $lyrics, $generated_file_path, $title);
                $stmt->execute();
                $stmt->close();
            }
        }
        echo "Placeholder song generated successfully at: $generated_file_path";
    } else {
        http_response_code(500);
        echo "Error: Placeholder audio file not found.";
    }
} else {
    http_response_code(405);
    echo "Error: Invalid request method.";
}
?>
