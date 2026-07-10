<?php

header('Content-Type: application/json');


$db_host = 'localhost';
$db_name = 'portfolio_db';
$db_user = 'root';        // XAMPP default
$db_pass = '';            // XAMPP default (empty password)
$db_charset = 'utf8mb4';


$recipient_email = 'derickgilbert001@gmail.com';
$subject_prefix = '[Portfolio Contact]';


function jsonResponse($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method.');
}


$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';


$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required.';
} elseif (strlen($name) < 2) {
    $errors[] = 'Name must be at least 2 characters.';
} elseif (strlen($name) > 100) {
    $errors[] = 'Name is too long (max 100 characters).';
}

if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (empty($subject)) {
    $errors[] = 'Subject is required.';
} elseif (strlen($subject) > 200) {
    $errors[] = 'Subject is too long (max 200 characters).';
}

if (empty($message)) {
    $errors[] = 'Message is required.';
} elseif (strlen($message) < 10) {
    $errors[] = 'Message must be at least 10 characters.';
} elseif (strlen($message) > 5000) {
    $errors[] = 'Message is too long (max 5000 characters).';
}


if (!empty($_POST['website'])) {
    jsonResponse(false, 'Spam detected.');
}


session_start();
$now = time();
if (isset($_SESSION['last_contact_time']) && ($now - $_SESSION['last_contact_time']) < 60) {
    jsonResponse(false, 'Please wait a minute before sending another message.');
}

if (!empty($errors)) {
    jsonResponse(false, implode(' ', $errors));
}


$name_safe = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$email_safe = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$subject_safe = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
$message_safe = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');


$dbh = null;
$dbSaved = false;

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $dbh = new PDO($dsn, $db_user, $db_pass, $options);

    $sql = "INSERT INTO contact_messages (name, email, subject, message, ip_address, user_agent) 
            VALUES (:name, :email, :subject, :message, :ip, :ua)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([
        ':name'    => $name_safe,
        ':email'   => $email_safe,
        ':subject' => $subject_safe,
        ':message' => $message_safe,
        ':ip'      => $_SERVER['REMOTE_ADDR'] ?? null,
        ':ua'      => $_SERVER['HTTP_USER_AGENT'] ?? null,
    ]);
    $dbSaved = true;
} catch (PDOException $e) {
    // Database failed — will fall back to email + file
    error_log("Portfolio DB Error: " . $e->getMessage());
}


$email_subject = $subject_prefix . ' ' . $subject_safe;
$email_body = "You have received a new message from your portfolio website.\n\n";
$email_body .= "Name: $name_safe\n";
$email_body .= "Email: $email_safe\n";
$email_body .= "Subject: $subject_safe\n";
$email_body .= "Message:\n$message_safe\n\n";
$email_body .= "---\n";
$email_body .= "Sent from: " . ($_SERVER['HTTP_HOST'] ?? 'unknown') . "\n";
$email_body .= "IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";
$email_body .= "Date: " . date('Y-m-d H:i:s') . "\n";
if ($dbSaved) {
    $email_body .= "Stored in database: YES\n";
}

$headers = "From: $name_safe <$email_safe>\r\n";
$headers .= "Reply-To: $email_safe\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$mail_sent = mail($recipient_email, $email_subject, $email_body, $headers);


if ($mail_sent && $dbSaved) {
    $_SESSION['last_contact_time'] = $now;
    jsonResponse(true, 'Thank you! Your message has been sent and saved. I will get back to you soon.');
} elseif ($dbSaved) {
    $_SESSION['last_contact_time'] = $now;
    jsonResponse(true, 'Thank you! Your message has been saved. (Email delivery is currently unavailable, but I have received your message.)');
} elseif ($mail_sent) {
    $_SESSION['last_contact_time'] = $now;
    jsonResponse(true, 'Thank you! Your message has been sent successfully. I will get back to you soon.');
} else {
    // Ultimate fallback: save to file
    $log_file = 'contact_messages.txt';
    $log_entry = "=== Message Received " . date('Y-m-d H:i:s') . " ===\n";
    $log_entry .= "Name: $name_safe\nEmail: $email_safe\nSubject: $subject_safe\nMessage: $message_safe\n\n";

    if (file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX)) {
        $_SESSION['last_contact_time'] = $now;
        jsonResponse(true, 'Thank you! Your message has been saved. (Note: Both email and database are currently unavailable, but I have received your message.)');
    } else {
        jsonResponse(false, 'Sorry, there was an error sending your message. Please try again later or contact me directly via email.');
    }
}
