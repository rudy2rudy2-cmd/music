<?php
session_start();
require_once 'config.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php?redirect=profile.php");
    exit;
}

// Fetch user data from database
$user_id = $_SESSION['id'];
$user_info = [];
$sql = "SELECT username, email, plan FROM users WHERE id = ?";
if($stmt = mysqli_prepare($link, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        $user_info = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}

// Fetch theme settings
$theme = 'dark';
$accent_color = 'blue';
// ... (code to fetch theme settings)
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilul Meu - AI Music Generator</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="profile.css">
</head>
<body data-theme="<?php echo htmlspecialchars($theme); ?>" data-color="<?php echo htmlspecialchars($accent_color); ?>">
    <?php include 'header.php'; ?>

    <main>
        <div class="profile-container">
            <h1>Profilul Meu</h1>
            <div class="profile-grid">
                <div class="profile-card">
                    <div class="avatar-placeholder">
                        <span><?php echo strtoupper(substr($user_info['username'], 0, 1)); ?></span>
                    </div>
                    <h2><?php echo htmlspecialchars($user_info['username']); ?></h2>
                    <p><?php echo htmlspecialchars($user_info['email']); ?></p>
                    <span class="plan-badge"><?php echo ucfirst(htmlspecialchars($user_info['plan'])); ?> Plan</span>
                </div>
                <div class="profile-settings">
                    <h2>Setări Profil</h2>
                    <form id="profile-form">
                        <label for="bio">Bio:</label>
                        <textarea id="bio" placeholder="Descrie-te pe scurt..."></textarea>
                        <label for="avatar">URL Avatar:</label>
                        <input type="text" id="avatar" placeholder="https://example.com/avatar.png">
                        <button type="submit" class="save-btn">Salvează Modificările</button>
                    </form>
                </div>
            </div>

            <div class="my-music">
                <h2>Melodiile Mele</h2>
                <div class="music-list">
                    <!-- Placeholder Item 1 -->
                    <div class="music-item">
                        <span class="music-title">Cântec de luptă cinematic</span>
                        <span class="music-details">Rock | 140 BPM | 1:30 min</span>
                        <div class="music-actions">
                            <button>▶️</button>
                            <button>💾</button>
                            <button>🗑️</button>
                        </div>
                    </div>
                    <!-- Placeholder Item 2 -->
                     <div class="music-item">
                        <span class="music-title">Beat Lofi pentru relaxare</span>
                        <span class="music-details">Lofi | 80 BPM | 2:00 min</span>
                        <div class="music-actions">
                            <button>▶️</button>
                            <button>💾</button>
                            <button>🗑️</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <script>
        document.getElementById('profile-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Funcționalitate în dezvoltare! Setările tale vor putea fi salvate aici.');
        });
    </script>
</body>
</html>
