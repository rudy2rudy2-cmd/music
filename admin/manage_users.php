<?php
session_start();
require_once '../config.php';

// Admin-only access
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["is_admin"]) {
    header("location: ../login.php");
    exit;
}

// Fetch all users
$users = [];
$sql = "SELECT id, username, email, coins FROM users ORDER BY id ASC";
$result = mysqli_query($link, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

// Fetch theme settings
// ... (code to fetch theme)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .users-table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
        .users-table th, .users-table td { padding: 0.8rem; text-align: left; border-bottom: 1px solid #444; }
        .users-table th { background-color: rgba(0,0,0,0.3); }
        .action-link { color: var(--accent-color); text-decoration: none; font-weight: bold; }
    </style>
</head>
<body data-theme="dark" data-color="blue">
    <header>
        <div class="logo"><a href="../index.php" style="text-decoration:none; color:inherit;">Admin Panel</a></div>
        <nav><a href="../logout.php" class="login-btn">Log Out</a></nav>
    </header>
    <main class="profile-container">
        <h1>Manage Users</h1>
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Coins</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><i class="fas fa-coins"></i> <?php echo $user['coins']; ?></td>
                        <td>
                            <a href="view_user.php?id=<?php echo $user['id']; ?>" class="action-link">View / Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
