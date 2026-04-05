<?php
$db_file = __DIR__ . '/../database.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Enable foreign keys
    $pdo->exec('PRAGMA foreign_keys = ON;');

    // Create tables if they don't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL
    );");

    $pdo->exec("CREATE TABLE IF NOT EXISTS channels (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL
    );");

    $pdo->exec("CREATE TABLE IF NOT EXISTS media (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        channel_id INTEGER NOT NULL,
        type TEXT NOT NULL,
        file_path TEXT NOT NULL,
        display_order INTEGER DEFAULT 0,
        duration INTEGER DEFAULT 10,
        FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
    );");

    // Insert default admin if not exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES ('admin', ?)");
        $stmt->execute([$hashed_password]);
    }

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
