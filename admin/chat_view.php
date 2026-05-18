<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

// Fetch discussion and user info
$stmt = $pdo->prepare("SELECT d.*, u.username, u.first_name, u.last_name FROM chat_discussions d LEFT JOIN users u ON d.user_id = u.id WHERE d.id = ?");
$stmt->execute([$id]);
$discussion = $stmt->fetch();

if (!$discussion) {
    header("Location: index.php");
    exit();
}

$header_title = "Chat Live: " . ($discussion['username'] ?? 'Vizitator');
require_once 'includes/admin_header.php';
?>

        <div class="max-w-4xl mx-auto">
            <div class="admin-card overflow-hidden flex flex-col h-[600px] shadow-2xl">
                <!-- Chat Header -->
                <div class="p-6 border-b border-gray-700/20 flex justify-between items-center bg-gray-800/50">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center font-bold text-white">
                            <?php echo strtoupper(substr($discussion['username'] ?? 'V', 0, 1)); ?>
                        </div>
                        <div>
                            <h4 class="font-bold <?php echo $admin_theme === 'neon' ? 'text-white' : 'text-gray-800'; ?>">
                                <?php echo htmlspecialchars($discussion['first_name'] . ' ' . $discussion['last_name'] ?: ($discussion['username'] ?? 'Vizitator')); ?>
                            </h4>
                            <p class="text-xs text-green-500 font-bold">Activ acum</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div id="admin-chat-body" class="flex-1 p-8 overflow-y-auto bg-gray-900/10 flex flex-col space-y-6">
                    <!-- Loaded via JS -->
                </div>

                <!-- Chat Input -->
                <div class="p-6 border-t border-gray-700/20 bg-gray-800/30">
                    <form id="admin-chat-form" class="flex space-x-4">
                        <input type="text" id="admin-chat-input" placeholder="Scrie un răspuns..." required class="flex-1 p-4 rounded-2xl modern-input <?php echo $admin_theme !== 'neon' ? 'border border-gray-200' : ''; ?>">
                        <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg">
                            <i class="fas fa-paper-plane mr-2"></i> Trimite
                        </button>
                    </form>
                </div>
            </div>
        </div>

    <script>
        const chatBody = document.getElementById('admin-chat-body');
        const chatForm = document.getElementById('admin-chat-form');
        const chatInput = document.getElementById('admin-chat-input');
        const discussionId = <?php echo $id; ?>;

        async function fetchMessages() {
            try {
                const response = await fetch(`/api/chat_v2.php?action=fetch&admin_discussion_id=${discussionId}`);
                if (!response.ok) return;
                const messages = await response.json();
                chatBody.innerHTML = '';
                messages.forEach(msg => {
                    const isMe = msg.sender === 'admin';
                    const div = document.createElement('div');
                    div.className = `max-w-[70%] p-4 rounded-2xl shadow-sm \${isMe ? 'bg-blue-600 text-white self-end rounded-br-none' : 'bg-white text-gray-800 self-start rounded-bl-none border border-gray-100'}`;
                    div.innerHTML = `<p class="text-sm">\${msg.message}</p><span class="text-[10px] opacity-50 block mt-2">\${msg.created_at}</span>`;
                    chatBody.appendChild(div);
                });
                chatBody.scrollTop = chatBody.scrollHeight;
            } catch (e) {}
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const msg = chatInput.value.trim();
            if(!msg) return;

            const formData = new FormData();
            formData.append('message', msg);
            formData.append('admin_discussion_id', discussionId);

            const btn = chatForm.querySelector('button');
            btn.disabled = true;

            try {
                const response = await fetch('/api/chat_v2.php?action=send', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if(result.status === 'success') {
                    chatInput.value = '';
                    await fetchMessages();
                }
            } catch (e) {
                console.error(e);
            } finally {
                btn.disabled = false;
            }
        });

        setInterval(fetchMessages, 3000);
        fetchMessages();
    </script>

<?php require_once 'includes/admin_footer.php'; ?>
