<?php
if (!file_exists('includes/config.php')) {
    header("Location: install.php");
    exit();
}
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = "Parolele nu coincid.";
    } else {
        // Verificare dacă utilizatorul sau email-ul există deja
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "Utilizatorul sau email-ul este deja înregistrat.";
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $activation_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, email, password, activation_code, is_active) VALUES (?, ?, ?, ?, ?, ?, 0)");
                $stmt->execute([$username, $first_name, $last_name, $email, $hashed_pass, $activation_code]);

                // Trimitere email (Simulat)
                $to = $email;
                $subject = "Confirmare Cont";
                $message = "Salut $first_name, codul tău de activare este: $activation_code";
                $headers = "From: no-reply@showcase.ro";

                // mail($to, $subject, $message, $headers);

                $_SESSION['unactivated_email'] = $email;
                $success = "Cont creat cu succes! Te rugăm să introduci codul primit pe email ($activation_code).";
                header("Location: activate.php");
                exit();
            } catch (PDOException $e) {
                $error = "A apărut o eroare: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Înregistrare</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-10">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Cont Nou</h1>
        <?php if ($error): ?>
            <p class="text-red-500 mb-4"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm">Prenume</label>
                    <input type="text" name="first_name" required class="w-full p-2 border rounded mt-1">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm">Nume</label>
                    <input type="text" name="last_name" required class="w-full p-2 border rounded mt-1">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm">Utilizator</label>
                <input type="text" name="username" required class="w-full p-2 border rounded mt-1">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm">Email</label>
                <input type="email" name="email" required class="w-full p-2 border rounded mt-1">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm">Parolă</label>
                <input type="password" name="password" required class="w-full p-2 border rounded mt-1">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm">Confirmă Parolă</label>
                <input type="password" name="confirm_password" required class="w-full p-2 border rounded mt-1">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Înregistrare</button>
            <p class="mt-4 text-center text-sm">Ai deja cont? <a href="login.php" class="text-blue-600 hover:underline">Autentifică-te</a></p>
        </form>
    </div>
</body>
</html>
