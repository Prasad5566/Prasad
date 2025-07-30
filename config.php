<?php
/**
 * Configuration file for Portfolio Contact Form
 */

// Email Configuration
define('ENABLE_EMAIL_NOTIFICATIONS', true);
define('NOTIFICATION_EMAIL', 'maddikarprasad@gmail.com'); // Email where notifications will be sent
define('SENDER_EMAIL', 'noreply@' . $_SERVER['HTTP_HOST']); // Sender email address

// Database Configuration (optional)
define('ENABLE_DATABASE_STORAGE', false); // Set to true if you want to store messages in database
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_db');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// File Logging Configuration
define('ENABLE_FILE_LOGGING', true); // Set to true to log messages to file

// Security Configuration
define('ENABLE_HONEYPOT', true); // Enable honeypot field for bot protection
define('MAX_MESSAGE_LENGTH', 5000); // Maximum message length
define('MIN_MESSAGE_LENGTH', 10); // Minimum message length

// Rate Limiting
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_WINDOW', 300); // Time window in seconds (5 minutes)
define('RATE_LIMIT_MAX_SUBMISSIONS', 3); // Maximum submissions per window

// Spam Protection
define('ENABLE_WORD_FILTER', true);
$spam_words = array(
    'viagra', 'casino', 'lottery', 'winner', 'prize', 'free money',
    'click here', 'make money fast', 'work from home', 'no experience'
);

// File Upload Configuration (if you add file upload feature later)
define('ENABLE_FILE_UPLOADS', false);
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_FILE_TYPES', array('pdf', 'doc', 'docx', 'txt'));

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error Reporting (set to false in production)
if ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

/**
 * Utility Functions
 */

/**
 * Check if message contains spam words
 */
function containsSpam($text) {
    global $spam_words;
    if (!ENABLE_WORD_FILTER) {
        return false;
    }
    
    $text = strtolower($text);
    foreach ($spam_words as $word) {
        if (strpos($text, strtolower($word)) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Clean and validate input
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (basic validation)
 */
function isValidPhone($phone) {
    if (empty($phone)) {
        return true; // Phone is optional
    }
    return preg_match('/^[\+]?[0-9\s\-\(\)]{10,15}$/', $phone);
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
