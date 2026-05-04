<?php
$db_path = __DIR__ . '/database/hotel_defects.sqlite';
$lock_file = __DIR__ . '/database/install.lock';

if (file_exists($lock_file)) {
    die("Instalarea a fost deja efectuată. Ștergeți database/install.lock pentru a reinstala.");
}

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!is_dir(__DIR__ . '/database')) {
            mkdir(__DIR__ . '/database', 0777, true);
        }
        if (!is_dir(__DIR__ . '/uploads')) {
            mkdir(__DIR__ . '/uploads', 0777, true);
        }

        $pdo = new PDO("sqlite:" . $db_path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = file_get_contents(__DIR__ . '/database/schema.sql');
        $pdo->exec($sql);

        // Admin creation with specified credentials
        $admin_user = 'admin';
        $admin_pass = password_hash('admin1234', PASSWORD_DEFAULT);

        // Check if admin exists
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$admin_user]);
        if (!$check->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
            $stmt->execute([$admin_user, $admin_pass]);
        }

        file_put_contents($lock_file, date('Y-m-d H:i:s'));
        $message = "Instalare reușită!<br>User: <b>admin</b><br>Parolă: <b>admin1234</b><br><br><a href='login.php' class='text-blue-500 underline font-bold'>Mergi la Login</a>";
    } catch (Exception $e) {
        $error = "Eroare: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalare - Hotel Management</title>
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
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 2rem;
            padding: 3rem;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="glass shadow-2xl">
        <div class="w-20 h-20 bg-blue-600/20 text-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6 text-3xl">
            <i class="fas fa-tools"></i>
        </div>
        <h1 class="text-3xl font-bold mb-2">Configurare Sistem</h1>
        <p class="text-gray-400 mb-8">Inițializarea bazei de date și a contului de administrator</p>

        <?php if ($message): ?>
            <div class="bg-green-500/10 border border-green-500/50 text-green-200 p-6 rounded-2xl mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-200 p-6 rounded-2xl mb-4 text-left">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!$message): ?>
        <form method="POST">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6 mb-8 text-left space-y-3">
                <div class="flex items-center gap-3 text-sm text-gray-300">
                    <i class="fas fa-database text-blue-500"></i>
                    <span>Bază de date: SQLite (Local)</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-300">
                    <i class="fas fa-user-shield text-blue-500"></i>
                    <span>Admin: admin / admin1234</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-300">
                    <i class="fas fa-copyright text-blue-500"></i>
                    <span>Drepturi: Copyright 2026</span>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition duration-300 shadow-lg shadow-blue-600/20">
                Lansează Instalarea
            </button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
