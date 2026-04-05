<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (is_logged_in()) {
    redirect('admin.php');
} else {
    redirect('login.php');
}
?>
