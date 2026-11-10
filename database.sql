-- Optional: the app creates this automatically on first run.
-- Use this file if you prefer to import it in phpMyAdmin (Import tab).
CREATE DATABASE IF NOT EXISTS omsu_auth CHARACTER SET utf8mb4;
USE omsu_auth;
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
