CREATE TABLE IF NOT EXISTS api_calls_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_at DATETIME NOT NULL,
    endpoint VARCHAR(255) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    processing_time DECIMAL(7,4) NOT NULL,
    http_code INT UNSIGNED NOT NULL,
    request_data TEXT,
    response_data TEXT,
    INDEX (endpoint)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;