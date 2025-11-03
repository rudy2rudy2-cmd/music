<?php
session_start();
require_once '../config.php';

// Check if the user is a logged-in admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["is_admin"]) {
    http_response_code(403); // Forbidden
    echo json_encode(["status" => "error", "message" => "Access denied."]);
    exit;
}

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['theme']) && isset($data['color'])) {
        $theme = $data['theme'];
        $color = $data['color'];

        // Basic validation
        $allowed_themes = ['dark', 'light'];
        $allowed_colors = ['blue', 'red', 'yellow', 'green'];

        if (in_array($theme, $allowed_themes) && in_array($color, $allowed_colors)) {
            // Update theme
            $sql_theme = "UPDATE settings SET setting_value = ? WHERE setting_key = 'theme'";
            if ($stmt_theme = mysqli_prepare($link, $sql_theme)) {
                mysqli_stmt_bind_param($stmt_theme, "s", $theme);
                mysqli_stmt_execute($stmt_theme);
                mysqli_stmt_close($stmt_theme);
            } else {
                 http_response_code(500);
                 echo json_encode(["status" => "error", "message" => "Database error (theme)."]);
                 exit;
            }

            // Update accent color
            $sql_color = "UPDATE settings SET setting_value = ? WHERE setting_key = 'accent_color'";
            if ($stmt_color = mysqli_prepare($link, $sql_color)) {
                mysqli_stmt_bind_param($stmt_color, "s", $color);
                mysqli_stmt_execute($stmt_color);
                mysqli_stmt_close($stmt_color);
            } else {
                 http_response_code(500);
                 echo json_encode(["status" => "error", "message" => "Database error (color)."]);
                 exit;
            }

            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Settings saved."]);

        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["status" => "error", "message" => "Invalid theme or color."]);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "Missing parameters."]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

mysqli_close($link);
?>
