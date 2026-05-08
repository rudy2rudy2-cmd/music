<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room = $_POST['room_number'];
    $type = $_POST['issue_type'];
    $desc = $_POST['description'];
    $prio = $_POST['priority'];
    $user_id = $_SESSION['user_id'];

    if (empty($room) || empty($type)) {
        $error = "Toate câmpurile obligatorii trebuie completate.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO defects (room_number, issue_type, description, priority, reported_by) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$room, $type, $desc, $prio, $user_id])) {
            $success = "Defecțiune raportată cu succes!";
            header("refresh:2;url=index.php");
        } else {
            $error = "Eroare la salvarea în baza de date.";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold">Raport Nou</h2>
        <p class="text-gray-400">Adaugă o defecțiune identificată în hotel</p>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-xl mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-500/10 border border-green-500/50 text-green-400 p-4 rounded-xl mb-6">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="glass p-8 rounded-2xl space-y-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Număr Cameră *</label>
                <input type="text" name="room_number" required placeholder="Ex: 101" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Tip Problemă *</label>
                <select name="issue_type" required class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition appearance-none">
                    <option value="" class="bg-slate-900">Selectează...</option>
                    <option value="Electricitate" class="bg-slate-900">Electricitate</option>
                    <option value="Sanitare" class="bg-slate-900">Sanitare (Apă/Scurgere)</option>
                    <option value="AC / Ventilație" class="bg-slate-900">AC / Ventilație</option>
                    <option value="Mobilier" class="bg-slate-900">Mobilier</option>
                    <option value="Electronică / TV" class="bg-slate-900">Electronică / TV</option>
                    <option value="Curățenie" class="bg-slate-900">Curățenie</option>
                    <option value="Altele" class="bg-slate-900">Altele</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">Prioritate</label>
            <div class="flex gap-4">
                <?php foreach (['Mică', 'Medie', 'Mare', 'Urgentă'] as $p): ?>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="priority" value="<?php echo $p; ?>" <?php echo $p == 'Medie' ? 'checked' : ''; ?> class="peer hidden">
                        <div class="text-center py-2 border border-white/10 rounded-xl peer-checked:bg-blue-600 peer-checked:border-blue-600 transition text-sm">
                            <?php echo $p; ?>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">Descriere Detaliată</label>
            <textarea name="description" rows="4" placeholder="Descrieți problema pe scurt..." class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition"></textarea>
        </div>

        <div class="flex gap-4 pt-4">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-blue-600/20">
                Salvează Raportul
            </button>
            <a href="index.php" class="flex-1 bg-white/5 hover:bg-white/10 text-center font-bold py-3 rounded-xl transition border border-white/10">
                Anulează
            </a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
