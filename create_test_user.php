<?php
// Test kullanıcısı oluşturma scripti
echo "<h2>Test Kullanıcısı Oluşturma</h2>";

try {
    require_once 'backend/config.php';
    
    $pdo = db_connect();
    echo "<p style='color: green;'>✅ Veritabanı bağlantısı başarılı</p>";
    
    // Users tablosunu kontrol et
    $checkTable = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($checkTable->rowCount() == 0) {
        echo "<p style='color: red;'>❌ Users tablosu bulunamadı!</p>";
        echo "<p>Users tablosunu oluşturuyorum...</p>";
        
        $createTable = "
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('user', 'admin') DEFAULT 'user',
            balance DECIMAL(15,2) DEFAULT 0.00,
            ad_soyad VARCHAR(100),
            telefon VARCHAR(20),
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            son_giris TIMESTAMP NULL,
            ip_adresi VARCHAR(45)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        
        $pdo->exec($createTable);
        echo "<p style='color: green;'>✅ Users tablosu oluşturuldu</p>";
    } else {
        echo "<p style='color: green;'>✅ Users tablosu mevcut</p>";
    }
    
    // Test kullanıcısını kontrol et
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
    $stmt->execute(['test']);
    $userExists = $stmt->fetchColumn() > 0;
    
    if (!$userExists) {
        echo "<p>Test kullanıcısı oluşturuluyor...</p>";
        
        $insertUser = $pdo->prepare("
            INSERT INTO users (username, email, password, role, balance, ad_soyad, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $insertUser->execute([
            'test',
            'test@test.com',
            '123456',
            'user',
            10000.00,
            'Test Kullanıcı',
            1
        ]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Test kullanıcısı oluşturuldu</p>";
            echo "<p><strong>Kullanıcı Adı:</strong> test</p>";
            echo "<p><strong>Şifre:</strong> 123456</p>";
            echo "<p><strong>Bakiye:</strong> ₺10,000</p>";
        } else {
            echo "<p style='color: red;'>❌ Test kullanıcısı oluşturulamadı</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ Test kullanıcısı zaten mevcut</p>";
        echo "<p><strong>Kullanıcı Adı:</strong> test</p>";
        echo "<p><strong>Şifre:</strong> 123456</p>";
    }
    
    // Admin kullanıcısını da kontrol et
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $adminExists = $stmt->fetchColumn() > 0;
    
    if (!$adminExists) {
        echo "<p>Admin kullanıcısı oluşturuluyor...</p>";
        
        $insertAdmin = $pdo->prepare("
            INSERT INTO users (username, email, password, role, balance, ad_soyad, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $insertAdmin->execute([
            'admin',
            'admin@admin.com',
            'admin123',
            'admin',
            50000.00,
            'Admin Kullanıcı',
            1
        ]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Admin kullanıcısı oluşturuldu</p>";
            echo "<p><strong>Kullanıcı Adı:</strong> admin</p>";
            echo "<p><strong>Şifre:</strong> admin123</p>";
            echo "<p><strong>Rol:</strong> Admin</p>";
        } else {
            echo "<p style='color: red;'>❌ Admin kullanıcısı oluşturulamadı</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ Admin kullanıcısı zaten mevcut</p>";
    }
    
    // Mevcut kullanıcıları listele
    echo "<h3>Mevcut Kullanıcılar:</h3>";
    $users = $pdo->query("SELECT username, role, balance, is_active FROM users ORDER BY id")->fetchAll();
    
    if (count($users) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Kullanıcı Adı</th><th>Rol</th><th>Bakiye</th><th>Durum</th></tr>";
        
        foreach ($users as $user) {
            $status = $user['is_active'] ? 'Aktif' : 'Pasif';
            $statusColor = $user['is_active'] ? 'green' : 'red';
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "<td>₺" . number_format($user['balance'], 2) . "</td>";
            echo "<td style='color: {$statusColor};'>" . $status . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>Henüz kullanıcı bulunmuyor.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Hata: " . $e->getMessage() . "</p>";
    echo "<p>Hata detayı: " . $e->getTraceAsString() . "</p>";
}

echo "<br><a href='create_test_user.php'>Sayfayı Yenile</a>";
echo "<br><a href='login.html'>Login Sayfasına Git</a>";
echo "<br><a href='test_db_connection.php'>Veritabanı Bağlantı Testi</a>";
?>
