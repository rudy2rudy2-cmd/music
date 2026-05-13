<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Absolute first check: Is the system installed?
$isInstalled = file_exists(__DIR__ . '/install.lock');

// 2. If not installed, handle redirection or direct execution of the installer
if (!$isInstalled) {
    $uri = $_SERVER['REQUEST_URI'];

    // If they are trying to access the installer, let them.
    // If the web server is correctly configured, it will serve install.php directly.
    // If it's the built-in server sending them here, we must not boot Laravel.
    if (str_contains($uri, 'install.php')) {
        // Just let the process continue or include the file if we have to.
        // But usually, we just want to avoid the code below.
        if (file_exists(__DIR__ . '/install.php')) {
            require __DIR__ . '/install.php';
            exit;
        }
    } else {
        // Not the installer? Redirect to it.
        header('Location: /install.php');
        exit;
    }
}

// -------------------------------------------------------------------------
// Standard Laravel Bootstrapping (Only happens if installed)
// -------------------------------------------------------------------------

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
