<?php include 'includes/header.php'; ?>

<main>
    <div class="container">
        <h2>Creează un cont nou</h2>
        <form action="register.php" method="post">
            <label for="username">Nume utilizator:</label>
            <input type="text" id="username" name="username" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Parolă:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Înregistrează-te</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
