<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$message_action = '';

if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    $message_action = "Mesaj șters!";
}

if (isset($_POST['mark_read'])) {
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $stmt->execute([$_POST['mark_read']]);
}

$stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();

$header_title = "Mesaje Chat Suport";
require_once 'includes/admin_header.php';
?>

        <?php if ($message_action): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-6 py-4 rounded-xl mb-8 shadow-sm">
                <i class="fas fa-info-circle mr-2"></i> <?php echo $message_action; ?>
            </div>
        <?php endif; ?>

        <div class="space-y-6">
            <?php foreach ($messages as $msg): ?>
                <div class="admin-card p-8 shadow-lg border-l-4 <?php echo $msg['is_read'] ? 'border-gray-400 opacity-75' : 'border-blue-600 shadow-blue-500/10'; ?>">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gray-700/20 rounded-full flex items-center justify-center font-bold text-lg">
                                <?php echo strtoupper(substr($msg['first_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="font-bold text-xl <?php echo $admin_theme === 'neon' ? 'text-white' : 'text-gray-800'; ?>">
                                    <?php echo htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']); ?>
                                </h4>
                                <p class="text-sm text-blue-500 font-medium"><?php echo htmlspecialchars($msg['email']); ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs opacity-50 mb-2"><?php echo $msg['created_at']; ?></p>
                            <?php if (!$msg['is_read']): ?>
                                <span class="bg-blue-600 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-widest">Nou</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="p-6 bg-gray-700/10 rounded-2xl mb-6">
                        <p class="<?php echo $admin_theme === 'neon' ? 'text-gray-300' : 'text-gray-700'; ?> leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($msg['message']); ?></p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <?php if (!$msg['is_read']): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="mark_read" value="<?php echo $msg['id']; ?>">
                                <button type="submit" class="text-xs font-bold text-green-500 hover:underline">Marchează ca citit</button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" onsubmit="return confirm('Ștergi mesajul?');" class="inline">
                            <input type="hidden" name="delete_id" value="<?php echo $msg['id']; ?>">
                            <button type="submit" class="text-xs font-bold text-red-500 hover:underline">Șterge Mesaj</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($messages)): ?>
                <div class="admin-card p-20 text-center opacity-40">
                    <i class="fas fa-comment-slash text-6xl mb-6 block"></i>
                    <p class="text-xl font-bold italic">Nu există mesaje primite.</p>
                </div>
            <?php endif; ?>
        </div>

<?php require_once 'includes/admin_footer.php'; ?>
