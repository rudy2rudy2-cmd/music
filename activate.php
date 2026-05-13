<?php
if (!file_exists('includes/config.php')) {
    header("Location: install.php");
    exit();
}
require_once 'includes/config.php';
require_once 'includes/auth.php';

$error = '';
$success = '';
$email = $_SESSION['unactivated_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $form_email = $_POST['email'] ?? $email;

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND activation_code = ? AND is_active = 0");
    $stmt->execute([$form_email, $code]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        unset($_SESSION['unactivated_email']);
        $success = "Cont activat cu succes! Acum te poți autentifica.";
    } else {
        $error = "Cod de activare invalid sau cont deja activ.";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Activare Cont</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Activare Cont</h1>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
                <div class="mt-4 text-center">
                    <a href="login.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Spre Login</a>
                </div>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <p class="text-red-500 mb-4"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required class="w-full p-2 border rounded mt-1">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700">Cod Activare</label>
                    <input type="text" name="code" required class="w-full p-2 border rounded mt-1 text-center text-2xl tracking-widest" placeholder="000000">
                </div>
                <button type="submit" class="w-full bg-green-600 text-white p-2 rounded hover:bg-green-700 font-bold">Activează Contul</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
