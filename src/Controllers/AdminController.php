<?php
/**
 * Admin Controller
 * Admin paneli tüm işlemleri için controller
 */

require_once SRC_PATH . '/Controllers/BaseController.php';
require_once SRC_PATH . '/Models/Service.php';
require_once SRC_PATH . '/Models/Message.php';
require_once SRC_PATH . '/Models/Page.php';
require_once SRC_PATH . '/Models/HeaderImage.php';
require_once SRC_PATH . '/Models/Setting.php';

class AdminController extends BaseController {
    private $serviceModel;
    private $messageModel;
    private $pageModel;
    private $headerImageModel;
    private $settingModel;
    
    public function __construct() {
        parent::__construct();
        requireAuth(); // Tüm admin işlemleri için auth gerekli
        
        $this->serviceModel = new Service();
        $this->messageModel = new Message();
        $this->pageModel = new Page();
        $this->headerImageModel = new HeaderImage();
        $this->settingModel = new Setting();
    }
    
    // ==========================================
    // DASHBOARD
    // ==========================================
    
    /**
     * Admin Dashboard
     */
    public function dashboard() {
        // İstatistikler
        $stats = [
            'total_services' => $this->serviceModel->count(),
            'total_messages' => $this->messageModel->count(),
            'unread_messages' => $this->messageModel->getUnreadCount(),
            'total_images' => $this->headerImageModel->count(['is_active' => 1])
        ];
        
        // Son mesajlar
        $recentMessages = $this->messageModel->getRecent(5);
        
        // Flash mesajı
        $flash = $this->getFlash();
        
        $this->view('admin/dashboard', [
            'stats' => $stats,
            'recentMessages' => $recentMessages,
            'flash' => $flash,
            'adminName' => $_SESSION['admin_full_name'] ?? 'Admin'
        ]);
    }
    
    // ==========================================
    // HİZMETLER YÖNETİMİ
    // ==========================================
    
    /**
     * Hizmetler Listesi
     */
    public function services() {
        $services = $this->serviceModel->getAllOrdered();
        $flash = $this->getFlash();
        
        $this->view('admin/services', [
            'services' => $services,
            'flash' => $flash
        ]);
    }
    
    /**
     * Yeni Hizmet Oluştur (POST)
     */
    public function serviceCreate() {
        $data = $this->getJsonInput();
        
        // Validasyon
        if (empty($data['name']) || empty($data['name_en'])) {
            $this->jsonError('Hizmet adı gereklidir.');
            return;
        }
        
        // Features dizileri kontrol et
        $data['features'] = $data['features'] ?? [];
        $data['features_en'] = $data['features_en'] ?? [];
        
        // Hizmeti oluştur
        $serviceId = $this->serviceModel->createService($data);
        
        if ($serviceId) {
            $this->jsonSuccess(['id' => $serviceId], 'Hizmet başarıyla eklendi.');
        } else {
            $this->jsonError('Hizmet eklenirken bir hata oluştu.');
        }
    }
    
    /**
     * Hizmet Güncelle (POST)
     */
    public function serviceUpdate() {
        $data = $this->getJsonInput();
        
        if (empty($data['id'])) {
            $this->jsonError('Hizmet ID gereklidir.');
            return;
        }
        
        $id = $data['id'];
        unset($data['id']); // ID'yi data'dan çıkar
        
        // Hizmeti güncelle
        if ($this->serviceModel->updateService($id, $data)) {
            $this->jsonSuccess(null, 'Hizmet başarıyla güncellendi.');
        } else {
            $this->jsonError('Hizmet güncellenirken bir hata oluştu.');
        }
    }
    
    /**
     * Hizmet Sil (POST)
     * 
     * @param int $id Hizmet ID
     */
    public function serviceDelete($id) {
        if ($this->serviceModel->delete($id)) {
            $this->jsonSuccess(null, 'Hizmet başarıyla silindi.');
        } else {
            $this->jsonError('Hizmet silinirken bir hata oluştu.');
        }
    }
    
