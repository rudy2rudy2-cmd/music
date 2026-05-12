<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'] ?? '';
        $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
        $role = $_POST['role'] ?? 'user';

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $password, $role]);
            $message = "Utilizator adăugat!";
        } catch (PDOException $e) {
            $message = "Eroare: Utilizatorul există deja.";
        }
    }

    if (isset($_POST['delete_id'])) {
        // Nu permitem ștergerea propriului cont sau a ultimului admin (simplificat aici)
        if ($_POST['delete_id'] != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_POST['delete_id']]);
            $message = "Utilizator șters!";
        } else {
            $message = "Nu îți poți șterge propriul cont!";
        }
    }
}

$stmt = $pdo->query("SELECT id, username, role, created_at FROM users");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Gestionare Utilizatori</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex">
    <div class="w-64 bg-slate-900 text-white min-h-screen p-6">
        <h1 class="text-2xl font-bold mb-10 text-blue-400">Admin Panel</h1>
        <nav class="space-y-4">
            <a href="index.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-800">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
            <a href="platforms.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-800">
                <i class="fas fa-layer-group mr-2"></i> Platforme
            </a>
            <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 bg-blue-600">
                <i class="fas fa-users mr-2"></i> Utilizatori
            </a>
            <a href="settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-800">
                <i class="fas fa-cog mr-2"></i> Setări
            </a>
        </nav>
    </div>

    <div class="flex-1 p-10">
        <h2 class="text-3xl font-bold mb-8">Gestionare Utilizatori</h2>

        <?php if ($message): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 mb-10">
            <h3 class="text-xl font-bold mb-6">Adaugă Utilizator Nou</h3>
            <form method="POST">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Utilizator</label>
                        <input type="text" name="username" required class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Parolă</label>
                        <input type="password" name="password" required class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rol</label>
                        <select name="role" class="mt-1 block w-full p-2 border rounded-md">
                            <option value="user">Utilizator (User)</option>
                            <option value="admin">Administrator (Admin)</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="add_user" class="mt-6 bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                    Adaugă Utilizator
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">Utilizator</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">Rol</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">Creat la</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 uppercase">Acțiuni</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-800"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-bold <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo strtoupper($user['role']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm"><?php echo $user['created_at']; ?></td>
                        <td class="px-6 py-4">
                            <form method="POST" onsubmit="return confirm('Sigur dorești să ștergi?');">
                                <input type="hidden" name="delete_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Șterge
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
