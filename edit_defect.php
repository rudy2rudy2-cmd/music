<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM defects WHERE id = ?");
$stmt->execute([$id]);
$defect = $stmt->fetch();

if (!$defect) {
    die("Defecțiune negăsită.");
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room = $_POST['room_number'];
    $type = $_POST['issue_type'];
    $desc = $_POST['description'];
    $prio = $_POST['priority'];
    $status = $_POST['status'];

    $resolved_at = ($status == 'rezolvat') ? ($defect['resolved_at'] ?? date('Y-m-d H:i:s')) : null;

    $stmt = $pdo->prepare("UPDATE defects SET room_number = ?, issue_type = ?, description = ?, priority = ?, status = ?, resolved_at = ? WHERE id = ?");
    if ($stmt->execute([$room, $type, $desc, $prio, $status, $resolved_at, $id])) {
        $success = "Modificări salvate!";
        header("refresh:1;url=index.php");
    } else {
        $error = "Eroare la actualizare.";
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-bold">Editează Defecțiunea</h2>
        <p class="text-gray-400">ID: #<?php echo $id; ?></p>
    </div>

    <form method="POST" class="glass p-8 rounded-2xl space-y-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Număr Cameră</label>
                <input type="text" name="room_number" value="<?php echo htmlspecialchars($defect['room_number']); ?>" required class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Status</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition appearance-none">
                    <option value="activ" <?php echo $defect['status'] == 'activ' ? 'selected' : ''; ?> class="bg-slate-900">🔴 Activ</option>
                    <option value="rezolvat" <?php echo $defect['status'] == 'rezolvat' ? 'selected' : ''; ?> class="bg-slate-900">🟢 Rezolvat</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">Tip Problemă</label>
            <select name="issue_type" required class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition appearance-none">
                <?php
                $types = ['Electricitate', 'Sanitare', 'AC / Ventilație', 'Mobilier', 'Electronică / TV', 'Curățenie', 'Altele'];
                foreach($types as $t): ?>
                    <option value="<?php echo $t; ?>" <?php echo $defect['issue_type'] == $t ? 'selected' : ''; ?> class="bg-slate-900"><?php echo $t; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">Prioritate</label>
            <div class="flex gap-4">
                <?php foreach (['Mică', 'Medie', 'Mare', 'Urgentă'] as $p): ?>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="priority" value="<?php echo $p; ?>" <?php echo $defect['priority'] == $p ? 'checked' : ''; ?> class="peer hidden">
                        <div class="text-center py-2 border border-white/10 rounded-xl peer-checked:bg-blue-600 peer-checked:border-blue-600 transition text-sm">
                            <?php echo $p; ?>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">Descriere Detaliată</label>
            <textarea name="description" rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition"><?php echo htmlspecialchars($defect['description']); ?></textarea>
        </div>

        <div class="flex gap-4 pt-4">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition">
                Actualizează
            </button>
            <a href="index.php" class="flex-1 bg-white/5 hover:bg-white/10 text-center font-bold py-3 rounded-xl transition border border-white/10">
                Anulează
            </a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
