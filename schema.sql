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
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('admin_theme', 'standard');
