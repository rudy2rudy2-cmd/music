<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["is_admin"]) || !$_SESSION["is_admin"]) {
    header("location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo"><a href="../index.php" style="text-decoration:none; color: #fff;">iLoveSong.ai</a></div>
            <div>
                <span>Welcome, Admin <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <a href="../logout.php" class="login-btn">Logout</a>
            </div>
        </nav>
    </header>
    <main>
        <section>
            <h2>Admin Dashboard</h2>
            <p>
                <a href="users.php" class="login-btn">Manage Users</a>
                <a href="voices.php" class="login-btn">Manage Voices</a>
                <a href="../index.php" class="login-btn">View Frontend</a>
            </p>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 iLoveSong.ai - All rights reserved</p>
    </footer>
</body>
</html>
