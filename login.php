<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (is_logged_in()) {
    redirect('admin.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            redirect('admin.php');
        } else {
            $error = 'Utilizator sau parolă incorectă.';
        }
    } else {
        $error = 'Vă rugăm să introduceți toate datele.';
    }
}
?>
<!DOCTYPE html>
<html lang="ro" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Viziere Digitale</title>
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
            --primary: 37, 99, 235; /* blue-600 */
            --primary-hover: 15, 23, 42; /* slate-900 */
            --bg-from: 219, 234, 254; /* blue-100 */
            --bg-via: 255, 255, 255;
            --bg-to: 241, 245, 249; /* slate-100 */
            --glass-bg: 255, 255, 255;
            --glass-border: 255, 255, 255;
            --text-main: 30, 41, 59;
            --text-muted: 148, 163, 184;
        }
        [data-theme="midnight"] {
            --primary: 139, 92, 246; /* violet-500 */
            --primary-hover: 124, 58, 237;
            --bg-from: 15, 23, 42; /* slate-900 */
            --bg-via: 30, 41, 59; /* slate-800 */
            --bg-to: 15, 23, 42;
            --glass-bg: 30, 41, 59;
            --glass-border: 71, 85, 105;
            --text-main: 248, 250, 252;
            --text-muted: 148, 163, 184;
        }
        [data-theme="emerald"] {
            --primary: 16, 185, 129; /* emerald-500 */
            --primary-hover: 5, 150, 105;
            --bg-from: 209, 250, 229;
            --bg-via: 255, 255, 255;
            --bg-to: 236, 253, 245;
            --glass-bg: 255, 255, 255;
            --glass-border: 167, 243, 208;
            --text-main: 6, 78, 59;
            --text-muted: 52, 211, 153;
        }
        [data-theme="sunset"] {
            --primary: 244, 63, 94; /* rose-500 */
            --primary-hover: 225, 29, 72;
            --bg-from: 255, 241, 242;
            --bg-via: 255, 255, 255;
            --bg-to: 255, 247, 237;
            --glass-bg: 255, 255, 255;
            --glass-border: 254, 205, 211;
            --text-main: 159, 18, 57;
            --text-muted: 251, 113, 133;
        }
    </style>
    <script>
        const savedTheme = localStorage.getItem('theme') || 'default';
        document.documentElement.setAttribute('data-theme', savedTheme);
        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center p-6 transition-colors duration-500"
      style="background: radial-gradient(circle at bottom left, rgb(var(--bg-from)), rgb(var(--bg-via)), rgb(var(--bg-to)))">

    <div class="fixed top-8 right-8 flex space-x-2 glass p-2 rounded-2xl shadow-xl z-50">
        <button onclick="setTheme('default')" class="w-8 h-8 rounded-xl bg-blue-500 border-2 border-white shadow-sm hover:scale-110 transition" title="Default"></button>
        <button onclick="setTheme('midnight')" class="w-8 h-8 rounded-xl bg-slate-900 border-2 border-white shadow-sm hover:scale-110 transition" title="Midnight"></button>
        <button onclick="setTheme('emerald')" class="w-8 h-8 rounded-xl bg-emerald-500 border-2 border-white shadow-sm hover:scale-110 transition" title="Emerald"></button>
        <button onclick="setTheme('sunset')" class="w-8 h-8 rounded-xl bg-rose-500 border-2 border-white shadow-sm hover:scale-110 transition" title="Sunset"></button>
    </div>

    <div class="w-full max-w-md glass rounded-[2.5rem] shadow-2xl overflow-hidden transition-all duration-500" style="color: rgb(var(--text-main))">
        <div class="p-10">
            <div class="flex flex-col items-center mb-10">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl mb-6 transform -rotate-3 transition-colors duration-500" style="background: rgb(var(--primary))">
                    <i class="fas fa-lock"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-center tracking-tight" style="color: rgb(var(--text-main))">Panou Control</h1>
                <p class="font-semibold mt-2 uppercase tracking-widest text-[10px]" style="color: rgb(var(--text-muted))">Autentificare Securizată</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl mb-8 flex items-center animate-pulse">
                    <i class="fas fa-circle-exclamation mr-3 text-rose-500"></i>
                    <p class="font-bold text-sm"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider ml-1" style="color: rgb(var(--text-muted))">Utilizator</label>
                    <div class="relative group">
                        <i class="fas fa-user absolute left-5 top-1/2 -translate-y-1/2 transition-colors" style="color: rgb(var(--text-muted))"></i>
                        <input class="w-full bg-white/20 border border-white/30 rounded-2xl py-4 pl-12 pr-6 outline-none transition font-medium placeholder:text-slate-400/50" style="color: rgb(var(--text-main))" type="text" name="username" placeholder="admin" required autofocus>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider ml-1" style="color: rgb(var(--text-muted))">Parolă</label>
                    <div class="relative group">
                        <i class="fas fa-key absolute left-5 top-1/2 -translate-y-1/2 transition-colors" style="color: rgb(var(--text-muted))"></i>
                        <input class="w-full bg-white/20 border border-white/30 rounded-2xl py-4 pl-12 pr-6 outline-none transition font-medium placeholder:text-slate-400/50" style="color: rgb(var(--text-main))" type="password" name="password" placeholder="********" required>
                    </div>
                </div>

                <div class="pt-4">
                    <button class="w-full text-white font-extrabold py-5 rounded-3xl transition duration-300 shadow-xl flex items-center justify-center space-x-2 group active:scale-[0.98]" style="background: rgb(var(--primary))" type="submit">
                        <span>Intră în Panou</span>
                        <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="bg-white/50 py-6 text-center border-t border-white/50">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">2026 Developer By Stoian Rudolf Florian</p>
        </div>
    </div>
</body>
</html>
