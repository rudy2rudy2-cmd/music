<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$lock_file = __DIR__ . '/install.lock';
if (file_exists($lock_file)) {
    die("Installation already completed. Delete install.lock to restart.");
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? '127.0.0.1';
    $db_name = $_POST['db_name'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';

    // 1. Update .env file
    $env_path = __DIR__ . '/../.env';
    $env_example_path = __DIR__ . '/../.env.example';

    if (!file_exists($env_path)) {
        if (file_exists($env_example_path)) {
            copy($env_example_path, $env_path);
        } else {
            // Create a basic .env if example is missing
            $basic_env = "APP_NAME=Laravel\nAPP_ENV=local\nAPP_KEY=\nAPP_DEBUG=true\nAPP_URL=http://localhost\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=\nDB_USERNAME=\nDB_PASSWORD=\n";
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

    // 2. Check for vendor directory
    chdir(__DIR__ . '/..');

    // Check PHP version
    if (version_compare(PHP_VERSION, '8.2.0', '<')) {
        $error = "PHP version 8.2 or higher is required. Current version: " . PHP_VERSION;
    }

    if (empty($error) && !file_exists('vendor/autoload.php')) {
        $composer_output = shell_exec('composer install --no-dev --ignore-platform-reqs 2>&1');
        if (!file_exists('vendor/autoload.php')) {
            $error = "Dependencies missing! 'vendor' folder not found and 'composer install' failed. <br> Output: <pre>" . htmlspecialchars($composer_output) . "</pre> Please run 'composer install' manually on the server.";
        }
    }

    if (empty($error)) {
        // 3. Run Migrations and Create Admin
        shell_exec('php artisan key:generate --force');
        shell_exec('php artisan config:clear');

        $output = shell_exec('php artisan migrate --force 2>&1');

        if (strpos($output, 'Error') !== false || strpos($output, 'Exception') !== false || strpos($output, 'Fatal error') !== false) {
            $error = "Migration failed: " . nl2br(htmlspecialchars($output));
        } else {
            // Create Admin User
            $admin_email = 'admin@example.com';
            $admin_pass = 'rudyrudy1989';
            $admin_name = 'Admin';

            $tinker_cmd = sprintf(
                'php artisan tinker --execute="\$user = App\Models\User::updateOrCreate([\'email\' => \'%s\'], [\'name\' => \'%s\', \'password\' => Hash::make(\'%s\')]);"',
                $admin_email, $admin_name, $admin_pass
            );
            shell_exec($tinker_cmd);

            file_put_contents($lock_file, date('Y-m-d H:i:s'));
            $message = "Installation successful! <br> Admin User: admin@example.com <br> Password: rudyrudy1989 <br><br> <a href='/admin' class='text-indigo-600 font-bold'>Go to Admin Panel</a>";
        }
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
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-lg border border-slate-200">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-server text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">System Installation</h1>
            <p class="text-slate-500">Configure your database to get started</p>
            <p class="text-xs text-slate-400">PHP Version: <?php echo PHP_VERSION; ?></p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <p class="text-red-700 text-sm"><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <?php if($message): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 text-center">
                <p class="text-green-700 text-sm"><?php echo $message; ?></p>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="grid grid-cols-1 gap-4 mb-6">
                    <div>
                        <label class="block text-slate-700 text-sm font-bold mb-2">DB Host</label>
                        <input type="text" name="db_host" value="127.0.0.1" required class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-700 text-sm font-bold mb-2">DB Name</label>
                        <input type="text" name="db_name" placeholder="my_database" required class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-700 text-sm font-bold mb-2">DB Username</label>
                        <input type="text" name="db_user" placeholder="root" required class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-700 text-sm font-bold mb-2">DB Password</label>
                        <input type="password" name="db_pass" placeholder="••••••••" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>

                <div class="bg-indigo-50 p-4 rounded-xl mb-6 flex items-start">
                    <i class="fas fa-info-circle text-indigo-500 mt-1 mr-3"></i>
                    <p class="text-xs text-indigo-700 leading-relaxed">
                        By clicking install, we will configure the .env file, try to install dependencies if missing, run migrations, and create the admin account with the password <strong>rudyrudy1989</strong>.
                    </p>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition">
                    Start Installation
                </button>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>
