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
    <title>Galerie - AI Music Generator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body data-theme="<?php echo htmlspecialchars($theme); ?>" data-color="<?php echo htmlspecialchars($accent_color); ?>">
    <?php include 'header.php'; ?>

    <main>
        <div class="placeholder-container" style="text-align: center; min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h1>🎨 Galerie Comunitate</h1>
            <p style="font-size: 1.2rem; color: #aaa;">În curând aici!</p>
            <p>Lucrăm la o secțiune unde vei putea explora, asculta și te vei putea inspira din creațiile altor utilizatori.</p>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
