<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: users.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: users.php");
    exit();
}

$message = '';

if (isset($_POST['update_role'])) {
    $stmt = $pdo->prepare("UPDATE users SET role = ?, is_active = ? WHERE id = ?");
    $stmt->execute([$_POST['role'], $_POST['is_active'], $id]);
    $message = "Cont actualizat!";
}

// Fetch Purchases
$stmt = $pdo->prepare("SELECT p.*, pur.created_at as purchase_date, pur.amount FROM purchases pur JOIN platforms p ON pur.platform_id = p.id WHERE pur.user_id = ? ORDER BY pur.created_at DESC");
$stmt->execute([$id]);
$purchases = $stmt->fetchAll();

$header_title = "Gestionare Utilizator: " . $user['username'];
require_once 'includes/admin_header.php';
?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- User Info -->
            <div class="admin-card p-8 shadow-lg h-fit">
                <h3 class="text-xl font-bold mb-6 flex items-center"><i class="fas fa-user-circle mr-3 text-blue-500"></i> Detalii Cont</h3>
                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold uppercase opacity-50 mb-1">Nume Complet</label>
                        <p class="text-lg font-bold"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase opacity-50 mb-1">Email</label>
                        <p class="font-medium"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Rol Utilizator</label>
                        <select name="role" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border border-gray-200' : ''; ?>">
                            <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>Utilizator Standard</option>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold opacity-75 mb-2">Status Cont</label>
                        <select name="is_active" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border border-gray-200' : ''; ?>">
                            <option value="1" <?php echo $user['is_active'] == 1 ? 'selected' : ''; ?>>Activ</option>
                            <option value="0" <?php echo $user['is_active'] == 0 ? 'selected' : ''; ?>>Inactiv / Suspendat</option>
                        </select>
                    </div>
                    <button type="submit" name="update_role" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition">Salvează Modificările</button>
                </form>
            </div>

            <!-- Purchase History -->
            <div class="lg:col-span-2 admin-card p-8 shadow-lg">
                <h3 class="text-xl font-bold mb-6 flex items-center"><i class="fas fa-history mr-3 text-purple-500"></i> Istoric Achiziții</h3>

                <?php if (empty($purchases)): ?>
                    <div class="py-12 text-center opacity-40 italic">Nicio achiziție înregistrată.</div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach($purchases as $p): ?>
                            <div class="flex items-center justify-between p-6 rounded-2xl bg-gray-700/10 border border-gray-700/20">
                                <div>
                                    <h4 class="font-bold text-lg"><?php echo htmlspecialchars($p['title']); ?></h4>
                                    <p class="text-xs opacity-60">Achiziționat pe: <?php echo $p['purchase_date']; ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-extrabold text-blue-500"><?php echo $p['amount']; ?> EUR</p>
                                    <span class="text-[10px] font-bold uppercase bg-green-500/10 text-green-500 px-2 py-1 rounded">Finalizat</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

<?php require_once 'includes/admin_footer.php'; ?>
