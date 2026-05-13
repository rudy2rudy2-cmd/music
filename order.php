<?php
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM platforms WHERE id = ?");
$stmt->execute([$id]);
$platform = $stmt->fetch();

if (!$platform) {
    header("Location: index.php");
    exit();
}

$final_price = ($platform['discount_price'] > 0) ? $platform['discount_price'] : $platform['price'];
?>

    <main class="max-w-4xl mx-auto px-6 py-20">
        <div class="admin-card p-10 shadow-2xl">
            <h2 class="text-3xl font-bold mb-8 text-center"><?php echo __('checkout'); ?></h2>

            <div class="flex flex-col md:flex-row gap-10 mb-10 pb-10 border-b border-gray-700/20">
                <div class="md:w-1/3">
                    <?php if($platform['image_url']): ?>
                        <img src="<?php echo $platform['image_url']; ?>" class="rounded-2xl shadow-lg w-full">
                    <?php endif; ?>
                </div>
                <div class="md:w-2/3">
                    <h3 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($platform['title']); ?></h3>
                    <p class="text-sm opacity-70 mb-4">Versiune: <?php echo htmlspecialchars($platform['version']); ?></p>
                    <div class="text-3xl font-extrabold text-blue-500">
                        <?php echo $final_price; ?> EUR
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-xl font-bold mb-6"><?php echo __('payment_method'); ?></h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Stripe Placeholder -->
                    <button class="flex items-center justify-center space-x-3 p-6 border-2 border-indigo-600 rounded-2xl hover:bg-indigo-600 hover:text-white transition group">
                        <i class="fab fa-stripe text-4xl"></i>
                        <span class="font-bold text-lg">Plătește cu Card (Stripe)</span>
                    </button>

                    <!-- PayPal Placeholder -->
                    <button class="flex items-center justify-center space-x-3 p-6 border-2 border-yellow-500 rounded-2xl hover:bg-yellow-500 hover:text-white transition group">
                        <i class="fab fa-paypal text-4xl"></i>
                        <span class="font-bold text-lg">Plătește cu PayPal</span>
                    </button>
                </div>
                <p class="text-center text-xs opacity-50 mt-10">Plăți securizate prin procesatori certificați.</p>
            </div>
        </div>
    </main>

<?php require_once 'includes/footer.php'; ?>
