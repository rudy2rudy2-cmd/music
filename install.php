<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$lock_file = __DIR__ . '/install.lock';
if (file_exists($lock_file)) {
    die("Installation already completed. Delete install.lock to restart.");
}

$message = '';
$error = '';

// Check Requirements
$requirements = [
    'PHP >= 8.2' => version_compare(PHP_VERSION, '8.2.0', '>='),
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

    $admin_email = $_POST['admin_email'] ?? 'admin@example.com';
    $admin_pass = $_POST['admin_pass'] ?? 'password';

    try {
        // Test Database Connection
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // 1. Update .env file
        $env_path = __DIR__ . '/.env';
        $env_example_path = __DIR__ . '/.env.example';

        if (!file_exists($env_path)) {
            if (file_exists($env_example_path)) {
                copy($env_example_path, $env_path);
            } else {
                $basic_env = "APP_NAME=\"Platform Store\"\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=http://localhost\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=\nDB_USERNAME=\nDB_PASSWORD=\n";
                file_put_contents($env_path, $basic_env);
            }
        }

        $env_content = file_get_contents($env_path);
        $env_content = preg_replace('/DB_HOST=.*/', 'DB_HOST=' . $db_host, $env_content);
        $env_content = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=' . $db_name, $env_content);
        $env_content = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=' . $db_user, $env_content);
        $env_content = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=' . $db_pass, $env_content);
        $env_content = preg_replace('/DB_CONNECTION=.*/', 'DB_CONNECTION=mysql', $env_content);
        file_put_contents($env_path, $env_content);

        // 2. Check for dependencies
        if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
            $composer_output = shell_exec('composer install --no-dev --optimize-autoloader 2>&1');
            if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
                throw new Exception("Dependencies missing and 'composer install' failed. Please run 'composer install' manually. <br> Output: <pre>$composer_output</pre>");
            }
        }

        // 3. Manually delete Laravel's cache files
        $cache_files = ['config.php', 'routes.php', 'services.php', 'packages.php'];
        foreach ($cache_files as $file) {
            $path = __DIR__ . '/bootstrap/cache/' . $file;
            if (file_exists($path)) { @unlink($path); }
        }

        // 4. Bootstrap Laravel and Run Commands internally
        require __DIR__ . '/vendor/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

        // Run key generate
        $kernel->call('key:generate', ['--force' => true]);

        // Run migrations
        $status = $kernel->call('migrate', ['--force' => true]);

        if ($status !== 0) {
            throw new Exception("Migration failed with exit code $status.");
        }

        // 5. Create Admin using Eloquent directly
        \App\Models\User::updateOrCreate(
            ['email' => $admin_email],
            [
                'name' => 'Administrator',
                'password' => \Illuminate\Support\Facades\Hash::make($admin_pass),
                'role' => 'admin'
            ]
        );

        file_put_contents($lock_file, date('Y-m-d H:i:s'));
        $message = "Installation completed successfully!";

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
    <title>Setup - Platform Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">

    <div class="bg-white p-10 rounded-3xl shadow-2xl w-full max-w-2xl border border-slate-100">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-indigo-100">
                <i class="fas fa-magic text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-900">Platform Installer</h1>
            <p class="text-slate-500 mt-2">Professional software deployment wizard</p>
        </div>

        <?php if($message): ?>
            <div class="bg-emerald-50 border border-emerald-100 p-8 rounded-3xl text-center">
                <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check text-white text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-emerald-900 mb-2"><?php echo $message; ?></h2>
                <p class="text-emerald-700 mb-8">Your platform is ready to go. You can now login to the admin panel.</p>
                <a href="/admin/login" class="inline-block bg-emerald-600 text-white font-black px-10 py-4 rounded-2xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-100">
                    Go to Admin Panel
                </a>
            </div>
        <?php else: ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <h3 class="font-bold text-slate-900 mb-4 flex items-center">
                        <i class="fas fa-list-check mr-2 text-indigo-500"></i> Server Requirements
                    </h3>
                    <ul class="space-y-3">
                        <?php foreach($requirements as $name => $met): ?>
                            <?php $is_met = is_bool($met) ? $met : $met(); ?>
                            <li class="flex items-center justify-between text-sm">
                                <span class="<?php echo $is_met ? 'text-slate-600' : 'text-rose-600 font-bold'; ?>"><?php echo $name; ?></span>
                                <i class="fas <?php echo $is_met ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500'; ?>"></i>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="flex flex-col justify-center">
                    <?php if($error): ?>
                        <div class="bg-rose-50 border border-rose-100 p-4 rounded-2xl mb-6">
                            <p class="text-rose-700 text-xs font-medium"><?php echo $error; ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if(!$all_met): ?>
                        <div class="bg-amber-50 border border-amber-100 p-6 rounded-2xl">
                            <h4 class="font-bold text-amber-900 mb-2">Attention!</h4>
                            <p class="text-amber-700 text-xs leading-relaxed mb-4">Some server requirements are not met. Please fix them to continue the installation.</p>

                            <?php if(!function_exists('shell_exec')): ?>
                                <div class="p-3 bg-white/50 rounded-xl border border-amber-200">
                                    <p class="text-[10px] text-amber-800 font-bold uppercase tracking-tighter mb-1">How to fix shell_exec:</p>
                                    <p class="text-[10px] text-amber-700 leading-tight">Remove <code>shell_exec</code> from <code>disable_functions</code> in your php.ini or hosting panel (e.g. cPanel/DirectAdmin/CloudPanel).</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-2xl text-indigo-700">
                            <p class="text-xs font-medium leading-relaxed">All requirements met! Fill in your database details below to complete the setup.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($all_met): ?>
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-slate-700 text-xs font-black uppercase tracking-widest mb-2">DB Host</label>
                            <input type="text" name="db_host" value="127.0.0.1" required class="w-full px-5 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-slate-700 text-xs font-black uppercase tracking-widest mb-2">DB Name</label>
                            <input type="text" name="db_name" placeholder="database_name" required class="w-full px-5 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-slate-700 text-xs font-black uppercase tracking-widest mb-2">DB Username</label>
                            <input type="text" name="db_user" placeholder="root" required class="w-full px-5 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-slate-700 text-xs font-black uppercase tracking-widest mb-2">DB Password</label>
                            <input type="password" name="db_pass" placeholder="••••••••" class="w-full px-5 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100">
                         <h4 class="font-bold text-slate-900 mb-4">Admin Account</h4>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-slate-700 text-xs font-black uppercase tracking-widest mb-2">Admin Email</label>
                                <input type="email" name="admin_email" value="admin@example.com" required class="w-full px-5 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-slate-700 text-xs font-black uppercase tracking-widest mb-2">Admin Password</label>
                                <input type="password" name="admin_pass" value="password" required class="w-full px-5 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            </div>
                         </div>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white font-black py-4 rounded-2xl hover:bg-indigo-700 transition shadow-xl shadow-indigo-100 active:scale-95">
                        Finish Installation
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</body>
</html>
