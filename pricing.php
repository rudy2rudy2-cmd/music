<?php
session_start();
require_once 'config.php';

// Fetch theme settings for consistent styling
$theme = 'dark';
$accent_color = 'blue';
$sql = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('theme', 'accent_color')";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['setting_key'] == 'theme') $theme = $row['setting_value'];
        if ($row['setting_key'] == 'accent_color') $accent_color = $row['setting_value'];
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planuri de Prețuri - AI Music Generator</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="pricing.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body data-theme="<?php echo htmlspecialchars($theme); ?>" data-color="<?php echo htmlspecialchars($accent_color); ?>">
    <?php include 'header.php'; ?>

    <main>
        <div class="pricing-container">
            <h1>Alege Planul Potrivit Pentru Tine</h1>
            <p>Deblochează mai multă putere și creativitate cu planurile noastre premium.</p>

            <div class="pricing-grid">
                <!-- Personal Plan -->
                <div class="plan-card">
                    <h2>Personal</h2>
                    <p class="price">€4.99<span>/lună</span></p>
                    <ul>
                        <li>10 Generări de muzică pe lună</li>
                        <li>Calitate audio standard</li>
                        <li>Licență personală</li>
                        <li>Suport prin email</li>
                    </ul>
                    <a href="checkout.php?plan=personal" class="plan-btn">Alege Planul</a>
                </div>

                <!-- Pro Plan -->
                <div class="plan-card featured">
                    <span class="featured-badge">Recomandat</span>
                    <h2>Pro</h2>
                    <p class="price">€9.99<span>/lună</span></p>
                    <ul>
                        <li>100 Generări de muzică pe lună</li>
                        <li>Calitate audio înaltă (HD)</li>
                        <li>Licență comercială</li>
                        <li>Suport prioritar prin email</li>
                    </ul>
                    <a href="checkout.php?plan=pro" class="plan-btn">Alege Planul</a>
                </div>

                <!-- Business Plan -->
                <div class="plan-card">
                    <h2>Business</h2>
                    <p class="price">€19.99<span>/lună</span></p>
                    <ul>
                        <li>Generări de muzică nelimitate</li>
                        <li>Calitate audio Lossless</li>
                        <li>Licență comercială extinsă</li>
                        <li>Suport dedicat 24/7</li>
                    </ul>
                    <a href="checkout.php?plan=business" class="plan-btn">Alege Planul</a>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
