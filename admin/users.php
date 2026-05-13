<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'] ?? '';
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
        $role = $_POST['role'] ?? 'user';

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, email, password, role, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$username, $first_name, $last_name, $email, $password, $role]);
            $message = "Utilizator adăugat!";
        } catch (PDOException $e) {
            $message = "Eroare: Utilizatorul sau email-ul există deja.";
        }
    }

    if (isset($_POST['delete_id'])) {
        if ($_POST['delete_id'] != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_POST['delete_id']]);
            $message = "Utilizator șters!";
        } else {
            $message = "Nu îți poți șterge propriul cont!";
        }
    }
}

$stmt = $pdo->query("SELECT id, username, first_name, last_name, email, role, is_active, created_at FROM users");
$users = $stmt->fetchAll();

$header_title = "Gestionare Utilizatori";
require_once 'includes/admin_header.php';
?>

        <?php if ($message): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-6 py-4 rounded-xl mb-8 shadow-sm">
                <i class="fas fa-info-circle mr-2"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="admin-card p-10 shadow-lg mb-12">
            <h3 class="text-xl font-bold mb-8 flex items-center">
                <i class="fas fa-user-plus mr-3 text-green-500"></i> Adaugă Utilizator Nou
            </h3>
            <form method="POST">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Prenume</label>
                        <input type="text" name="first_name" required class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Nume</label>
                        <input type="text" name="last_name" required class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Email</label>
                        <input type="email" name="email" required class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Username</label>
                        <input type="text" name="username" required class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Parolă</label>
                        <input type="password" name="password" required class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Rol Atribuit</label>
                        <select name="role" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                            <option value="user">Utilizator Standard</option>
                            <option value="admin">Administrator Sistem</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="add_user" class="mt-8 bg-green-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-green-700 transition shadow-lg">
                    <i class="fas fa-check mr-2"></i> Adaugă Utilizator
                </button>
            </form>
        </div>

        <div class="admin-card shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="<?php echo $admin_theme === 'neon' ? 'bg-slate-800/50' : 'bg-gray-50'; ?> border-b border-gray-700/30">
                        <tr>
                            <th class="px-8 py-5 text-sm font-bold uppercase tracking-wider opacity-60">Identitate</th>
                            <th class="px-8 py-5 text-sm font-bold uppercase tracking-wider opacity-60">Nume Complet</th>
                            <th class="px-8 py-5 text-sm font-bold uppercase tracking-wider opacity-60">Status</th>
                            <th class="px-8 py-5 text-sm font-bold uppercase tracking-wider opacity-60">Rol</th>
                            <th class="px-8 py-5 text-sm font-bold uppercase tracking-wider opacity-60">Creat la</th>
                            <th class="px-8 py-5 text-sm font-bold uppercase tracking-wider opacity-60">Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/20">
                        <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-blue-500/5 transition">
                            <td class="px-8 py-6">
                                <div class="font-bold <?php echo $admin_theme === 'neon' ? 'text-white' : 'text-gray-800'; ?>"><?php echo htmlspecialchars($user['username']); ?></div>
                                <div class="text-xs opacity-60"><?php echo htmlspecialchars($user['email']); ?></div>
                            </td>
                            <td class="px-8 py-6 text-sm">
                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                            </td>
                            <td class="px-8 py-6">
                                <?php if ($user['is_active']): ?>
                                    <span class="bg-green-500/10 text-green-500 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest">Activ</span>
                                <?php else: ?>
                                    <span class="bg-red-500/10 text-red-500 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest">Inactiv</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest <?php echo $user['role'] === 'admin' ? 'bg-purple-500/10 text-purple-500' : 'bg-gray-500/10 text-gray-400'; ?>">
                                    <?php echo $user['role']; ?>
                                </span>
                            </td>
                            <td class="px-8 py-6 text-xs opacity-60"><?php echo $user['created_at']; ?></td>
                            <td class="px-8 py-6">
                                <form method="POST" onsubmit="return confirm('Sigur dorești să ștergi?');">
                                    <input type="hidden" name="delete_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-400 font-bold flex items-center">
                                        <i class="fas fa-user-times mr-2"></i> Șterge
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

<?php require_once 'includes/admin_footer.php'; ?>
