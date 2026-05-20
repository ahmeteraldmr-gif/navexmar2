<?php
/**
 * Uygulama Yapılandırma Dosyası
 * Application Configuration File
 * 
 * Uygulama genelinde kullanılan ayarlar ve sabitler
 */

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ortam bilgisini config/database.php'den al
require_once __DIR__ . '/database.php';

// Uygulama dizini
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('SRC_PATH', ROOT_PATH . '/src');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('VIEWS_PATH', SRC_PATH . '/Views');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// URL ayarları
if (APP_ENV === 'local') {
    define('BASE_URL', 'http://localhost/navexmar/public');
    define('ASSETS_URL', BASE_URL . '/assets');
    define('UPLOADS_URL', BASE_URL . '/uploads');
} else {
    // Production için
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'navexmar.com';
    
    // Eğer URL'de /public/ varsa (Document Root ayarlanmamışsa)
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($requestUri, '/public/') !== false) {
        // /public/ ile erişiliyorsa
        define('BASE_URL', $protocol . '://' . $host . '/public');
        define('ASSETS_URL', $protocol . '://' . $host . '/public/assets');
        define('UPLOADS_URL', $protocol . '://' . $host . '/public/uploads');
    } else {
        // Document Root /public/ klasörüne işaret ediyorsa (ideal durum) veya .htaccess yönlendirmesi varsa
        define('BASE_URL', $protocol . '://' . $host);
        define('ASSETS_URL', BASE_URL . '/assets');
        define('UPLOADS_URL', BASE_URL . '/uploads');
    }
}

// Dil ayarları
define('DEFAULT_LANG', 'tr');
define('AVAILABLE_LANGS', ['tr', 'en']);

// Sayfa başına öğe sayıları
define('ITEMS_PER_PAGE', 10);

// Dosya yükleme ayarları
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Hata raporlama
if (APP_ENV === 'local') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . '/logs/error.log');
}

// Timezone ayarı
date_default_timezone_set('Europe/Istanbul');

/**
 * Aktif dili döndürür
 * 
 * @return string Dil kodu (tr, en)
 */
function getCurrentLang() {
    if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], AVAILABLE_LANGS)) {
        return $_SESSION['lang'];
    }
    return DEFAULT_LANG;
}

/**
 * Dili değiştirir
 * 
 * @param string $lang Dil kodu
 * @return bool Başarılı ise true
 */
function setLang($lang) {
    if (in_array($lang, AVAILABLE_LANGS)) {
        $_SESSION['lang'] = $lang;
        return true;
    }
    return false;
}

/**
 * Admin oturum kontrolü
 * 
 * @return bool Admin giriş yapmışsa true
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Admin oturum kontrolü - yönlendirmeli
 * 
 * @param string $redirectUrl Yönlendirme URL'si
 */
function requireAuth($redirectUrl = '/admin/login') {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . $redirectUrl);
        exit;
    }
}

/**
 * Çıkış yap
 */
function logout() {
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
}

/**
 * XSS koruması için HTML encode
 * 
 * @param string $string Temizlenecek metin
 * @return string Temizlenmiş metin
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * URL oluştur
 * 
 * @param string $path URL yolu
 * @return string Tam URL
 */
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

/**
 * Asset URL oluştur
 * 
 * @param string $path Asset yolu
 * @return string Tam asset URL
 */
function asset($path = '') {
    $path = ltrim($path, '/');
    return ASSETS_URL . '/' . $path;
}

/**
 * Upload URL oluştur
 * 
 * @param string $path Upload yolu
 * @return string Tam upload URL
 */
function upload($path = '') {
    $path = ltrim($path, '/');
    return UPLOADS_URL . '/' . $path;
}

/**
 * Yönlendirme
 * 
 * @param string $url Yönlendirilecek URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * JSON yanıt döndür
 * 
 * @param mixed $data Yanıt verisi
 * @param int $statusCode HTTP durum kodu
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Slug oluştur (SEO dostu URL)
 * 
 * @param string $text Metin
 * @return string Slug
 */
function createSlug($text) {
    $text = trim($text);
    $text = mb_strtolower($text, 'UTF-8');
    
    // Türkçe karakterleri değiştir
    $turkish = ['ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç'];
    $english = ['s', 's', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c'];
    $text = str_replace($turkish, $english, $text);
    
    // Özel karakterleri temizle
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    
    return $text;
}

/**
 * CSRF Token oluştur
 * 
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF Token doğrula
 * 
 * @param string $token Doğrulanacak token
 * @return bool Geçerli ise true
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
