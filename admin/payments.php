<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [
        'paypal_email' => $_POST['paypal_email'] ?? '',
        'stripe_publishable_key' => $_POST['stripe_publishable_key'] ?? '',
        'stripe_secret_key' => $_POST['stripe_secret_key'] ?? '',
        'enable_paypal' => isset($_POST['enable_paypal']) ? '1' : '0',
        'enable_stripe' => isset($_POST['enable_stripe']) ? '1' : '0'
    ];

    foreach ($updates as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    $message = "Configurare plăți salvată cu succes!";
}

$header_title = "Configurează Plăți";
require_once 'includes/admin_header.php';
?>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-8 shadow-sm">
                <i class="fas fa-check-circle mr-2"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="admin-card p-10 shadow-xl max-w-4xl">
            <form method="POST">
                <div class="space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <!-- PayPal Section -->
                        <div class="space-y-6">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500">
                                    <i class="fab fa-paypal text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-bold">PayPal</h3>
                            </div>

                            <label class="flex items-center space-x-3 cursor-pointer p-4 rounded-xl <?php echo $admin_theme === 'neon' ? 'bg-blue-500/10 border-blue-500/30' : 'bg-gray-50 border-gray-200'; ?> border transition">
                                <input type="checkbox" name="enable_paypal" value="1" <?php echo ($admin_settings['enable_paypal'] ?? '0') === '1' ? 'checked' : ''; ?> class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="font-bold">Activează Metoda PayPal</span>
                            </label>

                            <div>
                                <label class="block text-sm font-bold opacity-75 mb-2">Email Business PayPal</label>
                                <input type="email" name="paypal_email" value="<?php echo htmlspecialchars($admin_settings['paypal_email'] ?? ''); ?>" placeholder="paypal@exemplu.com" class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                                <p class="text-xs opacity-50 mt-2">Plățile vor fi trimise către acest cont.</p>
                            </div>
                        </div>

                        <!-- Stripe Section -->
                        <div class="space-y-6">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="w-12 h-12 bg-indigo-500/10 rounded-xl flex items-center justify-center text-indigo-500">
                                    <i class="fab fa-stripe text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-bold">Stripe</h3>
                            </div>

                            <label class="flex items-center space-x-3 cursor-pointer p-4 rounded-xl <?php echo $admin_theme === 'neon' ? 'bg-indigo-500/10 border-indigo-500/30' : 'bg-gray-50 border-gray-200'; ?> border transition">
                                <input type="checkbox" name="enable_stripe" value="1" <?php echo ($admin_settings['enable_stripe'] ?? '0') === '1' ? 'checked' : ''; ?> class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="font-bold">Activează Metoda Stripe</span>
                            </label>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold opacity-75 mb-2">Publishable Key</label>
                                    <input type="text" name="stripe_publishable_key" value="<?php echo htmlspecialchars($admin_settings['stripe_publishable_key'] ?? ''); ?>" placeholder="pk_test_..." class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold opacity-75 mb-2">Secret Key</label>
                                    <input type="password" name="stripe_secret_key" value="<?php echo htmlspecialchars($admin_settings['stripe_secret_key'] ?? ''); ?>" placeholder="sk_test_..." class="w-full p-3 rounded-xl modern-input <?php echo $admin_theme !== 'neon' ? 'border-gray-200 border' : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-2xl hover:bg-blue-700 transition shadow-lg transform hover:-translate-y-1">
                        <i class="fas fa-save mr-2"></i> Salvează Configurarea Plăților
                    </button>
                </div>
            </form>
        </div>

<?php require_once 'includes/admin_footer.php'; ?>
