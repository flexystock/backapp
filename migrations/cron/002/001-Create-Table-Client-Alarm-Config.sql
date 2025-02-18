CREATE TABLE client_alarm_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id CHAR(36) NOT NULL,
    product_id INT NOT NULL,
    alarm_threshold DECIMAL(5, 2) NOT NULL,
    alarm_type VARCHAR(50) NOT NULL,  -- Por ejemplo, 'min' o 'max'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);