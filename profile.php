<?php
session_start();
require_once 'config.php';

// If user is not logged in, redirect
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php?redirect=profile.php");
    exit;
}

// Fetch user data
$user_id = $_SESSION['id'];
$user_info = [];
$sql_user = "SELECT username, email, coins FROM users WHERE id = ?";
if($stmt_user = mysqli_prepare($link, $sql_user)){
    mysqli_stmt_bind_param($stmt_user, "i", $user_id);
    if(mysqli_stmt_execute($stmt_user)){
        $result_user = mysqli_stmt_get_result($stmt_user);
        $user_info = mysqli_fetch_assoc($result_user);
    }
    mysqli_stmt_close($stmt_user);
}

// Fetch user's songs
$songs = [];
$sql_songs = "SELECT id, prompt, file_path, cover_art_path, dedication_text, created_at FROM songs WHERE user_id = ? ORDER BY created_at DESC";
if ($stmt_songs = mysqli_prepare($link, $sql_songs)) {
    mysqli_stmt_bind_param($stmt_songs, "i", $user_id);
    if (mysqli_stmt_execute($stmt_songs)) {
        $result_songs = mysqli_stmt_get_result($stmt_songs);
        while ($row = mysqli_fetch_assoc($result_songs)) {
            $songs[] = $row;
        }
    }
    mysqli_stmt_close($stmt_songs);
}

// Fetch theme settings
// ... (code to fetch theme)
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Portofoliul Meu</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="profile.css">
    <!-- Font Awesome -->
</head>
<body data-theme="dark" data-color="blue">
    <?php include 'header.php'; ?>
    <main class="profile-container">
        <h1>Portofoliul Meu</h1>
        <div class="profile-card" style="margin-bottom: 2rem;">
            <h2><?php echo htmlspecialchars($user_info['username']); ?></h2>
            <p>Monede: <i class="fas fa-coins"></i> <?php echo htmlspecialchars($user_info['coins']); ?></p>
        </div>

        <div class="my-music">
            <h2>Melodiile Mele (<?php echo count($songs); ?>)</h2>
            <div class="music-list">
                <?php if (empty($songs)): ?>
                    <p>Nu ai generat nicio melodie încă. <a href="generate_music.php">Creează una acum!</a></p>
                <?php else: ?>
                    <?php foreach ($songs as $song): ?>
                        <div class="music-item-large">
                            <img src="<?php echo htmlspecialchars($song['cover_art_path']); ?>" alt="Coperta melodiei" class="music-cover">
                            <div class="music-info">
                               <h3><?php echo htmlspecialchars($song['prompt']); ?></h3>
                               <?php if (!empty($song['dedication_text'])): ?>
                                   <p class="dedication"><em>Dedicație: <?php echo htmlspecialchars($song['dedication_text']); ?></em></p>
                               <?php endif; ?>
                               <audio controls src="<?php echo htmlspecialchars($song['file_path']); ?>"></audio>
                               <div class="music-actions-large">
                                   <button class="action-btn extend-btn" data-song-id="<?php echo $song['id']; ?>">Extinde Melodia</button>
                                   <button class="action-btn delete-btn" data-song-id="<?php echo $song['id']; ?>">Șterge</button>
                               </div>
                           </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <script>
        // Placeholder for extend/delete functionality
        document.querySelectorAll('.extend-btn, .delete-btn').forEach(btn => {
            btn.addEventListener('click', () => alert('Funcționalitate în dezvoltare.'));
        });
    </script>
</body>
</html>
