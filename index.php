<?php
if (!file_exists('includes/config.php')) {
    header("Location: install.php");
    exit();
}
require_once 'includes/header.php';

// Simple Router
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/';
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace($base_path, '', $path);
$parts = explode('/', trim($path, '/'));

if (empty($parts[0]) || $parts[0] === 'index.php') {
    include 'views/home.php';
} elseif ($parts[0] === 'product' && isset($parts[1])) {
    $_GET['id'] = $parts[1];
    include 'views/product_details.php';
} elseif ($parts[0] === 'offers') {
    include 'views/offers_list.php';
} elseif ($parts[0] === 'page' && isset($parts[1])) {
    $_GET['slug'] = $parts[1];
    include 'views/dynamic_page.php';
} else {
    // If it's a real file (like login.php), don't route
    $file = $parts[0] . '.php';
    if (file_exists($file)) {
        include $file;
    } else {
        include 'views/home.php';
    }
}

require_once 'includes/footer.php';
?>
