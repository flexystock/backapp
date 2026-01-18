ALTER TABLE scales
ADD COLUMN active TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Estado de activación de la báscula (0=desactivada, 1=activa)' AFTER uuid_scale;
