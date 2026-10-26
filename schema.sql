-- ==========================================================
-- Smart Accident Alert System - Database Schema
-- ==========================================================
 
 
-- ----------------------------------------------------------
-- Users table (Login / Signup)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    blood_group VARCHAR(10) DEFAULT NULL,
    vehicle_no VARCHAR(30) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
-- If you previously added the qr_token column for the QR code feature
-- (now removed) and want to clean it up, you can optionally run:
-- ALTER TABLE users DROP COLUMN qr_token;
 
-- ----------------------------------------------------------
-- Emergency Contacts (Family, Hospital, Ambulance)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS emergency_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    contact_name VARCHAR(100) NOT NULL,
    contact_type ENUM('Family','Hospital','Ambulance','Friend','Other') DEFAULT 'Family',
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(120) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
 
-- ----------------------------------------------------------
-- Accident / SOS Alerts Log
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    alert_type ENUM('Manual SOS','AI Auto-Detected','Impact Detected','No Movement') DEFAULT 'Manual SOS',
    latitude DECIMAL(10,7) DEFAULT NULL,
    longitude DECIMAL(10,7) DEFAULT NULL,
    speed_kmh DECIMAL(6,2) DEFAULT NULL,
    ai_confidence INT DEFAULT NULL COMMENT 'AI risk score 0-100',
    status ENUM('Sent','Cancelled','Resolved') DEFAULT 'Sent',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
 
-- ----------------------------------------------------------
-- Driving Behaviour Log (for AI analysis / false-alert reduction)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS driving_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    speed_kmh DECIMAL(6,2) DEFAULT NULL,
    acceleration DECIMAL(6,2) DEFAULT NULL,
    latitude DECIMAL(10,7) DEFAULT NULL,
    longitude DECIMAL(10,7) DEFAULT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
 
