<?php
session_start();
require_once 'config.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php?redirect=generate_music.php");
    exit;
}

// Fetch theme settings for consistent styling
$theme = 'dark';
$accent_color = 'blue';
$sql_theme = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('theme', 'accent_color')";
if ($result_theme = mysqli_query($link, $sql_theme)) {
    while ($row = mysqli_fetch_assoc($result_theme)) {
        if ($row['setting_key'] == 'theme') $theme = $row['setting_value'];
        if ($row['setting_key'] == 'accent_color') $accent_color = $row['setting_value'];
    }
}

// Get prompt from URL if available
$prompt_from_home = isset($_GET['prompt']) ? htmlspecialchars($_GET['prompt']) : '';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator de Muzică - AI Music Generator</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="animations.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body data-theme="<?php echo htmlspecialchars($theme); ?>" data-color="<?php echo htmlspecialchars($accent_color); ?>">
    <div class="bg-polygon polygon1"></div>
    <div class="bg-polygon polygon2"></div>
    <div class="bg-polygon polygon3"></div>
    <?php include 'header.php'; ?>

    <main>
        <div class="generator-container">
            <h1>Generator de Muzică</h1>
            <p>Descrie ce vrei să auzi, ajustează opțiunile și lasă AI-ul să compună.</p>

            <form class="generator-form" method="post">
                <input type="text" id="prompt" name="prompt" placeholder="e.g., epic cinematic battle music..." value="<?php echo $prompt_from_home; ?>">

                <!-- Advanced Options -->
                <div class="advanced-options">
                    <div class="option-control">
                        <label for="genre">Gen:</label>
                        <select name="genre" id="genre-input">
                            <option value="Pop">🎤 Pop</option>
                            <option value="Rock">🎸 Rock</option>
                            <option value="HipHop">🎧 Hip Hop</option>
                            <option value="Trap">🎛️ Trap</option>
                            <option value="Orchestral">🎻 Orchestral</option>
                            <option value="Ambient">🌌 Ambient</option>
                        </select>
                    </div>
                     <div class="option-control">
                        <label for="duration">Durată: <span id="duration-value">30</span>s</label>
                        <input type="range" id="duration" name="duration" class="duration-slider" min="5" max="180" value="30">
                    </div>
                     <div class="option-control">
                        <label for="tempo">Tempo: <span id="tempo-value">120</span> BPM</label>
                        <input type="range" id="tempo" name="tempo" class="duration-slider" min="60" max="180" value="120">
                    </div>
                </div>

                <div class="option-group">
                    <label>Voce:</label>
                    <button type="button" class="option-btn active" data-type="voice" data-value="Instrumental">🎶 Instrumental</button>
                    <button type="button" class="option-btn" data-type="voice" data-value="Male">👨 Masculină</button>
                    <button type="button" class="option-btn" data-type="voice" data-value="Female">👩 Feminină</button>
                </div>

                <input type="hidden" name="voice" id="voice-input" value="Instrumental">

                <button id="generate-btn" type="submit"><span>Generează</span></button>
            </form>

            <div id="generation-result" class="hidden">
                <h2>Creația ta este gata!</h2>
                <div id="audio-player"></div>
                <a href="" id="download-link" download>Descarcă WAV</a>
                <button id="save-project-btn" class="secondary-btn">Salvează Proiectul</button>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
