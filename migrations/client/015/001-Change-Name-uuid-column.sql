-- 1. Elimina la FK si existe
ALTER TABLE scales DROP FOREIGN KEY fk_scales_pool_scales;

-- 2. Renombra el campo en ambas tablas
ALTER TABLE scales CHANGE uuid uuid_scale VARCHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE pool_scales CHANGE uuid uuid_scale VARCHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

-- 3. (Opcional) Verifica que las PRIMARY KEY están bien
-- Normalmente se actualizan solas, pero puedes revisar

-- 4. Crea la FK de nuevo
ALTER TABLE scales
    ADD CONSTRAINT fk_scales_pool_scales
    FOREIGN KEY (uuid_scale) REFERENCES pool_scales(uuid_scale)
    ON DELETE CASCADE ON UPDATE CASCADE;
