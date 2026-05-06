<?php
require_once __DIR__ . '/includes/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/includes/header.php';

// Statistics queries for Weekly and Monthly
// Week starts on Monday. We calculate boundaries in UTC to match DB timestamps.
$tz = new DateTimeZone($site_settings['timezone'] ?? 'Europe/Bucharest');

$week_start_local = new DateTime('monday this week 00:00:00', $tz);
$week_start_utc = clone $week_start_local;
$week_start_utc->setTimezone(new DateTimeZone('UTC'));
$week_start = $week_start_utc->format('Y-m-d H:i:s');

$month_start_local = new DateTime('first day of this month 00:00:00', $tz);
$month_start_utc = clone $month_start_local;
$month_start_utc->setTimezone(new DateTimeZone('UTC'));
$month_start = $month_start_utc->format('Y-m-d H:i:s');

function getStats($pdo, $start_date) {
    // Total reported
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM defects WHERE reported_at >= ?");
    $stmt->execute([$start_date]);
    $total = $stmt->fetchColumn();

    // Total resolved (Status is 'rezolvat' OR at least one subtask is resolved)
    // Actually, following index.php logic: stats for resolved usually mean fully resolved?
    // User asked "cate probleme s-au rezolvat si cate nu"
    // Let's use the same logic as dashboard for consistency

    // Fully resolved or has resolved subtasks
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM defects WHERE reported_at >= ? AND (status = 'rezolvat' OR (resolved_subtasks != '' AND resolved_subtasks != '[]' AND resolved_subtasks IS NOT NULL))");
    $stmt->execute([$start_date]);
    $resolved = $stmt->fetchColumn();

    $active = $total - $resolved;

    return [
        'total' => $total,
        'resolved' => $resolved,
        'active' => $active
    ];
}

$weekly = getStats($pdo, $week_start);
$monthly = getStats($pdo, $month_start);

// Custom Report Logic
$custom_from = $_GET['from'] ?? '';
$custom_to = $_GET['to'] ?? '';
$custom_status = $_GET['status'] ?? 'all';
$custom_results = null;
$custom_stats = null;

if (!empty($custom_from) && !empty($custom_to)) {
    $from_utc = new DateTime($custom_from . ' 00:00:00', $tz);
    $from_utc->setTimezone(new DateTimeZone('UTC'));
    $from_str = $from_utc->format('Y-m-d H:i:s');

    $to_utc = new DateTime($custom_to . ' 23:59:59', $tz);
    $to_utc->setTimezone(new DateTimeZone('UTC'));
    $to_str = $to_utc->format('Y-m-d H:i:s');

    // Stats for custom range
    $stmt_total = $pdo->prepare("SELECT COUNT(*) FROM defects WHERE reported_at BETWEEN ? AND ?");
    $stmt_total->execute([$from_str, $to_str]);
    $total_count = $stmt_total->fetchColumn();

    $stmt_res = $pdo->prepare("SELECT COUNT(*) FROM defects WHERE reported_at BETWEEN ? AND ? AND (status = 'rezolvat' OR (resolved_subtasks != '' AND resolved_subtasks != '[]' AND resolved_subtasks IS NOT NULL))");
    $stmt_res->execute([$from_str, $to_str]);
    $res_count = $stmt_res->fetchColumn();

    $custom_stats = [
        'total' => $total_count,
        'resolved' => $res_count
    ];
    $custom_stats['active'] = $custom_stats['total'] - $custom_stats['resolved'];

    // Query for custom list
    $q = "SELECT d.*, u.username as reported_by_user FROM defects d LEFT JOIN users u ON d.reported_by = u.id WHERE d.reported_at BETWEEN ? AND ?";
    $p = [$from_str, $to_str];

    if ($custom_status === 'active') {
        $q .= " AND d.status = 'activ'";
    } elseif ($custom_status === 'resolved') {
        $q .= " AND (d.status = 'rezolvat' OR (d.resolved_subtasks != '' AND d.resolved_subtasks != '[]' AND d.resolved_subtasks IS NOT NULL))";
    }

    $q .= " ORDER BY d.reported_at DESC";
    $stmt_custom = $pdo->prepare($q);
    $stmt_custom->execute($p);
    $custom_results = $stmt_custom->fetchAll();
}

?>

