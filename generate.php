<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "src/config.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $lyrics = trim($_POST["lyrics"]);
    $voice_id = trim($_POST["voice_id"]);
    $user_id = $_SESSION["id"];

    // --- AI Music Generation Placeholder ---
    // In a real application, you would call an AI music generation API here.
    // For now, we'll copy a placeholder audio file.
    $placeholder_file = "assets/placeholder.mp3";
    $generated_file_path = "uploads/song_" . $user_id . "_" . time() . ".mp3";

    if (file_exists($placeholder_file)) {
        copy($placeholder_file, $generated_file_path);
    } else {
        // Fallback to creating a text file if placeholder is missing
        file_put_contents($generated_file_path, "This is a placeholder for the generated song with lyrics:\n\n" . $lyrics);
    }
    // --- End of Placeholder ---

    if(!empty($lyrics) && !empty($voice_id)){
        $sql = "INSERT INTO songs (user_id, voice_id, lyrics, file_path) VALUES (?, ?, ?, ?)";

        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("iiss", $user_id, $voice_id, $lyrics, $generated_file_path);

            if(!$stmt->execute()){
                echo "Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }

    $conn->close();

    header("location: index.php");
}
?>
