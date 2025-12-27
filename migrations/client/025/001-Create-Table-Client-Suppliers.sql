CREATE TABLE IF NOT EXISTS `client_suppliers` (
  -- Primary Key
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  
  -- Referencia a BD MAIN (sin FK Doctrine)
  `supplier_id` INT NOT NULL COMMENT 'FK a main.suppliers.id',
  
  -- Info específica del cliente
  `email` VARCHAR(255),
  `phone` VARCHAR(50),
  `contact_person` VARCHAR(255),
  `delivery_days` INT DEFAULT 2,
  `address` TEXT,
  
  -- Integración
  `integration_enabled` BOOLEAN DEFAULT FALSE,
  `integration_config` JSON,
  
  -- Notas
  `notes` TEXT,
  `internal_code` VARCHAR(100),
  
  -- Estado
  `is_active` BOOLEAN DEFAULT TRUE,
  
  -- Timestamps
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Índices
  UNIQUE KEY `unique_supplier_per_client` (`supplier_id`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
