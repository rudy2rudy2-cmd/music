<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Eroare de securitate (CSRF).";
    } else {
        if ($_POST['action'] == 'add_channel') {
            $name = $_POST['channel_name'] ?? '';
            if ($name) {
                $stmt = $pdo->prepare("INSERT INTO channels (name) VALUES (?)");
                $stmt->execute([$name]);
                $message = "Canal adăugat cu succes.";
            }
        } elseif ($_POST['action'] == 'delete_channel') {
            $channel_id = $_POST['channel_id'] ?? 0;
            if ($channel_id) {
                // Delete media files associated with the channel
                $stmt = $pdo->prepare("SELECT file_path FROM media WHERE channel_id = ?");
                $stmt->execute([$channel_id]);
                $medias = $stmt->fetchAll();
                foreach ($medias as $m) {
                    if (file_exists($m['file_path'])) {
                        unlink($m['file_path']);
                    }
                }
                // Delete logo if exists
                $stmt = $pdo->prepare("SELECT logo_path FROM channels WHERE id = ?");
                $stmt->execute([$channel_id]);
                $chan = $stmt->fetch();
                if ($chan && $chan['logo_path'] && file_exists($chan['logo_path'])) {
                    unlink($chan['logo_path']);
                }

                $stmt = $pdo->prepare("DELETE FROM channels WHERE id = ?");
                $stmt->execute([$channel_id]);
                $message = "Canal șters.";
            }
        } elseif ($_POST['action'] == 'update_channel') {
            $channel_id = $_POST['channel_id'] ?? 0;
            $channel_name = $_POST['channel_name'] ?? '';
            $ticker_text = $_POST['ticker_text'] ?? '';

            if ($channel_id) {
                // Get current channel data to handle old logo
                $stmt = $pdo->prepare("SELECT logo_path FROM channels WHERE id = ?");
                $stmt->execute([$channel_id]);
                $chan = $stmt->fetch();
                $logo_path = $chan['logo_path'] ?? null;

                if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
                    $file_ext = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
                    if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        // Delete old logo file if it exists and is different
                        if ($logo_path && file_exists($logo_path)) {
                            unlink($logo_path);
                        }

                        if (!is_dir('uploads')) {
                            mkdir('uploads', 0755, true);
                        }

                        $new_logo = 'uploads/logo_' . $channel_id . '_' . uniqid() . '.' . $file_ext;
                        if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $new_logo)) {
                            $logo_path = $new_logo;
                        }
                    }
                }

                $stmt = $pdo->prepare("UPDATE channels SET name = ?, ticker_text = ?, logo_path = ? WHERE id = ?");
                $stmt->execute([$channel_name, $ticker_text, $logo_path, $channel_id]);
                $message = "Canal actualizat cu succes.";
            }
        } elseif ($_POST['action'] == 'upload_media') {
            $channel_id = $_POST['channel_id'] ?? 0;
            $duration = $_POST['duration'] ?? 10;
            if ($channel_id && isset($_FILES['media_file'])) {
                $file = $_FILES['media_file'];
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $error = "Eroare la încărcare: " . $file['error'];
                } else {
                    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg'];

                    if (in_array($file_ext, $allowed_exts)) {
                        $type = in_array($file_ext, ['mp4', 'webm', 'ogg']) ? 'video' : 'image';
                        $new_filename = uniqid() . '.' . $file_ext;
                        $upload_path = 'uploads/' . $new_filename;

                        if (!is_dir('uploads')) {
                            mkdir('uploads', 0755, true);
                        }

                        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                            $stmt = $pdo->prepare("INSERT INTO media (channel_id, type, file_path, duration) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$channel_id, $type, $upload_path, $duration]);
                            $message = "Media încărcată cu succes.";
                        } else {
                            $error = "Eroare la salvarea fișierului.";
                        }
                    } else {
                        $error = "Tip de fișier nepermis.";
                    }
                }
            }
        } elseif ($_POST['action'] == 'delete_media') {
            $media_id = $_POST['media_id'] ?? 0;
            if ($media_id) {
                $stmt = $pdo->prepare("SELECT file_path FROM media WHERE id = ?");
                $stmt->execute([$media_id]);
                $media = $stmt->fetch();
                if ($media) {
                    if (file_exists($media['file_path'])) {
                        unlink($media['file_path']);
                    }
                    $stmt = $pdo->prepare("DELETE FROM media WHERE id = ?");
                    $stmt->execute([$media_id]);
                    $message = "Media ștearsă.";
                }
            }
        } elseif ($_POST['action'] == 'reorder_media') {
            $order = $_POST['order'] ?? [];
            foreach ($order as $index => $media_id) {
                $stmt = $pdo->prepare("UPDATE media SET display_order = ? WHERE id = ?");
                $stmt->execute([$index, $media_id]);
            }
            $message = "Ordinea a fost salvată.";
        }
    }
}