<div class="mb-8">
    <h2 class="text-3xl font-bold">Rapoarte Activitate</h2>
    <p class="text-gray-400">Statistici saptămânale și lunare privind defectele raportate</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Weekly Report -->
    <div class="glass p-8 rounded-3xl border border-white/10 shadow-2xl relative overflow-hidden group">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>

        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-xl font-bold text-white">Raport Săptămânal</h3>
                <p class="text-sm text-gray-500">De la <?php echo date('d.m.Y', strtotime($week_start)); ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-600/20 text-blue-400 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-calendar-week"></i>
            </div>
        </div>

        <div class="space-y-6">
            <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5">
                <span class="text-gray-400">Total Raportate</span>
                <span class="text-2xl font-bold text-white"><?php echo $weekly['total']; ?></span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-green-500/10 rounded-2xl border border-green-500/20">
                    <p class="text-xs text-green-500 font-bold uppercase tracking-wider mb-1">Rezolvate</p>
                    <p class="text-2xl font-bold text-green-400"><?php echo $weekly['resolved']; ?></p>
                </div>
                <div class="p-4 bg-red-500/10 rounded-2xl border border-red-500/20">
                    <p class="text-xs text-red-500 font-bold uppercase tracking-wider mb-1">Active</p>
                    <p class="text-2xl font-bold text-red-400"><?php echo $weekly['active']; ?></p>
                </div>
            </div>

            <?php if ($weekly['total'] > 0): ?>
            <div class="w-full bg-white/5 h-2 rounded-full overflow-hidden">
                <div class="bg-green-500 h-full transition-all duration-1000" style="width: <?php echo ($weekly['resolved'] / $weekly['total']) * 100; ?>%"></div>
            </div>
            <p class="text-center text-xs text-gray-500">Rată de rezolvare: <?php echo round(($weekly['resolved'] / $weekly['total']) * 100); ?>%</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Monthly Report -->
    <div class="glass p-8 rounded-3xl border border-white/10 shadow-2xl relative overflow-hidden group">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-purple-500/10 rounded-full blur-3xl group-hover:bg-purple-500/20 transition-all duration-500"></div>

        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-xl font-bold text-white">Raport Lunar</h3>
                <p class="text-sm text-gray-500">Luna <?php echo date('F Y'); ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-600/20 text-purple-400 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>

        <div class="space-y-6">
            <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5">
                <span class="text-gray-400">Total Raportate</span>
                <span class="text-2xl font-bold text-white"><?php echo $monthly['total']; ?></span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-green-500/10 rounded-2xl border border-green-500/20">
                    <p class="text-xs text-green-500 font-bold uppercase tracking-wider mb-1">Rezolvate</p>
                    <p class="text-2xl font-bold text-green-400"><?php echo $monthly['resolved']; ?></p>
                </div>
                <div class="p-4 bg-red-500/10 rounded-2xl border border-red-500/20">
                    <p class="text-xs text-red-500 font-bold uppercase tracking-wider mb-1">Active</p>
                    <p class="text-2xl font-bold text-red-400"><?php echo $monthly['active']; ?></p>
                </div>
            </div>

            <?php if ($monthly['total'] > 0): ?>
            <div class="w-full bg-white/5 h-2 rounded-full overflow-hidden">
                <div class="bg-purple-500 h-full transition-all duration-1000" style="width: <?php echo ($monthly['resolved'] / $monthly['total']) * 100; ?>%"></div>
            </div>
            <p class="text-center text-xs text-gray-500">Rată de rezolvare: <?php echo round(($monthly['resolved'] / $monthly['total']) * 100); ?>%</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="mt-12">
    <div class="glass p-8 rounded-3xl border border-white/10 shadow-2xl">
        <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <i class="fas fa-lightbulb text-amber-400"></i> Analiză Performanță
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-2">
                <p class="text-gray-400 text-sm">Probleme nerezolvate (Active)</p>
                <div class="flex items-center gap-3">
                    <span class="text-3xl font-bold text-red-500"><?php echo $monthly['active']; ?></span>
                    <span class="text-xs text-gray-500">necesită atenție imediată</span>
                </div>
            </div>
            <div class="space-y-2">
                <p class="text-gray-400 text-sm">Media de rezolvare</p>
                <div class="flex items-center gap-3">
                    <span class="text-3xl font-bold text-blue-400">
                        <?php echo $monthly['total'] > 0 ? round($monthly['resolved'] / $monthly['total'] * 100) : 0; ?>%
                    </span>
                    <span class="text-xs text-gray-500">eficiență lunară</span>
                </div>
            </div>
            <div class="space-y-2">
                <p class="text-gray-400 text-sm">Trend Săptămânal</p>
                <div class="flex items-center gap-3">
                    <?php if ($weekly['resolved'] > $weekly['active']): ?>
                        <span class="text-green-500 flex items-center gap-1 font-bold">
                            <i class="fas fa-arrow-up"></i> Pozitiv
                        </span>
                    <?php else: ?>
                        <span class="text-amber-500 flex items-center gap-1 font-bold">
                            <i class="fas fa-minus"></i> Stabil
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Report Section -->
<div class="mt-12">
    <div class="glass p-8 rounded-3xl border border-white/10 shadow-2xl">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8">
            <div>
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <i class="fas fa-search-plus text-blue-500"></i> Raport Personalizat
                </h3>
                <p class="text-gray-400 text-sm">Selectează perioada și criteriile de filtrare</p>
            </div>
        </div>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-widest pl-1">De la</label>
                <input type="date" name="from" value="<?php echo htmlspecialchars($custom_from); ?>" required class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition text-white">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-widest pl-1">Până la</label>
                <input type="date" name="to" value="<?php echo htmlspecialchars($custom_to); ?>" required class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition text-white">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-widest pl-1">Status</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 transition text-white appearance-none">
                    <option value="all" <?php echo $custom_status == 'all' ? 'selected' : ''; ?>>Toate</option>
                    <option value="active" <?php echo $custom_status == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="resolved" <?php echo $custom_status == 'resolved' ? 'selected' : ''; ?>>Rezolvate</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                    <i class="fas fa-filter"></i> Generează Raport
                </button>
            </div>
        </form>

        <?php if ($custom_stats): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="p-6 bg-white/5 rounded-2xl border border-white/5">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-2">Total în Perioadă</p>
                    <p class="text-3xl font-bold text-white"><?php echo $custom_stats['total']; ?></p>
                </div>
                <div class="p-6 bg-green-500/10 rounded-2xl border border-green-500/20">
                    <p class="text-xs text-green-500 font-bold uppercase tracking-wider mb-2">Rezolvate</p>
                    <p class="text-3xl font-bold text-green-400"><?php echo $custom_stats['resolved']; ?></p>
                </div>
                <div class="p-6 bg-red-500/10 rounded-2xl border border-red-500/20">
                    <p class="text-xs text-red-500 font-bold uppercase tracking-wider mb-2">Active</p>
                    <p class="text-3xl font-bold text-red-400"><?php echo $custom_stats['active']; ?></p>
                </div>
            </div>

            <?php if (!empty($custom_results)): ?>
                <div class="overflow-x-auto rounded-2xl border border-white/5">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white/[0.02] text-gray-500 text-[10px] uppercase font-bold tracking-widest border-b border-white/5">
                                <th class="px-6 py-4">Cameră</th>
                                <th class="px-6 py-4">Defecțiune</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Data Raportării</th>
                                <th class="px-6 py-4 text-right">Raportat de</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php foreach ($custom_results as $res): ?>
                                <tr class="hover:bg-white/[0.03] transition">
                                    <td class="px-6 py-4 font-bold text-blue-500">#<?php echo htmlspecialchars($res['room_number']); ?></td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-white"><?php echo htmlspecialchars($res['issue_type']); ?></div>
                                        <div class="text-xs text-gray-500 truncate max-w-xs"><?php echo htmlspecialchars($res['description']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php $is_res = ($res['status'] == 'rezolvat' || (!empty($res['resolved_subtasks']) && $res['resolved_subtasks'] != '' && $res['resolved_subtasks'] != '[]')); ?>
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold <?php echo $is_res ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'; ?>">
                                            <?php echo $is_res ? 'REZOLVAT' : 'ACTIV'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-400">
                                        <?php
                                            $d = new DateTime($res['reported_at'], new DateTimeZone('UTC'));
                                            $d->setTimezone($tz);
                                            echo $d->format('d.m.Y H:i');
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-500">
                                        <?php echo htmlspecialchars($res['reported_by_user'] ?? 'Sistem'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-12 text-center bg-white/5 rounded-2xl border border-white/5">
                    <p class="text-gray-500">Nu au fost găsite înregistrări pentru perioada selectată.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
