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
-- Password for 'admin' is: AdminPass123!
-- Password for 'student' is: StudentPass123!
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@lab.local', '$2y$10$wE9r7tC9A5XGk8M1N2O3P.qR4S5T6U7V8W9X0Y1Z2A3B4C5D6E7F8', 'administrator'),
('student', 'student@lab.local', '$2y$10$Z1Y0X9W8V7U6T5S4R3Q2P.O1N0M9L8K7J6I5H4G3F2E1D0C9B8', 'user');
