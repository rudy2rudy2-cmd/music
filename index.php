<?php
require_once __DIR__ . '/includes/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Global settings fetch
$stmt_settings = $pdo->query("SELECT * FROM settings");
$site_settings_idx = [];
while ($row = $stmt_settings->fetch()) {
    $site_settings_idx[$row['setting_key']] = $row['setting_value'];
}

$default_filter_idx = $site_settings_idx['default_filter'] ?? 'all';
$total_rooms_idx = $site_settings_idx['total_rooms'] ?? '100';

// Statistics update logic - Optimized for subtasks
$active_defects = $pdo->query("SELECT COUNT(*) FROM defects WHERE status = 'activ'")->fetchColumn();
$resolved_defects = $pdo->query("SELECT COUNT(*) FROM defects WHERE status = 'rezolvat' OR (resolved_subtasks != '' AND resolved_subtasks != '[]' AND resolved_subtasks IS NOT NULL)")->fetchColumn();

// Filters
$filter = $_GET['filter'] ?? $default_filter_idx;
$search = $_GET['search'] ?? '';

$query = "SELECT d.*, u.username as reported_by_user FROM defects d LEFT JOIN users u ON d.reported_by = u.id WHERE 1=1";
$params = [];

if ($filter === 'active') {
    // In active view, we show things that are NOT fully resolved
    $query .= " AND d.status = 'activ'";
} elseif ($filter === 'resolved') {
    // In resolved view, we show things that have AT LEAST one subtask resolved OR are fully resolved
    $query .= " AND (d.status = 'rezolvat' OR (d.resolved_subtasks != '' AND d.resolved_subtasks != '[]' AND d.resolved_subtasks IS NOT NULL))";
}

