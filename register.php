<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (is_logged_in()) {
    redirect('admin.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username && $password && $confirm_password) {
        if ($password !== $confirm_password) {
            $error = 'Parolele nu se potrivesc.';
        } else {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Acest utilizator există deja.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                if ($stmt->execute([$username, $hashed_password])) {
                    $success = 'Cont creat cu succes! Te poți autentifica.';
                } else {
                    $error = 'A apărut o eroare la crearea contului.';
                }
            }
        }
    } else {
        $error = 'Toate câmpurile sunt obligatorii.';
    }
}
?>
<!DOCTYPE html>
<html lang="ro" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare - Viziere Digitale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.5s ease; }
        .glass {
            background: rgba(var(--glass-bg), 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(var(--glass-border), 0.5);
        }
        :root {
            --primary: 37, 99, 235;
            --primary-hover: 15, 23, 42;
            --bg-from: 219, 234, 254;
            --bg-via: 255, 255, 255;
            --bg-to: 241, 245, 249;
            --glass-bg: 255, 255, 255;
            --glass-border: 255, 255, 255;
            --text-main: 30, 41, 59;
            --text-muted: 148, 163, 184;
        }
        [data-theme="noir"] {
            --primary: 255, 255, 255;
            --primary-hover: 226, 232, 240;
            --bg-from: 0, 0, 0;
            --bg-via: 15, 23, 42;
            --bg-to: 0, 0, 0;
            --glass-bg: 15, 23, 42;
            --glass-border: 51, 65, 85;
            --text-main: 255, 255, 255;
            --text-muted: 148, 163, 184;
        }
        [data-theme="noir"] .glass {
            border: 1px solid transparent;
            background-image: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)),
                              linear-gradient(90deg, #1e293b, #ffffff, #1e293b);
            background-origin: border-box;
            background-clip: padding-box, border-box;
            background-size: 200% 100%;
            animation: lightLine 3s linear infinite;
        }
        @keyframes lightLine {
            0% { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }
    </style>
    <script>
        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            const iconColor = theme === 'noir' ? 'black' : 'white';
            document.documentElement.style.setProperty('--theme-icon-color', iconColor);
        }
        const savedTheme = localStorage.getItem('theme') || 'default';
        setTheme(savedTheme);
    </script>
</head>
<body class="min-h-screen flex items-center justify-center p-6 transition-colors duration-500"
      style="background: radial-gradient(circle at bottom left, rgb(var(--bg-from)), rgb(var(--bg-via)), rgb(var(--bg-to)))">

    <div class="w-full max-w-md glass rounded-[2.5rem] shadow-2xl overflow-hidden transition-all duration-500" style="color: rgb(var(--text-main))">
        <div class="p-10">
            <div class="flex flex-col items-center mb-10">
                <?php
                    $stmt_logo = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_logo'");
                    $stmt_logo->execute();
                    $site_logo = $stmt_logo->fetchColumn();
                ?>
                <?php if ($site_logo): ?>
                    <div class="w-20 h-20 mb-6 flex items-center justify-center overflow-hidden">
                        <img src="<?php echo htmlspecialchars($site_logo); ?>" class="max-w-full max-h-full object-contain filter drop-shadow-lg">
                    </div>
                <?php else: ?>
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl shadow-xl mb-6 transform -rotate-3 transition-colors duration-500" style="background: rgb(var(--primary)); color: var(--theme-icon-color, white)">
                        <i class="fas fa-user-plus"></i>
                    </div>
                <?php endif; ?>
                <h1 class="text-3xl font-extrabold text-center tracking-tight" style="color: rgb(var(--text-main))">Creează Cont</h1>
                <p class="font-semibold mt-2 uppercase tracking-widest text-[10px]" style="color: rgb(var(--text-muted))">Administrator Nou</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl mb-8 flex items-center">
                    <i class="fas fa-circle-exclamation mr-3 text-rose-500"></i>
                    <p class="font-bold text-sm"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl mb-8 flex items-center">
                    <i class="fas fa-circle-check mr-3 text-emerald-500"></i>
                    <p class="font-bold text-sm"><?php echo $success; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider ml-1" style="color: rgb(var(--text-muted))">Utilizator</label>
                    <div class="relative group">
                        <i class="fas fa-user absolute left-5 top-1/2 -translate-y-1/2 transition-colors" style="color: rgb(var(--text-muted))"></i>
                        <input class="w-full bg-white/20 border border-white/30 rounded-2xl py-4 pl-12 pr-6 outline-none transition font-medium placeholder:text-slate-400/50" style="color: rgb(var(--text-main))" type="text" name="username" placeholder="admin_nou" required autofocus>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider ml-1" style="color: rgb(var(--text-muted))">Parolă</label>
                    <div class="relative group">
                        <i class="fas fa-key absolute left-5 top-1/2 -translate-y-1/2 transition-colors" style="color: rgb(var(--text-muted))"></i>
                        <input id="password" class="w-full bg-white/20 border border-white/30 rounded-2xl py-4 pl-12 pr-12 outline-none transition font-medium placeholder:text-slate-400/50" style="color: rgb(var(--text-main))" type="password" name="password" placeholder="********" required>
                        <button type="button" onclick="togglePassword('password', 'toggleIcon1')" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition">
                            <i id="toggleIcon1" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider ml-1" style="color: rgb(var(--text-muted))">Confirmă Parola</label>
                    <div class="relative group">
                        <i class="fas fa-shield-halved absolute left-5 top-1/2 -translate-y-1/2 transition-colors" style="color: rgb(var(--text-muted))"></i>
                        <input id="confirm_password" class="w-full bg-white/20 border border-white/30 rounded-2xl py-4 pl-12 pr-12 outline-none transition font-medium placeholder:text-slate-400/50" style="color: rgb(var(--text-main))" type="password" name="confirm_password" placeholder="********" required>
                        <button type="button" onclick="togglePassword('confirm_password', 'toggleIcon2')" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition">
                            <i id="toggleIcon2" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-4 space-y-4">
                    <button class="w-full font-extrabold py-5 rounded-3xl transition duration-300 shadow-xl flex items-center justify-center space-x-2 group active:scale-[0.98]" style="background: rgb(var(--primary)); color: var(--theme-icon-color, white)" type="submit">
                        <span>Înregistrare</span>
                        <i class="fas fa-user-plus text-xs group-hover:translate-x-1 transition-transform"></i>
                    </button>

                    <a href="login.php" class="w-full bg-white/10 hover:bg-white/20 text-center font-bold py-4 rounded-2xl transition duration-300 border border-white/10 flex items-center justify-center space-x-2" style="color: rgb(var(--text-main))">
                        <i class="fas fa-arrow-left text-xs"></i>
                        <span>Înapoi la Login</span>
                    </a>
                </div>
            </form>
        </div>
        <script>
            function togglePassword(id, iconId) {
                const pwd = document.getElementById(id);
                const icon = document.getElementById(iconId);
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
        <div class="bg-white/10 py-6 text-center border-t border-white/10">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">Digital Signage Engine © 2026 Developer By Stoian Rudolf Florian</p>
        </div>
    </div>
</body>
</html>
