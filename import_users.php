<?php
require_once __DIR__ . '/includes/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $handle = fopen($file['tmp_name'], 'r');

        // Skip BOM
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Skip header
        fgetcsv($handle, 0, ",", "\"", "");

        $imported = 0;
        $pdo->beginTransaction();

        try {
            while (($data = fgetcsv($handle, 0, ",", "\"", "")) !== FALSE) {
                // Mapping: 0:ID, 1:Username, 2:Role, 3:Created At
                if (count($data) < 3) continue;

                $username = $data[1];
                $role = $data[2];
                // Default password for imported users
                $password = password_hash("hotel1234", PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT OR IGNORE INTO users (username, password, role, created_at) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $username,
                    $password,
                    $role,
                    $data[3] ?? gmdate('Y-m-d H:i:s')
                ]);
                $imported++;
            }
            $pdo->commit();
            $message = "S-au importat $imported utilizatori cu succes! Parola implicită este 'hotel1234'.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Eroare la import: " . $e->getMessage();
        }
        fclose($handle);
    } else {
        $error = "Eroare la încărcarea fișierului.";
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="users.php" class="text-gray-400 hover:text-white transition flex items-center gap-2 mb-4">
            <i class="fas fa-arrow-left text-xs"></i> Înapoi la Utilizatori
        </a>
        <h2 class="text-3xl font-bold">Import Utilizatori CSV</h2>
        <p class="text-gray-400">Încarcă un fișier CSV de utilizatori. Parola va fi setată la 'hotel1234'.</p>
    </div>

    <?php if ($message): ?>
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-xl mb-6 flex items-center gap-3">
            <i class="fas fa-check-circle"></i> <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl mb-6 flex items-center gap-3">
            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="glass p-8 rounded-3xl border border-white/10 shadow-2xl">
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-300">Selectează Fișierul CSV</label>
                <div class="relative group">
                    <input type="file" name="csv_file" accept=".csv" required class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                <i class="fas fa-file-upload"></i> Pornește Importul
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
