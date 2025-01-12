ALTER TABLE scales
ADD COLUMN voltage_percentage DECIMAL(5,2) NOT NULL DEFAULT '0' COMMENT 'Porcentaje de carga de la báscula' AFTER voltage_min;