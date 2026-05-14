<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendEmail($to, $subject, $body) {
    global $pdo;

    // Fetch mail settings
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'smtp_%' OR setting_key = 'use_smtp' OR setting_key = 'site_name'");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $mail = new PHPMailer(true);

    try {
        if (($settings['use_smtp'] ?? '0') === '1') {
            $mail->isSMTP();
            $mail->Host       = $settings['smtp_host'] ?? '';
            $mail->SMTPAuth   = true;
            $mail->Username   = $settings['smtp_user'] ?? '';
            $mail->Password   = $settings['smtp_pass'] ?? '';
            $mail->SMTPSecure = $settings['smtp_encryption'] ?? 'tls';
            $mail->Port       = $settings['smtp_port'] ?? 587;
        }

        $mail->setFrom($settings['smtp_user'] ?? 'no-reply@showcase.ro', $settings['site_name'] ?? 'Showcase');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
