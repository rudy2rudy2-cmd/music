<?php
session_start();
require_once '../config.php'; // DB connection

// Check if the user is a logged-in admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["is_admin"]) {
    header("location: ../login.php");
    exit;
}

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body data-theme="<?php echo htmlspecialchars($theme); ?>" data-color="<?php echo htmlspecialchars($accent_color); ?>">
    <header>
        <div class="logo">Admin Panel</div>
        <nav>
            <a href="../index.php">View Site</a>
            <a href="../logout.php" class="login-btn">Log Out</a>
        </nav>
    </header>
    <main>
        <div class="generator-container">
            <h2>Theme Settings</h2>
            <div class="generator-form">
                <div class="option-group">
                    <label>Theme:</label>
                    <button class="option-btn" data-theme="dark">Midnight Dark</button>
                    <button class="option-btn" data-theme="neon">Neon Wave</button>
                    <button class="option-btn" data-theme="forest">Forest Light</button>
                </div>
                <div class="option-group">
                    <label>Accent Color:</label>
                    <button class="option-btn" data-color="blue">Blue</button>
                    <button class="option-btn" data-color="green">Green</button>
                    <button class="option-btn" data-color="red">Red</button>
                    <button class="option-btn" data-color="yellow">Yellow</button>
                </div>
            </div>
        </div>
    </main>
    <script src="theme.js"></script>
</body>
</html>
