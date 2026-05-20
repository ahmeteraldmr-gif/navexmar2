-- ==========================================
-- NAVEXMAR COMPLETE DATABASE
-- Sunucuya Yüklemek İçin Tek SQL Dosyası
-- ==========================================

-- Veritabanını oluştur (eğer yoksa)
CREATE DATABASE IF NOT EXISTS navexmar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE navexmar;

-- ==========================================
-- 1. ADMIN TABLOSU
-- ==========================================
DROP TABLE IF EXISTS admin;
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    email VARCHAR(255),
    last_login TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan admin kullanıcısı (kullanıcı: admin, şifre: admin31)
INSERT INTO admin (username, password, full_name, email, is_active) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin@navexmar.com', 1);

-- ==========================================
-- 2. SERVICES TABLOSU
-- ==========================================
DROP TABLE IF EXISTS services;
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    name_en VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    description_en TEXT NOT NULL,
    icon VARCHAR(100) NOT NULL DEFAULT 'fas fa-cog',
    features TEXT,
    features_en TEXT,
    display_order INT DEFAULT 0,
    image_path VARCHAR(255),
    slug VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_display_order (display_order),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan hizmetler (Admin panelinden ekleyin)
-- Hizmetler boş başlıyor, admin panelinden /admin/services adresinden ekleyebilirsiniz


-- ==========================================
-- 3. MESSAGES TABLOSU
-- ==========================================
DROP TABLE IF EXISTS messages;
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    company VARCHAR(255),
    service VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    admin_note TEXT,
    replied_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 4. PAGES TABLOSU
-- ==========================================
DROP TABLE IF EXISTS pages;
CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(50) NOT NULL UNIQUE COMMENT 'home, services, contact, approach, policies',
    title_tr VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    subtitle_tr TEXT,
    subtitle_en TEXT,
    meta_description_tr TEXT,
    meta_description_en TEXT,
    meta_keywords_tr TEXT,
    meta_keywords_en TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_page_key (page_key),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan sayfalar
INSERT INTO pages (page_key, title_tr, title_en, subtitle_tr, subtitle_en, meta_description_tr, meta_description_en) VALUES
('home', 'Ana Sayfa', 'Home', 'OPTİMİZASYONA ADANMIŞ', 'DEDICATED TO OPTIMIZATION', 
 'Navexmar - Gemi Bakım ve Uluslararası Ticaret Hizmetleri', 
 'Navexmar - Ship Maintenance and International Trade Services'),
 
('services', 'Hizmetlerimiz', 'Our Services', 'Denizcilik sektöründe kapsamlı çözümler', 'Comprehensive solutions in maritime industry',
 'Navexmar Hizmetleri - Gemi Bakım ve Lojistik Çözümleri',
 'Navexmar Services - Ship Maintenance and Logistics Solutions'),
 
('contact', 'İletişim', 'Contact', 'Bizimle iletişime geçin', 'Get in touch with us',
 'Navexmar İletişim - Bizimle İletişime Geçin',
 'Navexmar Contact - Get in Touch'),
 
('approach', 'Yaklaşımımız', 'Our Approach', 'İş felsefemiz ve değerlerimiz', 'Our business philosophy and values',
 'Navexmar Yaklaşımımız - İş Felsefemiz ve Değerlerimiz',
 'Navexmar Our Approach - Our Business Philosophy and Values'),
 
('policies', 'Politikalarımız', 'Our Policies', 'Kurumsal değerler ve ilkelerimiz', 'Our corporate values and principles',
 'Navexmar Politikalar - Kurumsal Politikalarımız',
 'Navexmar Policies - Our Corporate Policies');

