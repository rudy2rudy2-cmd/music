<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$stmt = $pdo->query("SELECT d.*, u.username, (SELECT message FROM chat_messages WHERE discussion_id = d.id ORDER BY created_at DESC LIMIT 1) as last_msg FROM chat_discussions d LEFT JOIN users u ON d.user_id = u.id ORDER BY d.last_activity DESC");
$discussions = $stmt->fetchAll();

$header_title = "Conversații Live";
require_once 'includes/admin_header.php';
?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($discussions as $d): ?>
                <div class="admin-card p-6 shadow-lg hover:border-blue-500/50 transition border border-transparent">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center font-bold text-white">
                                <?php echo strtoupper(substr($d['username'] ?? 'V', 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="font-bold <?php echo $admin_theme === 'neon' ? 'text-white' : 'text-gray-800'; ?>">
                                    <?php echo htmlspecialchars($d['username'] ?? 'Vizitator'); ?>
                                </h4>
                                <p class="text-[10px] opacity-50 uppercase font-extrabold"><?php echo $d['last_activity']; ?></p>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm opacity-70 line-clamp-2 mb-6 italic">"<?php echo htmlspecialchars($d['last_msg'] ?? 'Niciun mesaj încă.'); ?>"</p>
                    <a href="chat_view.php?id=<?php echo $d['id']; ?>" class="block text-center bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                        Deschide Chat
                    </a>
                </div>
            <?php endforeach; ?>

            <?php if (empty($discussions)): ?>
                <div class="col-span-full admin-card p-20 text-center opacity-40 italic">Nicio conversație activă.</div>
            <?php endif; ?>
        </div>

    <script>
        async function refreshDiscussions() {
            // Simply reload parts or the whole list if we want it truly live
            // For now, let's just reload the page if there's a new unread message detected via another hidden API?
            // Or just poll this page's data.
            // For simplicity in this vanilla project, we'll just poll the entire content area every 10s
        }
        // setInterval(() => location.reload(), 15000);
    </script>

<?php require_once 'includes/admin_footer.php'; ?>
