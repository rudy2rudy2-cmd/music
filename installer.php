<?php
/**
 * Platform Automated Installer
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration - Change this to your actual platform URL
define('MAIN_SERVER_URL', 'http://localhost');
define('VERIFY_API_ENDPOINT', MAIN_SERVER_URL . '/api/license/verify');
define('DOWNLOAD_ENDPOINT', MAIN_SERVER_URL . '/download/');

session_start();

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 1) {
        $license_key = $_POST['license_key'] ?? '';
        $domain = $_SERVER['HTTP_HOST'];

        // Verify license via API
        $ch = curl_init(VERIFY_API_ENDPOINT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'license_key' => $license_key,
            'domain' => $domain
        ]));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($http_code === 200 && isset($data['valid']) && $data['valid']) {
            $_SESSION['license_key'] = $license_key;
            $_SESSION['platform_name'] = $data['platform'];
            header('Location: installer.php?step=2');
            exit;
        } else {
            $error = $data['message'] ?? 'License verification failed. Make sure your domain is correct.';
        }
    }

    if ($step === 2) {
        $license_key = $_SESSION['license_key'] ?? '';

        if (!$license_key) {
            header('Location: installer.php?step=1');
            exit;
        }

        $download_url = DOWNLOAD_ENDPOINT . $license_key;
        $zip_file = 'platform_package.zip';

        // Download the package
        $file_content = @file_get_contents($download_url);

        if ($file_content === false) {
            $error = "Could not download the platform package. Please contact support.";
        } else {
            if (file_put_contents($zip_file, $file_content)) {
                // Extract the package
                $zip = new ZipArchive;
                if ($zip->open($zip_file) === TRUE) {
                    if ($zip->extractTo('./')) {
                        $zip->close();
                        unlink($zip_file);
                        $message = "Installation completed successfully! You can now access your platform.";
                        $step = 3; // Success step
                    } else {
                        $error = "Failed to extract the package. Check folder permissions.";
                    }
                } else {
                    $error = "Could not open the ZIP package.";
                }
            } else {
                $error = "Failed to save the package on your server.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-slate-200">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-magic text-indigo-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Platform Installer</h1>
            <p class="text-slate-500">Step <?php echo min($step, 2); ?> of 2</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-700 text-sm font-medium"><?php echo $error; ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($message): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-green-700 text-sm font-medium"><?php echo $message; ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($step === 1): ?>
            <form method="POST">
                <div class="mb-6">
                    <label class="block text-slate-700 text-sm font-bold mb-2">License Key</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-key"></i>
                        </span>
                        <input type="text" name="license_key" required placeholder="ABCD-1234-EFGH-IJKL"
                            class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                    <p class="mt-2 text-xs text-slate-400 text-center">Your domain <strong><?php echo $_SERVER['HTTP_HOST']; ?></strong> will be linked to this license.</p>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                    Verify & Continue
                </button>
            </form>
        <?php endif; ?>

        <?php if($step === 2): ?>
            <div class="text-center">
                <div class="p-4 bg-slate-50 rounded-xl mb-8 border border-slate-100">
                    <div class="text-xs text-slate-400 uppercase font-bold tracking-wider mb-1">Platform to Install</div>
                    <div class="text-lg font-bold text-slate-800"><?php echo $_SESSION['platform_name'] ?? 'Ready'; ?></div>
                </div>
                <form method="POST">
                    <button type="submit" class="w-full bg-green-600 text-white font-bold py-4 rounded-xl hover:bg-green-700 transition shadow-lg shadow-green-100">
                        <i class="fas fa-download mr-2"></i> Start Auto-Installation
                    </button>
                </form>
                <a href="installer.php?step=1" class="block mt-4 text-sm text-slate-400 hover:text-slate-600">Back to license</a>
            </div>
        <?php endif; ?>

        <?php if($step === 3): ?>
            <div class="text-center">
                <div class="mb-8">
                    <i class="fas fa-rocket text-5xl text-indigo-500 mb-4"></i>
                    <p class="text-slate-600">The platform is ready. You can now delete this installer file for security.</p>
                </div>
                <a href="/" class="block w-full bg-indigo-600 text-white font-bold py-4 rounded-xl hover:bg-indigo-700 transition">
                    Go to Homepage
                </a>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>
