<?php
require_once 'includes/db.php';

$message = '';
$error = '';

// Check if admin already exists
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$admin_exists = $stmt->fetchColumn() > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($admin_exists) {
        $error = "Platforma este deja instalată. Vă rugăm să ștergeți fișierul install.php pentru securitate.";
    } else {
        $admin_user = $_POST['admin_user'] ?? '';
        $admin_pass = $_POST['admin_pass'] ?? '';

        if ($admin_user && $admin_pass) {
            try {
                $hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $stmt->execute([$admin_user, $hashed_pass]);

                if (!is_dir('uploads')) {
                    mkdir('uploads', 0755, true);
                }

                $message = "Instalare reușită! Acum te poți autentifica.";
                $admin_exists = true;
            } catch (PDOException $e) {
                $error = "Eroare la instalare: " . $e->getMessage();
            }
        } else {
            $error = "Vă rugăm să introduceți toate datele.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalare Viziere Digitale</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md border-t-8 border-blue-600">
        <h1 class="text-2xl font-bold mb-6 text-center text-blue-600">Platforma Viziere Digitale</h1>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $message; ?> <br> <a href="login.php" class="underline font-bold">Mergi la Login</a>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!$admin_exists): ?>
            <p class="text-gray-600 mb-8 text-center text-sm italic">Pagina de instalare inițială</p>
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Utilizator Administrator</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" type="text" name="admin_user" placeholder="admin" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Parolă Administrator</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3" type="password" name="admin_pass" placeholder="********" required>
                </div>
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full transition duration-300" type="submit">
                    Instalează Platforma
                </button>
            </form>
        <?php else: ?>
            <div class="text-center">
                <p class="text-gray-700 mb-6 font-semibold text-lg">Platforma este deja instalată.</p>
                <p class="text-red-500 mb-6 text-sm">Atenție: Pentru securitate, vă rugăm să ștergeți fișierul <code class="bg-gray-100 px-1 rounded">install.php</code> de pe server.</p>
                <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded inline-block transition duration-300">Mergi la Autentificare</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
