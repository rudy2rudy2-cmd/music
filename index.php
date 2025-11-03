<?php
session_start();
require_once 'config.php'; // DB connection

// Fetch theme settings from the database
$theme = 'dark'; // default
$accent_color = 'blue'; // default

$sql = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('theme', 'accent_color')";
$result = mysqli_query($link, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['setting_key'] == 'theme') {
            $theme = $row['setting_value'];
        } elseif ($row['setting_key'] == 'accent_color') {
            $accent_color = $row['setting_value'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Music Generator</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="animations.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body data-theme="<?php echo htmlspecialchars($theme); ?>" data-color="<?php echo htmlspecialchars($accent_color); ?>">
    <div class="bg-polygon polygon1"></div>
    <div class="bg-polygon polygon2"></div>
    <div class="bg-polygon polygon3"></div>
    <header>
        <div class="logo">Mubert</div>
        <nav>
            <a href="#">Products</a>
            <a href="#">Use cases</a>
            <a href="#">Pricing</a>
            <a href="#">For Developers</a>
            <a href="#">About</a>
            <a href="login.php" class="login-btn">Log In</a>
            <a href="register.php" class="signup-btn">Sign Up</a>
        </nav>
    </header>

    <main>
        <div class="generator-container">
            <h1>Generate Music with AI</h1>
            <p>Describe what you want to hear in a few words, and our AI will create a unique track for you.</p>

            <form class="generator-form" action="generate.php" method="post">
                <input type="text" id="prompt" name="prompt" placeholder="e.g., epic cinematic battle music for a video game...">

                <div class="duration-control">
                    <label for="duration">Duration: <span id="duration-value">30</span>s</label>
                    <input type="range" id="duration" name="duration" class="duration-slider" min="5" max="180" value="30">
                </div>

                <div class="option-group">
                    <button type="button" class="option-btn active" data-type="voice" data-value="Male">Male Voice</button>
                    <button type="button" class="option-btn" data-type="voice" data-value="Female">Female Voice</button>
                    <button type="button" class="option-btn" data-type="voice" data-value="Instrumental">Instrumental</button>
                </div>
                <div class="option-group">
                    <button type="button" class="option-btn active" data-type="genre" data-value="Pop">Pop</button>
                    <button type="button" class="option-btn" data-type="genre" data-value="Rock">Rock</button>
                    <button type="button" class="option-btn" data-type="genre" data-value="HipHop">Hip Hop</button>
                </div>
                <div class="option-group">
                    <button type="button" class="option-btn active" data-type="mood" data-value="Happy">Happy</button>
                    <button type="button" class="option-btn" data-type="mood" data-value="Sad">Sad</button>
                    <button type="button" class="option-btn" data-type="mood" data-value="Energetic">Energetic</button>
                </div>

                 <!-- Hidden inputs to store selected values -->
                <input type="hidden" name="voice" id="voice-input" value="Male">
                <input type="hidden" name="genre" id="genre-input" value="Pop">
                <input type="hidden" name="mood" id="mood-input" value="Happy">

                <button id="generate-btn" type="submit"><span>Generate</span></button>
            </form>
            <div id="generation-result" class="hidden">
                <h2>Your Track is Ready!</h2>
                <div id="audio-player"></div>
                <a href="" id="download-link" download>Download Track</a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 AI Music Generator. Toate drepturile rezervate.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
