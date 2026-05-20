<?php
/**
 * Veritabanı Yapılandırma Dosyası
 * Database Configuration File
 * 
 * Ortam algılama ve veritabanı bağlantı ayarları
 */

// Ortam algılama (Local / Production)
function detectEnvironment() {
    $isLocal = false;
    
    // HTTP_HOST kontrolü
    if (isset($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
        $isLocal = (
            $host === 'localhost' || 
            $host === '127.0.0.1' ||
            strpos($host, 'localhost:') === 0 ||
            strpos($host, '127.0.0.1:') === 0
        );
    }
    
    // SERVER_NAME kontrolü (fallback)
    if (!$isLocal && isset($_SERVER['SERVER_NAME'])) {
        $serverName = $_SERVER['SERVER_NAME'];
        $isLocal = ($serverName === 'localhost' || $serverName === '127.0.0.1');
    }
    
    // REMOTE_ADDR kontrolü (fallback)
    if (!$isLocal && isset($_SERVER['REMOTE_ADDR'])) {
        $isLocal = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
    }
    
    return $isLocal ? 'local' : 'production';
}

// Ortam her koşulda sunucuya (production) göre ayarlandı
define('APP_ENV', 'production');

// Veritabanı ayarları
if (APP_ENV === 'local') {
    // LOCAL (XAMPP/MAMP) AYARLARI
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'navexmar');
} else {
    // PRODUCTION (SUNUCU) AYARLARI
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_USER', 'navexmar_admin');
    define('DB_PASS', 'Abozoglan01');
    define('DB_NAME', 'navexmar_navex');
}

/**
 * Veritabanı bağlantısını döndürür
 * 
 * @return PDO Veritabanı bağlantı nesnesi
 * @throws PDOException Bağlantı hatası durumunda
 */
function getDB() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $conn = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
        } catch (PDOException $e) {
            // Production'da detaylı hata gösterme
            if (APP_ENV === 'production') {
                error_log("Database Error: " . $e->getMessage());
                die("Veritabanı bağlantı hatası. Lütfen sistem yöneticisi ile iletişime geçin.");
            }
            
            // Development'ta detaylı hata göster
            $errorMsg = "Veritabanı bağlantı hatası: " . $e->getMessage();
            $errorMsg .= "\n\nKontrol edin:";
            $errorMsg .= "\n- Veritabanı adı: " . DB_NAME;
            $errorMsg .= "\n- Kullanıcı adı: " . DB_USER;
            $errorMsg .= "\n- Host: " . DB_HOST . ":" . DB_PORT;
            $errorMsg .= "\n- Ortam: " . APP_ENV;
            die("<pre style='background:#fee;padding:2rem;border-left:4px solid #c33;font-family:monospace;'>" . 
                htmlspecialchars($errorMsg) . "</pre>");
        }
    }
    
    return $conn;
}
