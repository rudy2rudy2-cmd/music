<?php
if (file_exists('includes/config.php')) {
    die("Instalarea a fost deja efectuată. Ștergeți includes/config.php pentru a reinstala.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? '';
    $db_name = $_POST['db_name'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';

    $admin_user = $_POST['admin_user'] ?? '';
    $admin_pass = $_POST['admin_pass'] ?? '';
    $admin_email = $_POST['admin_email'] ?? '';

    try {
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    } catch (PDOException $e) {
        $error = "Eroare de conectare: " . $e->getMessage();
    }

    if (empty($error)) {
        try {
            $pdo->exec("USE `$db_name` ;");

            // Creare folder uploads dacă nu există
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            $sql = file_get_contents('schema.sql');
            $pdo->exec($sql);

            $hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role, is_active) VALUES (?, ?, ?, 'admin', 1)");
            $stmt->execute([$admin_user, $hashed_pass, $admin_email]);

            $db_host_esc = addslashes($db_host);
            $db_name_esc = addslashes($db_name);
            $db_user_esc = addslashes($db_user);
            $db_pass_esc = addslashes($db_pass);

            $config_content = "<?php
define('DB_HOST', '$db_host_esc');
define('DB_NAME', '$db_name_esc');
define('DB_USER', '$db_user_esc');
define('DB_PASS', '$db_pass_esc');

try {
    \$pdo = new PDO(\"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=utf8mb4\", DB_USER, DB_PASS);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException \$e) {
    die(\"Connection failed: \" . \$e->getMessage());
}
";
            file_put_contents('includes/config.php', $config_content);
            $success = "Instalare reușită! Puteți acum să vă autentificați.";
        } catch (PDOException $e) {
            $error = "Eroare la crearea tabelelor: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalare Platformă</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Configurare Inițială</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
                <div class="mt-4 text-center">
                    <a href="index.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Mergi la Site</a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Baza de Date</h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Host (ex: localhost)</label>
                        <input type="text" name="db_host" value="localhost" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nume Bază de Date</label>
                        <input type="text" name="db_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Utilizator Bază de Date</label>
                        <input type="text" name="db_user" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Parolă Bază de Date</label>
                        <input type="password" name="db_pass" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                    </div>

                    <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 pt-4">Administrator</h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Admin</label>
                        <input type="email" name="admin_email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Utilizator Admin</label>
                        <input type="text" name="admin_user" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Parolă Admin</label>
                        <input type="password" name="admin_pass" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300 mt-6">
                        Instalează
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
