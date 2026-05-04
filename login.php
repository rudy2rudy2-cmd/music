<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Utilizator sau parolă incorectă.";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hotel Defects</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            color: white;
        }
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 1.5rem;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
        }
        .input-group input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s;
        }
        .input-group input:focus {
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.08);
            outline: none;
        }
    </style>
</head>
<body>
    <div class="glass shadow-2xl">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-600/20 text-blue-500 mb-4">
                <i class="fas fa-hotel text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold">Bine ai revenit</h1>
            <p class="text-gray-400 mt-2">Introdu datele pentru a accesa panoul</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div class="input-group">
                <label class="block text-sm font-medium text-gray-400 mb-2 pl-1">Utilizator</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" required class="w-full rounded-xl py-3 pl-11 pr-4 text-white" placeholder="Introduceți numele de utilizator">
                </div>
            </div>

            <div class="input-group">
                <label class="block text-sm font-medium text-gray-400 mb-2 pl-1">Parolă</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" required class="w-full rounded-xl py-3 pl-11 pr-4 text-white" placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between px-1">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" class="rounded border-gray-700 bg-gray-800 text-blue-600 focus:ring-blue-500 focus:ring-offset-gray-900">
                    <span class="text-sm text-gray-400 group-hover:text-gray-300 transition">Ține-mă minte</span>
                </label>
                <a href="#" class="text-sm text-blue-500 hover:text-blue-400 transition">Ai uitat parola?</a>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition duration-300 shadow-lg shadow-blue-600/20">
                Autentificare
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-500">
            Nu ai cont? Contactează administratorul.
        </div>
    </div>
</body>
</html>
