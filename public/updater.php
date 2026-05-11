<?php

/**
 * Platform Updater Script
 * This script is intended to be included in the platforms sold.
 * It connects to the main store to check for and install updates.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration - These would normally be set during installation
$config_file = __DIR__ . '/update_config.php';
if (file_exists($config_file)) {
    $config = include($config_file);
} else {
    $config = [
        'store_url' => 'http://localhost:8000', // Change to your store URL
        'license_key' => '',
        'current_version' => '1.0.0',
    ];
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'check') {
        checkUpdate($config);
    } elseif ($action === 'update') {
        performUpdate($config);
    }
}

function checkUpdate($config) {
    $url = $config['store_url'] . '/api/update/check';
    $data = [
        'license_key' => $config['license_key'],
        'current_version' => $config['current_version'],
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    header('Content-Type: application/json');
    if ($http_code === 200) {
        echo $response;
    } else {
        echo json_encode(['update_available' => false, 'message' => 'Error checking for updates.']);
    }
    exit;
}

function performUpdate($config) {
    // 1. Check for update first to get download URL
    $url = $config['store_url'] . '/api/update/check';
    $data = [
        'license_key' => $config['license_key'],
        'current_version' => $config['current_version'],
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (!$response || !isset($response['update_available']) || !$response['update_available']) {
        die("No update available or error connecting to store.");
    }

    $downloadUrl = $response['download_url'];
    $zipFile = __DIR__ . '/update.zip';

    // 2. Download the update
    file_put_contents($zipFile, fopen($downloadUrl, 'r'));

    if (!file_exists($zipFile) || filesize($zipFile) == 0) {
        die("Failed to download update ZIP.");
    }

    // 3. Extract the update
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo(__DIR__); // Extracts to current directory
        $zip->close();
        unlink($zipFile);

        // 4. Update the local version config
        $config['current_version'] = $response['latest_version'];
        $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        file_put_contents(__DIR__ . '/update_config.php', $content);

        echo "Update successful! Platform updated to version " . $response['latest_version'];
    } else {
        echo "Failed to open ZIP file.";
    }
    exit;
}

// Simple UI for the updater
?>
<!DOCTYPE html>
<html>
<head>
    <title>Platform Updater</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 2rem; border-radius: 1rem; shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { margin-top: 0; font-size: 1.5rem; }
        .status { margin: 1rem 0; padding: 1rem; border-radius: 0.5rem; background: #eef2ff; color: #4338ca; }
        button { background: #4f46e5; color: white; border: none; padding: 0.75rem 1rem; border-radius: 0.5rem; cursor: pointer; width: 100%; font-weight: bold; }
        button:hover { background: #4338ca; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Platform Updater</h1>
        <div class="status">
            Current Version: <strong><?php echo $config['current_version']; ?></strong>
        </div>
        <div id="message"></div>
        <button onclick="checkUpdate()">Check for Updates</button>
        <button id="updateBtn" onclick="performUpdate()" style="display:none; margin-top: 10px; background: #10b981;">Install Update</button>
    </div>

    <script>
        function checkUpdate() {
            fetch('?action=check')
                .then(r => r.json())
                .then(data => {
                    const msg = document.getElementById('message');
                    if (data.update_available) {
                        msg.innerHTML = `<p style="color: #10b981;">New version available: <strong>${data.latest_version}</strong></p>`;
                        document.getElementById('updateBtn').style.display = 'block';
                    } else {
                        msg.innerHTML = `<p style="color: #6b7280;">${data.message || 'You are up to date!'}</p>`;
                    }
                });
        }

        function performUpdate() {
            const msg = document.getElementById('message');
            msg.innerHTML = '<p>Updating... please wait.</p>';
            window.location.href = '?action=update';
        }
    </script>
</body>
</html>
