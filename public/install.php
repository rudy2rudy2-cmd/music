<?php

/**
 * Professional Platform Installer
 * Completely isolated from Laravel bootstrapping during the installation request
 * to prevent dependency injection errors on fresh installs.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$base_path = dirname(__DIR__);
$lock_file = $base_path . '/public/install.lock';

if (file_exists($lock_file)) {
    header("Location: /");
    exit;
}

$message = '';
$error = '';
$console_log = '';

// Check Requirements
$requirements = [
    'PHP >= 8.3' => version_compare(PHP_VERSION, '8.3.0', '>='),
    'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
    'BCMath Extension' => extension_loaded('bcmath'),
    'Ctype Extension' => extension_loaded('ctype'),
    'JSON Extension' => extension_loaded('json'),
    'Mbstring Extension' => extension_loaded('mbstring'),
    'OpenSSL Extension' => extension_loaded('openssl'),
    'PDO Extension' => extension_loaded('pdo'),
    'Tokenizer Extension' => extension_loaded('tokenizer'),
    'XML Extension' => extension_loaded('xml'),
    'Zip Extension' => extension_loaded('zip'),
    'Execution Functions (shell_exec)' => function_exists('shell_exec'),
];

$all_met = true;
foreach ($requirements as $req) {
    if ($req === false) {
        $all_met = false;
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $all_met) {
    $db_host = $_POST['db_host'] ?? '127.0.0.1';
    $db_name = $_POST['db_name'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';

    $admin_user = $_POST['admin_user'] ?? 'admin';
    $admin_pass = $_POST['admin_pass'] ?? 'password';

    try {
        // 0. Test Database Connection
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
        try {
            $pdo = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5
            ]);
        } catch (PDOException $e) {
            throw new Exception("DATABASE CONNECTION FAILED: " . $e->getMessage());
        }

        // 1. Create .env file
        $env_path = $base_path . '/.env';
        $env_example_path = $base_path . '/.env.example';

        if (file_exists($env_example_path)) {
            copy($env_example_path, $env_path);
        } else {
            $basic_env = "APP_NAME=\"Software Platform\"\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=http://localhost\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=\nDB_USERNAME=\nDB_PASSWORD=\n";
            file_put_contents($env_path, $basic_env);
        }

        $env_content = file_get_contents($env_path);
        $env_content = preg_replace('/DB_HOST=.*/', 'DB_HOST=' . $db_host, $env_content);
        $env_content = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=' . $db_name, $env_content);
        $env_content = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=' . $db_user, $env_content);
        $env_content = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD="' . addslashes($db_pass) . '"', $env_content);
        file_put_contents($env_path, $env_content);

        // 2. Clear bootstrap cache
        foreach (glob("$base_path/bootstrap/cache/*.php") as $file) {
            @unlink($file);
        }

        // 3. Run artisan commands in separate processes
        $php = PHP_BINARY;
        $artisan = 'artisan';
        $cmd_base = "cd " . escapeshellarg($base_path) . " && $php $artisan ";

        $console_log .= "Generating Application Key...\n";
        $console_log .= shell_exec($cmd_base . "key:generate --force 2>&1");

        $console_log .= "\nRunning Database Migrations...\n";
        $migration_output = shell_exec($cmd_base . "migrate --force 2>&1");
        $console_log .= $migration_output;

        if (str_contains(strtolower($migration_output), 'error') || str_contains($migration_output, 'Exception')) {
             throw new Exception("MIGRATION FAILED. Check the console log for details.");
        }

        // 4. Create Admin User via direct SQL to avoid any bootstrapping
        $console_log .= "\nCreating Administrator account...\n";
        $hashed_pass = password_hash($admin_pass, PASSWORD_BCRYPT);
        $now = date('Y-m-d H:i:s');

        $sql = "INSERT INTO users (name, username, password, role, created_at, updated_at)
                VALUES ('Administrator', ?, ?, 'admin', ?, ?)
                ON DUPLICATE KEY UPDATE password = ?, updated_at = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$admin_user, $hashed_pass, $now, $now, $hashed_pass, $now]);

        $console_log .= "Admin account setup complete.\n";

        // 5. Finalize
        file_put_contents($lock_file, $now);
        $message = "Installation successful! The platform is now ready.";

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-2xl w-full max-w-2xl border border-slate-100">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-indigo-100">
                <i class="fas fa-magic text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">System Setup</h1>
            <p class="text-slate-500 mt-2 font-medium">Configure your platform environment</p>
        </div>

        <?php if($message): ?>
            <div class="bg-emerald-50 border border-emerald-100 p-8 rounded-3xl text-center">
                <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-100">
                    <i class="fas fa-check text-white text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-emerald-900 mb-2"><?php echo $message; ?></h2>
                <p class="text-emerald-700 mb-8 font-medium">You can now proceed to the management panel.</p>
                <a href="/admin/login" class="inline-block bg-indigo-600 text-white font-black px-12 py-4 rounded-2xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                    Go to Admin Panel
                </a>
            </div>
        <?php else: ?>

            <?php if($error): ?>
                <div class="bg-rose-50 border border-rose-100 p-6 rounded-2xl mb-8">
                    <p class="text-rose-700 text-sm font-bold mb-4"><?php echo $error; ?></p>
                    <?php if($console_log): ?>
                        <pre class="bg-slate-900 text-slate-300 p-4 rounded-xl text-[10px] overflow-x-auto max-h-40"><?php echo htmlspecialchars($console_log); ?></pre>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <h3 class="font-bold text-slate-900 mb-4 flex items-center text-sm uppercase tracking-wider">
                        <i class="fas fa-server mr-2 text-indigo-500"></i> Requirements
                    </h3>
                    <ul class="space-y-2">
                        <?php foreach($requirements as $name => $is_met): ?>
                            <li class="flex items-center justify-between text-xs">
                                <span class="<?php echo $is_met ? 'text-slate-500' : 'text-rose-600 font-bold'; ?>"><?php echo $name; ?></span>
                                <i class="fas <?php echo $is_met ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-300'; ?>"></i>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="flex flex-col justify-center bg-indigo-50 p-6 rounded-3xl border border-indigo-100">
                    <p class="text-indigo-700 text-xs leading-relaxed font-medium">
                        <i class="fas fa-info-circle mr-1"></i> Ensure your database is created and empty before starting. This setup will handle all migrations and account creation.
                    </p>
                </div>
            </div>

            <?php if($all_met): ?>
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-slate-700 text-xs font-bold uppercase ml-1 tracking-widest">DB Host</label>
                            <input type="text" name="db_host" value="127.0.0.1" required class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition shadow-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-slate-700 text-xs font-bold uppercase ml-1 tracking-widest">DB Name</label>
                            <input type="text" name="db_name" required class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition shadow-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-slate-700 text-xs font-bold uppercase ml-1 tracking-widest">DB User</label>
                            <input type="text" name="db_user" required class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition shadow-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-slate-700 text-xs font-bold uppercase ml-1 tracking-widest">DB Password</label>
                            <input type="password" name="db_pass" class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition shadow-sm">
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100">
                         <h4 class="font-bold text-slate-900 mb-4 text-xs uppercase tracking-widest">Initial Admin User</h4>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-slate-700 text-xs font-bold uppercase ml-1 tracking-widest">Username</label>
                                <input type="text" name="admin_user" value="admin" required class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition shadow-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-slate-700 text-xs font-bold uppercase ml-1 tracking-widest">Password</label>
                                <input type="password" name="admin_pass" value="password" required class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition shadow-sm">
                            </div>
                         </div>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 text-white font-black py-4 rounded-2xl hover:bg-slate-800 transition shadow-xl active:scale-95">
                        Initialize Platform
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</body>
</html>
