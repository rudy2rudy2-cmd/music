<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Music Generator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo">AI Music Platform</div>
        <nav>
            <a href="#">Products</a>
            <a href="#">Blog</a>
            <button class="login-btn">Log In</button>
            <button class="signup-btn">Sign Up</button>
        </nav>
    </header>

    <main>
        <div class="generator-container">
            <h1>Human and AI Music Generator</h1>
            <p>For your video content, podcasts and apps</p>
            <div class="generator-form">
                <input type="text" id="prompt" placeholder="Describe your track...">

                <div class="option-group">
                    <label>Voice:</label>
                    <button class="option-btn active" data-group="voice" value="random">Random</button>
                    <button class="option-btn" data-group="voice" value="male">Male</button>
                    <button class="option-btn" data-group="voice" value="female">Female</button>
                </div>

                <div class="option-group">
                    <label>Genre:</label>
                    <button class="option-btn active" data-group="genre" value="any">Any</button>
                    <button class="option-btn" data-group="genre" value="manele">Manele</button>
                    <button class="option-btn" data-group="genre" value="pop">Pop</button>
                    <button class="option-btn" data-group="genre" value="rock">Rock</button>
                    <button class="option-btn" data-group="genre" value="hiphop">Hip Hop</button>
                </div>

                <div class="option-group">
                    <label>Mood:</label>
                    <button class="option-btn active" data-group="mood" value="any">Any</button>
                    <button class="option-btn" data-group="mood" value="happy">Happy</button>
                    <button class="option-btn" data-group="mood" value="sad">Sad</button>
                    <button class="option-btn" data-group="mood" value="energetic">Energetic</button>
                </div>

                <div class="duration-slider">
                    <span>1:00</span>
                    <input type="range" id="duration" min="60" max="300" value="60">
                    <span id="duration-value">1:00</span>
                </div>
                <button id="generate-btn">Generate a track now</button>
            </div>

            <div id="generated-track-container" style="display: none; margin-top: 2rem;">
                <h2>Your Track is Ready:</h2>
                <audio controls id="audio-player" style="width: 100%;"></audio>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 AI Music Platform</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
