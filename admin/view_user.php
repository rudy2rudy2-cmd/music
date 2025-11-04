<?php
session_start();
require_once '../config.php';

// Admin-only access
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["is_admin"]) {
    header("location: ../login.php");
    exit;
}

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($user_id === 0) {
    header("location: manage_users.php");
    exit;
}

// --- Handle POST Requests ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle adding coins
    if (isset($_POST['coins'])) {
        $coins_to_add = (int)$_POST['coins'];
        $sql_update_coins = "UPDATE users SET coins = coins + ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql_update_coins)) {
            mysqli_stmt_bind_param($stmt, "ii", $coins_to_add, $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    // Handle adding/updating a dedication
    if (isset($_POST['dedication_text']) && isset($_POST['song_id'])) {
        $dedication_text = $_POST['dedication_text'];
        $song_id = (int)$_POST['song_id'];
        $sql_update_dedication = "UPDATE songs SET dedication_text = ? WHERE id = ? AND user_id = ?";
        if ($stmt = mysqli_prepare($link, $sql_update_dedication)) {
            mysqli_stmt_bind_param($stmt, "sii", $dedication_text, $song_id, $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    header("location: view_user.php?id=" . $user_id); // Redirect to refresh
    exit;
}

// --- Fetch Data ---
$user_info = mysqli_fetch_assoc(mysqli_query($link, "SELECT id, username, email, coins FROM users WHERE id = $user_id"));
$songs = [];
$sql_songs = "SELECT id, prompt, file_path, cover_art_path, dedication_text, created_at FROM songs WHERE user_id = ? ORDER BY created_at DESC";
if ($stmt_songs = mysqli_prepare($link, $sql_songs)) {
    mysqli_stmt_bind_param($stmt_songs, "i", $user_id);
    if (mysqli_stmt_execute($stmt_songs)) {
        $result_songs = mysqli_stmt_get_result($stmt_songs);
        while ($row = mysqli_fetch_assoc($result_songs)) $songs[] = $row;
    }
    mysqli_stmt_close($stmt_songs);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View User</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../profile.css"> <!-- Reuse profile styles -->
</head>
<body data-theme="dark" data-color="blue">
    <header>
        <div class="logo">Admin Panel</div>
        <nav><a href="manage_users.php">Back to Users List</a></nav>
    </header>
    <main class="profile-container">
        <h1>Viewing User: <?php echo htmlspecialchars($user_info['username']); ?></h1>

        <div class="admin-management-section">
            <h2>Manage Coins</h2>
            <form method="POST" class="coin-form" style="display:flex; gap:1rem; align-items:center;">
                <input type="number" name="coins" placeholder="Amount..." required style="margin:0;">
                <button type="submit">Add Coins</button>
            </form>
        </div>

        <div class="my-music">
            <h2>User's Portfolio (<?php echo count($songs); ?>)</h2>
            <?php foreach ($songs as $song): ?>
                <div class="music-item-large">
                    <img src="../<?php echo htmlspecialchars($song['cover_art_path']); ?>" alt="Cover Art" class="music-cover">
                    <div class="music-info">
                        <h3><?php echo htmlspecialchars($song['prompt']); ?></h3>
                        <audio controls src="../<?php echo htmlspecialchars($song['file_path']); ?>"></audio>

                        <form method="POST">
                            <input type="hidden" name="song_id" value="<?php echo $song['id']; ?>">
                            <textarea name="dedication_text" placeholder="Add a dedication..."><?php echo htmlspecialchars($song['dedication_text'] ?? ''); ?></textarea>
                            <button type="submit">Save Dedication</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
