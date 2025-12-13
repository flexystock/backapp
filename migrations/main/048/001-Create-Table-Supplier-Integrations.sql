CREATE TABLE IF NOT EXISTS `supplier_integrations` (
  -- Primary Key
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  
  -- Foreign Key
  `supplier_id` INT NOT NULL,
  
  -- Configuración
  `integration_type` ENUM('choco', 'direct_api', 'email', 'manual') NOT NULL,
  `api_endpoint` VARCHAR(500),
  `api_key_required` BOOLEAN DEFAULT FALSE,
  `webhook_support` BOOLEAN DEFAULT FALSE,
  
  -- Documentación
  `documentation_url` VARCHAR(500),
  `setup_instructions` TEXT,
  `config_schema` JSON,
  
  -- Estado
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Foreign Keys
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE CASCADE,
  
  -- Índices
  INDEX `idx_supplier_type` (`supplier_id`, `integration_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