    /**
     * Hizmet Sıralamasını Güncelle (POST)
     */
    public function serviceReorder() {
        $data = $this->getJsonInput();
        
        if (empty($data['order'])) {
            $this->jsonError('Sıralama verisi gereklidir.');
            return;
        }
        
        if ($this->serviceModel->updateOrder($data['order'])) {
            $this->jsonSuccess(null, 'Sıralama başarıyla güncellendi.');
        } else {
            $this->jsonError('Sıralama güncellenirken bir hata oluştu.');
        }
    }
    
    /**
     * Hizmet Detayı Getir (GET) - Admin AJAX için
     * 
     * @param int $id Hizmet ID
     */
    public function serviceGet($id) {
        $service = $this->serviceModel->find($id);
        
        if (!$service) {
            $this->jsonError('Hizmet bulunamadı.');
            return;
        }
        
        // JSON alanlarını decode et
        $service['features'] = json_decode($service['features'] ?? '[]', true);
        $service['features_en'] = json_decode($service['features_en'] ?? '[]', true);
        
        // TR alias alanları ekle
        $service['name_tr'] = $service['name'];
        $service['description_tr'] = $service['description'];
        
        $this->jsonSuccess($service, 'Hizmet bulundu.');
    }
    
    // ==========================================
    // MESAJLAR YÖNETİMİ
    // ==========================================
    