$stmt = $pdo->query("SELECT * FROM channels");
$channels = $stmt->fetchAll();
$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="ro" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Viziere Digitale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .sidebar-item-active {
            background-color: #3b82f6;
            color: white;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        .sortable-ghost { opacity: 0.3; transform: scale(0.95); }
        .modal {
            transition: opacity 0.25s ease;
            display: none;
        }
        .modal-active {
            display: flex;
            opacity: 1;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col md:flex-row">
    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-white border-r border-slate-200 flex flex-col z-20">
        <div class="p-6 border-b border-slate-100 flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <i class="fas fa-tv"></i>
            </div>
            <div>
                <h1 class="text-sm font-bold text-slate-800">Viziere Digitale</h1>
                <p class="text-[10px] text-slate-400 font-medium">Control v2.0</p>
            </div>
        </div>

        <nav class="flex-grow p-4 space-y-2 overflow-y-auto">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mb-2 mt-4">Navigare</div>
            <a href="admin.php" class="sidebar-item-active flex items-center space-x-3 px-4 py-3 rounded-xl transition duration-200">
                <i class="fas fa-layer-group text-sm"></i>
                <span class="text-sm font-semibold">Canale Media</span>
            </a>
            <!-- Placeholder for other sections -->
            <a href="#" class="text-slate-500 hover:bg-slate-50 flex items-center space-x-3 px-4 py-3 rounded-xl transition duration-200">
                <i class="fas fa-chart-line text-sm"></i>
                <span class="text-sm font-semibold">Statistici</span>
            </a>
            <a href="#" class="text-slate-500 hover:bg-slate-50 flex items-center space-x-3 px-4 py-3 rounded-xl transition duration-200">
                <i class="fas fa-cog text-sm"></i>
                <span class="text-sm font-semibold">Setări Sistem</span>
            </a>
        </nav>

        <div class="p-4 border-t border-slate-100">
            <div class="flex items-center space-x-3 px-4 py-3 bg-slate-50 rounded-xl mb-4">
                <div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center text-slate-500">
                    <i class="fas fa-user-shield text-xs"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-slate-700 truncate"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></p>
                    <p class="text-[10px] text-slate-400">Administrator</p>
                </div>
            </div>
            <a href="logout.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 transition duration-200">
                <i class="fas fa-sign-out-alt text-sm"></i>
                <span class="text-sm font-bold">Deconectare</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col min-h-screen">
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-10">
            <h2 class="text-lg font-bold text-slate-800">Gestionare Canale</h2>
            <div class="flex items-center space-x-4">
                <span class="text-xs font-medium text-slate-400"><?php echo date('d M Y'); ?></span>
                <button onclick="document.getElementById('addChannelModal').classList.add('modal-active')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition duration-200 shadow-md shadow-blue-100 flex items-center">
                    <i class="fas fa-plus mr-2"></i> Adaugă Canal
                </button>
            </div>
        </header>

        <div class="p-8 space-y-8">
            <?php if ($message): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <span class="text-sm font-semibold"><?php echo $message; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-3"></i>
                        <span class="text-sm font-semibold"><?php echo $error; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Grid Canale -->
            <div class="grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-8 mb-12">
                <?php foreach ($channels as $channel): ?>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col transition hover:shadow-xl hover:shadow-slate-200/50 group">
                    <!-- Header Card -->
                    <div class="p-6 border-b border-slate-50 flex justify-between items-start">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shadow-sm relative overflow-hidden">
                                <?php if ($channel['logo_path']): ?>
                                    <img src="<?php echo htmlspecialchars($channel['logo_path']); ?>" class="w-full h-full object-contain p-2">
                                <?php else: ?>
                                    <i class="fas fa-broadcast-tower text-lg"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-lg group-hover:text-blue-600 transition duration-200"><?php echo htmlspecialchars($channel['name']); ?></h3>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Activ • ID: <?php echo $channel['id']; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="openPreview(<?php echo $channel['id']; ?>)" class="w-9 h-9 bg-slate-50 hover:bg-blue-50 text-slate-400 hover:text-blue-600 rounded-xl transition flex items-center justify-center border border-transparent hover:border-blue-100" title="Previzualizare">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                            <a href="player.php?channel=<?php echo $channel['id']; ?>" target="_blank" class="w-9 h-9 bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-700 rounded-xl transition flex items-center justify-center" title="Lansează Player">
                                <i class="fas fa-external-link-alt text-sm"></i>
                            </a>
                            <form method="POST" onsubmit="return confirm('Ești sigur că vrei să ștergi acest canal?');">
                                <input type="hidden" name="action" value="delete_channel">
                                <input type="hidden" name="channel_id" value="<?php echo $channel['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <button type="submit" class="w-9 h-9 bg-rose-50 hover:bg-rose-100 text-rose-400 hover:text-rose-600 rounded-xl transition flex items-center justify-center">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Config Panel -->
                    <div class="p-6 space-y-6">
                        <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100">
                            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                                <input type="hidden" name="action" value="update_channel">
                                <input type="hidden" name="channel_id" value="<?php echo $channel['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                <div class="flex items-center space-x-4">
                                    <div class="relative group/logo w-16 h-16 border-2 border-dashed border-slate-200 bg-white rounded-xl flex items-center justify-center overflow-hidden transition hover:border-blue-400">
                                        <?php if ($channel['logo_path']): ?>
                                            <img src="<?php echo htmlspecialchars($channel['logo_path']); ?>" class="w-full h-full object-contain p-2">
                                        <?php else: ?>
                                            <i class="fas fa-camera text-slate-300"></i>
                                        <?php endif; ?>
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/logo:opacity-100 transition-opacity flex items-center justify-center">
                                            <i class="fas fa-plus text-white text-[10px]"></i>
                                        </div>
                                        <input type="file" name="logo_file" class="absolute inset-0 opacity-0 cursor-pointer">
                                    </div>
                                    <div class="flex-grow space-y-2">
                                        <input type="text" name="channel_name" value="<?php echo htmlspecialchars($channel['name']); ?>" placeholder="Nume canal" class="w-full text-sm font-bold border-none bg-transparent focus:ring-0 p-0 text-slate-800" required>
                                        <input type="text" name="ticker_text" placeholder="Adaugă un mesaj de rulare..." value="<?php echo htmlspecialchars($channel['ticker_text'] ?? ''); ?>" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition bg-white outline-none">
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-white hover:bg-slate-50 text-slate-700 text-[10px] font-bold py-2 rounded-xl border border-slate-200 transition duration-200 uppercase tracking-widest">Salvează Setările</button>
                            </form>
                        </div>

                        <!-- Media section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Conținut Media</h4>
                                <button onclick="toggleMediaUpload(<?php echo $channel['id']; ?>)" class="text-blue-600 hover:text-blue-700 text-xs font-bold flex items-center">
                                    <i class="fas fa-plus-circle mr-1"></i> Adaugă
                                </button>
                            </div>

                            <!-- Upload media hidden form -->
                            <div id="media-upload-<?php echo $channel['id']; ?>" class="hidden mb-6 bg-slate-50 border border-slate-200 rounded-2xl p-4 animate-fadeIn">
                                <form method="POST" enctype="multipart/form-data" class="space-y-3">
                                    <input type="hidden" name="action" value="upload_media">
                                    <input type="hidden" name="channel_id" value="<?php echo $channel['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="file" name="media_file" class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700" required>
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-grow">
                                            <label class="text-[9px] font-bold text-slate-400 uppercase">Durată Afișare (sec)</label>
                                            <input type="number" name="duration" value="10" min="1" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-100 transition outline-none">
                                        </div>
                                        <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-lg shadow-slate-200 transition hover:bg-black mt-4">Încărcare</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Media sorting list -->
                            <form method="POST" id="reorder-form-<?php echo $channel['id']; ?>">
                                <input type="hidden" name="action" value="reorder_media">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <div class="grid grid-cols-4 gap-3 media-list" data-channel="<?php echo $channel['id']; ?>">
                                    <?php
                                    $stmt_media = $pdo->prepare("SELECT * FROM media WHERE channel_id = ? ORDER BY display_order ASC");
                                    $stmt_media->execute([$channel['id']]);
                                    $medias = $stmt_media->fetchAll();
                                    foreach ($medias as $media):
                                    ?>
                                    <div class="relative group/media cursor-grab active:cursor-grabbing rounded-xl overflow-hidden aspect-square shadow-sm" data-id="<?php echo $media['id']; ?>">
                                        <input type="hidden" name="order[]" value="<?php echo $media['id']; ?>">
                                        <?php if ($media['type'] == 'image'): ?>
                                            <img src="<?php echo htmlspecialchars($media['file_path']); ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                                                <i class="fas fa-video"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover/media:opacity-100 transition-opacity duration-200 flex flex-col items-center justify-center space-y-2">
                                            <button type="button" onclick="deleteMedia(<?php echo $media['id']; ?>)" class="w-7 h-7 bg-rose-500 text-white rounded-lg flex items-center justify-center hover:bg-rose-600 shadow-md">
                                                <i class="fas fa-trash text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php if (empty($medias)): ?>
                                        <div class="col-span-4 py-8 border-2 border-dashed border-slate-100 rounded-2xl flex flex-col items-center justify-center text-slate-300">
                                            <i class="fas fa-photo-film text-2xl mb-2"></i>
                                            <p class="text-[10px] font-bold uppercase tracking-wider">Gol</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($medias)): ?>
                                    <button type="submit" class="mt-4 w-full text-[9px] font-bold text-slate-400 hover:text-blue-600 hover:bg-blue-50 py-2 rounded-xl transition duration-200 border border-transparent hover:border-blue-100 uppercase tracking-widest">Salvează Ordinea Nouă</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-auto py-6 px-12 bg-white border-t border-slate-100 text-slate-400 flex justify-between items-center">
            <p class="text-[10px] font-bold uppercase tracking-widest">Sistem Viziere Digitale © 2025</p>
            <div class="flex space-x-6 text-[10px] font-bold uppercase tracking-widest">
                <a href="#" class="hover:text-blue-600 transition">Documentație</a>
                <a href="#" class="hover:text-blue-600 transition">Suport</a>
            </div>
        </footer>
    </main>

    <!-- Modal Adăugare Canal -->
    <div id="addChannelModal" class="modal fixed inset-0 z-50 items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-100">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-800">Creează Canal Nou</h3>
                <button onclick="document.getElementById('addChannelModal').classList.remove('modal-active')" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" class="p-8 space-y-6">
                <input type="hidden" name="action" value="add_channel">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase block mb-3">Numele Canalului</label>
                    <input class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-slate-800 font-medium" type="text" name="channel_name" placeholder="Ex: Scara Bloc C1" required autofocus>
                </div>
                <div class="flex space-x-4">
                    <button type="button" onclick="document.getElementById('addChannelModal').classList.remove('modal-active')" class="flex-grow py-4 text-slate-500 font-bold hover:bg-slate-50 rounded-2xl transition duration-200">Anulează</button>
                    <button class="flex-grow bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition duration-200 shadow-lg shadow-blue-100" type="submit">Creează</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Previzualizare -->
    <div id="previewModal" class="modal fixed inset-0 z-50 items-center justify-center bg-slate-900/90 backdrop-blur-sm p-4">
        <div class="bg-black rounded-3xl w-full max-w-4xl aspect-video shadow-2xl overflow-hidden border border-white/10 relative">
            <div class="absolute top-4 right-4 z-50">
                <button onclick="closePreview()" class="w-10 h-10 bg-white/20 hover:bg-white/40 text-white rounded-full flex items-center justify-center backdrop-blur-md transition"><i class="fas fa-times"></i></button>
            </div>
            <iframe id="previewIframe" src="" class="w-full h-full border-none"></iframe>
        </div>
    </div>

    <!-- Hidden form for deleting media -->
    <form id="delete-media-form" method="POST" style="display:none;">
        <input type="hidden" name="action" value="delete_media">
        <input type="hidden" name="media_id" id="delete-media-id">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    </form>

    <script>
        document.querySelectorAll('.media-list').forEach(el => {
            new Sortable(el, {
                animation: 250,
                ghostClass: 'sortable-ghost',
                forceFallback: true
            });
        });

        function deleteMedia(id) {
            if (confirm('Ești sigur că vrei să ștergi această media?')) {
                document.getElementById('delete-media-id').value = id;
                document.getElementById('delete-media-form').submit();
            }
        }

        function toggleMediaUpload(id) {
            const el = document.getElementById('media-upload-' + id);
            el.classList.toggle('hidden');
        }

        function openPreview(id) {
            const modal = document.getElementById('previewModal');
            const iframe = document.getElementById('previewIframe');
            iframe.src = 'player.php?channel=' + id + '&preview=1';
            modal.classList.add('modal-active');
        }

        function closePreview() {
            const modal = document.getElementById('previewModal');
            const iframe = document.getElementById('previewIframe');
            iframe.src = '';
            modal.classList.remove('modal-active');
        }

        // Modal closing when clicking background
        window.onclick = function(event) {
            const addModal = document.getElementById('addChannelModal');
            const previewModal = document.getElementById('previewModal');
            if (event.target == addModal) {
                addModal.classList.remove('modal-active');
            }
            if (event.target == previewModal) {
                closePreview();
            }
        }
    </script>
</body>
</html>
