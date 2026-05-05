<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Global settings fetch
$stmt = $pdo->query("SELECT * FROM settings");
$site_settings = [];
while ($row = $stmt->fetch()) {
    $site_settings[$row['setting_key']] = $row['setting_value'];
}

// Set Timezone
date_default_timezone_set($site_settings['timezone'] ?? 'Europe/Bucharest');

$theme = $site_settings['theme'] ?? 'blue';
$logo = !empty($site_settings['logo_path']) ? $site_settings['logo_path'] : '';
$copyright = $site_settings['copyright'] ?? 'Copyright 2026 Autor Stoian Rudolf';
$site_title = $site_settings['site_title'] ?? 'HotelDefects';
$logo_size = $site_settings['logo_size'] ?? '32';
$report_font_size = $site_settings['report_font_size'] ?? '14';
$subtask_font_size = $site_settings['subtask_font_size'] ?? '10';
$report_text_color_val = $site_settings['report_text_color'] ?? 'white';
$color_map = [
    'white' => '#ffffff',
    'red' => '#ef4444',
    'green' => '#22c55e',
    'orange' => '#f97316'
];
$report_text_color = $color_map[$report_text_color_val] ?? '#ffffff';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Default Blue Theme */
            --bg-color: #0f172a;
            --sidebar-bg: rgba(255, 255, 255, 0.03);
            --card-bg: rgba(255, 255, 255, 0.03);
            --border-color: rgba(255, 255, 255, 0.05);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --accent-color: #3b82f6;
            --input-bg: rgba(255, 255, 255, 0.05);
            --report-font-size: <?php echo $report_font_size; ?>px;
            --subtask-font-size: <?php echo $subtask_font_size; ?>px;
            --report-text-color: <?php echo $report_text_color; ?>;
            --subtask-bg: rgba(255, 255, 255, 0.05);
            --subtask-text: #ffffff;
        }

        <?php if ($theme === 'black'): ?>
        :root {
            --bg-color: #000000;
            --sidebar-bg: #0a0a0a;
            --card-bg: #0f0f0f;
            --border-color: #1a1a1a;
            --text-primary: #ffffff;
            --text-secondary: #71717a;
            --accent-color: #3f3f46;
            --input-bg: #111111;
            --subtask-bg: #111111;
            --subtask-text: #ffffff;
        }
        <?php elseif ($theme === 'white'): ?>
        :root {
            --bg-color: #f8fafc;
            --sidebar-bg: #ffffff;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --accent-color: #3b82f6;
            --input-bg: #f1f5f9;
            --subtask-bg: #f1f5f9;
            --subtask-text: #0f172a;
        }
        .glass {
            backdrop-filter: none !important;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        .sidebar {
            box-shadow: 1px 0 0 0 var(--border-color);
        }
        input, select, textarea {
            color: #0f172a !important;
        }
        .defect-desc {
            color: #0f172a !important;
        }
        .white-time {
            color: #1e293b !important;
        }
        <?php else: ?>
        /* Explicitly handle 'blue' if needed, though it's the default in :root */
        <?php endif; ?>

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-color);
            color: var(--text-primary);
            transition: background 0.3s, color 0.3s;
        }
        .glass {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
        }
        .sidebar {
            width: 260px;
            transition: all 0.3s;
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
        }
        .main-content {
            flex: 1;
        }
        .status-activ {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .status-rezolvat {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .defect-desc {
            color: var(--report-text-color) !important;
        }
        .white-time {
            color: var(--text-primary);
        }
    </style>
</head>
<body class="flex min-h-screen">
    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Sidebar -->
    <aside class="sidebar border-r border-white/5 flex flex-col h-screen sticky top-0">
        <div class="p-6">
            <h1 class="text-xl font-bold flex items-center gap-3">
                <?php if ($logo): ?>
                    <img src="<?php echo $logo; ?>" style="height: <?php echo $logo_size; ?>px; width: auto;" class="object-contain">
                <?php else: ?>
                    <i class="fas fa-hotel text-blue-500" style="font-size: <?php echo $logo_size; ?>px;"></i>
                <?php endif; ?>
                <span><?php echo htmlspecialchars($site_title); ?></span>
            </h1>
        </div>

        <nav class="flex-1 px-4 space-y-2">
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-blue-600/20 text-blue-400' : ''; ?>">
                <i class="fas fa-chart-line w-5"></i> Dashboard
            </a>
            <a href="add_defect.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition <?php echo basename($_SERVER['PHP_SELF']) == 'add_defect.php' ? 'bg-blue-600/20 text-blue-400' : ''; ?>">
                <i class="fas fa-plus-circle w-5"></i> Raport Nou
            </a>
            <?php if (isAdmin()): ?>
            <a href="users.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-blue-600/20 text-blue-400' : ''; ?>">
                <i class="fas fa-users w-5"></i> Utilizatori
            </a>
            <a href="settings.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'bg-blue-600/20 text-blue-400' : ''; ?>">
                <i class="fas fa-cog w-5"></i> Setări Sistem
            </a>
            <?php endif; ?>
            <button onclick="document.getElementById('about-modal').classList.remove('hidden')" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition text-gray-300">
                <i class="fas fa-info-circle w-5"></i> Despre Platformă
            </button>
        </nav>

        <div class="p-4 mt-auto border-t border-white/5">
            <div class="flex items-center gap-3 px-4 py-3">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-xs font-bold text-white">
                    <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-sm font-medium truncate"><?php echo $_SESSION['username'] ?? 'User'; ?></p>
                    <p class="text-xs text-gray-500 capitalize"><?php echo $_SESSION['role'] ?? 'Staff'; ?></p>
                </div>
                <a href="logout.php" class="text-gray-400 hover:text-red-400 transition" title="Deconectare">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </aside>
    <?php endif; ?>

    <main class="main-content p-8">

    <!-- About Modal -->
    <div id="about-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="glass max-w-2xl w-full max-h-[90vh] overflow-y-auto rounded-3xl p-8 relative shadow-2xl border-white/10">
            <button onclick="document.getElementById('about-modal').classList.add('hidden')" class="absolute top-6 right-6 text-gray-400 hover:text-white transition text-xl">
                <i class="fas fa-times"></i>
            </button>

            <div class="space-y-6 text-gray-200">
                <div class="text-center pb-6 border-b border-white/5">
                    <h2 class="text-2xl font-bold text-white mb-2">Management Defecțiuni Hotel</h2>
                    <p class="text-blue-400 font-medium">Platformă pentru Managementul HoReCa</p>
                </div>

                <div class="space-y-4 leading-relaxed">
                    <p>Platforma Management Defecțiuni Hotel este o soluție digitală modernă dedicată industriei HoReCa, concepută pentru a eficientiza gestionarea problemelor tehnice și operaționale din cadrul unităților de cazare și alimentație publică.</p>

                    <p>Aceasta permite personalului să raporteze rapid defecțiuni sau incidente (ex: echipamente defecte, probleme în camere, instalații, curățenie), direct dintr-o interfață intuitivă, accesibilă de pe mobil, tabletă sau desktop. Fiecare sesizare este înregistrată în sistem, prioritizată și alocată automat sau manual către echipa responsabilă (mentenanță, housekeeping, IT etc.).</p>

                    <div class="bg-white/5 p-6 rounded-2xl border border-white/5">
                        <p class="font-bold text-white mb-3">Platforma oferă funcționalități esențiale precum:</p>
                        <ul class="space-y-2 list-none">
                            <li class="flex items-start gap-3"><i class="fas fa-check-circle text-green-500 mt-1"></i> Monitorizarea în timp real a tuturor defecțiunilor raportate</li>
                            <li class="flex items-start gap-3"><i class="fas fa-check-circle text-green-500 mt-1"></i> Istoric complet al intervențiilor și trasabilitate</li>
                            <li class="flex items-start gap-3"><i class="fas fa-check-circle text-green-500 mt-1"></i> Notificări automate pentru echipele implicate</li>
                            <li class="flex items-start gap-3"><i class="fas fa-check-circle text-green-500 mt-1"></i> Managementul priorităților și al timpilor de rezolvare</li>
                            <li class="flex items-start gap-3"><i class="fas fa-check-circle text-green-500 mt-1"></i> Rapoarte și statistici pentru optimizarea proceselor interne</li>
                        </ul>
                    </div>

                    <p>Prin digitalizarea fluxului de lucru, platforma contribuie la reducerea timpilor de intervenție, creșterea eficienței echipelor și îmbunătățirea experienței clienților.</p>

                    <p>Soluția este scalabilă și adaptabilă, fiind potrivită atât pentru hoteluri independente, cât și pentru lanțuri hoteliere, restaurante sau alte afaceri din domeniul HoReCa.</p>
                </div>

                <div class="pt-6 border-t border-white/5 text-center text-sm text-gray-500 italic">
                    Dezvoltator Platforma @Stoian Rudolf
                </div>

                <button onclick="document.getElementById('about-modal').classList.add('hidden')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition mt-4">
                    Am înțeles
                </button>
            </div>
        </div>
    </div>
