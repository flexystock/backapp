ALTER TABLE business_group
ADD COLUMN active tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si el grupo empresarial esta o no activo';