if (!empty($search)) {
    $query .= " AND (room_number LIKE ? OR issue_type LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY CASE WHEN d.status = 'activ' THEN 0 ELSE 1 END, reported_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$defects = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h2 class="text-3xl font-bold">Dashboard</h2>
        <div class="flex items-center gap-2 text-gray-400">
            <i class="far fa-calendar-alt"></i>
            <span id="live-clock"><?php echo date('d.m.Y H:i:s'); ?></span>
        </div>
    </div>
    <div class="flex flex-wrap gap-4">
        <div class="glass p-4 rounded-2xl flex items-center gap-4 min-w-[150px]">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-500 flex items-center justify-center">
                <i class="fas fa-door-open"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Total</p>
                <p class="text-xl font-bold"><?php echo $total_rooms_idx; ?></p>
            </div>
        </div>
        <div class="glass p-4 rounded-2xl flex items-center gap-4 min-w-[150px]">
            <div class="w-10 h-10 rounded-xl bg-red-500/20 text-red-500 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Active</p>
                <p class="text-xl font-bold"><?php echo $active_defects; ?></p>
            </div>
        </div>
        <div class="glass p-4 rounded-2xl flex items-center gap-4 min-w-[150px]">
            <div class="w-10 h-10 rounded-xl bg-green-500/20 text-green-500 flex items-center justify-center">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Rezolvate</p>
                <p class="text-xl font-bold"><?php echo $resolved_defects; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="glass rounded-2xl overflow-hidden shadow-xl border border-white/10">
    <div class="p-6 border-b border-white/5 flex flex-col lg:flex-row justify-between items-center gap-6">
        <div class="flex bg-white/5 p-1 rounded-xl">
            <a href="?filter=all" class="px-6 py-2 rounded-lg text-sm font-bold transition <?php echo $filter == 'all' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white'; ?>">Toate</a>
            <a href="?filter=active" class="px-6 py-2 rounded-lg text-sm font-bold transition <?php echo $filter == 'active' ? 'bg-red-600 text-white shadow-lg' : 'text-gray-400 hover:text-white'; ?>">Active</a>
            <a href="?filter=resolved" class="px-6 py-2 rounded-lg text-sm font-bold transition <?php echo $filter == 'resolved' ? 'bg-green-600 text-white shadow-lg' : 'text-gray-400 hover:text-white'; ?>">Rezolvate</a>
        </div>

        <div class="flex-1 w-full lg:max-w-md">
            <form class="relative group">
                <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500 group-focus-within:text-blue-500 transition">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Căutare după cameră, problemă..." class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-12 pr-4 focus:outline-none focus:border-blue-500 focus:bg-white/10 transition">
            </form>
        </div>

        <a href="add_defect.php" class="w-full lg:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-lg shadow-blue-600/20">
            <i class="fas fa-plus-circle"></i> Adaugă Raport
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="defects-table">
            <thead>
                <tr class="bg-white/[0.02] text-gray-500 text-[10px] uppercase font-bold tracking-widest border-b border-white/5">
                    <th class="px-6 py-4 cursor-pointer hover:text-white transition" onclick="sortTable(0)">Cameră <i class="fas fa-sort ml-1 opacity-30"></i></th>
                    <th class="px-6 py-4 cursor-pointer hover:text-white transition" onclick="sortTable(1)">Defecțiune <i class="fas fa-sort ml-1 opacity-30"></i></th>
                    <th class="px-6 py-4 cursor-pointer hover:text-white transition" onclick="sortTable(2)">Prioritate <i class="fas fa-sort ml-1 opacity-30"></i></th>
                    <th class="px-6 py-4 cursor-pointer hover:text-white transition" onclick="sortTable(3)">Status <i class="fas fa-sort ml-1 opacity-30"></i></th>
                    <th class="px-6 py-4">Raportat de</th>
                    <th class="px-6 py-4 cursor-pointer hover:text-white transition" onclick="sortTable(5)">Data <i class="fas fa-sort ml-1 opacity-30"></i></th>
                    <th class="px-6 py-4 text-right">Acțiuni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5" style="font-size: var(--report-font-size);">
                <?php if (empty($defects)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-600 mb-2"><i class="fas fa-folder-open text-4xl"></i></div>
                            <p class="text-gray-500">Nu am găsit înregistrări care să corespundă criteriilor.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($defects as $defect): ?>
                        <?php
                            $description = (string)$defect['description'];
                            $subtasks = array_filter(array_map('trim', explode('.', $description)), 'strlen');
                            $resolved_data = json_decode((string)$defect['resolved_subtasks'], true) ?: [];

                            // Filtering subtasks based on view
                            $visible_subtasks = $subtasks;
                            if ($filter === 'active') {
                                $visible_subtasks = array_filter($subtasks, function($k) use ($resolved_data) { return !isset($resolved_data[$k]); }, ARRAY_FILTER_USE_KEY);
                            } elseif ($filter === 'resolved') {
                                $visible_subtasks = array_filter($subtasks, function($k) use ($resolved_data) { return isset($resolved_data[$k]); }, ARRAY_FILTER_USE_KEY);
                            }

                            // If we have a filter active and no subtasks match, skip only if there was a description
                            // If description was empty, it should probably only show in 'active' filter
                            if (!empty($description) && empty($visible_subtasks)) continue;
                            if (empty($description) && $filter === 'resolved') continue;
                        ?>
                        <tr class="hover:bg-white/[0.03] transition group" id="defect-row-<?php echo $defect['id']; ?>">
                            <td class="px-6 py-5">
                                <span class="text-lg font-black text-blue-500">#<?php echo htmlspecialchars($defect['room_number']); ?></span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="font-semibold defect-desc"><?php echo htmlspecialchars($defect['issue_type']); ?></div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <?php foreach($visible_subtasks as $index => $task):
                                            $resolver = $resolved_data[$index] ?? null;
                                    ?>
                                        <div class="flex items-center gap-1">
                                            <span
                                                onclick="event.stopPropagation(); toggleSubtask(<?php echo $defect['id']; ?>, '<?php echo $index; ?>')"
                                                class="cursor-pointer px-2 py-0.5 rounded transition border <?php echo $resolver ? 'bg-green-500/20 text-green-400 border-green-500/30' : 'bg-subtask-bg text-subtask-text border-white/10 hover:bg-white/10'; ?>"
                                                style="font-size: var(--subtask-font-size);"
                                            >
                                                <?php echo htmlspecialchars($task); ?>
                                            </span>
                                            <?php if ($resolver): ?>
                                                <span class="text-gray-500 italic flex items-center gap-0.5" style="font-size: var(--subtask-font-size);" title="Rezolvat de <?php echo htmlspecialchars($resolver); ?>">
                                                    <i class="fas fa-user-check" style="font-size: 0.8em;"></i> <?php echo htmlspecialchars($resolver); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <?php
                                    $prio_color = 'bg-blue-500/10 text-blue-500 border-blue-500/20';
                                    if($defect['priority'] == 'Mare') $prio_color = 'bg-orange-500/10 text-orange-500 border-orange-500/20';
                                    if($defect['priority'] == 'Urgentă') $prio_color = 'bg-red-500/10 text-red-500 border-red-500/20';
                                ?>
                                <span class="px-2 py-1 rounded text-[10px] font-bold border <?php echo $prio_color; ?>">
                                    <?php echo htmlspecialchars($defect['priority']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full <?php echo $defect['status'] == 'activ' ? 'bg-red-500 animate-pulse' : 'bg-green-500'; ?>"></div>
                                    <span class="text-sm font-medium capitalize <?php echo $defect['status'] == 'activ' ? 'text-red-400' : 'text-green-400'; ?>">
                                        <?php echo $defect['status']; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-400">
                                <i class="far fa-user mr-1 opacity-50"></i>
                                <?php echo htmlspecialchars($defect['reported_by_user'] ?? 'Sistem'); ?>
                            </td>
                            <td class="px-6 py-5">
                                <div class="mb-1">
                                    <span class="text-[9px] text-gray-500 uppercase font-bold tracking-tighter">Raportat:</span>
                                    <span class="text-xs white-time ml-1">
                                        <?php
                                            $reported_utc = new DateTime($defect['reported_at'], new DateTimeZone('UTC'));
                                            $reported_utc->setTimezone(new DateTimeZone($site_settings_idx['timezone'] ?? 'Europe/Bucharest'));
                                            echo $reported_utc->format('d.m H:i:s');
                                        ?>
                                    </span>
                                </div>
                                <?php if ($defect['resolved_at']): ?>
                                <div>
                                    <span class="text-[9px] text-green-600 uppercase font-bold tracking-tighter">Rezolvat:</span>
                                    <span class="text-xs white-time ml-1">
                                        <?php
                                            $resolved_utc = new DateTime($defect['resolved_at'], new DateTimeZone('UTC'));
                                            $resolved_utc->setTimezone(new DateTimeZone($site_settings_idx['timezone'] ?? 'Europe/Bucharest'));
                                            echo $resolved_utc->format('d.m H:i:s');
                                        ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition">
                                    <a href="edit_defect.php?id=<?php echo $defect['id']; ?>" class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center hover:bg-blue-500 hover:text-white transition" title="Editare">
                                        <i class="fas fa-pen-nib text-xs"></i>
                                    </a>
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <a href="delete_defect.php?id=<?php echo $defect['id']; ?>" onclick="return confirm('Ștergi definitiv această înregistrare?')" class="w-8 h-8 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition" title="Ștergere">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleSubtask(defectId, index) {
        console.log("Toggling subtask:", defectId, index);
        const formData = new FormData();
        formData.append('defect_id', defectId);
        formData.append('subtask_index', index);

        fetch('toggle_subtask.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Response:", data);
            if (data.status === 'success') {
                location.reload();
            } else {
                alert("Eroare: " + (data.message || "Necunoscută"));
            }
        })
        .catch(err => {
            console.error("Fetch error:", err);
            alert("Eroare de conexiune.");
        });
    }

    function updateClock() {
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        const el = document.getElementById('live-clock');
        if (el) el.textContent = `${day}.${month}.${year} ${hours}:${minutes}:${seconds}`;
    }
    setInterval(updateClock, 1000);

    function sortTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById("defects-table");
        switching = true;
        dir = "asc";
        while (switching) {
            switching = false;
            rows = table.rows;
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[n];
                y = rows[i + 1].getElementsByTagName("TD")[n];

                let xVal = x.innerText.toLowerCase();
                if (dir == "asc") {
                    if (xVal > y.innerText.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if (xVal < y.innerText.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount ++;
            } else {
                if (switchcount == 0 && dir == "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
