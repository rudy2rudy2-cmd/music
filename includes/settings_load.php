<?php
// Global settings fetch
$stmt_settings_load = $pdo->query("SELECT * FROM settings");
$site_settings = [];
while ($row = $stmt_settings_load->fetch()) {
    $site_settings[$row['setting_key']] = $row['setting_value'];
}

// Set Timezone
date_default_timezone_set($site_settings['timezone'] ?? 'Europe/Bucharest');

// Derived variables
$theme = $site_settings['theme'] ?? 'blue';
$logo = !empty($site_settings['logo_path']) ? $site_settings['logo_path'] : '';
$copyright = $site_settings['copyright'] ?? 'Copyright 2026 Autor Stoian Rudolf';
$site_title = $site_settings['site_title'] ?? 'HotelDefects';
$logo_size = $site_settings['logo_size'] ?? '32';
$report_font_size = $site_settings['report_font_size'] ?? '14';
$subtask_font_size = $site_settings['subtask_font_size'] ?? '10';
$report_text_color_val = $site_settings['report_text_color'] ?? 'white';

$color_map = [
    'white' => '#ffffff',
    'red' => '#ef4444',
    'green' => '#22c55e',
    'orange' => '#f97316'
];
$report_text_color = $color_map[$report_text_color_val] ?? '#ffffff';
?>
