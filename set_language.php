<?php
// Prevent direct access to the file
defined('SITE_PATH') or die('Direct access is not allowed.');

// Environment Configuration
define('ENVIRONMENT', 'development'); // Options: development, production, testing

// Database Configuration
class DatabaseConfig {
    private static $instance = null;
    private $connection;

    private function __construct() {
        // Prevent direct instantiation
        $this->initializeConnection();
    }

    private function initializeConnection() {
        $host = ENVIRONMENT === 'production' ? 'prod-host' : 'localhost';
        $username = ENVIRONMENT === 'production' ? 'prod-username' : 'root';
        $password = ENVIRONMENT === 'production' ? 'prod-password' : '';
        $database = 'project';

        try {
            $this->connection = new PDO(
                "mysql:host={$host};dbname={$database};charset=utf8mb4", 
                $username, 
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            // Log error securely
            error_log('Database Connection Error: ' . $e->getMessage());
            die('Database connection failed. Please try again later.');
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}

// Security Configuration
class SecurityConfig {
    // Password hashing settings
    public const PASSWORD_ALGORITHM = PASSWORD_ARGON2ID;
    public const PASSWORD_OPTIONS = [
        'memory_cost' => 1024 * 64,
        'time_cost' => 4,
        'threads' => 3
    ];

    // Session configuration
    public static function sessionConfig() {
        // Strict session security settings
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        
        // HTTPS only in production
        if (ENVIRONMENT === 'production') {
            ini_set('session.cookie_secure', 1);
        }

        // Session timeout (1 hour)
        ini_set('session.gc_maxlifetime', 3600);
    }

    // Generate CSRF Token
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Validate CSRF Token
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
}

// Logging Configuration
class LoggerConfig {
    private static $logFile;

    public static function initialize() {
        $logDir = __DIR__ . '/logs/';
        
        // Create logs directory if it doesn't exist
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        self::$logFile = $logDir . 'app_' . date('Y-m-d') . '.log';
    }

    public static function log($message, $level = 'INFO') {
        if (self::$logFile === null) {
            self::initialize();
        }

        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        
        error_log($logEntry, 3, self::$logFile);
    }
}

// Application Configuration
class AppConfig {
    public const APP_NAME = 'Ministry Portal';
    public const APP_VERSION = '1.0.0';
    public const DEFAULT_LANGUAGE = 'rw';
    
    // Supported languages
    public const SUPPORTED_LANGUAGES = ['rw', 'en'];

    // Application paths
    public const PATHS = [
        'root' => __DIR__,
        'views' => __DIR__ . '/views/',
        'uploads' => __DIR__ . '/public/uploads/',
    ];
}

// Error Handling
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // Log all errors
    LoggerConfig::log(
        "Error [{$errno}]: {$errstr} in {$errfile} on line {$errline}", 
        'ERROR'
    );

    // Don't execute PHP's internal error handler
    return true;
}

// Set error handlers and reporting based on environment
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Register custom error handler
set_error_handler('customErrorHandler');

// Initialize session configuration
SecurityConfig::sessionConfig();

// Prevent session fixation
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Return database connection for easy access
return DatabaseConfig::getInstance()->getConnection();