    /**
     * Mesajlar Listesi
     */
    public function messages() {
        $messages = $this->messageModel->getAllMessages();
        $unreadCount = $this->messageModel->getUnreadCount();
        $flash = $this->getFlash();
        
        $this->view('admin/messages', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'flash' => $flash
        ]);
    }
    
    /**
     * Mesajı Okundu İşaretle (POST)
     * 
     * @param int $id Mesaj ID
     */
    public function messageRead($id) {
        if ($this->messageModel->markAsRead($id)) {
            $this->jsonSuccess(null, 'Mesaj okundu olarak işaretlendi.');
        } else {
            $this->jsonError('İşlem başarısız oldu.');
        }
    }
    
    /**
     * Mesaj Sil (POST)
     * 
     * @param int $id Mesaj ID
     */
    public function messageDelete($id) {
        if ($this->messageModel->delete($id)) {
            $this->jsonSuccess(null, 'Mesaj başarıyla silindi.');
        } else {
            $this->jsonError('Mesaj silinirken bir hata oluştu.');
        }
    }
    
    // ==========================================
    // SAYFA YÖNETİMİ
    // ==========================================
    
    /**
     * Sayfalar Listesi
     */
    public function pages() {
        $pages = $this->pageModel->getActivePages();
        $flash = $this->getFlash();
        
        $this->view('admin/pages', [
            'pages' => $pages,
            'flash' => $flash
        ]);
    }
    
    /**
     * Sayfa Düzenleme Formu
     * 
     * @param string $key Sayfa key'i
     */
    public function pageEdit($key) {
        $page = $this->pageModel->getByKey($key);
        $sections = $this->pageModel->getSections($key);
        
        if (!$page) {
            $this->setFlash('error', 'Sayfa bulunamadı.');
            $this->redirect(url('/admin/pages'));
            return;
        }
        
        $flash = $this->getFlash();
        
        $this->view('admin/page-edit', [
            'page' => $page,
            'sections' => $sections,
            'flash' => $flash
        ]);
    }
    
    /**
     * Sayfa Güncelle (POST)
     */
    public function pageUpdate() {
        $data = $this->post();
        $pageKey = $data['page_key'] ?? '';
        
        if (empty($pageKey)) {
            $this->setFlash('error', 'Geçersiz sayfa.');
            $this->redirect(url('/admin/pages'));
            return;
        }
        
        // Sayfa bilgilerini güncelle
        $pageData = [
            'title_tr' => $data['title_tr'] ?? '',
            'title_en' => $data['title_en'] ?? '',
            'subtitle_tr' => $data['subtitle_tr'] ?? '',
            'subtitle_en' => $data['subtitle_en'] ?? '',
            'meta_description_tr' => $data['meta_description_tr'] ?? '',
            'meta_description_en' => $data['meta_description_en'] ?? ''
        ];
        
        if ($this->pageModel->updateByKey($pageKey, $pageData)) {
            $this->setFlash('success', 'Sayfa başarıyla güncellendi.');
        } else {
            $this->setFlash('error', 'Sayfa güncellenirken bir hata oluştu.');
        }
        
        $this->redirect(url('/admin/pages/edit/' . $pageKey));
    }
    
    // ==========================================
    // HEADER GÖRSELLERİ YÖNETİMİ
    // ==========================================
    
    /**
     * Header Görselleri Yönetimi
     */
    public function headers() {
        $images = $this->headerImageModel->all([], 'page_key ASC, display_order ASC');
        $pages = $this->pageModel->getActivePages();
        $flash = $this->getFlash();
        
        // Sayfa başına görselleri grupla
        $imagesByPage = [];
        foreach ($images as $image) {
            $pageKey = $image['page_key'];
            if (!isset($imagesByPage[$pageKey])) {
                $imagesByPage[$pageKey] = [];
            }
            $imagesByPage[$pageKey][] = $image;
        }
        
        $this->view('admin/headers', [
            'images' => $images,
            'imagesByPage' => $imagesByPage,
            'pages' => $pages,
            'flash' => $flash
        ]);
    }
    
    /**
     * Header Görseli Yükle (POST)
     */
    public function headerUpload() {
        // Dosya yüklendi mi kontrol et
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $this->jsonError('Dosya yüklenirken bir hata oluştu.');
            return;
        }
        
        $file = $_FILES['image'];
        $pageKey = $this->post('page_key', 'home');
        
        // Dosya tipi kontrolü
        $allowedTypes = ALLOWED_IMAGE_TYPES;
        if (!in_array($file['type'], $allowedTypes)) {
            $this->jsonError('Geçersiz dosya tipi. Sadece resim dosyaları yükleyebilirsiniz.');
            return;
        }
        
        // Dosya boyutu kontrolü
        if ($file['size'] > MAX_FILE_SIZE) {
            $this->jsonError('Dosya boyutu çok büyük. Maksimum ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB olmalıdır.');
            return;
        }
        
        // Benzersiz dosya adı oluştur
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('header_' . $pageKey . '_') . '.' . $extension;
        $uploadPath = UPLOADS_PATH . '/headers/' . $fileName;
        
        // Dosyayı taşı
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Veritabanına kaydet
            $imageId = $this->headerImageModel->uploadImage([
                'page_key' => $pageKey,
                'image_name' => $file['name'],
                'image_path' => 'headers/' . $fileName,
                'image_size' => $file['size'],
                'is_active' => 1
            ]);
            
            if ($imageId) {
                $this->jsonSuccess([
                    'id' => $imageId,
                    'path' => upload('headers/' . $fileName)
                ], 'Görsel başarıyla yüklendi.');
            } else {
                // Dosyayı sil
                unlink($uploadPath);
                $this->jsonError('Görsel kaydedilirken bir hata oluştu.');
            }
        } else {
            $this->jsonError('Dosya yüklenirken bir hata oluştu.');
        }
    }
    
    /**
     * Header Görseli Sil (POST)
     * 
     * @param int $id Görsel ID
     */
    public function headerDelete($id) {
        $image = $this->headerImageModel->find($id);
        
        if (!$image) {
            $this->jsonError('Görsel bulunamadı.');
            return;
        }
        
        // Dosyayı sil
        $filePath = UPLOADS_PATH . '/' . $image['image_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Veritabanından sil
        if ($this->headerImageModel->delete($id)) {
            $this->jsonSuccess(null, 'Görsel başarıyla silindi.');
        } else {
            $this->jsonError('Görsel silinirken bir hata oluştu.');
        }
    }
    
    /**
     * Header Görseli Aktif/Pasif Yap (POST)
     * 
     * @param int $id Görsel ID
     */
    public function headerToggle($id) {
        if ($this->headerImageModel->toggleActive($id)) {
            $this->jsonSuccess(null, 'Görsel durumu güncellendi.');
        } else {
            $this->jsonError('İşlem başarısız oldu.');
        }
    }
    
    /**
     * Sayfa Header Ayarlarını Güncelle (POST)
     */
    public function headerAssign() {
        $data = $this->getJsonInput();
        
        $pageKey = $data['page_key'] ?? '';
        $settings = [
            'selected_image_id' => $data['selected_image_id'] ?? null,
            'use_random' => $data['use_random'] ?? 0,
            'overlay_opacity' => $data['overlay_opacity'] ?? 0.5,
            'overlay_color' => $data['overlay_color'] ?? '#000000'
        ];
        
        if ($this->headerImageModel->updatePageSettings($pageKey, $settings)) {
            $this->jsonSuccess(null, 'Header ayarları güncellendi.');
        } else {
            $this->jsonError('Ayarlar güncellenirken bir hata oluştu.');
        }
    }
    
    // ==========================================
    // AYARLAR YÖNETİMİ
    // ==========================================
    
    /**
     * Site Ayarları
     */
    public function settings() {
        $settings = $this->settingModel->getAllGrouped();
        $flash = $this->getFlash();
        
        $this->view('admin/settings', [
            'settings' => $settings,
            'flash' => $flash
        ]);
    }
    
    /**
     * Ayarları Güncelle (POST)
     */
    public function settingsUpdate() {
        $data = $this->post();
        
        // CSRF token kontrolü
        if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
            $this->setFlash('error', 'Geçersiz istek.');
            $this->redirect(url('/admin/settings'));
            return;
        }
        
        unset($data['csrf_token']);
        
        // Ayarları güncelle
        if ($this->settingModel->updateMultiple($data)) {
            $this->setFlash('success', 'Ayarlar başarıyla güncellendi.');
        } else {
            $this->setFlash('error', 'Ayarlar güncellenirken bir hata oluştu.');
        }
        
        $this->redirect(url('/admin/settings'));
    }
    
    /**
     * Logo Yükle (POST)
     */
    public function logoUpload() {
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            $this->jsonError('Dosya yüklenirken bir hata oluştu.');
            return;
        }
        
        $file = $_FILES['logo'];
        
        // Dosya tipi kontrolü
        if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
            $this->jsonError('Geçersiz dosya tipi.');
            return;
        }
        
        // Dosya boyutu kontrolü
        if ($file['size'] > MAX_FILE_SIZE) {
            $this->jsonError('Dosya boyutu çok büyük.');
            return;
        }
        
        // Benzersiz dosya adı oluştur
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'logo_' . time() . '.' . $extension;
        $uploadPath = PUBLIC_PATH . '/assets/images/' . $fileName;
        
        // Eski logoyu sil
        $oldLogo = $this->settingModel->get('site_logo');
        if ($oldLogo) {
            $oldLogoPath = PUBLIC_PATH . '/assets/images/' . basename($oldLogo);
            if (file_exists($oldLogoPath)) {
                unlink($oldLogoPath);
            }
        }
        
        // Yeni logoyu yükle
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $logoPath = 'images/' . $fileName;
            $this->settingModel->set('site_logo', $logoPath, 'general', 'Site logosu');
            
            $this->jsonSuccess([
                'path' => asset($logoPath)
            ], 'Logo başarıyla yüklendi.');
        } else {
            $this->jsonError('Logo yüklenirken bir hata oluştu.');
        }
    }
}
