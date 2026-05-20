<?php
/**
 * URL Yönlendirme Yapılandırması
 * Route Configuration
 * 
 * Tüm URL yönlendirmeleri burada tanımlanır
 */

return [
    // Public Routes - Kullanıcı Sayfaları
    '/' => ['controller' => 'HomeController', 'action' => 'index'],
    '/anasayfa' => ['controller' => 'HomeController', 'action' => 'index'],
    '/home' => ['controller' => 'HomeController', 'action' => 'index'],
    
    '/yaklasimimiz' => ['controller' => 'HomeController', 'action' => 'approach'],
    '/approach' => ['controller' => 'HomeController', 'action' => 'approach'],
    
    '/politikalarimiz' => ['controller' => 'HomeController', 'action' => 'policies'],
    '/policies' => ['controller' => 'HomeController', 'action' => 'policies'],
    
    '/hizmetlerimiz' => ['controller' => 'ServiceController', 'action' => 'index'],
    '/hizmetler' => ['controller' => 'ServiceController', 'action' => 'index'],
    '/services' => ['controller' => 'ServiceController', 'action' => 'index'],
    
    '/hizmet/{slug}' => ['controller' => 'ServiceController', 'action' => 'detail'],
    '/service/{slug}' => ['controller' => 'ServiceController', 'action' => 'detail'],
    
    '/iletisim' => ['controller' => 'ContactController', 'action' => 'index'],
    '/contact' => ['controller' => 'ContactController', 'action' => 'index'],
    
    '/iletisim/gonder' => ['controller' => 'ContactController', 'action' => 'submit', 'method' => 'POST'],
    '/contact/submit' => ['controller' => 'ContactController', 'action' => 'submit', 'method' => 'POST'],
    
    // Language Routes
    '/lang/{code}' => ['controller' => 'HomeController', 'action' => 'changeLanguage'],
    
    // Admin Routes - Yönetim Paneli
    '/admin' => ['controller' => 'AdminController', 'action' => 'dashboard', 'auth' => true],
    '/admin/dashboard' => ['controller' => 'AdminController', 'action' => 'dashboard', 'auth' => true],
    
    '/admin/login' => ['controller' => 'AuthController', 'action' => 'login'],
    '/admin/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    '/admin/auth' => ['controller' => 'AuthController', 'action' => 'authenticate', 'method' => 'POST'],
    
    // Admin - Hizmetler
    '/admin/services' => ['controller' => 'AdminController', 'action' => 'services', 'auth' => true],
    '/admin/services/get/{id}' => ['controller' => 'AdminController', 'action' => 'serviceGet', 'auth' => true],
    '/admin/services/create' => ['controller' => 'AdminController', 'action' => 'serviceCreate', 'auth' => true, 'method' => 'POST'],
    '/admin/services/update' => ['controller' => 'AdminController', 'action' => 'serviceUpdate', 'auth' => true, 'method' => 'POST'],
    '/admin/services/delete/{id}' => ['controller' => 'AdminController', 'action' => 'serviceDelete', 'auth' => true, 'method' => 'POST'],
    '/admin/services/reorder' => ['controller' => 'AdminController', 'action' => 'serviceReorder', 'auth' => true, 'method' => 'POST'],
    
    // Admin - Mesajlar
    '/admin/messages' => ['controller' => 'AdminController', 'action' => 'messages', 'auth' => true],
    '/admin/messages/read/{id}' => ['controller' => 'AdminController', 'action' => 'messageRead', 'auth' => true, 'method' => 'POST'],
    '/admin/messages/delete/{id}' => ['controller' => 'AdminController', 'action' => 'messageDelete', 'auth' => true, 'method' => 'POST'],
    
    // Admin - Sayfalar
    '/admin/pages' => ['controller' => 'AdminController', 'action' => 'pages', 'auth' => true],
    '/admin/pages/edit/{key}' => ['controller' => 'AdminController', 'action' => 'pageEdit', 'auth' => true],
    '/admin/pages/update' => ['controller' => 'AdminController', 'action' => 'pageUpdate', 'auth' => true, 'method' => 'POST'],
    
    // Admin - Header Görselleri
    '/admin/headers' => ['controller' => 'AdminController', 'action' => 'headers', 'auth' => true],
    '/admin/headers/upload' => ['controller' => 'AdminController', 'action' => 'headerUpload', 'auth' => true, 'method' => 'POST'],
    '/admin/headers/delete/{id}' => ['controller' => 'AdminController', 'action' => 'headerDelete', 'auth' => true, 'method' => 'POST'],
    '/admin/headers/assign' => ['controller' => 'AdminController', 'action' => 'headerAssign', 'auth' => true, 'method' => 'POST'],
    '/admin/headers/toggle/{id}' => ['controller' => 'AdminController', 'action' => 'headerToggle', 'auth' => true, 'method' => 'POST'],
    
    // Admin - Ayarlar
    '/admin/settings' => ['controller' => 'AdminController', 'action' => 'settings', 'auth' => true],
    '/admin/settings/update' => ['controller' => 'AdminController', 'action' => 'settingsUpdate', 'auth' => true, 'method' => 'POST'],
    '/admin/settings/logo' => ['controller' => 'AdminController', 'action' => 'logoUpload', 'auth' => true, 'method' => 'POST'],
    
    // API Routes (AJAX için)
    '/api/services' => ['controller' => 'ApiController', 'action' => 'services'],
    '/api/services/{id}' => ['controller' => 'ApiController', 'action' => 'serviceDetail'],
    '/api/contact' => ['controller' => 'ApiController', 'action' => 'contactSubmit', 'method' => 'POST'],
];
