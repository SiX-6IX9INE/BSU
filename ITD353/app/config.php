<?php
/**
 * app/config.php
 * Central configuration – DB, paths, env flags
 */

// -------------------------------------------------------
// Environment  (set to false in production)
// -------------------------------------------------------
define('APP_DEBUG', true);
define('APP_NAME', 'Community Issue Reporter');
define('APP_VERSION', '1.0.0');

// -------------------------------------------------------
// Base URL  – ปรับตามที่ deploy จริง
// -------------------------------------------------------
define('BASE_URL', 'http://localhost/bsu/ITD353');

// -------------------------------------------------------
// Load .env file (ROOT_PATH is not defined yet, use dirname)
// -------------------------------------------------------
(function () {
    $envFile = dirname(__DIR__) . '/.env';
    if (!file_exists($envFile)) return;
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $key = trim($key);
        $val = trim($val);
        // Strip optional surrounding quotes
        $val = trim($val, '"\"');
        if (!array_key_exists($key, $_ENV) && !array_key_exists($key, $_SERVER)) {
            $_ENV[$key]    = $val;
            $_SERVER[$key] = $val;
            putenv("$key=$val");
        }
    }
})();

// -------------------------------------------------------
// Database  (values from .env, fallback to defaults)
// -------------------------------------------------------
define('DB_HOST',    $_ENV['DB_HOST'] ?? 'localhost');
define('DB_PORT',    $_ENV['DB_PORT'] ?? '3306');
define('DB_NAME',    $_ENV['DB_NAME'] ?? 'community_issues');
define('DB_USER',    $_ENV['DB_USER'] ?? 'root');
define('DB_PASS',    $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

// -------------------------------------------------------
// Paths (absolute filesystem)
// -------------------------------------------------------
define('ROOT_PATH',    dirname(__DIR__));           // ITD353/
define('APP_PATH',     ROOT_PATH . '/app');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('UPLOAD_PATH',  STORAGE_PATH . '/uploads');
define('UPLOAD_URL',   BASE_URL . '/../storage/uploads'); // URL สำหรับ img src

// -------------------------------------------------------
// Upload limits
// -------------------------------------------------------
define('MAX_UPLOAD_SIZE',  5 * 1024 * 1024);   // 5 MB per image
define('MAX_UPLOAD_COUNT', 3);                  // สูงสุด 3 รูปต่อ issue
define('ALLOWED_MIME',     ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// -------------------------------------------------------
// Rate limit
// -------------------------------------------------------
define('RATE_LIMIT_MAX',    5);    // ครั้ง
define('RATE_LIMIT_WINDOW', 600);  // วินาที (10 นาที)

// -------------------------------------------------------
// Session
// -------------------------------------------------------
define('SESSION_LIFETIME', 86400 * 7); // 7 วัน

// -------------------------------------------------------
// PDO Singleton
// -------------------------------------------------------
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                die('<pre style="color:red">Database Error: ' . $e->getMessage() . '</pre>');
            }
            die('Database connection error. Please try again later.');
        }
    }
    return $pdo;
}

// -------------------------------------------------------
// Start session (once)
// -------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'secure'   => false,  // set true on HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
