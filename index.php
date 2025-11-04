<?php
session_start();
require_once 'config.php';

// Fetch theme settings for consistent styling
$theme = 'dark';
$accent_color = 'blue';
$sql = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('theme', 'accent_color')";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['setting_key'] == 'theme') $theme = $row['setting_value'];
        if ($row['setting_key'] == 'accent_color') $accent_color = $row['setting_value'];
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
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body data-theme="<?php echo htmlspecialchars($theme); ?>" data-color="<?php echo htmlspecialchars($accent_color); ?>">
    <div class="bg-polygon polygon1"></div>
    <div class="bg-polygon polygon2"></div>
    <div class="bg-polygon polygon3"></div>
    <?php include 'header.php'; ?>

    <main>
        <div class="hero-container">
            <h1>Scrie o idee. Ascultă magia AI.</h1>
            <p class="subtitle">Transformă-ți textul în muzică originală în câteva secunde.</p>

            <form action="generate_music.php" method="get" class="hero-form">
                <input type="text" name="prompt" class="hero-prompt" placeholder="Descrie melodia ta...">
                <button type="submit" class="hero-generate-btn">Generează Muzică</button>
            </form>

            <div class="prompt-examples">
                <p>Încearcă asta:</p>
                <a href="generate_music.php?prompt=rock epic cu voce feminină">🎸 rock epic cu voce feminină</a>
                <a href="generate_music.php?prompt=beat trap dark 90 BPM">🎧 beat trap dark 90 BPM</a>
                <a href="generate_music.php?prompt=muzică orchestrală de film">🎻 muzică orchestrală de film</a>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
