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

        // Skip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Skip header row
        $headers = fgetcsv($handle, 0, ",", "\"", "");

        $imported = 0;
        $pdo->beginTransaction();

        try {
            while (($data = fgetcsv($handle, 0, ",", "\"", "")) !== FALSE) {
                // Mapping (based on export structure)
                // 0:ID, 1:Cameră, 2:Tip Problemă, 3:Descriere, 4:Status, 5:Prioritate, 6:Raportat De, 7:Data Raportării, 8:Data Rezolvării, 9:Subtask-uri (JSON)
                if (count($data) < 7) continue;

                $stmt = $pdo->prepare("INSERT INTO defects (room_number, issue_type, description, status, priority, reported_by, reported_at, resolved_at, resolved_subtasks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->execute([
                    $data[1], // camera
                    $data[2], // issue_type
                    $data[3], // description
                    $data[4], // status
                    $data[5], // priority
                    $_SESSION['user_id'], // current user as reporter for simplicity in import
                    $data[7] ?: gmdate('Y-m-d H:i:s'),
                    $data[8] ?: null,
                    $data[9] ?: '[]'
                ]);
                $imported++;
            }
            $pdo->commit();
            $message = "S-au importat $imported înregistrări cu succes!";
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
        <a href="index.php" class="text-gray-400 hover:text-white transition flex items-center gap-2 mb-4">
            <i class="fas fa-arrow-left text-xs"></i> Înapoi la Dashboard
        </a>
        <h2 class="text-3xl font-bold">Import Rapoarte CSV</h2>
        <p class="text-gray-400">Încarcă un fișier CSV exportat anterior pentru a popula baza de date.</p>
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
                <p class="text-[10px] text-gray-500 italic">Asigură-te că fișierul respectă formatul exportat de sistem.</p>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                <i class="fas fa-file-upload"></i> Pornește Importul
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
