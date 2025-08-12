<?php
// Veritabanı bağlantı test dosyası
echo "<h2>Veritabanı Bağlantı Testi</h2>";

// Config dosyasını dahil et
require_once 'backend/config.php';

echo "<h3>1. PDO Bağlantı Testi</h3>";
try {
    $pdo = db_connect();
    echo "<p style='color: green;'>✅ PDO bağlantısı başarılı</p>";
    
    // Test sorgusu
    $stmt = $pdo->query("SELECT COUNT(*) as user_count FROM users");
    $result = $stmt->fetch();
    echo "<p>Kullanıcı sayısı: " . $result['user_count'] . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ PDO bağlantı hatası: " . $e->getMessage() . "</p>";
}

echo "<h3>2. MySQLi Bağlantı Testi</h3>";
try {
    global $conn;
    if ($conn->ping()) {
        echo "<p style='color: green;'>✅ MySQLi bağlantısı başarılı</p>";
        
        // Test sorgusu
        $result = $conn->query("SELECT COUNT(*) as user_count FROM users");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>Kullanıcı sayısı: " . $row['user_count'] . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ MySQLi bağlantısı başarısız</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ MySQLi bağlantı hatası: " . $e->getMessage() . "</p>";
}

echo "<h3>3. Veritabanı Bilgileri</h3>";
echo "<p>Host: " . $DB_HOST . "</p>";
echo "<p>Database: " . $DB_NAME . "</p>";
echo "<p>User: " . $DB_USER . "</p>";
echo "<p>Debug Mode: " . (defined('DEBUG_MODE') && DEBUG_MODE ? 'Açık' : 'Kapalı') . "</p>";

echo "<h3>4. Tablo Kontrolü</h3>";
try {
    $pdo = db_connect();
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p>Mevcut tablolar:</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . $table . "</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Tablo listesi alınamadı: " . $e->getMessage() . "</p>";
}

echo "<h3>5. Users Tablosu Kontrolü</h3>";
try {
    $pdo = db_connect();
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Users tablosu kolonları:</p>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>" . $column['Field'] . " - " . $column['Type'] . "</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Users tablosu kontrol edilemedi: " . $e->getMessage() . "</p>";
}

echo "<br><a href='test_db_connection.php'>Sayfayı Yenile</a>";
?>
