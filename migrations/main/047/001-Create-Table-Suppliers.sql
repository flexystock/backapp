CREATE TABLE IF NOT EXISTS `suppliers` (
  -- Primary Key
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  
  -- Identificación
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  
  -- Branding
  `logo_url` VARCHAR(500),
  `website` VARCHAR(500),
  
  -- Clasificación
  `category` ENUM('mayorista', 'distribuidor', 'fabricante', 'marketplace') DEFAULT 'distribuidor',
  `country` VARCHAR(2) DEFAULT 'ES',
  `coverage_area` TEXT,
  `description` TEXT,
  
  -- Integración
  `has_api_integration` BOOLEAN DEFAULT FALSE,
  `integration_type` VARCHAR(50),
  
  -- Estado
  `is_active` BOOLEAN DEFAULT TRUE,
  `featured` BOOLEAN DEFAULT FALSE,
  
  -- Timestamps
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Índices
  INDEX `idx_slug` (`slug`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_category` (`category`),
  INDEX `idx_featured` (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
