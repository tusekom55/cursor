<?php
// Basit login test dosyası
echo "<h2>Login Sistemi Test</h2>";

try {
    // Config dosyasını dahil et
    require_once 'backend/config.php';
    require_once 'backend/auth.php';
    
    echo "<p style='color: green;'>✅ Config ve auth dosyaları dahil edildi</p>";
    
    // Test kullanıcısı ile login denemesi
    $username = 'test';
    $password = '123456';
    
    echo "<p>Test kullanıcısı ile login deneniyor...</p>";
    echo "<p><strong>Kullanıcı Adı:</strong> {$username}</p>";
    echo "<p><strong>Şifre:</strong> {$password}</p>";
    
    // Login fonksiyonunu çağır
    $login_result = login_user($username, $password);
    
    if ($login_result) {
        echo "<p style='color: green;'>✅ Login başarılı!</p>";
        echo "<p>Session bilgileri:</p>";
        echo "<ul>";
        echo "<li>User ID: " . ($_SESSION['user_id'] ?? 'Yok') . "</li>";
        echo "<li>Username: " . ($_SESSION['username'] ?? 'Yok') . "</li>";
        echo "<li>Role: " . ($_SESSION['role'] ?? 'Yok') . "</li>";
        echo "<li>Balance: " . ($_SESSION['balance'] ?? 'Yok') . "</li>";
        echo "<li>Last Activity: " . ($_SESSION['last_activity'] ?? 'Yok') . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>❌ Login başarısız!</p>";
    }
    
    // Session durumunu kontrol et
    echo "<h3>Session Durumu</h3>";
    echo "<p>Session ID: " . session_id() . "</p>";
    echo "<p>Session Status: " . session_status() . "</p>";
    echo "<p>Session Name: " . session_name() . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Hata: " . $e->getMessage() . "</p>";
    echo "<p>Hata detayı: " . $e->getTraceAsString() . "</p>";
}

echo "<br><a href='test_login_simple.php'>Sayfayı Yenile</a>";
echo "<br><a href='login.html'>Login Sayfasına Git</a>";
echo "<br><a href='create_test_user.php'>Test Kullanıcısı Oluştur</a>";
?>
