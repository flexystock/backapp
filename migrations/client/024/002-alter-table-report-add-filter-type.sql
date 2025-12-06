-- Añadir nueva columna filter_type
ALTER TABLE report
ADD COLUMN filter_type ENUM('all', 'below_stock', 'specific') NOT NULL DEFAULT 'all'
AFTER product_filter;

-- Migrar datos existentes: copiar product_filter a filter_type
UPDATE report SET filter_type = product_filter;

-- Nota: Mantenemos product_filter por compatibilidad temporal
-- En una migración futura se puede eliminar después de actualizar todo el código