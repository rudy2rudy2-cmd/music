<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
checkLogin();

// Statistics queries for Weekly and Monthly
// Week starts on Monday
$week_start = date('Y-m-d 00:00:00', strtotime('monday this week'));
$month_start = date('Y-m-01 00:00:00');

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
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM defects WHERE reported_at >= ? AND (status = 'rezolvat' OR (resolved_subtasks != '[]' AND resolved_subtasks IS NOT NULL))");
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
