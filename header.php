<header>
    <div class="logo">Mubert</div>
    <nav>
        <a href="index.php"><i class="fas fa-home"></i> Acasă</a>
        <a href="generate_music.php"><i class="fas fa-magic"></i> Generează</a>
        <a href="gallery.php"><i class="fas fa-images"></i> Galerie</a>
        <a href="pricing.php"><i class="fas fa-dollar-sign"></i> Prețuri</a>
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
            <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
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
                <a href="admin/index.php"><i class="fas fa-cog"></i> Admin</a>
            <?php endif; ?>
            <a href="logout.php" class="login-btn"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        <?php else: ?>
            <a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> Log In</a>
            <a href="register.php" class="signup-btn"><i class="fas fa-user-plus"></i> Sign Up</a>
        <?php endif; ?>
    </nav>
</header>
