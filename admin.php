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
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Viziere Digitale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; display: flex; flex-direction: column; min-height: 100vh; }
        .sortable-ghost { opacity: 0.4; }
        main { flex: 1; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <h1 class="text-xl font-bold flex items-center">
                    <i class="fas fa-tv mr-2"></i> Viziere Digitale
                </h1>
                <span class="ml-6 text-xs opacity-75 hidden md:inline">Panou Control v2.0</span>
            </div>
            <div class="flex items-center space-x-6">
                <span class="text-xs italic hidden lg:inline">© 2025 Viziere Digitale. Toate drepturile rezervate.</span>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-semibold border-r pr-3 border-blue-400">Salut, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-sm transition duration-300 shadow-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-6">
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 shadow-sm" role="alert">
                <span class="block sm:inline"><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 shadow-sm" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <!-- Add Channel -->
        <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
            <h2 class="text-lg font-semibold mb-4">Adaugă Canal Nou</h2>
            <form method="POST" class="flex gap-4">
                <input type="hidden" name="action" value="add_channel">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input class="flex-grow shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="channel_name" placeholder="Ex: Canal 1 Scara A" required>
                <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded transition duration-300 shadow-sm" type="submit">
                    <i class="fas fa-plus mr-2"></i>Adaugă
                </button>
            </form>
        </div>

        <!-- Channels List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <?php foreach ($channels as $channel): ?>
            <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-blue-500 flex flex-col">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($channel['name']); ?></h3>
                    <div class="flex space-x-2">
                        <a href="player.php?channel=<?php echo $channel['id']; ?>" target="_blank" class="text-blue-500 hover:text-blue-700" title="Vezi Player">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <form method="POST" onsubmit="return confirm('Ești sigur că vrei să ștergi acest canal?');">
                            <input type="hidden" name="action" value="delete_channel">
                            <input type="hidden" name="channel_id" value="<?php echo $channel['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Config Channel -->
                <div class="mb-4 bg-blue-50 p-4 rounded-xl border border-blue-100 shadow-inner">
                    <form method="POST" enctype="multipart/form-data" class="flex flex-col space-y-4">
                        <input type="hidden" name="action" value="update_channel">
                        <input type="hidden" name="channel_id" value="<?php echo $channel['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                        <div class="flex space-x-4 items-start">
                            <div class="flex-shrink-0">
                                <label class="text-[9px] text-gray-500 block mb-1 uppercase font-bold">Logo Canal</label>
                                <div class="w-16 h-16 border-2 border-dashed border-blue-200 bg-white rounded-lg flex items-center justify-center overflow-hidden relative group shadow-inner">
                                    <?php if ($channel['logo_path']): ?>
                                        <img src="<?php echo htmlspecialchars($channel['logo_path']); ?>" class="w-full h-full object-contain p-1">
                                    <?php else: ?>
                                        <i class="fas fa-camera text-gray-300 text-xl"></i>
                                    <?php endif; ?>
                                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                        <i class="fas fa-upload text-white text-xs"></i>
                                    </div>
                                    <input type="file" name="logo_file" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                            </div>
                            <div class="flex-grow flex flex-col space-y-3">
                                <div>
                                    <label class="text-[9px] text-gray-500 block mb-1 uppercase font-bold">Redenumește Canal</label>
                                    <input type="text" name="channel_name" value="<?php echo htmlspecialchars($channel['name']); ?>" placeholder="Nume canal" class="w-full text-sm border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400 font-semibold text-gray-700 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="text-[9px] text-gray-500 block mb-1 uppercase font-bold">Text Live (Ticker)</label>
                                    <input type="text" name="ticker_text" placeholder="Mesaj scrollant..." value="<?php echo htmlspecialchars($channel['ticker_text'] ?? ''); ?>" class="w-full text-xs border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold py-2 rounded transition duration-300 uppercase shadow-md">Salvează Modificările</button>
                    </form>
                </div>

                <!-- Media Upload for Channel -->
                <div class="mb-4">
                    <form method="POST" enctype="multipart/form-data" class="flex flex-col space-y-2 border p-3 rounded bg-gray-50">
                        <input type="hidden" name="action" value="upload_media">
                        <input type="hidden" name="channel_id" value="<?php echo $channel['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <label class="block text-sm font-medium text-gray-700">Adaugă Media</label>
                        <input type="file" name="media_file" class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                        <div class="flex items-center space-x-2">
                            <label class="text-xs text-gray-500">Durată (s):</label>
                            <input type="number" name="duration" value="10" min="1" class="w-16 border rounded text-xs p-1">
                        </div>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold py-2 rounded transition duration-300 shadow-sm">Încarcă Media</button>
                    </form>
                </div>

                <!-- Media List (Sortable) -->
                <div class="mt-auto">
                    <div class="flex justify-between items-center mb-2 border-b pb-1">
                        <h4 class="text-sm font-semibold text-gray-600">Media (reordonează):</h4>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="reorder_media">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <div class="grid grid-cols-3 gap-2 media-list" data-channel="<?php echo $channel['id']; ?>">
                            <?php
                            $stmt_media = $pdo->prepare("SELECT * FROM media WHERE channel_id = ? ORDER BY display_order ASC");
                            $stmt_media->execute([$channel['id']]);
                            $medias = $stmt_media->fetchAll();
                            foreach ($medias as $media):
                            ?>
                            <div class="relative group cursor-move" data-id="<?php echo $media['id']; ?>">
                                <input type="hidden" name="order[]" value="<?php echo $media['id']; ?>">
                                <?php if ($media['type'] == 'image'): ?>
                                    <img src="<?php echo htmlspecialchars($media['file_path']); ?>" class="w-full h-16 object-cover rounded shadow-sm">
                                <?php else: ?>
                                    <div class="w-full h-16 bg-gray-200 flex items-center justify-center rounded shadow-sm">
                                        <i class="fas fa-video text-gray-400"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center space-x-2 rounded">
                                    <button type="button" onclick="deleteMedia(<?php echo $media['id']; ?>)" class="text-white bg-red-500 p-1 rounded-full hover:bg-red-600">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($medias)): ?>
                                <p class="text-xs text-gray-400 italic col-span-3">Nicio media incarcata.</p>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($medias)): ?>
                            <button type="submit" class="mt-3 w-full text-xs bg-gray-200 hover:bg-gray-300 py-1 rounded text-gray-700">Salvează ordinea</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="bg-white border-t p-4 mt-auto">
        <div class="container mx-auto text-right text-gray-400 text-[10px] uppercase font-bold tracking-widest">
            &copy; 2025 Viziere Digitale. Toate drepturile rezervate.
        </div>
    </footer>

    <!-- Hidden form for deleting media -->
    <form id="delete-media-form" method="POST" style="display:none;">
        <input type="hidden" name="action" value="delete_media">
        <input type="hidden" name="media_id" id="delete-media-id">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    </form>

    <script>
        document.querySelectorAll('.media-list').forEach(el => {
            new Sortable(el, {
                animation: 150,
                ghostClass: 'sortable-ghost'
            });
        });

        function deleteMedia(id) {
            if (confirm('Ești sigur că vrei să ștergi această media?')) {
                document.getElementById('delete-media-id').value = id;
                document.getElementById('delete-media-form').submit();
            }
        }
    </script>
</body>
</html>
