<?php
require_once 'includes/db.php';

$message = '';
$error = '';

// Check directory permissions
if (!is_writable(__DIR__)) {
    $error = "Eroare: Directorul rădăcină nu are permisiuni de scriere. Vă rugăm să setați permisiunile (CHMOD 775 sau 777).";
}

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
                    chmod('uploads', 0777);
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
<html lang="ro" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalare - Viziere Digitale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-blue-100 via-white to-slate-100">

    <div class="w-full max-w-lg glass rounded-[2.5rem] shadow-2xl shadow-blue-200/50 overflow-hidden">
        <div class="p-12">
            <div class="flex flex-col items-center mb-10">
                <div class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center text-white text-3xl shadow-xl shadow-blue-200 mb-6 transform rotate-3">
                    <i class="fas fa-tv"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-800 text-center">Viziere Digitale</h1>
                <p class="text-slate-400 font-semibold mt-2 uppercase tracking-widest text-[10px]">Setup Initial V2.0</p>
            </div>

            <?php if ($message): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl mb-8 flex items-center animate-bounce">
                    <i class="fas fa-check-circle mr-3 text-xl text-emerald-500"></i>
                    <div>
                        <p class="font-bold text-sm"><?php echo $message; ?></p>
                        <a href="login.php" class="text-xs underline hover:text-emerald-800 transition">Mergi la Autentificare</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl mb-8 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3 text-xl text-rose-500"></i>
                    <p class="font-bold text-sm"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <?php if (!$admin_exists): ?>
                <form method="POST" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Utilizator Admin</label>
                        <div class="relative group">
                            <i class="fas fa-user absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                            <input class="w-full bg-white/50 border border-slate-200 rounded-2xl py-4 pl-12 pr-6 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-slate-700 font-medium placeholder:text-slate-300" type="text" name="admin_user" placeholder="ex: admin" required autofocus>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Parolă Admin</label>
                        <div class="relative group">
                            <i class="fas fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                            <input id="password" class="w-full bg-white/50 border border-slate-200 rounded-2xl py-4 pl-12 pr-12 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-slate-700 font-medium placeholder:text-slate-300" type="password" name="admin_pass" placeholder="********" required>
                            <button type="button" onclick="togglePassword()" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-blue-500 transition">
                                <i id="toggleIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-5 rounded-3xl transition duration-300 shadow-xl shadow-blue-200 flex items-center justify-center space-x-2 group active:scale-[0.98]" type="submit">
                            <span>Instalează Platforma</span>
                            <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center space-y-8">
                    <div class="py-10 bg-slate-100/50 rounded-[2rem] border border-slate-100">
                        <i class="fas fa-shield-halved text-5xl text-blue-500/20 mb-6"></i>
                        <p class="text-slate-600 font-bold px-8">Platforma este configurată și securizată.</p>
                        <p class="text-rose-500 mt-4 text-[11px] font-bold uppercase tracking-widest px-8">Atenție: Ștergeți fișierul <code class="bg-rose-100 px-2 rounded-lg text-rose-700">install.php</code></p>
                    </div>
                    <a href="login.php" class="inline-flex items-center space-x-3 bg-slate-800 hover:bg-black text-white font-bold py-4 px-10 rounded-2xl transition duration-300 shadow-xl shadow-slate-200 group">
                        <span>Accesează Panoul</span>
                        <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="bg-white/50 py-6 text-center border-t border-white/50">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">Digital Signage Engine © 2026 Developer By Stoian Rudolf Florian</p>
        </div>
    </div>
    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
