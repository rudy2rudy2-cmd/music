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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 bg-[radial-gradient(circle_at_bottom_left,_var(--tw-gradient-stops))] from-blue-100 via-white to-slate-100">

    <div class="w-full max-w-md glass rounded-[2.5rem] shadow-2xl shadow-blue-200/50 overflow-hidden">
        <div class="p-10">
            <div class="flex flex-col items-center mb-10">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-blue-200 mb-6 transform -rotate-3">
                    <i class="fas fa-lock"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-800 text-center tracking-tight">Panou Control</h1>
                <p class="text-slate-400 font-semibold mt-2 uppercase tracking-widest text-[10px]">Autentificare Securizată</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl mb-8 flex items-center animate-pulse">
                    <i class="fas fa-circle-exclamation mr-3 text-rose-500"></i>
                    <p class="font-bold text-sm"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Utilizator</label>
                    <div class="relative group">
                        <i class="fas fa-user absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                        <input class="w-full bg-white/50 border border-slate-200 rounded-2xl py-4 pl-12 pr-6 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-slate-700 font-medium placeholder:text-slate-300" type="text" name="username" placeholder="admin" required autofocus>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Parolă</label>
                    <div class="relative group">
                        <i class="fas fa-key absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                        <input class="w-full bg-white/50 border border-slate-200 rounded-2xl py-4 pl-12 pr-6 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-slate-700 font-medium placeholder:text-slate-300" type="password" name="password" placeholder="********" required>
                    </div>
                </div>

                <div class="pt-4">
                    <button class="w-full bg-slate-900 hover:bg-black text-white font-extrabold py-5 rounded-3xl transition duration-300 shadow-xl shadow-slate-200 flex items-center justify-center space-x-2 group active:scale-[0.98]" type="submit">
                        <span>Intră în Panou</span>
                        <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="bg-white/50 py-6 text-center border-t border-white/50">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">Digital Signage Engine &copy; 2025</p>
        </div>
    </div>
</body>
</html>
