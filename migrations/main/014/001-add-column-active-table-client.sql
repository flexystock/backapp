ALTER TABLE client
ADD COLUMN active TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Si el cliente está o no activo' AFTER host;

