CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'user') DEFAULT 'user',
    activation_code VARCHAR(10),
    is_active TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS platforms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    version VARCHAR(20),
    demo_url VARCHAR(255),
    image_url VARCHAR(255),
    price DECIMAL(10,2) DEFAULT 0.00,
    discount_price DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT
) ENGINE=InnoDB;

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('theme', 'light');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('site_name', 'Web Platforms Showcase');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('hero_title', 'Platformele Noastre Web');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('hero_subtitle', 'Explorați creațiile noastre recente și testați demo-urile interactive.');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('order_button_text', 'Comandă Acum');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('order_url', 'offers.php');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('admin_theme', 'standard');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('paypal_email', '');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('stripe_publishable_key', '');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('stripe_secret_key', '');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('enable_paypal', '0');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('enable_stripe', '0');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('live_chat_code', '');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('logo_type', 'text');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('logo_image', '');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('footer_name', 'Web Showcase');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('seo_meta_title', 'Web Platforms Showcase');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('seo_meta_description', '');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('seo_keywords', '');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('use_smtp', '0');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('smtp_host', '');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('smtp_port', '587');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('smtp_user', '');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('smtp_pass', '');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('smtp_encryption', 'tls');

CREATE TABLE IF NOT EXISTS translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lang VARCHAR(5) NOT NULL,
    translation_key VARCHAR(100) NOT NULL,
    translation_value TEXT,
    UNIQUE KEY (lang, translation_key)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    seo_title VARCHAR(255),
    seo_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location ENUM('header', 'footer') DEFAULT 'header',
    title VARCHAR(100),
    url VARCHAR(255),
    sort_order INT DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    platform_id INT,
    amount DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (platform_id) REFERENCES platforms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS chat_discussions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    visitor_token VARCHAR(100) NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    discussion_id INT,
    sender ENUM('user', 'admin') DEFAULT 'user',
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (discussion_id) REFERENCES chat_discussions(id) ON DELETE CASCADE
) ENGINE=InnoDB;
