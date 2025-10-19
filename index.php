<?php
session_start();
require_once "src/config.php"; // Ensure config is included for DB connection

$songs = [];
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $user_id = $_SESSION["id"];
    $songs_sql = "SELECT file_path, title FROM songs WHERE user_id = ? ORDER BY created_at DESC";
    if ($stmt = $conn->prepare($songs_sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $songs[] = $row;
        }
        $stmt->close();
    }
}

// Fetch available voices
$voices = [];
$voices_sql = "SELECT id, name FROM voices";
$voices_result = $conn->query($voices_sql);
while ($row = $voices_result->fetch_assoc()) {
    $voices[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iLoveSong.ai - AI Music Generator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">iLoveSong.ai</div>
            <ul>
                <li><a href="#generator">AI Music Generator</a></li>
                <li><a href="#docs">Documentation</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <li><a href="#music">My Music</a></li>
                <?php endif; ?>
            </ul>
            <div>
                <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <a href="logout.php" class="login-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="login-btn">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <section id="generator">
            <h1>AI Music Generator</h1>
            <p>Create your own unparalleled music, male or female vocals, MP3 audios and MP4 videos.<br>Enjoy innovative music instantly.</p>
            <div class="generator-form">
                <textarea id="lyrics" placeholder="[Verse 1]
Wake up to the morning light
Coffee brewing, feeling right
..."></textarea>
                <input type="text" id="style" placeholder="Funk pop, groovy bassline, syncopated rhythm, danceable beat">
                <input type="text" id="title" placeholder="Find My Groove">
                <select id="voice_id">
                    <option value="">Select a Voice</option>
                    <?php foreach ($voices as $voice): ?>
                        <option value="<?php echo $voice['id']; ?>"><?php echo htmlspecialchars($voice['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button id="generate-btn">Generate</button>
            </div>
        </section>

        <section id="showcase">
            <h2>AI Music Generator Showcase</h2>
            <!-- Video placeholders -->
        </section>

        <section id="updates">
            <h2>Latest Updates</h2>
            <!-- Update log placeholders -->
        </section>

        <section id="pricing">
            <h2>Get Plan For Song AI Music Generator</h2>
            <!-- Pricing plans placeholders -->
        </section>

        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
        <section id="music">
            <h2>My Music</h2>
            <div class="song-list">
                <?php if (!empty($songs)): ?>
                    <ul>
                        <?php foreach ($songs as $song): ?>
                            <li>
                                <a href="<?php echo htmlspecialchars($song['file_path']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($song['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>You haven't generated any songs yet.</p>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 iLoveSong.ai - All rights reserved</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
