-- Database Creation & Setup Script
CREATE DATABASE IF NOT EXISTS lab_security_db;
USE lab_security_db;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users
-- Password for 'admin' is: admin123
-- Password for 'student' is: student123
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@lab.local', '$2y$10$Xdwnkj5UTYZyTV3Y4HjHKOBe49hgpygUKD5cYEpnv0JGqOgIqw/GG', 'administrator'),
('student', 'student@lab.local', '$2y$10$B5oRYC4Qgg3DoS2USxcFHuqqjizNnZlAL/Y7TctJcxdfR3psNczXC', 'user');
