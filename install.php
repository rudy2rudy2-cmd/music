<?php
$db_path = __DIR__ . '/database/hotel_defects.sqlite';
$lock_file = __DIR__ . '/database/install.lock';

if (file_exists($lock_file)) {
    die("Installation already completed. Delete database/install.lock to reinstall.");
}

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("sqlite:" . $db_path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = file_get_contents(__DIR__ . '/database/schema.sql');
        $pdo->exec($sql);

        $admin_user = $_POST['username'];
        $admin_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
        $stmt->execute([$admin_user, $admin_pass]);

        file_put_contents($lock_file, date('Y-m-d H:i:s'));
        $message = "Instalare reușită! Puteți merge la <a href='login.php' class='text-blue-500 underline'>Login</a>.";
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
    <title>Instalare - Hotel Defects Control</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
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
            border-radius: 1rem;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="glass shadow-2xl">
        <h1 class="text-2xl font-bold text-center mb-6">
            <i class="fas fa-tools mr-2 text-blue-400"></i>Instalare Sistem
        </h1>

        <?php if ($message): ?>
            <div class="bg-green-500/20 border border-green-500 text-green-200 p-3 rounded mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-200 p-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!$message): ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block mb-2 text-sm">Utilizator Admin</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" required class="w-full bg-white/10 border border-white/20 rounded py-2 pl-10 pr-4 focus:outline-none focus:border-blue-400">
                </div>
            </div>
            <div class="mb-6">
                <label class="block mb-2 text-sm">Parolă Admin</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" required class="w-full bg-white/10 border border-white/20 rounded py-2 pl-10 pr-4 focus:outline-none focus:border-blue-400">
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded transition duration-200">
                Lansează Instalarea
            </button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
