-- migrations/main/001/001.sql
CREATE TABLE IF NOT EXISTS migrations_version (
      id INT AUTO_INCREMENT PRIMARY KEY,
      version VARCHAR(255) NOT NULL,
      script VARCHAR(255) NOT NULL,
      executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
