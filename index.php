<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Statistics
$total_rooms = 100;
$active_stmt = $pdo->query("SELECT COUNT(*) FROM defects WHERE status = 'activ'");
$active_defects = $active_stmt->fetchColumn();

$resolved_stmt = $pdo->query("SELECT COUNT(*) FROM defects WHERE status = 'rezolvat'");
$resolved_defects = $resolved_stmt->fetchColumn();

// Filters
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

$query = "SELECT d.*, u.username as reported_by_user FROM defects d LEFT JOIN users u ON d.reported_by = u.id WHERE 1=1";
$params = [];

if ($filter === 'active') {
    $query .= " AND status = 'activ'";
} elseif ($filter === 'resolved') {
    $query .= " AND status = 'rezolvat'";
}

if (!empty($search)) {
    $query .= " AND (room_number LIKE ? OR issue_type LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY CASE WHEN status = 'activ' THEN 0 ELSE 1 END, reported_at DESC";
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
                <p class="text-xl font-bold"><?php echo $total_rooms; ?></p>
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
            <tbody class="divide-y divide-white/5">
                <?php if (empty($defects)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-600 mb-2"><i class="fas fa-folder-open text-4xl"></i></div>
                            <p class="text-gray-500">Nu am găsit înregistrări care să corespundă criteriilor.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($defects as $defect): ?>
                        <tr class="hover:bg-white/[0.03] transition group" id="defect-row-<?php echo $defect['id']; ?>">
                            <td class="px-6 py-5">
                                <span class="text-lg font-black text-blue-500">#<?php echo htmlspecialchars($defect['room_number']); ?></span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="font-semibold text-gray-200"><?php echo htmlspecialchars($defect['issue_type']); ?></div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <?php
                                        $subtasks = array_filter(array_map('trim', explode('.', $defect['description'])));
                                        $resolved = array_filter(explode(',', $defect['resolved_subtasks']));
                                        foreach($subtasks as $index => $task):
                                            $is_resolved = in_array($index, $resolved);
                                    ?>
                                        <span
                                            onclick="toggleSubtask(<?php echo $defect['id']; ?>, <?php echo $index; ?>)"
                                            class="cursor-pointer px-2 py-0.5 rounded text-[11px] transition border <?php echo $is_resolved ? 'bg-green-500/20 text-green-400 border-green-500/30 line-through opacity-50' : 'bg-white/5 text-white border-white/10 hover:bg-white/10'; ?>"
                                        >
                                            <?php echo htmlspecialchars($task); ?>
                                        </span>
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
                                <div class="flex items-center gap-2 status-container">
                                    <div class="w-2 h-2 rounded-full status-dot <?php echo $defect['status'] == 'activ' ? 'bg-red-500 animate-pulse' : 'bg-green-500'; ?>"></div>
                                    <span class="text-sm font-medium capitalize status-text <?php echo $defect['status'] == 'activ' ? 'text-red-400' : 'text-green-400'; ?>">
                                        <?php echo $defect['status']; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-400">
                                <i class="far fa-user mr-1 opacity-50"></i>
                                <?php echo htmlspecialchars($defect['reported_by_user'] ?? 'Sistem'); ?>
                            </td>
                            <td class="px-6 py-5" data-sort="<?php echo strtotime($defect['reported_at']); ?>">
                                <div class="text-xs font-medium"><?php echo date('d M Y', strtotime($defect['reported_at'])); ?></div>
                                <div class="text-[10px] text-gray-600"><?php echo date('H:i', strtotime($defect['reported_at'])); ?></div>
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
        const formData = new FormData();
        formData.append('defect_id', defectId);
        formData.append('subtask_index', index);

        fetch('toggle_subtask.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Refresh the row or relevant elements
                location.reload(); // Simple reload for now to update all badges and stats
            }
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

                let xVal = x.getAttribute('data-sort') || x.innerText.toLowerCase();
                let yVal = y.getAttribute('data-sort') || y.innerText.toLowerCase();

                if (dir == "asc") {
                    if (xVal > yVal) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if (xVal < yVal) {
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
