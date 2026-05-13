<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$user_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM platforms");
$platform_count = $stmt->fetchColumn();

$header_title = "Overview Dashboard";
require_once 'includes/admin_header.php';
?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="admin-card p-8 shadow-lg group hover:translate-y-1 transition-transform">
                <div class="flex items-center">
                    <div class="p-4 bg-blue-100 rounded-2xl group-hover:bg-blue-200 transition">
                        <i class="fas fa-layer-group text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-6">
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-widest">Total Platforme</p>
                        <p class="text-3xl font-extrabold <?php echo $admin_theme === 'neon' ? 'text-white' : 'text-gray-800'; ?> mt-1"><?php echo $platform_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="admin-card p-8 shadow-lg group hover:translate-y-1 transition-transform">
                <div class="flex items-center">
                    <div class="p-4 bg-green-100 rounded-2xl group-hover:bg-green-200 transition">
                        <i class="fas fa-users text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-6">
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-widest">Total Utilizatori</p>
                        <p class="text-3xl font-extrabold <?php echo $admin_theme === 'neon' ? 'text-white' : 'text-gray-800'; ?> mt-1"><?php echo $user_count; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 admin-card p-12 shadow-xl text-center border-2 border-dashed border-gray-300/30">
            <div class="max-w-2xl mx-auto">
                <div class="w-20 h-20 bg-blue-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                     <i class="fas fa-hand-sparkles text-blue-500 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 <?php echo $admin_theme === 'neon' ? 'text-white' : 'text-gray-800'; ?>">Sistemul este gata de acțiune.</h3>
                <p class="<?php echo $admin_theme === 'neon' ? 'text-gray-400' : 'text-gray-600'; ?> leading-relaxed mb-8">Utilizați panoul din stânga pentru a naviga prin funcționalități. Toate modificările se aplică instantaneu în platformă.</p>
                <div class="flex justify-center space-x-4">
                     <a href="../index.php" target="_blank" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">
                        <i class="fas fa-external-link-alt mr-2"></i> Vezi Site
                     </a>
                </div>
            </div>
        </div>

<?php require_once 'includes/admin_footer.php'; ?>
