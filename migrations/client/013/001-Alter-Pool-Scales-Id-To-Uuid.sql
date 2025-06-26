ALTER TABLE pool_scales
    ADD COLUMN uuid VARCHAR(36) NOT NULL AFTER id;

UPDATE pool_scales SET uuid = UUID() WHERE uuid IS NULL;

ALTER TABLE pool_scales
    DROP PRIMARY KEY,
    DROP COLUMN id,
    ADD PRIMARY KEY (uuid);
