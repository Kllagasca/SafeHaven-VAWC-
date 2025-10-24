<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Autoload PHPMailer classes
require __DIR__ . "/vendor/autoload.php";

try {
    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    // Enable SMTP
    $mail->isSMTP();

    // Enable SMTP authentication
    $mail->SMTPAuth = true;

    // SMTP server configuration
    $mail->Host = getenv('SMTP_HOST') ?: "smtp.gmail.com"; // Use environment variable or fallback
    $mail->Port = getenv('SMTP_PORT') ?: 587; // Use environment variable or fallback
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    // SMTP credentials
    $mail->Username = getenv('SMTP_USER') ?: "gfordariola@gmail.com"; // Use environment variable or fallback
    $mail->Password = getenv('SMTP_PASS') ?: "agay oycm cewu osoi"; // Use environment variable or fallback

    // Enable HTML in emails
    $mail->isHTML(true);

    // Return the configured mail object
    return $mail;

} catch (Exception $e) {
    // Handle exceptions
    die("Mailer Error: " . $e->getMessage());
}
