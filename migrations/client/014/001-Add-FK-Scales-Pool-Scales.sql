ALTER TABLE scales
    MODIFY uuid VARCHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

ALTER TABLE pool_scales
    MODIFY uuid VARCHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;


ALTER TABLE scales
    ADD CONSTRAINT fk_scales_pool_scales
    FOREIGN KEY (uuid) REFERENCES pool_scales(uuid)
    ON DELETE CASCADE ON UPDATE CASCADE;
