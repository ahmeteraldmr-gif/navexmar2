<?php
/**
 * Service Model
 * Hizmetler tablosu için model sınıfı
 */

require_once __DIR__ . '/BaseModel.php';

class Service extends BaseModel {
    protected $table = 'services';
    
    /**
     * Tüm aktif hizmetleri sıralı olarak getir
     * 
     * @param string $lang Dil kodu
     * @return array
     */
    public function getAllOrdered($lang = 'tr') {
        $services = $this->all([], 'display_order ASC, created_at DESC');
        
        // JSON alanlarını decode et ve TR alanları için alias oluştur
        foreach ($services as &$service) {
            $service['features'] = json_decode($service['features'] ?? '[]', true);
            $service['features_en'] = json_decode($service['features_en'] ?? '[]', true);
            
            // Views'larda name_tr ve description_tr kullanıldığı için TR alanlarını aliase et
            $service['name_tr'] = $service['name'];
            $service['description_tr'] = $service['description'];
            $service['features_tr'] = $service['features'];
        }
        
        return $services;
    }
    
    /**
     * Slug'a göre hizmet getir
     * 
     * @param string $slug Hizmet slug'ı
     * @return array|null
     */
    public function findBySlug($slug) {
        $service = $this->findBy(['slug' => $slug]);
        
        if ($service) {
            $service['features'] = json_decode($service['features'] ?? '[]', true);
            $service['features_en'] = json_decode($service['features_en'] ?? '[]', true);
            
            // Views'larda name_tr ve description_tr kullanıldığı için TR alanlarını aliase et
            $service['name_tr'] = $service['name'];
            $service['description_tr'] = $service['description'];
            $service['features_tr'] = $service['features'];
        }
        
        return $service;
    }
    
    /**
     * Hizmet oluştur
     * 
     * @param array $data Hizmet verileri
     * @return int|bool
     */
    public function createService($data) {
        // Features dizilerini JSON'a çevir
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = json_encode($data['features'], JSON_UNESCAPED_UNICODE);
        }
        if (isset($data['features_en']) && is_array($data['features_en'])) {
            $data['features_en'] = json_encode($data['features_en'], JSON_UNESCAPED_UNICODE);
        }
        
        // Slug oluştur
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }
        
        // Display order ayarla
        if (!isset($data['display_order'])) {
            $maxOrder = $this->queryOne("SELECT MAX(display_order) as max_order FROM {$this->table}");
            $data['display_order'] = ($maxOrder['max_order'] ?? 0) + 1;
        }
        
        return $this->create($data);
    }
    
    /**
     * Hizmet güncelle
     * 
     * @param int $id Hizmet ID
     * @param array $data Güncellenecek veriler
     * @return bool
     */
    public function updateService($id, $data) {
        // Features dizilerini JSON'a çevir
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = json_encode($data['features'], JSON_UNESCAPED_UNICODE);
        }
        if (isset($data['features_en']) && is_array($data['features_en'])) {
            $data['features_en'] = json_encode($data['features_en'], JSON_UNESCAPED_UNICODE);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Benzersiz slug oluştur
     * 
     * @param string $text Metin
     * @return string
     */
    private function generateUniqueSlug($text) {
        $slug = createSlug($text);
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->findBy(['slug' => $slug])) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Hizmet sıralamasını güncelle
     * 
     * @param array $order ID ve sıra çiftleri [['id' => 1, 'order' => 1], ...]
     * @return bool
     */
    public function updateOrder($order) {
        try {
            $this->db->beginTransaction();
            
            foreach ($order as $item) {
                $this->update($item['id'], ['display_order' => $item['order']]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
