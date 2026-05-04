<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Global settings fetch for logo
$stmt = $pdo->query("SELECT * FROM settings");
$site_settings = [];
while ($row = $stmt->fetch()) {
    $site_settings[$row['setting_key']] = $row['setting_value'];
}
$logo = !empty($site_settings['logo_path']) ? $site_settings['logo_path'] : '';

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
    </style>
</head>
<body>
    <div class="glass shadow-2xl">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center mb-4">
                <?php if ($logo): ?>
                    <img src="<?php echo $logo; ?>" class="h-16 w-auto object-contain">
                <?php else: ?>
                    <div class="w-16 h-16 rounded-2xl bg-blue-600/20 text-blue-500 flex items-center justify-center">
                        <i class="fas fa-hotel text-3xl"></i>
                    </div>
                <?php endif; ?>
            </div>
            <h1 class="text-3xl font-bold">Autentificare</h1>
            <p class="text-gray-400 mt-2">Introdu datele pentru a accesa platforma</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2 pl-1">Utilizator</label>
                <input type="text" name="username" required class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition" placeholder="Nume utilizator">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2 pl-1">Parolă</label>
                <input type="password" name="password" required class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition duration-300 shadow-lg shadow-blue-600/20">
                Login
            </button>
        </form>

        <div class="mt-8 text-center text-[10px] text-gray-600 uppercase tracking-widest">
            &copy; 2026 Stoian Rudolf
        </div>
    </div>
</body>
</html>
