<?php
// Session yönetimi - çakışma önleme
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session timeout süresini uzat (2 saat)
ini_set('session.gc_maxlifetime', 7200);
ini_set('session.cookie_lifetime', 7200);

// Error reporting - JSON API için
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// CORS headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// OPTIONS request için
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Sadece POST metoduna izin ver
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Sadece POST metoduna izin verilir']);
    exit;
}

try {
    // Config dosyasını dahil et
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../auth.php';
    
    // Form verilerini al
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Validasyon
    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Kullanıcı adı ve şifre gereklidir'
        ]);
        exit;
    }
    
    // Login işlemi
    if (login_user($username, $password)) {
        // Session'ı güçlendir
        $_SESSION['last_activity'] = time();
        $_SESSION['login_time'] = time();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Giriş başarılı',
            'user' => [
                'username' => $username,
                'role' => $_SESSION['role'] ?? 'user'
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Kullanıcı adı veya şifre hatalı'
        ]);
    }
    
} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Giriş işlemi sırasında bir hata oluştu: ' . $e->getMessage()
    ]);
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Veritabanı bağlantı hatası'
    ]);
} catch (Error $e) {
    error_log('PHP error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Sistem hatası'
    ]);
}
?> 