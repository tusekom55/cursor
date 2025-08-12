<?php
// Session test dosyası
session_start();

// Session timeout süresini uzat (2 saat)
ini_set('session.gc_maxlifetime', 7200);
ini_set('session.cookie_lifetime', 7200);

echo "<h2>Session Test</h2>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Name: " . session_name() . "</p>";
echo "<p>Session Status: " . session_status() . "</p>";
echo "<p>Session Save Path: " . session_save_path() . "</p>";
echo "<p>Session Cookie Lifetime: " . ini_get('session.cookie_lifetime') . "</p>";
echo "<p>Session GC Max Lifetime: " . ini_get('session.gc_maxlifetime') . "</p>";

if (isset($_SESSION['user_id'])) {
    echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
    echo "<p>Last Activity: " . ($_SESSION['last_activity'] ?? 'Not set') . "</p>";
    echo "<p>Current Time: " . time() . "</p>";
    
    if (isset($_SESSION['last_activity'])) {
        $time_diff = time() - $_SESSION['last_activity'];
        echo "<p>Time Since Last Activity: " . $time_diff . " seconds</p>";
        
        if ($time_diff > 7200) {
            echo "<p style='color: red;'>⚠️ Session expired!</p>";
        } else {
            echo "<p style='color: green;'>✅ Session active</p>";
        }
    }
} else {
    echo "<p style='color: orange;'>No user session found</p>";
}

// Test session yenileme
if (isset($_GET['refresh'])) {
    $_SESSION['last_activity'] = time();
    echo "<p style='color: green;'>Session refreshed!</p>";
    echo "<script>setTimeout(() => window.location.reload(), 1000);</script>";
}

echo "<br><a href='?refresh=1'>Refresh Session</a>";
echo "<br><a href='test_session.php'>Reload Page</a>";
?>
