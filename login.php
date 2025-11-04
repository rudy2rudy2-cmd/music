<?php include 'includes/header.php'; ?>

<main>
    <div class="container">
        <h2>Autentificare</h2>
        <form action="login.php" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Parolă:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Intră în cont</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
