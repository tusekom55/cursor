<?php
// Oturum yönetimi - çakışmaları önle
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session timeout süresini uzat (2 saat)
ini_set('session.gc_maxlifetime', 7200);
ini_set('session.cookie_lifetime', 7200);

// Session'ı güçlendir
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
}

// Veritabanı bağlantı ayarları
$DB_HOST = 'localhost';
$DB_USER = 'u225998063_yenip';
$DB_PASS = '123456Tubb';
$DB_NAME = 'u225998063_yenip';

// PDO Bağlantı fonksiyonu (hata kontrolü ile)
function db_connect() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    
    try {
        $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_TIMEOUT => 10, // 10 saniye timeout
            PDO::MYSQL_ATTR_CONNECT_TIMEOUT => 10
        ];
        
        $conn = new PDO($dsn, $DB_USER, $DB_PASS, $options);
        
        // Bağlantıyı test et
        $conn->query("SELECT 1");
        
        return $conn;
        
    } catch (PDOException $e) {
        error_log('Database connection error: ' . $e->getMessage());
        
        // Test modu için detaylı hata, production'da generic mesaj
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            throw new Exception('Veritabanı bağlantı hatası: ' . $e->getMessage());
        } else {
            throw new Exception('Veritabanı bağlantısı kurulamadı');
        }
    }
}

// MySQLi bağlantı değişkeni (global kullanım için) - try-catch ile
try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    
    // Bağlantı kontrolü
    if ($conn->connect_error) {
        error_log('MySQLi connection error: ' . $conn->connect_error);
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            throw new Exception('MySQLi bağlantı hatası: ' . $conn->connect_error);
        } else {
            throw new Exception('Veritabanı bağlantısı kurulamadı');
        }
    }
    
    // Timeout ayarları
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
    $conn->options(MYSQLI_OPT_READ_TIMEOUT, 10);
    
    // UTF-8 karakter seti ayarla
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    error_log('MySQLi connection setup error: ' . $e->getMessage());
    $conn = null; // Bağlantıyı null yap
}

// Debug modu (production'da false yapın)
define('DEBUG_MODE', true); // Test için geçici olarak true

// Error reporting - JSON API'leri için kapalı
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Sadece debug modunda test session değerleri
if (!isset($_SESSION['user_id']) && defined('DEBUG_MODE') && DEBUG_MODE) {
    $_SESSION['user_id'] = 1; // Test kullanıcısı
    $_SESSION['role'] = 'user';
}
