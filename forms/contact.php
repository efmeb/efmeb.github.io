<?php
// Standalone contact handler (no external library required)
// Returns "OK" on success to satisfy assets/vendor/php-email-form/validate.js

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

function clean($v) {
  return htmlspecialchars(trim($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$name    = clean($_POST['name'] ?? '');
$email   = clean($_POST['email'] ?? '');
$subject = clean($_POST['subject'] ?? 'Message du site EFMEB');
$message = clean($_POST['message'] ?? '');

if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message) {
  http_response_code(400);
  exit('Veuillez remplir correctement tous les champs.');
}

$to = 'info@efmeb.com';

// Build email
$body  = "Nom: $name\n";
$body .= "Email: $email\n";
$body .= "Objet: $subject\n\n";
$body .= "Message:\n$message\n";

// Headers
$headers  = "From: EFMEB <info@efmeb.com>\r\n";
$headers .= "Reply-To: $name <$email>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send (native PHP mail). If your host blocks mail(), switch to SMTP/PHPMailer.
$sent = @mail($to, $subject, $body, $headers);

// Return what validate.js expects
if ($sent) {
  echo 'OK';
} else {
  http_response_code(500);
  echo "Erreur lors de l’envoi. Essayez plus tard ou écrivez-nous directement à info@efmeb.com.";
}