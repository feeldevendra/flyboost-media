-- ------------------------------------------------------------
-- Flyboost Media 360° — Database Schema
-- ------------------------------------------------------------
-- Version: 1.0
-- Compatible with MySQL 5.7+ / 8.0+
-- ------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS flyboost_media CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE flyboost_media;

-- ==========================
-- 1. USERS
-- ==========================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  role ENUM('ADMIN','CLIENT','PROJECT_MANAGER') DEFAULT 'CLIENT',
  last_login DATETIME,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- 2. SERVICES
-- ==========================
CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL UNIQUE,
  description TEXT,
  short_description VARCHAR(255),
  icon VARCHAR(255),
  feature_image VARCHAR(255),
  min_price DECIMAL(10,2),
  max_price DECIMAL(10,2),
  status ENUM('active','inactive') DEFAULT 'active',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- 3. SERVICE OPTIONS (for configurator)
-- ==========================
CREATE TABLE service_options (
  id INT AUTO_INCREMENT PRIMARY KEY,
  service_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) DEFAULT 0,
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- ==========================
-- 4. LEADS / QUOTE REQUESTS
-- ==========================
CREATE TABLE leads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150),
  email VARCHAR(150),
  phone VARCHAR(20),
  service VARCHAR(150),
  message TEXT,
  status ENUM('pending','contacted') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- 5. PROJECTS
-- ==========================
CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(150),
  description TEXT,
  milestones JSON,
  files JSON,
  status ENUM('pending','in_progress','completed','paused') DEFAULT 'pending',
  progress INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================
-- 6. CHAT MESSAGES
-- ==========================
CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  user_id INT NOT NULL,
  message TEXT,
  file_path VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================
-- 7. PAYMENTS
-- ==========================
CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_id VARCHAR(100) UNIQUE,
  amount DECIMAL(10,2),
  currency VARCHAR(10) DEFAULT 'INR',
  gateway ENUM('cashfree','razorpay','stripe') DEFAULT 'cashfree',
  status ENUM('PENDING','SUCCESS','FAILED') DEFAULT 'PENDING',
  transaction_id VARCHAR(150),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================
-- 8. BLOGS
-- ==========================
CREATE TABLE blogs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  content LONGTEXT NOT NULL,
  feature_image VARCHAR(255),
  meta_title VARCHAR(255),
  meta_description TEXT,
  is_published TINYINT(1) DEFAULT 0,
  show_ads TINYINT(1) DEFAULT 0,
  published_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- 9. MEDIA
-- ==========================
CREATE TABLE media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  file_name VARCHAR(255),
  file_path VARCHAR(255),
  file_type VARCHAR(50),
  uploader_id INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (uploader_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ==========================
-- 10. SETTINGS
-- ==========================
CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(150) UNIQUE,
  setting_value TEXT,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================
-- 11. NOTIFICATIONS
-- ==========================
CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(50),
  channel VARCHAR(50),
  message TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- 12. BACKUPS
-- ==========================
CREATE TABLE backups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255),
  size VARCHAR(50),
  type ENUM('manual','auto') DEFAULT 'manual',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- 13. SUBSCRIPTIONS (optional recurring services)
-- ==========================
CREATE TABLE subscriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  plan_name VARCHAR(150),
  amount DECIMAL(10,2),
  interval_unit ENUM('month','year') DEFAULT 'month',
  next_billing_date DATE,
  status ENUM('active','cancelled') DEFAULT 'active',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================
-- 14. REFERRALS / AFFILIATES
-- ==========================
CREATE TABLE referrals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  referral_code VARCHAR(50),
  referred_email VARCHAR(150),
  reward_amount DECIMAL(10,2) DEFAULT 0,
  status ENUM('pending','approved','paid') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================
-- DEFAULT ADMIN ACCOUNT
-- ==========================
INSERT INTO users (name, email, password, role)
VALUES ('Administrator', 'admin@flyboostmedia.com', '$2y$10$exampleHashedPassword', 'ADMIN');
