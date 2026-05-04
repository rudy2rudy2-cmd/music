CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'staff',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS defects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    room_number TEXT NOT NULL,
    issue_type TEXT NOT NULL,
    description TEXT,
    status TEXT NOT NULL DEFAULT 'activ',
    priority TEXT NOT NULL DEFAULT 'Medie',
    reported_by INTEGER,
    reported_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    resolved_at DATETIME,
    resolved_subtasks TEXT DEFAULT '',
    FOREIGN KEY (reported_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS settings (
    setting_key TEXT PRIMARY KEY,
    setting_value TEXT
);

INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES ('copyright', 'Copyright 2026 Autor Stoian Rudolf');
INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES ('logo_path', '');
INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES ('theme', 'default');
INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES ('site_title', 'HotelDefects');
INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES ('logo_size', '32');
