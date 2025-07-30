<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include configuration
require_once 'config.php';

// Initialize response
$response = array('success' => false, 'message' => '');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Validate and sanitize input data
$name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : '';
$phone = isset($_POST['phone']) ? trim(htmlspecialchars($_POST['phone'])) : '';
$subject = isset($_POST['subject']) ? trim(htmlspecialchars($_POST['subject'])) : '';
$message = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'])) : '';

// Validation
$errors = array();

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($subject)) {
    $errors[] = 'Subject is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

// Check for errors
if (!empty($errors)) {
    $response['message'] = implode(', ', $errors);
    echo json_encode($response);
    exit;
}

// Rate limiting (simple implementation)
session_start();
$current_time = time();
$time_limit = 300; // 5 minutes
$max_submissions = 3;

if (!isset($_SESSION['submissions'])) {
    $_SESSION['submissions'] = array();
}

// Clean old submissions
$_SESSION['submissions'] = array_filter($_SESSION['submissions'], function($time) use ($current_time, $time_limit) {
    return ($current_time - $time) < $time_limit;
});

// Check if user has exceeded submission limit
if (count($_SESSION['submissions']) >= $max_submissions) {
    $response['message'] = 'Too many submissions. Please wait before sending another message.';
    echo json_encode($response);
    exit;
}

// Add current submission time
$_SESSION['submissions'][] = $current_time;

try {
    // Database connection (if you want to store messages)
    if (ENABLE_DATABASE_STORAGE) {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            )
        );

        // Create messages table if it doesn't exist
        $create_table_sql = "
            CREATE TABLE IF NOT EXISTS contact_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(20),
                subject VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $pdo->exec($create_table_sql);

        // Insert message into database
        $stmt = $pdo->prepare("
            INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $stmt->execute([$name, $email, $phone, $subject, $message, $ip_address, $user_agent]);
    }

    // Send email notification
    if (ENABLE_EMAIL_NOTIFICATIONS) {
        $success = sendEmail($name, $email, $phone, $subject, $message);
        
        if (!$success) {
            throw new Exception('Failed to send email notification');
        }
    }

    // Log the message to file
    if (ENABLE_FILE_LOGGING) {
        logMessage($name, $email, $phone, $subject, $message);
    }

    $response['success'] = true;
    $response['message'] = 'Thank you for your message! I will get back to you soon.';

} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage());
    $response['message'] = 'An error occurred while processing your message. Please try again later.';
}

echo json_encode($response);

/**
 * Send email notification
 */
function sendEmail($name, $email, $phone, $subject, $message) {
    if (!ENABLE_EMAIL_NOTIFICATIONS) {
        return true;
    }

    $to = NOTIFICATION_EMAIL;
    $email_subject = "Portfolio Contact: " . $subject;
    
    $email_body = "
    New contact form submission:
    
    Name: {$name}
    Email: {$email}
    Phone: {$phone}
    Subject: {$subject}
    
    Message:
    {$message}
    
    ---
    Sent from: {$_SERVER['HTTP_HOST']}
    IP Address: {$_SERVER['REMOTE_ADDR']}
    Time: " . date('Y-m-d H:i:s') . "
    ";
    
    $headers = array(
        'From' => SENDER_EMAIL,
        'Reply-To' => $email,
        'X-Mailer' => 'PHP/' . phpversion(),
        'Content-Type' => 'text/plain; charset=UTF-8'
    );
    
    $header_string = '';
    foreach ($headers as $key => $value) {
        $header_string .= $key . ': ' . $value . "\r\n";
    }
    
    return mail($to, $email_subject, $email_body, $header_string);
}

/**
 * Log message to file
 */
function logMessage($name, $email, $phone, $subject, $message) {
    if (!ENABLE_FILE_LOGGING) {
        return;
    }

    $log_dir = dirname(__FILE__) . '/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/contact_messages.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    $log_entry = "[{$timestamp}] IP: {$ip} | Name: {$name} | Email: {$email} | Phone: {$phone} | Subject: {$subject} | Message: " . str_replace("\n", " ", $message) . "\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}
?>