-- ==========================================
-- 5. PAGE_SECTIONS TABLOSU
-- ==========================================
DROP TABLE IF EXISTS page_sections;
CREATE TABLE page_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(50) NOT NULL,
    section_key VARCHAR(50) NOT NULL,
    title_tr VARCHAR(255),
    title_en VARCHAR(255),
    content_tr TEXT,
    content_en TEXT,
    section_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_page_section (page_key, section_key),
    INDEX idx_page_key (page_key),
    INDEX idx_section_order (section_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan bölümler
INSERT INTO page_sections (page_key, section_key, title_tr, title_en, content_tr, content_en, section_order) VALUES
('home', 'hero_title', 'EN İYİYİ KEŞFEDİN', 'DISCOVER THE BEST', '', '', 1),
('home', 'hero_subtitle', 'OPTİMİZASYONA ADANMIŞ', 'DEDICATED TO OPTIMIZATION', '', '', 2),
('home', 'hero_description', '', '', 
 'Navexmar, sektördeki diğer firmalardan farklı bir yaklaşımla gemi bakım ve uluslararası ticaret hizmetleri sunan bir şirkettir. Kuruluşumuzdan bu yana, insan odaklı, tüm ortaklarımızı kapsayan ve her yönüyle entegre bir şirket olmaya çalışıyoruz.',
 'Navexmar is a company that provides ship maintenance and international trade services with a different approach from other companies in the industry. Since our establishment, we have been trying to be a human-oriented company that includes all our partners and is integrated in every aspect.', 3),
('home', 'about_description', '', '',
 'Navexmar, sektördeki diğer firmalardan farklı bir yaklaşımla gemi bakım hizmetleri sunan bir şirkettir. Kuruluşumuzdan bu yana, insan odaklı, tüm ortaklarımızı kapsayan ve her yönüyle entegre bir şirket olmaya çalışıyoruz.',
 'Navexmar is a company that provides ship maintenance services with a different approach from other companies in the industry. Since our establishment, we have been trying to be a human-oriented company that includes all our partners and is integrated in every aspect.', 4),
('home', 'cta_title', 'Şimdi Bizimle İletişime Geçin!', 'Contact Us Now!', '', '', 5);

-- ==========================================
-- 6. HEADER_IMAGES TABLOSU
-- ==========================================
DROP TABLE IF EXISTS header_images;
CREATE TABLE header_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(50) NOT NULL COMMENT 'home, services, contact, approach, policies, all',
    image_name VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_size INT COMMENT 'Dosya boyutu byte cinsinden',
    is_active TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_page_key (page_key),
    INDEX idx_is_active (is_active),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 7. PAGE_HEADER_SETTINGS TABLOSU
-- ==========================================
DROP TABLE IF EXISTS page_header_settings;
CREATE TABLE page_header_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(50) NOT NULL UNIQUE,
    selected_image_id INT,
    use_random TINYINT(1) DEFAULT 0 COMMENT '1: Rastgele görsel, 0: Seçili görsel',
    overlay_opacity DECIMAL(3,2) DEFAULT 0.50 COMMENT '0.00 - 1.00 arası',
    overlay_color VARCHAR(20) DEFAULT '#000000',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (selected_image_id) REFERENCES header_images(id) ON DELETE SET NULL,
    INDEX idx_page_key (page_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan header ayarları
INSERT INTO page_header_settings (page_key, use_random, overlay_opacity) VALUES
('home', 0, 0.50),
('services', 0, 0.50),
('contact', 0, 0.50),
('approach', 0, 0.50),
('policies', 0, 0.50);

-- ==========================================
-- 8. SETTINGS TABLOSU
-- ==========================================
DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_group VARCHAR(50) COMMENT 'general, contact, social, seo',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key),
    INDEX idx_setting_group (setting_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan ayarlar
INSERT INTO settings (setting_key, setting_value, setting_group, description) VALUES
-- Genel Ayarlar
('site_name', 'NAVEXMAR', 'general', 'Site adı'),
('site_logo', '', 'general', 'Logo dosya yolu'),
('site_favicon', '', 'general', 'Favicon dosya yolu'),
('site_language', 'tr', 'general', 'Varsayılan dil'),
('maintenance_mode', '0', 'general', 'Bakım modu (0: Kapalı, 1: Açık)'),

-- İletişim Bilgileri
('contact_email_1', 'agency@navexmar.com', 'contact', 'Birincil e-posta'),
('contact_email_2', 'olcay@navexmar.com', 'contact', 'İkincil e-posta 1'),
('contact_email_3', 'burak@navexmar.com', 'contact', 'İkincil e-posta 2'),
('contact_phone_1', '+90 530 379 31 33', 'contact', 'Telefon 1 (Olcay)'),
('contact_phone_2', '+90 544 401 21 86', 'contact', 'Telefon 2 (Burak)'),
('contact_address', 'Numune Evler Mah/Sahil 1 Nolu Sok/no2/Dörtyol/Hatay', 'contact', 'Adres'),
('contact_city', 'Hatay', 'contact', 'Şehir'),
('contact_country', 'Türkiye', 'contact', 'Ülke'),
('working_hours', 'Zaman Kısıtı Olmaksızın 7/24 Operayyon Takibi', 'contact', 'Çalışma saatleri'),

-- Sosyal Medya
('social_facebook', '', 'social', 'Facebook URL'),
('social_twitter', '', 'social', 'Twitter URL'),
('social_linkedin', '', 'social', 'LinkedIn URL'),
('social_instagram', '', 'social', 'Instagram URL'),

-- SEO Ayarları
('seo_meta_title_tr', 'Navexmar - Gemi Bakım ve Lojistik Hizmetleri', 'seo', 'Site başlığı (TR)'),
('seo_meta_title_en', 'Navexmar - Ship Maintenance and Logistics Services', 'seo', 'Site başlığı (EN)'),
('seo_meta_description_tr', 'Navexmar, gemi bakım ve uluslararası ticaret hizmetleri sunan denizcilik şirketidir.', 'seo', 'Site açıklaması (TR)'),
('seo_meta_description_en', 'Navexmar is a maritime company providing ship maintenance and international trade services.', 'seo', 'Site açıklaması (EN)'),
('seo_meta_keywords', 'gemi bakımı, ship maintenance, denizcilik, maritime, lojistik, logistics', 'seo', 'Anahtar kelimeler'),
('google_analytics', '', 'seo', 'Google Analytics ID');

-- ==========================================
-- TAMAMLANDI!
-- ==========================================
-- Bu SQL dosyasını MySQL'e import edin:
-- mysql -u kullanici_adi -p veritabani_adi < database_complete.sql
-- 
-- Admin Giriş Bilgileri:
-- Kullanıcı: admin
-- Şifre: admin31
-- ==========================================
