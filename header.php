<header>
    <div class.logo">Mubert</div>
    <nav>
        <a href="index.php">Acasă</a>
        <a href="generate_music.php">Generează</a>
        <a href="gallery.php">Galerie</a>
        <a href="pricing.php">Prețuri</a>
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
            <a href="profile.php">Profil</a>
            <?php
            // Fetch remaining generations to display in header
            $generations_left_header = 0;
            $user_id_header = $_SESSION['id'];
            $sql_header = "SELECT generations_left FROM users WHERE id = ?";
            if($stmt_header = mysqli_prepare($link, $sql_header)){
                mysqli_stmt_bind_param($stmt_header, "i", $user_id_header);
                if(mysqli_stmt_execute($stmt_header)){
                    mysqli_stmt_bind_result($stmt_header, $generations_left_header);
                    mysqli_stmt_fetch($stmt_header);
                }
                mysqli_stmt_close($stmt_header);
            }
            ?>
            <span class="generations-counter">Generări: <?php echo $generations_left_header; ?></span>
            <?php if ($_SESSION["is_admin"]): ?>
                <a href="admin/index.php">Admin</a>
            <?php endif; ?>
            <a href="logout.php" class="login-btn">Log Out</a>
        <?php else: ?>
            <a href="login.php" class="login-btn">Log In</a>
            <a href="register.php" class="signup-btn">Sign Up</a>
        <?php endif; ?>
    </nav>
</header>
