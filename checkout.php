<?php
session_start();
require_once 'config.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php?redirect=checkout.php?plan=" . ($_GET['plan'] ?? 'personal'));
    exit;
}

// Allowed plans
$allowed_plans = ['personal', 'pro', 'business'];
$plan = $_GET['plan'] ?? 'personal';
if (!in_array($plan, $allowed_plans)) {
    header("location: pricing.php");
    exit;
}

// Plan details
$plans_details = [
    'personal' => ['name' => 'Personal', 'price' => '€4.99/lună'],
    'pro' => ['name' => 'Pro', 'price' => '€9.99/lună'],
    'business' => ['name' => 'Business', 'price' => '€19.99/lună']
];
$current_plan = $plans_details[$plan];

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
    <title>Finalizează Comanda - AI Music Generator</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="checkout.css">
</head>
<body data-theme="<?php echo htmlspecialchars($theme); ?>" data-color="<?php echo htmlspecialchars($accent_color); ?>">
    <?php include 'header.php'; ?>

    <main>
        <div class="checkout-container">
            <h1>Finalizează Comanda</h1>
            <div class="checkout-grid">
                <div class="plan-summary">
                    <h2>Rezumatul Comenzii</h2>
                    <div class="summary-card">
                        <h3>Plan: <?php echo htmlspecialchars($current_plan['name']); ?></h3>
                        <p>Preț: <?php echo htmlspecialchars($current_plan['price']); ?></p>
                        <p>Accesați funcționalități premium și creșteți-vă numărul de generări lunare.</p>
                    </div>
                </div>
                <div class="payment-methods">
                    <h2>Alege Metoda de Plată</h2>
                    <div class="payment-tabs">
                        <button class="tab-link active" data-tab="card">Card Bancar</button>
                        <button class="tab-link" data-tab="qr">Cod QR</button>
                        <button class="tab-link" data-tab="paypal">PayPal</button>
                        <button class="tab-link" data-tab="sms">SMS</button>
                    </div>

                    <form action="payment_success.php" method="POST">
                        <input type="hidden" name="plan" value="<?php echo htmlspecialchars($plan); ?>">

                        <!-- Card Payment -->
                        <div id="card" class="payment-tab active">
                            <input type="text" placeholder="Număr Card" required>
                            <input type="text" placeholder="Nume Titular" required>
                            <div class="half-width">
                                <input type="text" placeholder="MM/YY" required>
                                <input type="text" placeholder="CVC" required>
                            </div>
                        </div>

                        <!-- QR Code Payment -->
                        <div id="qr" class="payment-tab">
                            <img src="images/qr-code-placeholder.png" alt="QR Code" style="max-width:200px; margin: 1rem auto; display:block;">
                            <p style="text-align:center;">Scanați codul QR cu aplicația dvs. bancară.</p>
                        </div>

                        <!-- PayPal Payment -->
                        <div id="paypal" class="payment-tab">
                             <p style="text-align:center; padding: 2rem 0;">Veți fi redirecționat către PayPal pentru a finaliza plata.</p>
                        </div>

                        <!-- SMS Payment -->
                        <div id="sms" class="payment-tab">
                            <input type="tel" placeholder="Număr de Telefon" required>
                             <p style="text-align:center; padding: 1rem 0;">Veți primi un SMS de confirmare.</p>
                        </div>

                        <button type="submit" class="pay-btn">Plătește Acum</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <script src="checkout.js"></script>
</body>
</html>
